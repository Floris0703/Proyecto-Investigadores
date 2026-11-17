<?php
require_once __DIR__.'/../includes/profesor_visual.php';
$registros=tesco_lista('titulacion',$tescoProfesor);
tesco_pagina_inicio('Titulación','titulacion.php');
tesco_encabezado('Titulación','Seguimiento de trabajos de titulación dirigidos por el investigador.');
?>
<div class="d-flex justify-content-between align-items-center mb-3">
 <span class="badge badge-light border p-3">Trabajos dirigidos: <?=count($registros)?></span>
 <button class="btn btn-outline-primary" type="button" data-toggle="modal" data-target="#modalTitulacion"><i class="fas fa-plus mr-2"></i>Nuevo trabajo</button>
</div>
<div class="row">
<section class="col-12 col-lg-9">
<?php tesco_tarjeta('Trabajos de titulación','fas fa-graduation-cap'); ?>
<?php tesco_tabla_resultados($registros,[
 'Título'=>'Titulo','Modalidad'=>'Tipo','Estado'=>'Estado',
 'Fecha de inicio'=>fn($r)=>tesco_fecha($r['Fecha']??null),
 'Conclusión'=>fn($r)=>tesco_fecha($r['FechaConclusion']??null)
]); ?>
<?php tesco_tarjeta_fin(); ?>
</section>
<aside class="col-12 col-lg-3">
<?php tesco_tarjeta('Estados de trabajos','fas fa-clipboard-check'); ?>
<?php $estados=[];foreach($registros as $r){$st=(string)($r['Estado']??'SIN ESTADO');$estados[$st]=($estados[$st]??0)+1;} ?>
<?php if(!$estados): ?><p class="text-muted">Sin trabajos dirigidos.</p><?php else: ?>
<?php foreach($estados as $estado=>$n): ?><div class="d-flex justify-content-between mb-2"><span><?=tesco_h($estado)?></span><strong><?=$n?></strong></div><?php endforeach; ?>
<?php endif; ?>
<p class="small text-muted">Los avances detallados del modelo 004 se integrarán cuando se definan sus reglas de edición.</p>
<?php tesco_tarjeta_fin(); ?>
</aside>
</div>
<?php tesco_modal_inicio('modalTitulacion','Registrar trabajo de titulación'); ?>
<?php tesco_formulario_inicio('titulacion'); ?>
<?php tesco_input('Título del trabajo','titulo','text','',true); ?>
<?php tesco_input('Modalidad','modalidad','text','',true); ?>
<?php tesco_input('Fecha de inicio','fecha','date'); ?>
<p class="small text-muted">Se creará el producto de titulación, el registro académico y la asociación del profesor como director.</p>
<?php tesco_formulario_fin(); tesco_modal_fin(); tesco_pagina_fin(); ?>
