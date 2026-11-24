<?php
declare(strict_types=1);
require_once __DIR__.'/../includes/alumno_layout.php';
$alumno=tesco_alumno_requerir();
$uid=(int)$alumno['UsuarioId'];
$solicitudes=tesco_alumno_postulaciones($uid);
$disponibles=tesco_alumno_convocatorias($uid);
$totales=['total'=>count($solicitudes),'revision'=>0,'aceptadas'=>0];
foreach($solicitudes as $sol) {
    if (($sol['Estado'] ?? '')==='EN_REVISION') $totales['revision']++;
    if (($sol['Estado'] ?? '')==='ACEPTADA') $totales['aceptadas']++;
}
tesco_alumno_encabezado('Resumen académico','index.php',$alumno);
?>
<div class="card border-0 shadow-sm mb-4">
  <div class="card-header bg-success text-dark">
    <h2 class="h4 mb-1">Bienvenido, <?=tesco_escape(tesco_alumno_nombre($alumno))?></h2>
    <small>Tu espacio para participar en proyectos de investigación y oportunidades académicas.</small>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-12 col-md-4 mb-2"><strong>Matrícula:</strong> <?=tesco_escape($alumno['Matricula'])?></div>
      <div class="col-12 col-md-5 mb-2"><strong>Carrera:</strong> <?=tesco_escape($alumno['Carrera'] ?: 'Pendiente de registrar')?></div>
      <div class="col-12 col-md-3 mb-2"><strong>Semestre:</strong> <?=tesco_escape($alumno['Semestre'] ?: 'Sin registrar')?></div>
    </div>
    <a class="btn btn-outline-primary btn-sm mt-2" href="perfil.php"><i class="fas fa-user-edit mr-2"></i>Actualizar mi perfil</a>
  </div>
</div>
<div class="row">
<?php foreach ([
  ['Mis postulaciones',$totales['total'],'fas fa-paper-plane','postulaciones.php'],
  ['En revisión',$totales['revision'],'fas fa-search','postulaciones.php'],
  ['Aceptadas',$totales['aceptadas'],'fas fa-check-circle','postulaciones.php'],
  ['Convocatorias vigentes',count($disponibles),'fas fa-bullhorn','postulaciones.php']
] as [$etiqueta,$numero,$icono,$ruta]): ?>
  <div class="col-6 col-lg-3 mb-3">
    <div class="card shadow-sm border-0 h-100"><div class="card-body">
      <i class="<?=tesco_escape($icono)?> text-primary mb-2" aria-hidden="true"></i>
      <h3 class="display-4 mb-1"><?= (int)$numero ?></h3>
      <p class="text-muted"><?=tesco_escape($etiqueta)?></p>
      <a class="small stretched-link" href="<?=tesco_escape($ruta)?>">Consultar</a>
    </div></div>
  </div>
<?php endforeach; ?>
</div>
<div class="row">
  <div class="col-12 col-lg-8">
    <?php tesco_alumno_tarjeta_inicio('Acceso rápido','fas fa-th-large'); ?>
    <div class="list-group list-group-flush">
      <a href="postulaciones.php" class="list-group-item list-group-item-action"><i class="fas fa-clipboard-check text-primary mr-2"></i>Explorar convocatorias y postularme</a>
      <a href="cv.php" class="list-group-item list-group-item-action"><i class="fas fa-file-alt text-primary mr-2"></i>Completar mi CV y experiencia</a>
      <a href="perfil.php" class="list-group-item list-group-item-action"><i class="fas fa-id-card text-primary mr-2"></i>Consultar y editar mis datos</a>
    </div>
    <?php tesco_alumno_tarjeta_fin(); ?>
  </div>
  <div class="col-12 col-lg-4">
    <?php tesco_alumno_tarjeta_inicio('Postulaciones recientes','fas fa-history'); ?>
    <?php if (!$solicitudes): ?>
      <p class="text-muted">Todavía no has enviado postulaciones.</p>
    <?php else: ?>
      <?php foreach (array_slice($solicitudes,0,4) as $sol): ?>
      <div class="border-bottom py-2">
        <strong class="d-block"><?=tesco_escape($sol['Titulo'])?></strong>
        <small class="text-muted"><?=tesco_escape($sol['Estado'])?> · <?=tesco_escape(tesco_alumno_fecha($sol['FechaPostulacion']))?></small>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
    <?php tesco_alumno_tarjeta_fin(); ?>
  </div>
</div>
<?php tesco_alumno_pie(); ?>
