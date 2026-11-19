<?php
/** TESCo | identidad por roles reales, sesiones supervisadas y CSRF. PHP 8+. */
declare(strict_types=1);
require_once __DIR__ . '/tesco_db.php';

function tesco_session_start(): void {
    if (session_status() === PHP_SESSION_ACTIVE) return;
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'lifetime' => 0, 'path' => '/', 'secure' => $https,
        'httponly' => true, 'samesite' => 'Lax'
    ]);
    session_start();
    if (isset($_SESSION['tesco_login_at'])) {
        $last = (int)($_SESSION['tesco_last_activity'] ?? $_SESSION['tesco_login_at']);
        if (time() - $last > 1800) {
            $_SESSION = [];
            session_regenerate_id(true);
        } else {
            $_SESSION['tesco_last_activity'] = time();
        }
    }
}
function tesco_csrf_public(): string {
    tesco_session_start();
    if (empty($_SESSION['tesco_public_csrf'])) {
        $_SESSION['tesco_public_csrf'] = bin2hex(random_bytes(32));
    }
    return (string)$_SESSION['tesco_public_csrf'];
}
function tesco_csrf_valid(mixed $posted): bool {
    return is_string($posted) && hash_equals(tesco_csrf_public(), $posted);
}
function tesco_usuario_actual(): ?int {
    tesco_session_start();
    $id = filter_var($_SESSION['usuario_id'] ?? null, FILTER_VALIDATE_INT);
    return $id !== false && $id > 0 ? (int)$id : null;
}
function tesco_roles_db(int $id): array {
    $rows = tesco_db_rows(
        "SELECT r.Nombre FROM plataforma.Usuario u
         JOIN plataforma.UsuarioRol ur ON ur.UsuarioId=u.UsuarioId
         JOIN plataforma.Rol r ON r.RolId=ur.RolId
         WHERE u.UsuarioId=? AND UPPER(LTRIM(RTRIM(u.EstadoCuenta))) IN ('ACTIVO','ACTIVA')
         ORDER BY r.Nombre", [$id]
    );
    $roles = [];
    foreach ($rows as $row) {
        $name = strtoupper(trim((string)$row['Nombre']));
        if (in_array($name, ['PROFESOR','ALUMNO','ADMIN'], true)) $roles[] = $name;
    }
    return array_values(array_unique($roles));
}
function tesco_login(string $email, string $password): bool {
    tesco_session_start();
    $now = time();
    $failed = array_values(array_filter((array)($_SESSION['tesco_failed_logins'] ?? []),
        static fn($t) => is_int($t) && $t > $now - 900));
    if (count($failed) >= 5) {
        $_SESSION['tesco_failed_logins'] = $failed;
        return false;
    }
    $email = trim($email);
    $user = null;
    if (strlen($email) <= 255 && filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($password) <= 4096) {
        $user = tesco_db_one(
            "SELECT UsuarioId,PasswordHash,EstadoCuenta FROM plataforma.Usuario WHERE Correo=?", [$email]);
    }
    $ok = is_array($user)
        && in_array(strtoupper(trim((string)$user['EstadoCuenta'])), ['ACTIVO','ACTIVA'], true)
        && password_verify($password, (string)$user['PasswordHash']);
    $roles = $ok ? tesco_roles_db((int)$user['UsuarioId']) : [];
    if (!$ok || !$roles) {
        $failed[] = $now;
        $_SESSION['tesco_failed_logins'] = $failed;
        return false;
    }
    session_regenerate_id(true);
    unset($_SESSION['tesco_failed_logins']);
    $_SESSION['usuario_id'] = (int)$user['UsuarioId'];
    $_SESSION['tesco_roles'] = $roles;
    $_SESSION['tesco_login_at'] = $now;
    $_SESSION['tesco_last_activity'] = $now;
    $_SESSION['tesco_rol_activo'] = count($roles) === 1 ? $roles[0] : null;
    $_SESSION['tesco_public_csrf'] = bin2hex(random_bytes(32));
    return true;
}
function tesco_roles_actuales(): array {
    $id = tesco_usuario_actual();
    if (!$id) return [];
    $roles = tesco_roles_db($id);
    if (!$roles) {
        tesco_logout_local();
        return [];
    }
    $_SESSION['tesco_roles'] = $roles;
    if (!in_array($_SESSION['tesco_rol_activo'] ?? null, $roles, true)) {
        $_SESSION['tesco_rol_activo'] = count($roles) === 1 ? $roles[0] : null;
    }
    return $roles;
}
function tesco_logout_local(): void {
    tesco_session_start();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 3600, 'path' => $params['path'],
            'domain' => $params['domain'], 'secure' => $params['secure'],
            'httponly' => $params['httponly'], 'samesite' => 'Lax'
        ]);
    }
    session_destroy();
}
function tesco_require_login(): array {
    if (!tesco_usuario_actual()) {
        header('Location: acceso.php', true, 303);
        exit;
    }
    $roles = tesco_roles_actuales();
    if (!$roles) {
        header('Location: acceso.php', true, 303);
        exit;
    }
    header('Cache-Control: private, no-store');
    return $roles;
}
function tesco_require_role(string $role): void {
    $roles = tesco_require_login();
    if (!in_array($role, $roles, true)) {
        http_response_code(403);
        exit('No tienes permiso para acceder a este módulo.');
    }
}
function tesco_redirect_post_login(): never {
    $roles = tesco_require_login();
    if (count($roles) > 1 && !in_array($_SESSION['tesco_rol_activo'] ?? null, $roles, true)) {
        header('Location: elegir_rol.php', true, 303);
    } else {
        header('Location: panel.php', true, 303);
    }
    exit;
}
