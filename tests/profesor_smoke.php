<?php
/**
 * Smoke tests de plantillas PHP sin acceso a LocalDB.
 * Los datos son exclusivamente de prueba y nunca se entregan en las vistas.
 */
declare(strict_types=1);
if (!defined('SQLSRV_FETCH_ASSOC')) define('SQLSRV_FETCH_ASSOC', 2);
if (!defined('SQLSRV_ERR_ALL')) define('SQLSRV_ERR_ALL', 0);
class MockStmt { public array $rows; public int $pos=0; public function __construct(array $rows){$this->rows=$rows;} }
function sqlsrv_connect($host,$options) { return new stdClass(); }
function sqlsrv_query($con,$sql,$params=[]) {
    if (str_contains($sql,'FROM academico.Profesor p') && str_contains($sql,'JOIN plataforma.Usuario u')) {
        return new MockStmt([[
            'ProfesorId'=>1,'UsuarioId'=>7,'Departamento'=>'Ingeniería en Sistemas Computacionales',
            'GradoAcademico'=>'Maestría','Correo'=>'profesor@example.test','Nombre'=>'Investigador',
            'ApellidoPaterno'=>'Demo','ApellidoMaterno'=>null,'Biografia'=>'Investigación académica'
        ]]);
    }
    if (str_contains($sql, 'OBJECT_ID(') || str_contains($sql,'COL_LENGTH('))
        return new MockStmt([['Existe'=>0]]);
    return new MockStmt([]);
}
function sqlsrv_fetch_array($stmt,$type) {
    return $stmt->rows[$stmt->pos++] ?? null;
}
function sqlsrv_free_stmt($stmt){ return true; }
function sqlsrv_errors($type=0){ return []; }
function sqlsrv_close($conn){ return true; }
$pagina=$argv[1]??'index.php';
$validas=['index.php','perfil.php','productividad.php','proyectos.php','publicaciones.php','congresos.php','titulacion.php','convocatorias.php','postulaciones.php'];
if(!in_array($pagina,$validas,true)) exit(2);
putenv('TESCO_PROFESOR_ID_DEV=1');
$_SERVER['REMOTE_ADDR']='127.0.0.1';
$_SERVER['SCRIPT_NAME']='/profesores/'.$pagina;
$_SERVER['REQUEST_METHOD']='GET';
ob_start();
require __DIR__.'/../profesores/'.$pagina;
$html=ob_get_clean();
foreach(['<main','menuLateral','nav flex-column','Investigador','bootstrap-tesco.min.css'] as $needle) {
    if (!str_contains($html,$needle)) {
        fwrite(STDERR, "Falta $needle en $pagina\n");
        exit(1);
    }
}
if(substr_count($html,'id="menuLateral"')!==1) {
    fwrite(STDERR, "Menú duplicado en $pagina\n");
    exit(1);
}
echo "OK: $pagina (".strlen($html)." bytes HTML)\n";
