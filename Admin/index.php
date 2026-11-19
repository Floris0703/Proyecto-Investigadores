<?php
declare(strict_types=1);
require_once __DIR__.'/../includes/admin_layout.php';
[$admin,$error]=tesco_admin_contexto();
$k=tesco_admin_resumen();
tesco_admin_inicio('Resumen administrativo','index.php',$admin,$error);
$tarjetas=[
    ['Usuarios','Usuarios','fas fa-users'],
    ['Profesores','Profesores','fas fa-chalkboard-teacher'],
    ['Alumnos','Alumnos','fas fa-user-graduate'],
    ['Proyectos','Proyectos','fas fa-project-diagram'],
    ['Publicaciones','Publicaciones','fas fa-book-open'],
    ['Congresos','Congresos','fas fa-calendar-alt'],
    ['Convocatorias','Convocatorias','fas fa-bullhorn'],
    ['Postulaciones','Postulaciones','fas fa-clipboard-check'],
    ['HabilidadesPendientes','Habilidades por revisar','fas fa-certificate']
];
?>
<div class="alert alert-info">
  <i class="fas fa-info-circle mr-2"></i>Indicadores institucionales consultados directamente en SQL Server. El rol ADMIN puede supervisar la plataforma; cada profesor conserva la decisión de aceptar o rechazar postulantes.
</div>
<div class="row">
<?php foreach ($tarjetas as [$clave,$nombre,$icono]): ?>
  <div class="col-6 col-lg-4 col-xl-3 mb-3">
    <div class="card shadow-sm border-0 tesco-admin-metrica h-100"><div class="card-body">
      <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="text-muted small"><?=tesco_escape($nombre)?></span><i class="<?=tesco_escape($icono)?> text-success" aria-hidden="true"></i>
      </div>
      <strong class="h3 mb-0 text-primary"><?=number_format((int)($k[$clave]??0))?></strong>
    </div></div>
  </div>
<?php endforeach; ?>
</div>
<?php tesco_admin_tarjeta_inicio('Acciones institucionales','fas fa-th-large'); ?>
<div class="row">
  <div class="col-md-6 col-xl-4 mb-3"><a class="btn btn-outline-primary btn-block text-left" href="usuarios.php"><i class="fas fa-users mr-2"></i>Administrar usuarios y roles</a></div>
  <div class="col-md-6 col-xl-4 mb-3"><a class="btn btn-outline-primary btn-block text-left" href="habilidades.php"><i class="fas fa-certificate mr-2"></i>Revisar habilidades</a></div>
  <div class="col-md-6 col-xl-4 mb-3"><a class="btn btn-outline-primary btn-block text-left" href="convocatorias.php"><i class="fas fa-bullhorn mr-2"></i>Supervisar convocatorias</a></div>
  <div class="col-md-6 col-xl-4 mb-3"><a class="btn btn-outline-primary btn-block text-left" href="postulaciones.php"><i class="fas fa-clipboard-check mr-2"></i>Consultar postulaciones</a></div>
  <div class="col-md-6 col-xl-4 mb-3"><a class="btn btn-outline-primary btn-block text-left" href="produccion.php"><i class="fas fa-book mr-2"></i>Producción académica</a></div>
  <div class="col-md-6 col-xl-4 mb-3"><a class="btn btn-outline-primary btn-block text-left" href="perfil.php"><i class="fas fa-user mr-2"></i>Mi perfil</a></div>
</div>
<?php tesco_admin_tarjeta_fin(); ?>
<?php tesco_admin_fin(); ?>
