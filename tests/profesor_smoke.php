<?php
/**
 * Render del módulo profesor, con SQLSRV simulado y cuenta autenticada.
 * Modos adicionales: uid12, alumno, inactivo, sin_perfil.
 * Ejecutar cada caso en proceso independiente para comprobar controles de salida.
 */
declare(strict_types=1);
if (!defined('SQLSRV_FETCH_ASSOC')) define('SQLSRV_FETCH_ASSOC', 2);
if (!defined('SQLSRV_ERR_ALL')) define('SQLSRV_ERR_ALL', 0);
final class MockStmt {
    public int $pos=0;
    public function __construct(public array $rows) {}
}
$pagina=$argv[1]??'index.php';
$modo=$argv[2]??'profesor';
$validas=['index.php','perfil.php','productividad.php','proyectos.php',
          'publicaciones.php','congresos.php','titulacion.php','convocatorias.php','postulaciones.php'];
if (!in_array($pagina,$validas,true) ||
    !in_array($modo,['profesor','uid12','alumno','inactivo','sin_perfil'],true)) exit(2);
$GLOBALS['mock_modo']=$modo;
$GLOBALS['mock_rol']=$modo==='alumno'?'ALUMNO':'PROFESOR';
$GLOBALS['mock_uid']=$modo==='uid12'?12:7;
$GLOBALS['mock_profesor_queries']=0;

function sqlsrv_connect($host,$options): object {return new stdClass();}
function sqlsrv_query($con,$sql,$params=[]): MockStmt {
    $modo=$GLOBALS['mock_modo'];
    if (str_contains($sql, 'FROM plataforma.Usuario u')
        && str_contains($sql, 'JOIN plataforma.UsuarioRol ur')) {
        return new MockStmt($modo==='inactivo'?[]:[['Nombre'=>$GLOBALS['mock_rol']]]);
    }
    if (str_contains($sql,'FROM academico.Profesor p')
        && str_contains($sql,'JOIN plataforma.Usuario u')) {
        $GLOBALS['mock_profesor_queries']++;
        // Nunca permitir el filtro antiguo p.ProfesorId ni otro UsuarioId.
        if (!str_contains($sql,'WHERE p.UsuarioId=?')
            || !str_contains($sql,"IN ('ACTIVO','ACTIVA')")
            || ($params[0]??null)!==$GLOBALS['mock_uid']) {
            throw new RuntimeException('La consulta debe usar UsuarioId de la sesión, no ProfesorId.');
        }
        if ($modo==='sin_perfil') return new MockStmt([]);
        return new MockStmt([[
            'ProfesorId'=>$GLOBALS['mock_uid']===12?5:1,'UsuarioId'=>$GLOBALS['mock_uid'],
            'Departamento'=>'Ingeniería en Sistemas Computacionales',
            'GradoAcademico'=>'Maestría','Correo'=>'profesor@example.test',
            'Nombre'=>$GLOBALS['mock_uid']===12?'Profesora':'Investigador',
            'ApellidoPaterno'=>'Demo','ApellidoMaterno'=>null,
            'Biografia'=>'Investigación académica'
        ]]);
    }
    if (str_contains($sql, 'OBJECT_ID(') || str_contains($sql,'COL_LENGTH('))
        return new MockStmt([['Existe'=>0]]);
    return new MockStmt([]);
}
function sqlsrv_fetch_array($stmt,$type): ?array {
    return $stmt->rows[$stmt->pos++]??null;
}
function sqlsrv_free_stmt($stmt): bool {return true;}
function sqlsrv_errors($type=0): array {return [];}
function sqlsrv_close($conn): bool {return true;}

$_SERVER['REMOTE_ADDR']='127.0.0.1';
$_SERVER['SCRIPT_NAME']='/profesores/'.$pagina;
$_SERVER['REQUEST_METHOD']='GET';
$_GET=[];
session_start();
$_SESSION['usuario_id']=$GLOBALS['mock_uid'];
$_SESSION['tesco_rol_activo']=$GLOBALS['mock_rol'];
$_SESSION['tesco_login_at']=time();
$_SESSION['tesco_last_activity']=time();
ob_start();
require __DIR__.'/../profesores/'.$pagina;
$html=ob_get_clean();
if (in_array($modo,['alumno','inactivo','sin_perfil'],true)) {
    fwrite(STDERR,"No se denegó el acceso al modo $modo\n");
    exit(1);
}
foreach (['<main','menuLateral','nav flex-column','bootstrap-tesco.min.css',
          'Cerrar sesión','Landing/Inicio.php'] as $needle) {
    if (!str_contains($html,$needle)) {
        fwrite(STDERR,"Falta $needle en $pagina\n");exit(1);
    }
}
$nombre=$modo==='uid12'?'Profesora Demo':'Investigador Demo';
if (!str_contains($html,$nombre) || !str_contains($html,'UsuarioId '.$GLOBALS['mock_uid'])) {
    fwrite(STDERR,"No se muestra la identidad correcta\n");exit(1);
}
if (substr_count($html,'id="menuLateral"')!==1 || $GLOBALS['mock_profesor_queries']!==1) {
    fwrite(STDERR,"Menú duplicado o consulta de identidad incorrecta\n");exit(1);
}
echo "OK: $pagina UsuarioId=".$GLOBALS['mock_uid']." (".strlen($html)." bytes)\n";
