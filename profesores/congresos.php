<?php
require_once __DIR__.'/../includes/profesor_visual.php';
$registros=tesco_lista('congresos',$tescoProfesor);
tesco_pagina_inicio('Congresos','congresos.php');
tesco_encabezado('Congresos','Participación en eventos académicos nacionales e internacionales.');
?>
<div class="d-flex justify-content-between align-items-center mb-3">
 <span class="badge badge-light border p-3">Participaciones: <?=count($registros)?></span>
 <button class="btn btn-outline-primary" type="button" data-toggle="modal" data-target="#modalCongreso"><i class="fas fa-plus mr-2"></i>Registrar congreso</button>
</div>
<div class="row">
<section class="col-12 col-lg-9">
<?php tesco_tarjeta('Congresos registrados','fas fa-globe-americas'); ?>
<ul class="nav nav-tabs mb-3" role="tablist">
 <li class="nav-item"><a href="#todos" class="nav-link active" data-toggle="tab">Todos</a></li>
 <li class="nav-item"><a href="#nacionales" class="nav-link" data-toggle="tab">Nacionales</a></li>
 <li class="nav-item"><a href="#internacionales" class="nav-link" data-toggle="tab">Internacionales</a></li>
</ul>
<div class="tab-content">
<?php foreach(['todos'=>null,'nacionales'=>'NACIONAL','internacionales'=>'INTERNACIONAL'] as $tab=>$alcance):
 $lista=$alcance===null?$registros:array_values(array_filter($registros,fn($r)=>($r['Tipo']??'')===$alcance)); ?>
<div id="<?=tesco_h($tab)?>" class="tab-pane fade <?=$tab==='todos'?'show active':''?>">
 <?php tesco_tabla_resultados($lista,[
  'Congreso'=>'Titulo','Alcance'=>'Tipo','Sede'=>'Sede','Modalidad'=>'Modalidad',
  'Fecha'=>fn($r)=>tesco_fecha($r['Fecha']??null)
 ]); ?>
</div>
<?php endforeach; ?>
</div>
<?php tesco_tarjeta_fin(); ?>
</section>
<aside class="col-12 col-lg-3">
<?php tesco_tarjeta('Alcance','fas fa-chart-pie'); ?>
<?php foreach(['NACIONAL'=>'Nacionales','INTERNACIONAL'=>'Internacionales','REGIONAL'=>'Regionales','LOCAL'=>'Locales'] as $codigo=>$nombre): ?>
<div class="d-flex justify-content-between mb-2"><span><?=tesco_h($nombre)?></span><strong><?=count(array_filter($registros,fn($r)=>($r['Tipo']??'')===$codigo))?></strong></div>
<?php endforeach; ?>
<?php tesco_tarjeta_fin(); ?>
</aside>
</div>
<?php tesco_modal_inicio('modalCongreso','Registrar participación en congreso'); ?>
<?php tesco_formulario_inicio('congreso'); ?>
<?php tesco_input('Nombre del congreso','nombre','text','',true); ?>
<div class="form-group"><label for="alcance">Alcance</label>
<select class="form-control" name="alcance" id="alcance">
<option value="NACIONAL">Nacional</option><option value="INTERNACIONAL">Internacional</option>
<option value="REGIONAL">Regional</option><option value="LOCAL">Local</option></select></div>
<?php tesco_input('Fecha de inicio','fecha','date'); ?>
<?php tesco_input('Sede','sede'); ?>
<?php tesco_input('Modalidad','modalidad'); ?>
<p class="small text-muted">Se registrará la actividad, el congreso y la participación del docente como ponente.</p>
<?php tesco_formulario_fin(); tesco_modal_fin(); tesco_pagina_fin(); ?>
