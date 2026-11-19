<?php
require_once __DIR__.'/../includes/profesor_visual.php';
$metricas=tesco_metricas($tescoProfesor,(int)date('Y'));
tesco_pagina_inicio('Resumen docente','index.php');
tesco_encabezado('Panel del investigador','Actividad académica de '.($tescoProfesor['NombreCompleto']??'profesor'));
?>
<div class="card shadow-sm mb-4">
  <div class="card-header bg-success text-dark"><h4 class="mb-1">Bienvenido, <?=tesco_h($tescoProfesor['NombreCompleto'])?></h4>
    <small>Tecnológico de Estudios Superiores de Coacalco</small></div>
  <div class="card-body">
    <div class="row">
      <div class="col-12 col-lg-6 mb-2"><strong>Correo:</strong> <?=tesco_h($tescoProfesor['Correo'])?></div>
      <div class="col-12 col-lg-6 mb-2"><strong>Departamento:</strong> <?=tesco_h($tescoProfesor['Departamento']?:'Pendiente de registrar')?></div>
      <div class="col-12 col-lg-6 mb-2"><strong>Grado académico:</strong> <?=tesco_h($tescoProfesor['GradoAcademico']?:'Pendiente de registrar')?></div>
      <div class="col-12 col-lg-6 mb-2"><strong>Periodo:</strong> <?=tesco_h(date('Y'))?> (según fecha de registro/actividad)</div>
    </div>
    <a href="perfil.php" class="btn btn-outline-primary btn-sm mt-2"><i class="fas fa-user-edit mr-2"></i>Ver perfil</a>
  </div>
</div>
<div class="row">
<?php
$cards=[
 ['Proyectos','proyectos','fas fa-project-diagram','proyectos.php'],
 ['Publicaciones','publicaciones','fas fa-book-open','publicaciones.php'],
 ['Congresos','congresos','fas fa-globe-americas','congresos.php'],
 ['Convocatorias','convocatorias','fas fa-bullhorn','convocatorias.php']
];
foreach($cards as [$titulo,$clave,$icono,$ruta]): ?>
  <div class="col-6 col-lg-3 mb-3">
    <div class="card shadow-sm h-100"><div class="card-body">
      <i class="<?=tesco_h($icono)?> text-primary mb-3"></i>
      <h3><?=tesco_h($metricas['totales'][$clave])?></h3>
      <p class="text-muted mb-2"><?=tesco_h($titulo)?> (<?=tesco_h(date('Y'))?>)</p>
      <a href="<?=tesco_h($ruta)?>" class="small">Consultar</a>
    </div></div>
  </div>
<?php endforeach; ?>
</div>
<div class="row">
 <section class="col-12 col-lg-8">
  <?php tesco_tarjeta('Acceso rápido a módulos','fas fa-th-large'); ?>
  <div class="list-group">
  <?php foreach([
    ['Mis proyectos','proyectos.php','fas fa-project-diagram'],
    ['Productividad académica','productividad.php','fas fa-chart-bar'],
    ['Publicaciones','publicaciones.php','fas fa-book'],
    ['Congresos','congresos.php','fas fa-globe-americas'],
    ['Titulación','titulacion.php','fas fa-graduation-cap'],
    ['Convocatorias','convocatorias.php','fas fa-bullhorn'],
    ['Postulaciones recibidas','postulaciones.php','fas fa-users']
  ] as [$label,$ruta,$icono]): ?>
   <a class="list-group-item list-group-item-action" href="<?=tesco_h($ruta)?>"><i class="<?=tesco_h($icono)?> text-primary mr-2"></i><?=tesco_h($label)?></a>
  <?php endforeach; ?>
  </div>
  <?php tesco_tarjeta_fin(); ?>
 </section>
 <aside class="col-12 col-lg-4">
  <?php tesco_tarjeta('Titulación','fas fa-graduation-cap'); ?>
  <h3 class="text-primary"><?=tesco_h($metricas['conteos']['titulacion'])?></h3>
  <p class="text-muted">Trabajos asociados al profesor (histórico).</p>
  <a class="btn btn-outline-primary btn-sm" href="titulacion.php">Consultar titulación</a>
  <?php tesco_tarjeta_fin(); ?>
  <?php tesco_tarjeta('Datos verificables','fas fa-info-circle'); ?>
  <p class="text-muted mb-0">Los indicadores anuales se calculan a partir de registros con fecha. Las actividades sin fecha no se atribuyen artificialmente a un año.</p>
  <?php tesco_tarjeta_fin(); ?>
 </aside>
</div>
<?php tesco_pagina_fin(); ?>
