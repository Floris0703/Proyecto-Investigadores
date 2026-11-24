<?php
/** Smoke del módulo Alumnos sin SQL Server real. */
declare(strict_types=1);
if (!defined('SQLSRV_FETCH_ASSOC')) define('SQLSRV_FETCH_ASSOC',2);
final class AlumnoMockStatement {
    public int $position=0;
    public function __construct(public array $rows) {}
}
function sqlsrv_connect($host,$opts): object { return (object)['connected'=>true]; }
function sqlsrv_query($conn,string $sql,array $params=[]): object {
    if (str_contains($sql,'FROM plataforma.Usuario u') && str_contains($sql,'plataforma.UsuarioRol')) {
        return new AlumnoMockStatement([['Nombre'=>($GLOBALS['alumno_smoke_role']??'ALUMNO')]]);
    }
    if (str_contains($sql,'FROM academico.Alumno a') && str_contains($sql,'JOIN plataforma.Usuario u')) {
        return new AlumnoMockStatement([[
            'AlumnoId'=>2,'UsuarioId'=>7,'Matricula'=>'ALU-0007','Carrera'=>'Ingeniería en Sistemas',
            'Semestre'=>5,'Correo'=>'alumno@example.test','Nombre'=>'Estudiante',
            'ApellidoPaterno'=>'Prueba','ApellidoMaterno'=>null,'Biografia'=>'Interés en investigación'
        ]]);
    }
    if (str_contains($sql,"OBJECT_ID(?, 'U')")) return new AlumnoMockStatement([['Disponible'=>0]]);
    if (str_contains($sql,'FROM academico.NivelDominio WHERE Activo=1')) {
        return new AlumnoMockStatement([['NivelDominioId'=>1,'Nombre'=>'Básico']]);
    }
    if (str_contains($sql,'FROM academico.UsuarioHabilidad')) return new AlumnoMockStatement([]);
    if (str_contains($sql,'FROM academico.Convocatoria c') && str_contains($sql,'JOIN academico.Profesor pr')) {
        return new AlumnoMockStatement([['ConvocatoriaId'=>1,'Titulo'=>'Proyecto escolar',
            'Descripcion'=>'Convocatoria abierta', 'FechaCierre'=>null,
            'Profesor'=>'Profesor de prueba','MiPostulacionId'=>null]]);
    }
    if (preg_match('/FROM academico\\.Postulacion p\\b/', $sql) && str_contains($sql,'JOIN academico.Convocatoria c')) {
        return new AlumnoMockStatement([['PostulacionId'=>3,'Estado'=>'PENDIENTE',
            'FechaPostulacion'=>new DateTimeImmutable('2026-11-19'),
            'Titulo'=>'Proyecto escolar','ConvocatoriaId'=>1,'FechaCierre'=>null]]);
    }
    if (str_contains($sql,'SELECT ProfesorId FROM academico.Profesor')) {
        return new AlumnoMockStatement([['ProfesorId'=>8]]);
    }
    if (str_contains($sql,'FROM academico.Postulacion po') && str_contains($sql,'c.UsuarioPublicadorId=?')) {
        return new AlumnoMockStatement([['PostulacionId'=>3,'EstadoPostulacion'=>'PENDIENTE',
            'Convocatoria'=>'Proyecto escolar','UsuarioId'=>7,'Matricula'=>'ALU-0007',
            'Carrera'=>'Ingeniería en Sistemas','Semestre'=>5,'Correo'=>'alumno@example.test',
            'Nombre'=>'Estudiante','ApellidoPaterno'=>'Prueba','ApellidoMaterno'=>null,
            'Biografia'=>'Interés en investigación']]);
    }
    return new AlumnoMockStatement([]);
}
function sqlsrv_fetch_array($stmt,int $mode): ?array { return $stmt->rows[$stmt->position++] ?? null; }
function sqlsrv_free_stmt($stmt): bool { return true; }
function sqlsrv_rows_affected($stmt): int { return 1; }

$pagina=$argv[1]??'index.php';
$alumnoPages=['index.php','perfil.php','postulaciones.php','cv.php'];
$esProfesor=$pagina==='cv-postulante.php';
if (!in_array($pagina,$alumnoPages,true) && !$esProfesor) exit(2);
$GLOBALS['alumno_smoke_role']=$esProfesor?'PROFESOR':'ALUMNO';
$_SERVER['REQUEST_METHOD']='GET';
$_SERVER['SCRIPT_NAME']=$esProfesor?'/profesores/cv-postulante.php':'/Alumnos/'.$pagina;
$_GET=$esProfesor?['postulacion_id'=>'3']:[];
session_start();
$_SESSION['usuario_id']=$esProfesor?8:7;
$_SESSION['tesco_rol_activo']=$GLOBALS['alumno_smoke_role'];
ob_start();
require __DIR__.'/../'.($esProfesor?'profesores/':'Alumnos/').$pagina;
$html=ob_get_clean();
foreach(['bootstrap-tesco.min.css','<main', $esProfesor?'CV de Estudiante':'TESCo'] as $needle) {
    if (!str_contains($html,$needle)) throw new RuntimeException('No aparece '.$needle.' en '.$pagina);
}
if (!$esProfesor && (substr_count($html,'id="menuLateral"')!==1 || !str_contains($html,'../Landing/Inicio.php'))) {
    throw new RuntimeException('El menú y acceso a bienvenida faltan en '.$pagina);
}
if ($esProfesor) {
    $source=file_get_contents(__DIR__.'/../includes/alumno_datos.php');
    if (!str_contains($source,'c.UsuarioPublicadorId=?')) throw new RuntimeException('Falta filtro dueño de convocatoria.');
}
if (!$esProfesor && $pagina === 'cv.php') {
    foreach (['name="habilidad_nombre"', 'name="nivel_dominio_id"', 'name="meses_experiencia"',
              'name="accion" value="habilidad"', 'Solo Administración podrá revisarlas'] as $needle) {
        if (!str_contains($html,$needle)) throw new RuntimeException('Falta formulario de habilidades: '.$needle);
    }
    $fuente=file_get_contents(__DIR__.'/../includes/alumno_datos.php');
    foreach (["EstadoVerificacion='DECLARADA'", "Pendiente de clasificación",
              'NivelDominioId=? AND Activo=1', "WHERE UsuarioId=? AND HabilidadId=?"] as $needle) {
        if (!str_contains($fuente,$needle)) throw new RuntimeException('Falta protección de habilidades: '.$needle);
    }
}
echo "OK: $pagina con rol ".($GLOBALS['alumno_smoke_role'])." y SQLSRV simulado\n";
