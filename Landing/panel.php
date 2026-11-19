<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/tesco_auth.php';
$roles = tesco_require_login();
$role = (string)($_SESSION['tesco_rol_activo'] ?? '');
if (!in_array($role, $roles, true)) {
    header('Location: elegir_rol.php', true, 303);
    exit;
}
if ($role === 'PROFESOR') {
    try {
        $profile = tesco_db_one('SELECT TOP (1) ProfesorId FROM academico.Profesor WHERE UsuarioId=?', [tesco_usuario_actual()]);
        if ($profile) {
            header('Location: ../profesores/index.php', true, 303);
            exit;
        }
    } catch (Throwable $e) {
        error_log('TESCo: error al validar perfil de profesor');
        http_response_code(503);
        exit('No fue posible verificar tu perfil en este momento.');
    }
    http_response_code(403);
    exit('Tu usuario tiene rol PROFESOR, pero no tiene un perfil docente asociado.');
}
if ($role === 'ALUMNO') {
    try {
        $perfilAlumno = tesco_db_one(
            'SELECT TOP (1) AlumnoId FROM academico.Alumno WHERE UsuarioId=?',
            [tesco_usuario_actual()]
        );
        if ($perfilAlumno) {
            header('Location: ../Alumnos/index.php', true, 303);
            exit;
        }
    } catch (Throwable $e) {
        error_log('TESCo: error al validar perfil de alumno');
        http_response_code(503);
        exit('No fue posible verificar tu perfil en este momento.');
    }
    http_response_code(403);
    exit('Tu usuario tiene rol ALUMNO, pero no tiene una matrícula asociada en academico.Alumno.');
}
$label = 'Administración';
?>
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?=tesco_escape($label)?> | TESCo</title><link rel="stylesheet" href="../css/bootstrap.min.css"><link rel="stylesheet" href="../css/style.css"></head>
<body class="bg-light"><main class="container py-5">
<h1 class="h3">Acceso verificado: <?=tesco_escape($label)?></h1>
<div class="alert alert-info">Tu sesión y tu rol se validaron contra SQL Server. El módulo <?=tesco_escape($label)?> aún no está integrado con controles de acceso PHP; por seguridad no se redirige a sus páginas HTML públicas.</div>
<a class="btn btn-outline-primary" href="Inicio.php">Ver Landing</a>
<?php if (count($roles) > 1): ?><a class="btn btn-outline-secondary" href="elegir_rol.php">Cambiar rol</a><?php endif; ?>
<form action="cerrar_sesion.php" method="post" class="d-inline">
<input type="hidden" name="csrf" value="<?=tesco_escape(tesco_csrf_public())?>">
<button type="submit" class="btn btn-outline-danger">Cerrar sesión</button></form>
</main></body></html>
