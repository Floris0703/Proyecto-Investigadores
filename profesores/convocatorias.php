<?php
require_once __DIR__.'/../includes/profesor_visual.php';
$registros=tesco_lista('convocatorias',$tescoProfesor);
tesco_pagina_inicio('Convocatorias','convocatorias.php');
tesco_encabezado('Convocatorias','Difusión de oportunidades académicas y de investigación.');
?>
<div class="d-flex justify-content-between align-items-center mb-3">
 <span class="badge badge-light border p-3">Convocatorias registradas: <?=count($registros)?></span>
 <button class="btn btn-outline-primary" type="button" data-toggle="modal" data-target="#modalConvocatoria"><i class="fas fa-plus mr-2"></i>Nueva convocatoria</button>
</div>
<div class="row">
<section class="col-12 col-lg-9">
<?php tesco_tarjeta('Mis convocatorias','fas fa-bullhorn'); ?>
<ul class="nav nav-tabs mb-3" role="tablist">
 <li class="nav-item"><a class="nav-link active" href="#conv-todas" data-toggle="tab">Todas</a></li>
 <li class="nav-item"><a class="nav-link" href="#conv-publicadas" data-toggle="tab">Publicadas</a></li>
 <li class="nav-item"><a class="nav-link" href="#conv-borradores" data-toggle="tab">Borradores</a></li>
</ul>
<div class="tab-content">
<?php foreach(['todas'=>null,'publicadas'=>'PUBLICADA','borradores'=>'BORRADOR'] as $tab=>$estado):
 $datos=$estado===null?$registros:array_values(array_filter($registros,fn($r)=>strtoupper((string)($r['Estado']??''))===$estado)); ?>
<div class="tab-pane fade <?=$tab==='todas'?'show active':''?>" id="conv-<?=tesco_h($tab)?>">
<?php tesco_tabla_resultados($datos,[
 'Convocatoria'=>'Titulo','Estado'=>'Estado',
 'Publicación'=>fn($r)=>tesco_fecha($r['Fecha']??null),
 'Cierre'=>fn($r)=>tesco_fecha($r['FechaCierre']??null)
]); ?>
</div>
<?php endforeach; ?>
</div>
<?php tesco_tarjeta_fin(); ?>
</section>
<aside class="col-12 col-lg-3">
<?php tesco_tarjeta('Estado de convocatorias','fas fa-info-circle'); ?>
<?php $estados=[];foreach($registros as $r){$st=strtoupper((string)($r['Estado']??'DESCONOCIDO'));$estados[$st]=($estados[$st]??0)+1;} ?>
<?php if(!$estados):?><p class="text-muted">Sin convocatorias registradas.</p><?php else: ?>
<?php foreach($estados as $estado=>$n):?><div class="d-flex justify-content-between mb-2"><span><?=tesco_h($estado)?></span><strong><?=$n?></strong></div><?php endforeach; ?>
<?php endif; ?>
<a href="postulaciones.php" class="btn btn-outline-primary btn-sm mt-2">Ver postulaciones</a>
<?php tesco_tarjeta_fin(); ?>
</aside>
</div>
<?php tesco_modal_inicio('modalConvocatoria','Registrar convocatoria'); ?>
<?php tesco_formulario_inicio('convocatoria'); ?>
<?php tesco_input('Título de la convocatoria','titulo','text','',true); ?>
<?php tesco_textarea('Descripción','descripcion'); ?>
<?php tesco_input('Fecha de cierre','fecha_cierre','date'); ?>
<div class="form-group"><label for="publicada"><input id="publicada" type="checkbox" name="publicada" value="1"> Publicar inmediatamente (desmarcar para borrador)</label></div>
<p class="small text-muted">Las convocatorias publicadas podrán ser consultadas en el módulo compartido cuando ese módulo use SQL Server.</p>
<?php tesco_formulario_fin(); tesco_modal_fin(); tesco_pagina_fin(); ?>
