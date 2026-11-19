<?php
require_once __DIR__.'/../includes/profesor_visual.php';
$registros=tesco_lista('publicaciones',$tescoProfesor);
tesco_pagina_inicio('Publicaciones','publicaciones.php');
tesco_encabezado('Publicaciones','Producción bibliográfica vinculada con el investigador.');
?>
<div class="d-flex justify-content-between align-items-center mb-3">
 <span class="badge badge-light border p-3">Total histórico: <?=count($registros)?></span>
 <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#modalPublicacion"><i class="fas fa-plus mr-2"></i>Nueva publicación</button>
</div>
<div class="row">
<section class="col-12 col-lg-9">
 <?php tesco_tarjeta('Producción bibliográfica','fas fa-book-open'); ?>
 <?php tesco_tabla_resultados($registros,[
  'Título'=>'Titulo','Tipo'=>'Tipo','DOI'=>'DOI','ISBN'=>'ISBN','Fecha'=>fn($r)=>tesco_fecha($r['Fecha']??null),'Validación'=>'Estado'
 ]); ?>
 <?php tesco_tarjeta_fin(); ?>
</section>
<aside class="col-12 col-lg-3">
 <?php tesco_tarjeta('Clasificación','fas fa-list'); ?>
 <?php $tipos=[];foreach($registros as $r){$t=(string)($r['Tipo']??'OTRA');$tipos[$t]=($tipos[$t]??0)+1;} ?>
 <?php if (!$tipos): ?><p class="text-muted">Sin publicaciones registradas.</p><?php else: ?>
 <?php foreach($tipos as $tipo=>$n):?><div class="d-flex justify-content-between mb-2"><span><?=tesco_h($tipo)?></span><strong><?=$n?></strong></div><?php endforeach; ?><?php endif; ?>
 <?php tesco_tarjeta_fin(); ?>
</aside>
</div>
<?php tesco_modal_inicio('modalPublicacion','Registrar publicación'); ?>
<?php tesco_formulario_inicio('publicacion'); ?>
<?php tesco_input('Título de la publicación','titulo','text','',true); ?>
<div class="form-group"><label for="publicacion-tipo">Tipo de publicación</label>
 <select class="form-control" id="publicacion-tipo" name="tipo" required>
   <option value="ARTICULO">Artículo</option><option value="LIBRO">Libro</option>
   <option value="CAPITULO">Capítulo de libro</option><option value="OTRA">Otra publicación</option>
 </select>
</div>
<?php tesco_input('Fecha académica','fecha','date'); ?>
<div class="form-row">
 <div class="col-12 col-md-6"><?php tesco_input('DOI','doi'); ?></div>
 <div class="col-12 col-md-6"><?php tesco_input('ISBN','isbn'); ?></div>
</div>
<p class="small text-muted">Se insertará Producto, Publicación y la contribución del docente en una transacción. La validación académica permanece pendiente.</p>
<?php tesco_formulario_fin(); tesco_modal_fin(); tesco_pagina_fin(); ?>
