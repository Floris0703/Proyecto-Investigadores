<?php
/** CV consultable únicamente por el profesor dueño de la convocatoria postulada. */
declare(strict_types=1);
require_once __DIR__.'/../includes/alumno_datos.php';
if (!tesco_usuario_actual()) {
    header('Location: ../Landing/login.php',true,303);
    exit;
}
$roles=tesco_roles_actuales();
if (!in_array('PROFESOR',$roles,true) || ($_SESSION['tesco_rol_activo'] ?? null)!=='PROFESOR') {
    http_response_code(403);
    exit('Acceso exclusivo al profesor responsable de la convocatoria.');
}
$uid=(int)tesco_usuario_actual();
$profesor=tesco_db_one('SELECT ProfesorId FROM academico.Profesor WHERE UsuarioId=?',[$uid]);
if (!$profesor) {
    http_response_code(403);
    exit('No hay un perfil docente asociado.');
}
$pid=filter_var($_GET['postulacion_id'] ?? null,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
if (!$pid) {
    http_response_code(400);
    exit('Identificador de postulación inválido.');
}
$postulante=tesco_alumno_cv_para_profesor((int)$pid,$uid);
if (!$postulante) {
    http_response_code(404);
    exit('Postulación no encontrada o no autorizada para este profesor.');
}
$cv=tesco_alumno_cv((int)$postulante['UsuarioId']);
header('Cache-Control: private, no-store');
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>CV del postulante | TESCo</title>
<link rel="stylesheet" href="../css/bootstrap-tesco.min.css">
<link rel="stylesheet" href="../css/alumnos.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css">
</head>
<body class="bg-light">
<header class="bg-primary text-white py-3">
 <div class="container-fluid px-3 px-md-4 d-flex flex-wrap justify-content-between align-items-center">
   <a href="postulaciones.php" class="text-white font-weight-bold"><i class="fas fa-arrow-left mr-2"></i>Volver a postulaciones</a>
   <span><i class="fas fa-graduation-cap mr-2"></i>TESCo Investigadores</span>
 </div>
</header>
<main class="container-fluid px-3 px-md-4 py-4">
  <div class="alert alert-info"><i class="fas fa-lock mr-2"></i>Consulta autorizada porque el alumno se postuló a una convocatoria publicada por tu cuenta. No compartas estos datos fuera del proceso de selección.</div>
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-success text-dark">
      <h1 class="h3 mb-1">CV de <?=tesco_escape(tesco_alumno_nombre($postulante))?></h1>
      <small>Postulación a: <?=tesco_escape($postulante['Convocatoria'])?></small>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-6 mb-2"><strong>Matrícula:</strong> <?=tesco_escape($postulante['Matricula'])?></div>
        <div class="col-md-6 mb-2"><strong>Correo:</strong> <?=tesco_escape($postulante['Correo'])?></div>
        <div class="col-md-6 mb-2"><strong>Carrera:</strong> <?=tesco_escape($postulante['Carrera'] ?: 'No registrada')?></div>
        <div class="col-md-6 mb-2"><strong>Semestre:</strong> <?=tesco_escape($postulante['Semestre'] ?: 'No registrado')?></div>
      </div>
      <h2 class="h5 mt-3">Presentación personal</h2>
      <p class="mb-0"><?=nl2br(tesco_escape($postulante['Biografia'] ?: 'Sin presentación registrada.'))?></p>
    </div>
  </div>
  <div class="row">
    <div class="col-12 col-lg-6">
      <section class="card border-0 shadow-sm mb-4"><div class="card-header bg-white"><h2 class="h5 mb-0"><i class="fas fa-briefcase mr-2 text-success"></i>Experiencia</h2></div><div class="card-body">
      <?php if(!$cv['experiencia_disponible']): ?><p class="text-muted">Historial de experiencia no disponible en esta instalación.</p>
      <?php elseif(!$cv['experiencia']): ?><p class="text-muted">El alumno aún no registró experiencia.</p><?php endif; ?>
      <?php foreach($cv['experiencia'] as $e): ?>
        <article class="border-bottom py-2">
          <h3 class="h6 mb-1"><?=tesco_escape($e['Cargo'])?></h3>
          <p class="mb-1"><?=tesco_escape($e['Organizacion'])?> · <?=tesco_escape($e['Tipo'])?></p>
          <p class="small text-muted mb-1"><?=tesco_escape(tesco_alumno_fecha($e['FechaInicio']))?> – <?= !empty($e['EsActual'])?'Actualidad':tesco_escape(tesco_alumno_fecha($e['FechaFin']))?></p>
          <?php if($e['Descripcion']): ?><p><?=nl2br(tesco_escape($e['Descripcion']))?></p><?php endif; ?>
        </article>
      <?php endforeach; ?>
      </div></section>
    </div>
    <div class="col-12 col-lg-6">
      <section class="card border-0 shadow-sm mb-4"><div class="card-header bg-white"><h2 class="h5 mb-0"><i class="fas fa-graduation-cap mr-2 text-success"></i>Formación académica</h2></div><div class="card-body">
      <?php if(!$cv['formacion_disponible']): ?><p class="text-muted">Historial de formación no disponible en esta instalación.</p>
      <?php elseif(!$cv['formacion']): ?><p class="text-muted">El alumno aún no registró formación adicional.</p><?php endif; ?>
      <?php foreach($cv['formacion'] as $f): ?>
        <article class="border-bottom py-2">
          <h3 class="h6 mb-1"><?=tesco_escape($f['Programa'])?></h3>
          <p class="mb-1"><?=tesco_escape($f['Grado'])?> · <?=tesco_escape($f['Institucion'])?></p>
          <p class="small text-muted mb-1"><?=tesco_escape(tesco_alumno_fecha($f['FechaInicio']))?> – <?=tesco_escape(tesco_alumno_fecha($f['FechaFin']))?></p>
          <?php if($f['Descripcion']): ?><p><?=tesco_escape($f['Descripcion'])?></p><?php endif; ?>
        </article>
      <?php endforeach; ?>
      </div></section>
    </div>
  </div>
  <section class="card border-0 shadow-sm mb-4"><div class="card-header bg-white"><h2 class="h5 mb-0"><i class="fas fa-tools mr-2 text-success"></i>Habilidades</h2></div><div class="card-body">
  <?php if(!$cv['habilidades']): ?><p class="text-muted">Sin habilidades registradas.</p><?php else: ?>
    <div class="d-flex flex-wrap">
      <?php foreach($cv['habilidades'] as $h): ?>
        <span class="badge badge-light border p-2 mb-2 mr-2">
          <?=tesco_escape($h['Habilidad'])?> · <?=tesco_escape($h['Nivel'])?>
          <?= $h['MesesExperiencia'] === null ? '' : ' · '.(int)$h['MesesExperiencia'].' meses' ?>
          · <?=tesco_escape($h['EstadoVerificacion'])?>
        </span>
      <?php endforeach; ?>
    </div>
    <p class="small text-muted mb-0">Las habilidades DECLARADAS son autorreportadas y todavía no han sido validadas institucionalmente.</p>
  <?php endif; ?>
  </div></section>
  <p class="small text-muted">La información corresponde a la versión actual del CV y puede cambiar si el alumno la edita.</p>
</main>
<script src="../js/jquery.min.js"></script><script src="../js/popper.min.js"></script><script src="../js/bootstrap.min.js"></script>
</body></html>
