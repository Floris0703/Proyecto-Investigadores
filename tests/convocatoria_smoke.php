<?php
/** Smoke de la vista de detalle del tablón sin SQL Server real. */
declare(strict_types=1);
if (!defined('SQLSRV_FETCH_ASSOC')) define('SQLSRV_FETCH_ASSOC', 2);
final class ConvocatoriaMockStatement {
    public int $at = 0;
    public function __construct(public array $rows) {}
}
$GLOBALS['insertCount'] = 0;
$GLOBALS['existing'] = false;
$GLOBALS['roles'] = ['ALUMNO'];

function sqlsrv_connect(string $server,array $options): object { return (object)['ok'=>true]; }
function sqlsrv_query(object $connection,string $sql,array $params=[]): ConvocatoriaMockStatement {
    if (str_contains($sql,'INSERT INTO academico.Postulacion')) {
        $GLOBALS['insertCount']++;
        return new ConvocatoriaMockStatement([]);
    }
    if (str_contains($sql,'FROM plataforma.Usuario u') && str_contains($sql,'plataforma.UsuarioRol')) {
        return new ConvocatoriaMockStatement(array_map(static fn($role)=>['Nombre'=>$role],$GLOBALS['roles']));
    }
    if (str_contains($sql,'FROM academico.Convocatoria c') && str_contains($sql,'LEFT JOIN academico.TipoConvocatoria')) {
        if (($params[0]??0)!==4) throw new RuntimeException('El detalle debe parametrizar el ID.');
        return new ConvocatoriaMockStatement([[
            'ConvocatoriaId'=>4,'Titulo'=>'Robótica aplicada a procesos industriales',
            'Descripcion'=>"Desarrollar un prototipo.\nSe requiere trabajo en laboratorio.",
            'Requisitos'=>'Interés en automatización y programación.',
            'FechaPublicacion'=>new DateTimeImmutable('2026-11-01'),
            'FechaInicioRecepcion'=>new DateTimeImmutable('2026-11-02'),
            'FechaCierre'=>new DateTimeImmutable('2026-12-01'),
            'CupoTotal'=>5,'HorarioColaboracion'=>'Lunes y miércoles de 13 a 16 h',
            'UrlInformacion'=>'https://example.test/convocatoria/4',
            'TipoConvocatoria'=>'Investigación','ProyectoTitulo'=>'Robot móvil autónomo',
            'Profesor'=>'Elena Gómez Méndez','DepartamentoProfesor'=>'Mecatrónica',
            'GradoAcademicoProfesor'=>'Maestría'
        ]]);
    }
    if (str_contains($sql,'FROM academico.ConvocatoriaHabilidad ch')) {
        return new ConvocatoriaMockStatement([['Habilidad'=>'Python','NivelMinimo'=>'INTERMEDIO','EsObligatoria'=>1]]);
    }
    if (str_contains($sql,'FROM academico.Alumno a') && str_contains($sql,'JOIN plataforma.Usuario u')) {
        return new ConvocatoriaMockStatement([[
            'AlumnoId'=>3,'UsuarioId'=>7,'Matricula'=>'TEST-7','Correo'=>'test@example.test',
            'Carrera'=>'Mecatrónica','Semestre'=>5
        ]]);
    }
    if (str_contains($sql,'FROM academico.Postulacion') && str_contains($sql,'ConvocatoriaId=? AND UsuarioId=?')) {
        if ($params !== [4,7]) throw new RuntimeException('La consulta de postulación debe limitarse al usuario actual.');
        return new ConvocatoriaMockStatement($GLOBALS['existing']
            ? [['PostulacionId'=>9,'Estado'=>'PENDIENTE','FechaPostulacion'=>new DateTimeImmutable('2026-11-12')]]
            : []);
    }
    return new ConvocatoriaMockStatement([]);
}
function sqlsrv_fetch_array(ConvocatoriaMockStatement $statement,int $mode): ?array {
    return $statement->rows[$statement->at++] ?? null;
}
function sqlsrv_free_stmt(ConvocatoriaMockStatement $statement): bool { return true; }
function sqlsrv_rows_affected(ConvocatoriaMockStatement $statement): int { return 1; }

$mode = $argv[1] ?? 'alumno';
$allowed = ['alumno','postulado','profesor','anonimo','multirrol','post_profe','post_csrf','post_alumno'];
if (!in_array($mode,$allowed,true)) exit(2);
$_GET = ['id'=>'4'];
$_SERVER['REQUEST_METHOD'] = str_starts_with($mode,'post_') ? 'POST' : 'GET';
session_start();
if ($mode !== 'anonimo') {
    $_SESSION['usuario_id']=7;
    $_SESSION['tesco_rol_activo']=in_array($mode,['profesor','post_profe','multirrol'],true) ? 'PROFESOR' : 'ALUMNO';
}
if (in_array($mode,['profesor','post_profe'],true)) $GLOBALS['roles']=['PROFESOR'];
if ($mode==='multirrol') $GLOBALS['roles']=['ALUMNO','PROFESOR'];
if ($mode==='postulado') $GLOBALS['existing']=true;
if ($_SERVER['REQUEST_METHOD']==='POST') {
    require_once __DIR__.'/../includes/tesco_auth.php';
    $_POST = ['accion'=>'postular','convocatoria_id'=>'4','csrf'=>$mode==='post_csrf'?'invalido':tesco_csrf_public()];
}
if ($mode==='post_profe') {
    register_shutdown_function(static function(): void {
        if (http_response_code()!==403 || $GLOBALS['insertCount']!==0) {
            fwrite(STDERR,"Fallo: profesor intentó postular o permiso incorrecto\n");
            exit(1);
        }
        echo "OK: postulación denegada al profesor\n";
    });
}
if ($mode==='post_alumno') {
    register_shutdown_function(static function(): void {
        if ($GLOBALS['insertCount']!==1) {
            fwrite(STDERR,"Fallo: postulación no insertada exactamente una vez\n");
            exit(1);
        }
        echo "OK: postulación desde detalle (SQLSRV simulado)\n";
    });
}

ob_start();
require __DIR__.'/../Landing/convocatoria.php';
$html=ob_get_clean();
$check=static function(bool $value,string $message):void {
    if (!$value) throw new RuntimeException('Fallo: '.$message);
};
$check(str_contains($html,'Robótica aplicada a procesos industriales'),'titulo');
$check(str_contains($html,'Elena Gómez Méndez'),'profesor');
$check(str_contains($html,'Robot móvil autónomo'),'proyecto vinculado');
$check(str_contains($html,'Interés en automatización'),'requisitos');
$check(str_contains($html,'INTERMEDIO'),'habilidades');
$check(str_contains($html,'Lunes y miércoles'),'horario');
$check(str_contains($html,'5 lugares'),'cupo informado');
if ($mode==='alumno') {
    $check(str_contains($html,'name="accion" value="postular"'),'botón de postulación alumno');
    $check(str_contains($html,'name="csrf"') && str_contains($html,'method="post"'),'formulario protegido');
}
if ($mode==='postulado') {
    $check(str_contains($html,'Ya te postulaste'),'mensaje de solicitud existente');
    $check(!str_contains($html,'name="accion" value="postular"'),'sin formulario duplicado');
}
if ($mode==='profesor') {
    $check(!str_contains($html,'name="accion" value="postular"'),'profesor sin acción alumno');
    $check(str_contains($html,'La postulación está disponible únicamente'),'rol protegido');
}
if ($mode==='anonimo') {
    $check(str_contains($html,'Iniciar sesión'),'login requerido');
    $check(!str_contains($html,'name="accion" value="postular"'),'no acción anónima');
}
if ($mode==='multirrol') {
    $check(str_contains($html,'Cambiar a rol Alumno'),'cambio explícito de rol');
    $check(!str_contains($html,'name="accion" value="postular"'),'sin acción para rol distinto');
}
if (in_array($mode,['post_profe','post_csrf'],true)) {
    $check(http_response_code()===403,'POST no autorizado');
    $check($GLOBALS['insertCount']===0,'sin inserción insegura');
}
echo "OK: detalle convocatoria $mode\n";
