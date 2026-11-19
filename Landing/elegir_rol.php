<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/tesco_auth.php';
$roles = tesco_require_login();
if (count($roles) === 1) {
    $_SESSION['tesco_rol_activo'] = $roles[0];
    header('Location: panel.php', true, 303);
    exit;
}
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!tesco_csrf_valid($_POST['csrf'] ?? null)) {
        http_response_code(403); exit('Formulario inválido.');
    }
    $chosen = (string)($_POST['rol'] ?? '');
    if (in_array($chosen, $roles, true)) {
        $_SESSION['tesco_rol_activo'] = $chosen;
        header('Location: panel.php', true, 303);
        exit;
    }
    http_response_code(403); exit('Rol no autorizado.');
}
?>
<!DOCTYPE html>
<html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Seleccionar rol | TESCo</title><link rel="stylesheet" href="../css/bootstrap.min.css"><link rel="stylesheet" href="../css/style.css"></head>
<body class="bg-light"><main class="container py-5" style="max-width:600px">
<h1 class="h3">Selecciona tu función institucional</h1>
<p>Tu cuenta tiene varios roles autorizados en SQL Server.</p>
<form method="post">
<input type="hidden" name="csrf" value="<?=tesco_escape(tesco_csrf_public())?>">
<?php foreach ($roles as $role): ?>
<div class="custom-control custom-radio mb-3">
<input class="custom-control-input" type="radio" name="rol" id="rol-<?=tesco_escape($role)?>" value="<?=tesco_escape($role)?>" required>
<label class="custom-control-label" for="rol-<?=tesco_escape($role)?>"><?=tesco_escape($role)?></label>
</div>
<?php endforeach; ?>
<button type="submit" class="btn btn-primary">Continuar</button>
</form>
<form method="post" action="cerrar_sesion.php" class="mt-3">
<input type="hidden" name="csrf" value="<?=tesco_escape(tesco_csrf_public())?>">
<button class="btn btn-link" type="submit">Cerrar sesión</button>
</form>
</main></body></html>
