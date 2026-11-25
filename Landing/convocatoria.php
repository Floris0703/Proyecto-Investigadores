<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/tesco_landing_datos.php';
require_once __DIR__ . '/../includes/tesco_auth.php';

$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$convocatoriaId = $id === false ? 0 : (int)$id;
$errorPostulacion = '';
$errorConsulta = false;
$errorHabilidades = false;
$convocatoria = null;
$habilidades = [];
$miPostulacion = null;

// Solo un alumno autenticado con el rol ALUMNO activo puede enviar POST.
// Se reutiliza la misma operación central que Alumnos/postulaciones.php.
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    require_once __DIR__ . '/../includes/alumno_datos.php';
    $alumno = tesco_alumno_requerir();
    if (!tesco_csrf_valid($_POST['csrf'] ?? null)) {
        http_response_code(403);
        $errorPostulacion = 'El formulario caducó. Recarga la página e inténtalo nuevamente.';
    } elseif (($_POST['accion'] ?? '') !== 'postular'
              || $convocatoriaId < 1
              || (string)($_POST['convocatoria_id'] ?? '') !== (string)$convocatoriaId) {
        http_response_code(400);
        $errorPostulacion = 'La solicitud no corresponde a una convocatoria válida.';
    } else {
        try {
            tesco_alumno_postular((int)$alumno['UsuarioId'], $convocatoriaId);
            header('Location: convocatoria.php?id='.$convocatoriaId.'&postulada=1', true, 303);
            exit;
        } catch (InvalidArgumentException $e) {
            http_response_code(409);
            $errorPostulacion = $e->getMessage();
        } catch (Throwable $e) {
            error_log('TESCo Landing: error al postular desde detalle de convocatoria');
            http_response_code(503);
            $errorPostulacion = 'No fue posible registrar tu postulación. Inténtalo más tarde.';
        }
    }
}

$usuarioId = tesco_usuario_actual();
$roles = $usuarioId ? tesco_roles_actuales() : [];
$rolActivo = (string)($_SESSION['tesco_rol_activo'] ?? '');
$esAlumno = $usuarioId !== null
    && in_array('ALUMNO', $roles, true)
    && $rolActivo === 'ALUMNO';

try {
    $convocatoria = $convocatoriaId > 0 ? tesco_landing_convocatoria($convocatoriaId) : null;
} catch (Throwable $e) {
    error_log('TESCo Landing: detalle de convocatoria no disponible');
    $errorConsulta = true;
}
if ($convocatoria) {
    try {
        $habilidades = tesco_landing_habilidades_convocatoria($convocatoriaId);
    } catch (Throwable $e) {
        error_log('TESCo Landing: habilidades de convocatoria no disponibles');
        $errorHabilidades = true;
    }
    if ($esAlumno) {
        try {
            $miPostulacion = tesco_landing_mi_postulacion($convocatoriaId, (int)$usuarioId);
        } catch (Throwable $e) {
            error_log('TESCo Landing: estado de postulación no disponible');
            $errorConsulta = true;
        }
    }
}
if ($errorConsulta) http_response_code(503);
elseif (!$convocatoria) http_response_code(404);

$urlInformacion = (string)($convocatoria['UrlInformacion'] ?? '');
$urlInformacion = filter_var($urlInformacion, FILTER_VALIDATE_URL)
    && in_array(strtolower((string)parse_url($urlInformacion, PHP_URL_SCHEME)), ['http','https'], true)
    ? $urlInformacion : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Detalle de convocatoria | TESCo Investigadores</title>
  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../css/mdb.min.css">
  <link rel="stylesheet" href="../css/style.css?v=8">
</head>
<body>
<header class="bg-white shadow-sm border-bottom">
  <nav class="container navbar navbar-light" aria-label="Navegación de convocatoria">
    <a class="navbar-brand font-weight-bold" href="Inicio.php">TESCo Investigadores</a>
    <a href="Inicio.php#convocatorias" class="btn btn-outline-primary btn-sm">Volver al tablón</a>
  </nav>
</header>
<main class="container py-5" style="max-width: 1050px">
<?php if ($errorConsulta): ?>
  <div class="alert alert-warning" role="alert">No fue posible consultar los datos de la convocatoria en este momento.</div>
<?php elseif (!$convocatoria): ?>
  <div class="alert alert-info" role="status">La convocatoria no existe, aún no está publicada o ya no está vigente.</div>
<?php else: ?>
  <div class="mb-4">
    <span class="section-kicker">CONVOCATORIA DE INVESTIGACIÓN</span>
    <h1 class="h2 mt-2 mb-3"><?=tesco_escape($convocatoria['Titulo'])?></h1>
    <span class="badge badge-success p-2">Convocatoria vigente</span>
    <?php if (!empty($convocatoria['TipoConvocatoria'])): ?>
      <span class="badge badge-light border p-2 ml-2"><?=tesco_escape($convocatoria['TipoConvocatoria'])?></span>
    <?php endif; ?>
  </div>
  <?php if ($errorPostulacion !== ''): ?>
    <div class="alert alert-danger" role="alert"><?=tesco_escape($errorPostulacion)?></div>
  <?php endif; ?>
  <?php if (isset($_GET['postulada']) && $miPostulacion): ?>
    <div class="alert alert-success" role="status">Tu postulación se registró correctamente. Ya puedes consultarla en Mis postulaciones.</div>
  <?php endif; ?>

  <div class="row">
    <div class="col-12 col-lg-8 mb-4">
      <article class="card shadow-sm h-100">
        <div class="card-body p-4">
          <h2 class="h5 mb-3">Descripción de la convocatoria</h2>
          <p style="white-space: pre-line"><?=tesco_escape($convocatoria['Descripcion'] ?: 'El profesor todavía no ha indicado una descripción adicional.')?></p>

          <?php if (!empty($convocatoria['ProyectoTitulo'])): ?>
            <hr>
            <h2 class="h5">Proyecto asociado</h2>
            <p><?=tesco_escape($convocatoria['ProyectoTitulo'])?></p>
          <?php endif; ?>

          <hr>
          <h2 class="h5">Requisitos para participar</h2>
          <?php if (!empty($convocatoria['Requisitos'])): ?>
            <p style="white-space: pre-line"><?=tesco_escape($convocatoria['Requisitos'])?></p>
          <?php else: ?>
            <p class="text-muted">No se especificaron requisitos adicionales.</p>
          <?php endif; ?>

          <?php if ($errorHabilidades): ?>
            <div class="alert alert-warning">No fue posible cargar las habilidades solicitadas.</div>
          <?php elseif ($habilidades): ?>
            <h3 class="h6 mt-3">Habilidades solicitadas</h3>
            <ul class="list-group list-group-flush">
              <?php foreach ($habilidades as $habilidad): ?>
                <li class="list-group-item px-0 d-flex justify-content-between align-items-center flex-wrap">
                  <span><?=tesco_escape($habilidad['Habilidad'])?></span>
                  <span class="small text-muted">
                    <?=tesco_escape($habilidad['NivelMinimo'])?>
                    · <?=!empty($habilidad['EsObligatoria']) ? 'Obligatoria' : 'Deseable'?>
                  </span>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      </article>
    </div>

    <aside class="col-12 col-lg-4 mb-4" aria-label="Datos de la convocatoria y postulación">
      <section class="card shadow-sm mb-4">
        <div class="card-body p-4">
          <h2 class="h5 mb-3">Información general</h2>
          <dl class="mb-0">
            <dt>Profesor responsable</dt>
            <dd><?=tesco_escape($convocatoria['Profesor'] ?: 'Docente investigador')?></dd>
            <?php if (!empty($convocatoria['GradoAcademicoProfesor'])): ?>
              <dt>Grado académico</dt>
              <dd><?=tesco_escape($convocatoria['GradoAcademicoProfesor'])?></dd>
            <?php endif; ?>
            <?php if (!empty($convocatoria['DepartamentoProfesor'])): ?>
              <dt>Departamento</dt>
              <dd><?=tesco_escape($convocatoria['DepartamentoProfesor'])?></dd>
            <?php endif; ?>
            <dt>Fecha de publicación</dt>
            <dd><?=tesco_escape(tesco_landing_fecha($convocatoria['FechaPublicacion']))?></dd>
            <dt>Recepción de postulaciones</dt>
            <dd><?=!empty($convocatoria['FechaInicioRecepcion']) ? tesco_escape(tesco_landing_fecha($convocatoria['FechaInicioRecepcion'])) : 'No especificada'?></dd>
            <dt>Fecha límite</dt>
            <dd><?=tesco_escape(tesco_landing_fecha($convocatoria['FechaCierre']))?></dd>
            <dt>Cupo anunciado</dt>
            <dd><?=isset($convocatoria['CupoTotal']) ? (int)$convocatoria['CupoTotal'].' lugares' : 'No especificado'?></dd>
            <dt>Horario de colaboración</dt>
            <dd><?=tesco_escape($convocatoria['HorarioColaboracion'] ?: 'Por definir con el profesor')?></dd>
          </dl>
          <?php if ($urlInformacion !== ''): ?>
            <a class="btn btn-outline-primary btn-sm mt-2" href="<?=tesco_escape($urlInformacion)?>" target="_blank" rel="noopener noreferrer">Información complementaria</a>
          <?php endif; ?>
        </div>
      </section>
      <section class="card shadow-sm">
        <div class="card-body p-4">
          <h2 class="h5">Participar en esta convocatoria</h2>
          <?php if ($esAlumno): ?>
            <?php if ($miPostulacion): ?>
              <div class="alert alert-info" role="status">
                Ya te postulaste. Estado: <strong><?=tesco_escape($miPostulacion['Estado'])?></strong>.
              </div>
              <a href="../Alumnos/postulaciones.php" class="btn btn-outline-primary btn-block">Ver mis postulaciones</a>
            <?php elseif ($errorConsulta): ?>
              <p class="text-muted">No es posible verificar tu postulación en este momento.</p>
            <?php else: ?>
              <p class="text-muted small">El profesor podrá consultar tu CV cuando revise la postulación.</p>
              <form method="post" action="convocatoria.php?id=<?=$convocatoriaId?>">
                <input type="hidden" name="csrf" value="<?=tesco_escape(tesco_csrf_public())?>">
                <input type="hidden" name="accion" value="postular">
                <input type="hidden" name="convocatoria_id" value="<?=$convocatoriaId?>">
                <button class="btn btn-primary btn-block" type="submit">Postularme</button>
              </form>
              <a href="../Alumnos/cv.php" class="btn btn-outline-primary btn-block">Revisar mi CV</a>
            <?php endif; ?>
          <?php elseif (!$usuarioId): ?>
            <p class="small text-muted">Inicia sesión con una cuenta de alumno para postularte.</p>
            <a href="login.php" class="btn btn-primary btn-block">Iniciar sesión</a>
          <?php elseif (in_array('ALUMNO',$roles,true)): ?>
            <p class="small text-muted">Para postularte, cambia al rol ALUMNO de tu cuenta.</p>
            <a href="elegir_rol.php" class="btn btn-outline-primary btn-block">Cambiar a rol Alumno</a>
          <?php else: ?>
            <p class="small text-muted">La postulación está disponible únicamente para cuentas con rol ALUMNO.</p>
          <?php endif; ?>
        </div>
      </section>
    </aside>
  </div>
<?php endif; ?>
</main>
<script src="../js/jquery.min.js"></script>
<script src="../js/popper.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
</body>
</html>
