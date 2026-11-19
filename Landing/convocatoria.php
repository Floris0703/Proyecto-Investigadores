<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/tesco_landing_datos.php';
require_once __DIR__ . '/../includes/tesco_auth.php';
require_once __DIR__ . '/../includes/alumno_datos.php';

$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
$convocatoria = null;
$habilidades = [];
$miPostulacion = null;
$tienePerfilAlumno = false;
$error = false;
$postulacionError = '';

try {
    $convocatoria = $id ? tesco_landing_convocatoria((int)$id) : null;
    if ($convocatoria) {
        $habilidades = tesco_landing_habilidades_convocatoria((int)$id);
    }
} catch (Throwable $e) {
    error_log('TESCo: no fue posible consultar el detalle de la convocatoria');
    $error = true;
}

$usuarioId = tesco_usuario_actual();
$roles = [];
if (!$error && $usuarioId !== null) {
    try {
        $roles = tesco_roles_actuales();
    } catch (Throwable $e) {
        error_log('TESCo: no fue posible verificar el rol en detalle de convocatoria');
        $error = true;
    }
}

$rolActivo = (string)($_SESSION['tesco_rol_activo'] ?? '');
$esAlumno = $usuarioId !== null && in_array('ALUMNO', $roles, true) && $rolActivo === 'ALUMNO';
$puedeElegirAlumno = in_array('ALUMNO', $roles, true) && !$esAlumno;

if (!$error && $convocatoria && $esAlumno) {
    try {
        $tienePerfilAlumno = tesco_db_one(
            "SELECT a.AlumnoId FROM academico.Alumno a
             JOIN plataforma.Usuario u ON u.UsuarioId=a.UsuarioId
             WHERE a.UsuarioId=? AND UPPER(LTRIM(RTRIM(u.EstadoCuenta))) IN ('ACTIVO','ACTIVA')",
            [$usuarioId]
        ) !== null;
        if ($tienePerfilAlumno) {
            $miPostulacion = tesco_landing_mi_postulacion((int)$id, (int)$usuarioId);
        }
    } catch (Throwable $e) {
        error_log('TESCo: estado de postulación no disponible');
        $error = true;
    }
}

// Solo una cuenta con rol ALUMNO activo y perfil académico puede enviar la solicitud.
// Se reutiliza el registro del módulo Alumnos (vigencia, duplicados y consulta parametrizada).
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && !$error) {
    if (!$esAlumno) {
        http_response_code(403);
        $postulacionError = 'Solo puedes postularte desde una sesión con el rol ALUMNO activo.';
    } elseif (!$convocatoria) {
        http_response_code(404);
        $postulacionError = 'La convocatoria no está disponible.';
    } elseif (!tesco_csrf_valid($_POST['csrf'] ?? null)) {
        http_response_code(403);
        $postulacionError = 'El formulario caducó. Recarga la página e inténtalo de nuevo.';
    } elseif (($_POST['accion'] ?? '') !== 'postular') {
        http_response_code(400);
        $postulacionError = 'Acción no permitida.';
    } elseif (!$tienePerfilAlumno) {
        http_response_code(403);
        $postulacionError = 'Tu cuenta necesita un perfil de alumno activo para postularse.';
    } elseif ($miPostulacion !== null) {
        http_response_code(409);
        $postulacionError = 'Ya existe una postulación de tu cuenta a esta convocatoria.';
    } else {
        try {
            tesco_alumno_requerir();
            tesco_alumno_postular((int)$usuarioId, (int)$id);
            header('Location: convocatoria.php?id='.(int)$id.'&postulada=1', true, 303);
            exit;
        } catch (InvalidArgumentException $e) {
            http_response_code(409);
            $postulacionError = $e->getMessage();
        } catch (Throwable $e) {
            error_log('TESCo: no fue posible registrar postulación desde el tablón');
            http_response_code(503);
            $postulacionError = 'No fue posible guardar la postulación. Inténtalo nuevamente.';
        }
    }
}

if ($error) http_response_code(503);
elseif (!$convocatoria) http_response_code(404);

$informacionUrl = trim((string)($convocatoria['UrlInformacion'] ?? ''));
if (!filter_var($informacionUrl, FILTER_VALIDATE_URL) || !preg_match('~^https?://~i', $informacionUrl)) {
    $informacionUrl = '';
}
$enviada = isset($_GET['postulada']) && $miPostulacion !== null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Detalles de convocatoria de investigación del TESCo.">
  <title><?=tesco_escape($convocatoria['Titulo'] ?? 'Convocatoria')?> | TESCo Investigadores</title>
  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../css/mdb.min.css">
  <link rel="stylesheet" href="../css/style.css?v=8">
</head>
<body class="bg-light">
  <header class="container-fluid px-0">
    <div class="tesco-public-nav-primary rounded-right pr-3">
      <div class="tesco-public-nav-success rounded-right pr-3">
        <nav class="navbar navbar-light bg-white rounded-right shadow-sm">
          <div class="container">
            <a class="navbar-brand font-weight-bold tesco-public-brand" href="Inicio.php">
              TESCo Investigadores
            </a>
            <a class="btn btn-outline-primary btn-sm mb-0" href="Inicio.php#profesores">Volver al tablón</a>
          </div>
        </nav>
      </div>
    </div>
  </header>

  <main class="container py-4 py-md-5" style="max-width:1120px">
    <?php if ($error): ?>
      <div class="alert alert-warning" role="alert">No es posible consultar esta convocatoria en este momento.</div>
    <?php elseif (!$convocatoria): ?>
      <div class="alert alert-info" role="status">La convocatoria no existe, no ha sido publicada o ya no está vigente.</div>
    <?php else: ?>
      <div class="mb-4">
        <span class="badge badge-success mb-2 px-3 py-2">Convocatoria vigente</span>
        <?php if (!empty($convocatoria['TipoConvocatoria'])): ?>
          <span class="badge badge-light border mb-2 px-3 py-2 ml-1"><?=tesco_escape($convocatoria['TipoConvocatoria'])?></span>
        <?php endif; ?>
        <h1 class="h2 font-weight-bold mb-2"><?=tesco_escape($convocatoria['Titulo'])?></h1>
        <p class="text-muted mb-0">Publicada el <?=tesco_escape(tesco_landing_fecha($convocatoria['FechaPublicacion']))?></p>
      </div>

      <?php if ($enviada): ?>
        <div class="alert alert-success" role="status">
          Tu postulación fue registrada correctamente. El profesor podrá revisarla junto con tu CV.
          <a href="../Alumnos/postulaciones.php" class="alert-link">Ver mis postulaciones</a>
        </div>
      <?php endif; ?>
      <?php if ($postulacionError !== ''): ?>
        <div class="alert alert-danger" role="alert"><?=tesco_escape($postulacionError)?></div>
      <?php endif; ?>

      <div class="row">
        <div class="col-12 col-lg-8">
          <section class="card shadow-sm border-0 mb-4" aria-labelledby="detalle-descripcion">
            <div class="card-body p-4">
              <h2 id="detalle-descripcion" class="h5 font-weight-bold">Descripción de la convocatoria</h2>
              <p class="mb-0" style="white-space:pre-wrap"><?=tesco_escape($convocatoria['Descripcion'] ?: 'No se proporcionó una descripción adicional.')?></p>
            </div>
          </section>

          <?php if (!empty($convocatoria['ProyectoTitulo'])): ?>
            <section class="card shadow-sm border-0 mb-4" aria-labelledby="detalle-proyecto">
              <div class="card-body p-4">
                <h2 id="detalle-proyecto" class="h5 font-weight-bold">Proyecto relacionado</h2>
                <p class="mb-0"><?=tesco_escape($convocatoria['ProyectoTitulo'])?></p>
              </div>
            </section>
          <?php endif; ?>

          <section class="card shadow-sm border-0 mb-4" aria-labelledby="detalle-requisitos">
            <div class="card-body p-4">
              <h2 id="detalle-requisitos" class="h5 font-weight-bold">Requisitos para participar</h2>
              <p class="mb-0" style="white-space:pre-wrap"><?=tesco_escape($convocatoria['Requisitos'] ?: 'El docente no especificó requisitos adicionales.')?></p>
              <?php if ($habilidades): ?>
                <h3 class="h6 font-weight-bold mt-4">Habilidades solicitadas</h3>
                <ul class="list-group list-group-flush">
                <?php foreach ($habilidades as $habilidad): ?>
                  <li class="list-group-item px-0 d-flex flex-wrap justify-content-between align-items-center">
                    <span>
                      <?=tesco_escape($habilidad['Habilidad'])?>
                      <small class="text-muted d-block">Nivel mínimo: <?=tesco_escape($habilidad['NivelMinimo'])?></small>
                    </span>
                    <span class="badge badge-<?= !empty($habilidad['EsObligatoria']) ? 'primary' : 'secondary' ?> ml-2">
                      <?=!empty($habilidad['EsObligatoria']) ? 'Obligatoria' : 'Deseable'?>
                    </span>
                  </li>
                <?php endforeach; ?>
                </ul>
              <?php endif; ?>
            </div>
          </section>
        </div>

        <aside class="col-12 col-lg-4" aria-label="Información y postulación">
          <section class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
              <h2 class="h5 font-weight-bold">Información general</h2>
              <dl class="mb-0">
                <dt class="small text-muted mt-3">Profesor responsable</dt>
                <dd><?=tesco_escape($convocatoria['Profesor'])?></dd>
                <?php if (!empty($convocatoria['GradoAcademicoProfesor']) || !empty($convocatoria['DepartamentoProfesor'])): ?>
                  <dd class="small text-muted mb-2">
                    <?=tesco_escape(implode(' · ', array_filter([
                        $convocatoria['GradoAcademicoProfesor'] ?? '',
                        $convocatoria['DepartamentoProfesor'] ?? ''
                    ])))?>
                  </dd>
                <?php endif; ?>
                <?php if (!empty($convocatoria['FechaInicioRecepcion'])): ?>
                  <dt class="small text-muted mt-3">Inicio de recepción</dt>
                  <dd><?=tesco_escape(tesco_landing_fecha($convocatoria['FechaInicioRecepcion']))?></dd>
                <?php endif; ?>
                <dt class="small text-muted mt-3">Fecha de cierre</dt>
                <dd><?=tesco_escape(tesco_landing_fecha($convocatoria['FechaCierre']))?></dd>
                <?php if (!empty($convocatoria['CupoTotal'])): ?>
                  <dt class="small text-muted mt-3">Cupo anunciado</dt>
                  <dd><?= (int)$convocatoria['CupoTotal'] ?> participantes</dd>
                <?php endif; ?>
                <?php if (!empty($convocatoria['HorarioColaboracion'])): ?>
                  <dt class="small text-muted mt-3">Horario de colaboración</dt>
                  <dd><?=tesco_escape($convocatoria['HorarioColaboracion'])?></dd>
                <?php endif; ?>
              </dl>
              <?php if ($informacionUrl !== ''): ?>
                <a class="btn btn-outline-secondary btn-block mt-3" href="<?=tesco_escape($informacionUrl)?>" target="_blank" rel="noopener noreferrer">Información complementaria</a>
              <?php endif; ?>
            </div>
          </section>

          <section class="card shadow-sm border-0 mb-4" aria-labelledby="detalle-postulacion">
            <div class="card-body p-4">
              <h2 id="detalle-postulacion" class="h5 font-weight-bold">Postulación</h2>
              <?php if ($esAlumno && $tienePerfilAlumno && $miPostulacion): ?>
                <div class="alert alert-success mb-3" role="status">
                  Ya te postulaste. Estado actual:
                  <strong><?=tesco_escape($miPostulacion['Estado'])?></strong>.
                </div>
                <a class="btn btn-outline-primary btn-block" href="../Alumnos/postulaciones.php">Ver mis postulaciones</a>
              <?php elseif ($esAlumno && $tienePerfilAlumno): ?>
                <p class="small text-muted">Envía tu postulación con tu cuenta institucional. El profesor podrá consultar tu CV durante la revisión.</p>
                <form method="post" action="convocatoria.php?id=<?= (int)$id ?>">
                  <input type="hidden" name="csrf" value="<?=tesco_escape(tesco_csrf_public())?>">
                  <input type="hidden" name="accion" value="postular">
                  <button type="submit" class="btn btn-primary btn-block">Postularme a esta convocatoria</button>
                </form>
                <a class="small d-block mt-3" href="../Alumnos/cv.php">Revisar mi CV antes de postularme</a>
              <?php elseif ($esAlumno): ?>
                <p class="alert alert-warning mb-0">Tu cuenta necesita completar su registro institucional como alumno para postularse.</p>
              <?php elseif ($puedeElegirAlumno): ?>
                <p class="small text-muted">Tu cuenta tiene permiso de alumno, pero actualmente tienes otro rol seleccionado.</p>
                <a class="btn btn-outline-primary btn-block" href="elegir_rol.php">Seleccionar rol ALUMNO</a>
              <?php elseif ($usuarioId === null): ?>
                <p class="small text-muted">Inicia sesión con una cuenta de alumno para enviar tu postulación.</p>
                <a class="btn btn-primary btn-block" href="login.php">Iniciar sesión</a>
              <?php else: ?>
                <p class="alert alert-light border mb-0">Solo las cuentas con rol ALUMNO activo pueden postularse.</p>
              <?php endif; ?>
            </div>
          </section>
        </aside>
      </div>
    <?php endif; ?>
  </main>

  <footer class="footer">
    <div class="container text-center">
      <p class="mb-0">TESCo Investigadores · Convocatorias institucionales</p>
    </div>
  </footer>
</body>
</html>
