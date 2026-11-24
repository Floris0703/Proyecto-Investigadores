<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/tesco_auth.php';
tesco_session_start();
header('Cache-Control: no-store');
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Método no permitido.');
}
if (!tesco_csrf_valid($_POST['csrf'] ?? null)) {
    http_response_code(403);
    exit('Sesión de formulario inválida. Regresa al acceso e inténtalo de nuevo.');
}
$email = (string)($_POST['correo'] ?? '');
$password = (string)($_POST['contrasena'] ?? '');
try {
    if (tesco_login($email, $password)) tesco_redirect_post_login();
    $_SESSION['tesco_login_error'] = 'No fue posible iniciar sesión. Revisa las credenciales y el estado de tu cuenta.';
} catch (Throwable $e) {
    error_log('TESCo auth: consulta de inicio de sesion no disponible');
    $_SESSION['tesco_login_error'] = 'El acceso no está disponible temporalmente. Inténtalo después.';
}
header('Location: acceso.php', true, 303);
exit;
