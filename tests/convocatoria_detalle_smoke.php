<?php
/** Smoke de detalle de convocatoria, roles y postulación visible; SQLSRV simulado. */
declare(strict_types=1);
if (!defined('SQLSRV_FETCH_ASSOC')) define('SQLSRV_FETCH_ASSOC', 2);
final class ConvocatoriaMockStatement {
    public int $position = 0;
    public function __construct(public array $rows) {}
}
function sqlsrv_connect(string $host,array $options): object { return (object)['connected'=>true]; }
function sqlsrv_query(object $conn,string $sql,array $params=[]): object {
    $role = $GLOBALS['conv_test_role'] ?? 'ANONIMO';
    if (str_contains($sql,'FROM plataforma.Usuario u') && str_contains($sql,'plataforma.UsuarioRol')) {
        if ($role === 'MULTIRROL') return new ConvocatoriaMockStatement([['Nombre'=>'ALUMNO'],['Nombre'=>'PROFESOR']]);
        return new ConvocatoriaMockStatement([['Nombre'=>$role==='POSTULADO'?'ALUMNO':$role]]);
    }
    if (str_contains($sql,'FROM academico.Convocatoria c') && str_contains($sql,'WHERE c.ConvocatoriaId=?')) {
        if (($params[0]??null)!==4) return new ConvocatoriaMockStatement([]);
        return new ConvocatoriaMockStatement([[
            'ConvocatoriaId'=>4,'Titulo'=>'Laboratorio de inteligencia artificial',
            'Descripcion'=>'Desarrollar un prototipo <seguro>.',
            'Requisitos'=>'Interés en Python y disponibilidad de 6 horas.',
            'FechaPublicacion'=>new DateTimeImmutable('2026-09-01'),
            'FechaInicioRecepcion'=>new DateTimeImmutable('2026-09-02'),
            'FechaCierre'=>new DateTimeImmutable('2026-12-01'),
            'CupoTotal'=>5,'HorarioColaboracion'=>'Lunes y miércoles',
            'UrlInformacion'=>'https://tesco.edu.mx/',
            'TipoConvocatoria'=>'Colaboración en investigación',
            'ProyectoTitulo'=>'Análisis de datos académicos',
            'Profesor'=>'Profesora Demo','DepartamentoProfesor'=>'Sistemas Computacionales',
            'GradoAcademicoProfesor'=>'Maestría'
        ]]);
    }
    if (str_contains($sql,'FROM academico.ConvocatoriaHabilidad')) {
        return new ConvocatoriaMockStatement([['Habilidad'=>'Python','NivelMinimo'=>'INTERMEDIO','EsObligatoria'=>1]]);
    }
    if (str_contains($sql,'FROM academico.Postulacion') && str_contains($sql,'ConvocatoriaId=?')) {
        return new ConvocatoriaMockStatement($role==='POSTULADO'
            ? [['PostulacionId'=>40,'Estado'=>'PENDIENTE','FechaPostulacion'=>new DateTimeImmutable()]]
            : []);
    }
    throw new RuntimeException('SQL mock no esperado: '.substr($sql,0,180));
}
function sqlsrv_fetch_array(object $stmt,int $mode): ?array { return $stmt->rows[$stmt->position++] ?? null; }
function sqlsrv_free_stmt(object $stmt): bool { return true; }

$role=$argv[1]??'ALUMNO';
if (!in_array($role,['ALUMNO','PROFESOR','ADMIN','ANONIMO','POSTULADO','MULTIRROL'],true)) exit(2);
$GLOBALS['conv_test_role']=$role;
$_SERVER['REQUEST_METHOD']='GET';
$_GET=['id'=>'4'];
session_start();
if ($role!=='ANONIMO') {
    $_SESSION['usuario_id']=7;
    $_SESSION['tesco_rol_activo']=$role==='POSTULADO'?'ALUMNO':($role==='MULTIRROL'?'PROFESOR':$role);
}
ob_start();
require __DIR__.'/../Landing/convocatoria.php';
$html=ob_get_clean();

foreach (['Laboratorio de inteligencia artificial','Profesora Demo','Análisis de datos académicos',
          'Requisitos para participar','Python','INTERMEDIO','5 lugares','Lunes y miércoles',
          'Fecha límite','Información complementaria'] as $needle) {
    if (!str_contains($html,$needle)) throw new RuntimeException("Falta detalle: $needle");
}
if (str_contains($html,'<seguro>')) throw new RuntimeException('Descripción sin escapar');
if (!str_contains($html,'&lt;seguro&gt;')) throw new RuntimeException('Escapado HTML faltante');
$formVisible = str_contains($html,'name="accion" value="postular"');
if (($role==='ALUMNO') !== $formVisible) {
    throw new RuntimeException('Acceso al formulario incorrecto para rol '.$role);
}
if ($role==='POSTULADO' && (!str_contains($html,'Ya te postulaste') || $formVisible)) {
    throw new RuntimeException('No se detectó postulación ya registrada');
}
if ($role==='MULTIRROL' && !str_contains($html,'Cambiar a rol Alumno')) {
    throw new RuntimeException('Falta cambio de rol para cuenta multirrol');
}
if ($role==='ANONIMO' && !str_contains($html,'Iniciar sesión')) {
    throw new RuntimeException('Falta acceso institucional para visitante');
}
if ($formVisible && (!str_contains($html,'name="csrf"') || !str_contains($html,'name="convocatoria_id" value="4"'))) {
    throw new RuntimeException('Formulario sin protección CSRF o ID concreto');
}
$source=file_get_contents(__DIR__.'/../Landing/convocatoria.php');
foreach (['tesco_alumno_requerir()','tesco_alumno_postular(','tesco_csrf_valid(','Location: convocatoria.php?id='] as $needle) {
    if (!str_contains($source,$needle)) throw new RuntimeException('Falta control de postulación: '.$needle);
}
echo "OK: detalle convocatoria con rol $role y SQLSRV simulado\n";
