<?php
/** Render de vistas ADMIN con SQLSRV simulado. No modifica SQL Server. */
declare(strict_types=1);
if (!defined('SQLSRV_FETCH_ASSOC')) define('SQLSRV_FETCH_ASSOC',2);
final class AdminMockStmt {
    public int $index=0;
    public function __construct(public array $rows) {}
}
function sqlsrv_connect($host,$opts): object { return (object)['conectado'=>true]; }
function sqlsrv_query($conn,string $sql,array $params=[]): object {
    if (str_contains($sql,'FROM plataforma.Usuario u') && str_contains($sql,'JOIN plataforma.UsuarioRol')) {
        return new AdminMockStmt([['Nombre'=>$GLOBALS['admin_smoke_role']??'ADMIN']]);
    }
    if (str_contains($sql,'FROM plataforma.Usuario u') && str_contains($sql,'LEFT JOIN academico.Alumno al')) {
        return new AdminMockStmt([[
            'UsuarioId'=>7,'Correo'=>'alumno@example.test','EstadoCuenta'=>'ACTIVO',
            'FechaRegistro'=>new DateTimeImmutable('2026-11-19'),
            'Nombre'=>'Estudiante','ApellidoPaterno'=>'Prueba','Matricula'=>'TEST-ALU-001',
            'NumeroEmpleado'=>null,'EsAdmin'=>0,'Roles'=>'ALUMNO'
        ]]);
    }
    if (str_contains($sql,'FROM plataforma.Administrativo ad')) {
        return new AdminMockStmt([[
            'AdministrativoId'=>1,'UsuarioId'=>9,'NumeroEmpleado'=>'ADMIN-001',
            'AreaAdscripcion'=>'Sistemas','Cargo'=>'Administrador','Correo'=>'admin@example.test',
            'Nombre'=>'Persona','ApellidoPaterno'=>'Administradora',
            'ApellidoMaterno'=>null,'Biografia'=>'Gestión institucional'
        ]]);
    }
    if (str_contains($sql,'AS HabilidadesPendientes')) {
        return new AdminMockStmt([[
            'Usuarios'=>3,'Profesores'=>1,'Alumnos'=>1,'Proyectos'=>1,
            'Publicaciones'=>1,'Congresos'=>1,'Convocatorias'=>1,
            'Postulaciones'=>1,'HabilidadesPendientes'=>1
        ]]);
    }
    if (str_contains($sql,'FROM academico.CategoriaHabilidad ORDER BY Nombre')) {
        return new AdminMockStmt([['CategoriaId'=>2,'Nombre'=>'Pendiente de clasificación','Activo'=>1]]);
    }
    if (str_contains($sql,'FROM academico.NivelDominio ORDER BY Orden')) {
        return new AdminMockStmt([['NivelDominioId'=>1,'Nombre'=>'Básico','Orden'=>1,'Activo'=>1]]);
    }
    if (str_contains($sql,'FROM academico.UsuarioHabilidad uh')) {
        return new AdminMockStmt([[
            'UsuarioId'=>7,'HabilidadId'=>4,'Habilidad'=>'Python',
            'EstadoVerificacion'=>'DECLARADA','MesesExperiencia'=>6,'Nivel'=>'Básico',
            'Correo'=>'alumno@example.test','Categoria'=>'Pendiente de clasificación',
            'EvidenciaReferencia'=>null
        ]]);
    }
    return new AdminMockStmt([]);
}
function sqlsrv_fetch_array($stmt,int $mode): ?array { return $stmt->rows[$stmt->index++]??null; }
function sqlsrv_free_stmt($stmt): bool {return true;}
function sqlsrv_rows_affected($stmt): int {return 1;}

$pagina=$argv[1]??'index.php';
$paginas=['index.php','usuarios.php','convocatorias.php','postulaciones.php','habilidades.php','produccion.php','perfil.php'];
if (!in_array($pagina,$paginas,true)) exit(2);
$GLOBALS['admin_smoke_role']='ADMIN';
session_start();
$_SESSION['usuario_id']=9;
$_SESSION['tesco_rol_activo']='ADMIN';
$_SERVER['REQUEST_METHOD']='GET';
$_SERVER['SCRIPT_NAME']='/Admin/'.$pagina;
$_GET=[];
ob_start();
require __DIR__.'/../Admin/'.$pagina;
$html=ob_get_clean();
foreach (['bootstrap-tesco.min.css','../css/admin.css','id="menuLateral"','../Landing/Inicio.php','Cerrar sesión','<main'] as $needle) {
    if (!str_contains($html,$needle)) throw new RuntimeException('Falta '.$needle.' en '.$pagina);
}
if ($pagina==='habilidades.php') {
    foreach (['Iniciar revisión','clasificar_habilidad','categoria_nueva','nivel_nuevo'] as $needle) {
        if (!str_contains($html,$needle)) throw new RuntimeException('Falta operación de habilidad: '.$needle);
    }
}
if ($pagina==='usuarios.php') {
    foreach (['alta_usuario','asignar_rol','estado_usuario','autocomplete="new-password"'] as $needle) {
        if (!str_contains($html,$needle)) throw new RuntimeException('Falta gestión de usuarios: '.$needle);
    }
}
if ($pagina==='index.php' && !str_contains($html,'Habilidades por revisar'))
    throw new RuntimeException('Falta métrica de habilidades');
echo "OK: Admin/$pagina renderizado con rol ADMIN y SQLSRV simulado\n";
