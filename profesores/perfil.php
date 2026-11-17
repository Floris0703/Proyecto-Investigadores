<?php
require_once __DIR__.'/../includes/profesor_visual.php';
$perfil=tesco_datos_perfil($tescoProfesor);
tesco_pagina_inicio('Mi perfil','perfil.php');
tesco_encabezado('Mi perfil académico','Información personal y trayectoria del investigador.');
?>
<div class="d-flex justify-content-end mb-3">
  <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#modalEditarPerfil"><i class="fas fa-edit mr-2"></i>Editar perfil</button>
</div>
<div class="row">
<section class="col-12 col-lg-8">
 <div class="card shadow-sm mb-4">
  <div class="card-header bg-success text-dark"><h4 class="mb-1">Perfil académico</h4><small>Tecnológico de Estudios Superiores de Coacalco</small></div>
  <div class="card-body">
   <div class="media align-items-center mb-3">
    <div class="rounded-circle bg-light p-3 mr-3"><i class="fas fa-user fa-2x text-secondary"></i></div>
    <div class="media-body"><h5 class="mt-0 mb-1"><?=tesco_h($perfil['NombreCompleto'])?></h5>
      <p class="text-muted mb-0">Docente investigador</p>
      <small class="text-muted"><?=tesco_h($perfil['Departamento']?:'Departamento pendiente')?></small>
    </div>
   </div>
   <hr class="my-4">
   <div class="row">
    <?php foreach([
      ['Correo institucional',$perfil['Correo']],
      ['Grado académico',$perfil['GradoAcademico']],
      ['Departamento',$perfil['Departamento']],
      ['ORCID',$perfil['ORCID']??null],
      ['Ubicación de atención',$perfil['UbicacionAtencion']??null]
    ] as [$titulo,$valor]): ?>
    <div class="col-12 col-md-6 mb-3"><strong><?=tesco_h($titulo)?>:</strong>
      <p class="text-muted mb-0"><?=tesco_h($valor?:'Pendiente de registrar')?></p></div>
    <?php endforeach; ?>
   </div>
   <h6>Biografía</h6>
   <p class="text-muted"><?=nl2br(tesco_h($perfil['Biografia']?:'Pendiente de registrar'))?></p>
  </div>
 </div>
 <?php tesco_tarjeta('Trayectoria académica','fas fa-graduation-cap'); ?>
  <h6>Formación académica</h6>
  <?php tesco_tabla_resultados($perfil['Formacion'],['Grado'=>'Grado','Programa'=>'Programa','Institución'=>'Institucion','Estado'=>'Estado']); ?>
  <hr>
  <h6>Experiencia docente y de investigación</h6>
  <?php tesco_tabla_resultados($perfil['Experiencia'],['Tipo'=>'Tipo','Cargo'=>'Cargo','Organización'=>'Organizacion']); ?>
  <hr>
  <h6>Reconocimientos</h6>
  <?php tesco_tabla_resultados($perfil['Reconocimientos'],['Distinción'=>'Titulo','Otorgante'=>'Otorgante','Fecha'=>fn($r)=>tesco_fecha($r['Fecha'])]); ?>
 <?php tesco_tarjeta_fin(); ?>
</section>
<aside class="col-12 col-lg-4">
 <?php tesco_tarjeta('Áreas de interés','fas fa-microscope'); ?>
 <?php if (!$perfil['Intereses']): ?><p class="text-muted">No hay áreas de interés registradas.</p><?php else: ?>
 <?php foreach($perfil['Intereses'] as $interes): ?><span class="badge badge-success text-dark p-2 mr-1 mb-2"><?=tesco_h($interes['Nombre'])?></span><?php endforeach; ?>
 <?php endif; ?>
 <hr><h6>Líneas de investigación</h6>
 <?php if (!$perfil['Lineas']): ?><p class="text-muted mb-0">Pendiente de registrar.</p><?php else: ?>
 <?php foreach($perfil['Lineas'] as $linea): ?><div class="mb-1"><?=tesco_h($linea['Nombre'])?></div><?php endforeach; ?><?php endif; ?>
 <?php tesco_tarjeta_fin(); ?>
 <?php tesco_tarjeta('Horarios de atención','fas fa-clock'); ?>
 <?php tesco_tabla_resultados($perfil['Horarios'],[
  'Día (1-7)'=>'DiaSemana',
  'Inicio'=>fn($r)=>tesco_fecha($r['HoraInicio'],'H:i'),
  'Fin'=>fn($r)=>tesco_fecha($r['HoraFin'],'H:i'),
  'Lugar'=>'Lugar'
 ]); ?>
 <?php tesco_tarjeta_fin(); ?>
 <?php tesco_tarjeta('Actividad académica','fas fa-th-list'); ?>
 <div class="list-group">
 <?php foreach(['Proyectos'=>'proyectos.php','Publicaciones'=>'publicaciones.php','Congresos'=>'congresos.php','Titulación'=>'titulacion.php'] as $label=>$url): ?>
 <a href="<?=tesco_h($url)?>" class="list-group-item list-group-item-action"><?=tesco_h($label)?></a>
 <?php endforeach; ?></div>
 <?php tesco_tarjeta_fin(); ?>
</aside>
</div>
<?php tesco_modal_inicio('modalEditarPerfil','Editar perfil académico'); ?>
<?php tesco_formulario_inicio('perfil'); ?>
<div class="form-row">
 <div class="col-md-6"><?php tesco_input('Nombre','nombre','text',$perfil['Nombre']??'',true); ?></div>
 <div class="col-md-6"><?php tesco_input('Apellido paterno','apellido_paterno','text',$perfil['ApellidoPaterno']??'',true); ?></div>
</div>
<?php tesco_input('Apellido materno','apellido_materno','text',$perfil['ApellidoMaterno']??''); ?>
<?php tesco_input('Departamento','departamento','text',$perfil['Departamento']??''); ?>
<?php tesco_input('Grado académico','grado','text',$perfil['GradoAcademico']??''); ?>
<?php if(tesco_columna('academico.Profesor','ORCID')) tesco_input('ORCID','orcid','text',$perfil['ORCID']??''); ?>
<?php if(tesco_columna('academico.Profesor','UbicacionAtencion')) tesco_input('Ubicación de atención','ubicacion','text',$perfil['UbicacionAtencion']??''); ?>
<?php tesco_textarea('Biografía','biografia',$perfil['Biografia']??''); ?>
<?php tesco_formulario_fin(); ?>
<?php tesco_modal_fin(); ?>
<?php tesco_pagina_fin(); ?>
