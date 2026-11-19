<?php
require_once __DIR__.'/../includes/profesor_visual.php';
$metricas=tesco_metricas($tescoProfesor,$tescoAnio);
$tablas=[
 ['proyectos','Proyectos','Proyectos','proyectos.php','bg-success',$metricas['series']['proyectos']],
 ['convocatorias','Convocatorias','Convocatorias publicadas','convocatorias.php','bg-primary',$metricas['series']['convocatorias']],
 ['nacionales','Congresos nacionales','Congresos nacionales','congresos.php','bg-success',$metricas['nacional']],
 ['internacionales','Congresos internacionales','Congresos internacionales','congresos.php','bg-primary',$metricas['internacional']],
 ['publicaciones','Publicaciones','Producción bibliográfica','publicaciones.php','bg-success',$metricas['series']['publicaciones']]
];
$meses=['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
$maximo=max(1,...array_merge(...array_map(fn($item)=>array_values($item[5]),$tablas)));
tesco_pagina_inicio('Productividad','productividad.php');
tesco_encabezado('Productividad académica','Resumen cuantitativo de actividades registradas. Sin evaluaciones ni puntuaciones ficticias.');
?>
<div class="d-flex justify-content-end align-items-center flex-wrap mb-3">
  <form class="form-inline mr-2" method="get">
   <label for="periodo" class="mr-2">Periodo</label>
   <select name="anio" id="periodo" class="form-control form-control-sm mr-2">
   <?php for($y=(int)date('Y')+1;$y>=2020;$y--): ?>
     <option value="<?=$y?>" <?=$y===$tescoAnio?'selected':''?>><?=$y?></option>
   <?php endfor; ?></select>
   <button class="btn btn-outline-primary btn-sm" type="submit">Consultar</button>
  </form>
  <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#modalCriterios">
    <i class="fas fa-info-circle mr-1"></i>Criterios
  </button>
</div>
<div class="row">
<?php foreach([
 ['Proyectos','proyectos','fas fa-project-diagram'],
 ['Publicaciones','publicaciones','fas fa-book-open'],
 ['Congresos','congresos','fas fa-globe-americas'],
 ['Convocatorias','convocatorias','fas fa-bullhorn']
] as [$titulo,$tipo,$icono]): ?>
 <div class="col-6 col-lg-3 mb-3"><div class="card shadow-sm h-100"><div class="card-body">
   <i class="<?=tesco_h($icono)?> text-primary mb-3"></i>
   <h3 class="card-title mb-1"><?=tesco_h($metricas['totales'][$tipo])?></h3>
   <p class="text-muted mb-0"><?=tesco_h($titulo)?> / <?=tesco_h($tescoAnio)?></p>
 </div></div></div>
<?php endforeach; ?>
</div>
<div class="card shadow-sm mb-4"><div class="card-body">
<h5 class="mb-2"><i class="fas fa-chart-bar text-success mr-2"></i>Distribución mensual</h5>
<p class="text-muted">Registros por mes según su fecha académica. Se incluyen actividades programadas.</p>
<ul class="nav nav-tabs flex-nowrap overflow-auto mb-4" role="tablist">
 <?php foreach($tablas as $i=>$t): ?>
 <li class="nav-item"><a href="#tab-<?=tesco_h($t[0])?>" class="nav-link text-nowrap <?=$i===0?'active':''?>" data-toggle="tab" role="tab" aria-selected="<?=$i===0?'true':'false'?>"><?=tesco_h($t[1])?></a></li>
 <?php endforeach; ?>
</ul>
<div class="tab-content">
<?php foreach($tablas as $i=>$t): ?>
 <section class="tab-pane fade <?=$i===0?'show active':''?>" id="tab-<?=tesco_h($t[0])?>" role="tabpanel">
  <h6><?=tesco_h($t[2])?></h6>
  <?php foreach($meses as $m=>$nombre): $cantidad=$t[5][$m+1]??0; $porcentaje=$cantidad>0?(100*$cantidad/$maximo):0; ?>
  <div class="mb-3"><div class="d-flex justify-content-between mb-1"><span><?=tesco_h($nombre)?></span><strong><?=$cantidad?></strong></div>
   <div class="progress"><div class="progress-bar <?=tesco_h($t[4])?>" role="progressbar"
       style="width: <?=number_format($porcentaje,2,'.','')?>%" aria-valuenow="<?=$cantidad?>" aria-valuemin="0" aria-valuemax="<?=$maximo?>"></div></div>
  </div>
  <?php endforeach; ?>
  <a href="<?=tesco_h($t[3])?>" class="btn btn-outline-primary btn-sm">Consultar registros</a>
 </section>
<?php endforeach; ?>
</div>
<hr><small class="text-muted">Escala gráfica: máximo observado <?=$maximo?> registro(s), no porcentaje de desempeño.</small>
</div></div>
<div class="row">
<section class="col-12 col-lg-8">
<?php tesco_tarjeta('Desglose de actividades','fas fa-table'); ?>
<?php
$detalle=[
 ['Investigaciones','Proyectos',$metricas['totales']['proyectos'],'proyectos.php'],
 ['Producción bibliográfica','Publicaciones',$metricas['totales']['publicaciones'],'publicaciones.php'],
 ['Congresos','Nacionales',array_sum($metricas['nacional']),'congresos.php'],
 ['Congresos','Internacionales',array_sum($metricas['internacional']),'congresos.php'],
 ['Convocatorias','Publicadas',$metricas['totales']['convocatorias'],'convocatorias.php'],
 ['Titulación','Trabajos dirigidos (histórico)',$metricas['conteos']['titulacion'],'titulacion.php']
];
?>
<div class="table-responsive"><table class="table table-striped table-hover">
<thead class="thead-light"><tr><th>Actividad</th><th>Categoría</th><th>Cantidad</th><th>Consultar</th></tr></thead><tbody>
<?php foreach($detalle as [$a,$b,$n,$ruta]): ?><tr><td><?=tesco_h($a)?></td><td><?=tesco_h($b)?></td><td><?=tesco_h($n)?></td>
<td><a href="<?=tesco_h($ruta)?>" class="btn btn-outline-primary btn-sm">Ver</a></td></tr><?php endforeach; ?>
</tbody></table></div>
<?php tesco_tarjeta_fin(); ?>
</section>
<aside class="col-12 col-lg-4">
<?php tesco_tarjeta('Dirección de trabajos','fas fa-graduation-cap');
$trabajos=$metricas['listas']['titulacion'];
$terminados=count(array_filter($trabajos,fn($t)=>in_array(strtoupper((string)($t['Estado']??'')),['CONCLUIDO','TERMINADO','FINALIZADO'],true)));
?>
<div class="d-flex justify-content-between mb-3"><span>Trabajos registrados</span><strong><?=count($trabajos)?></strong></div>
<div class="d-flex justify-content-between mb-3"><span>Terminados</span><span class="badge badge-success text-dark p-2"><?=$terminados?></span></div>
<div class="d-flex justify-content-between mb-3"><span>Otros estados</span><span class="badge badge-warning p-2"><?=count($trabajos)-$terminados?></span></div>
<a href="titulacion.php" class="btn btn-outline-primary btn-sm">Consultar titulación</a>
<?php tesco_tarjeta_fin(); ?>
</aside>
</div>
<?php tesco_tarjeta('Muestra de actividad académica','fas fa-history'); ?>
<?php
$muestra=[];
foreach(['proyectos'=>'Proyecto','publicaciones'=>'Publicación','congresos'=>'Congreso','convocatorias'=>'Convocatoria','titulacion'=>'Titulación'] as $tipo=>$label)
 foreach($metricas['listas'][$tipo] as $fila) {
   $fila['Categoria']=$label;$fila['Titulo']=$fila['Titulo']??'Sin título';$muestra[]=$fila;
 }
usort($muestra,fn($a,$b)=>strcmp((string)tesco_fecha($b['Fecha']??null,'Y-m-d'),(string)tesco_fecha($a['Fecha']??null,'Y-m-d')));
tesco_tabla_resultados(array_slice($muestra,0,8),['Actividad'=>'Titulo','Categoría'=>'Categoria','Fecha'=>fn($r)=>tesco_fecha($r['Fecha']??null)]);
tesco_tarjeta_fin();
tesco_modal_inicio('modalCriterios','Criterios de productividad académica');
?>
<p>Este panel contiene <strong>conteos descriptivos</strong>, no calificaciones de desempeño.</p>
<ul>
<li>Proyectos: registros donde participa el profesor.</li>
<li>Publicaciones: productos con contribución del profesor.</li>
<li>Congresos: actividades donde participó el profesor; se separan por alcance.</li>
<li>Convocatorias: publicaciones registradas por el profesor.</li>
<li>Titulación: trabajos dirigidos, con resumen histórico.</li>
</ul>
<p class="mb-0">La ponderación institucional, validación y rúbricas deben aprobarse antes de generar índices de productividad.</p>
<?php tesco_modal_fin(); tesco_pagina_fin(); ?>
