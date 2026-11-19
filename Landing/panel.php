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
if ($role === 'ADMIN') {
    try {
        $perfilAdmin = tesco_db_one(
            'SELECT TOP (1) AdministrativoId FROM plataforma.Administrativo WHERE UsuarioId=?',
            [tesco_usuario_actual()]
        );
        if ($perfilAdmin) {
            header('Location: ../Admin/index.php', true, 303);
            exit;
        }
    } catch (Throwable $e) {
        error_log('TESCo: error al validar perfil administrativo');
        http_response_code(503);
        exit('No fue posible verificar tu perfil de administración.');
    }
    http_response_code(403);
    exit('Tu cuenta tiene rol ADMIN, pero falta su registro en plataforma.Administrativo.');
}
http_response_code(403);
exit('No tienes un rol autorizado para acceder al panel.');

