<?php
declare(strict_types=1);
require_once __DIR__.'/../includes/alumno_layout.php';
$alumno=tesco_alumno_requerir();
$uid=(int)$alumno['UsuarioId'];
$error='';
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!tesco_csrf_valid($_POST['csrf'] ?? null)) {
        http_response_code(403);
        $error='La sesión del formulario caducó. Recarga la página.';
    } elseif (($_POST['accion'] ?? '') !== 'postular') {
        http_response_code(400);
        $error='Acción no permitida.';
    } else {
        try {
            $convocatoriaId=filter_var($_POST['convocatoria_id'] ?? null,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
            if (!$convocatoriaId) throw new InvalidArgumentException('La convocatoria no es válida.');
            tesco_alumno_postular($uid,(int)$convocatoriaId);
            header('Location: postulaciones.php?enviada=1',true,303);
            exit;
        } catch (InvalidArgumentException $e) {
            $error=$e->getMessage();
        } catch (Throwable $e) {
            error_log('TESCo alumnos: postulación no registrada');
            $error='No se pudo enviar la postulación. Comprueba que no exista otra y que siga vigente.';
        }
    }
}
$convocatorias=tesco_alumno_convocatorias($uid);
$solicitudes=tesco_alumno_postulaciones($uid);
tesco_alumno_encabezado('Convocatorias y postulaciones','postulaciones.php',$alumno);
if (isset($_GET['enviada'])) tesco_alumno_alerta('Tu postulación fue registrada y ahora aparece en Mis postulaciones.','success');
if ($error!=='') tesco_alumno_alerta($error,'danger');
?>
<div class="row">
  <section class="col-12 col-lg-8" aria-labelledby="convocatorias-titulo">
    <?php tesco_alumno_tarjeta_inicio('Convocatorias vigentes de profesores','fas fa-bullhorn'); ?>
    <p id="convocatorias-titulo" class="text-muted mb-3">Solo se muestran convocatorias publicadas cuya fecha de cierre no ha vencido.</p>
    <?php if (!$convocatorias): ?>
      <div class="alert alert-info">No hay convocatorias abiertas para postularte en este momento.</div>
    <?php else: ?>
      <div class="row">
      <?php foreach($convocatorias as $c): ?>
        <article class="col-12 col-md-6 mb-3">
          <div class="card h-100 border shadow-sm">
            <div class="card-body d-flex flex-column">
              <span class="badge badge-success align-self-start mb-2">Convocatoria vigente</span>
              <h3 class="h5"><?=tesco_escape($c['Titulo'])?></h3>
              <p class="small text-muted">Publica: <?=tesco_escape($c['Profesor'])?></p>
              <p class="small mb-2"><?=tesco_escape($c['Descripcion'] ?: 'Sin descripción adicional.')?></p>
              <p class="small text-muted mt-auto">Cierre: <?=tesco_escape($c['FechaCierre'] ? tesco_alumno_fecha($c['FechaCierre']) : 'Sin fecha límite')?></p>
              <?php if ($c['MiPostulacionId']): ?>
                <span class="btn btn-outline-secondary disabled" aria-disabled="true"><i class="fas fa-check mr-2"></i>Ya te postulaste</span>
              <?php else: ?>
                <form method="post">
                  <input type="hidden" name="csrf" value="<?=tesco_escape(tesco_csrf_public())?>">
                  <input type="hidden" name="accion" value="postular">
                  <input type="hidden" name="convocatoria_id" value="<?= (int)$c['ConvocatoriaId'] ?>">
                  <button class="btn btn-primary btn-block" type="submit">
                    <i class="fas fa-paper-plane mr-2"></i>Postularme
                  </button>
                </form>
              <?php endif; ?>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <?php tesco_alumno_tarjeta_fin(); ?>
  </section>
  <aside class="col-12 col-lg-4" aria-label="Mis postulaciones">
    <?php tesco_alumno_tarjeta_inicio('Mis postulaciones','fas fa-clipboard-list'); ?>
    <p class="text-muted small">El docente podrá consultar tu CV al revisar tu solicitud.</p>
    <?php if (!$solicitudes): ?>
      <p class="text-muted">Aún no tienes solicitudes registradas.</p>
    <?php else: ?>
      <?php foreach($solicitudes as $p): ?>
        <div class="border-bottom py-3">
          <h3 class="h6 mb-1"><?=tesco_escape($p['Titulo'])?></h3>
          <span class="badge badge-<?=($p['Estado']==='ACEPTADA'?'success':($p['Estado']==='RECHAZADA'?'danger':'warning'))?>">
            <?=tesco_escape($p['Estado'])?>
          </span>
          <small class="d-block text-muted mt-1">Enviada el <?=tesco_escape(tesco_alumno_fecha($p['FechaPostulacion']))?></small>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
    <a href="cv.php" class="btn btn-outline-primary btn-block mt-3"><i class="fas fa-file-alt mr-2"></i>Actualizar mi CV</a>
    <?php tesco_alumno_tarjeta_fin(); ?>
  </aside>
</div>
<?php tesco_alumno_pie(); ?>
