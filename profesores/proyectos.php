<?php
require_once __DIR__.'/../includes/profesor_visual.php';
$registros=tesco_lista('proyectos',$tescoProfesor);
tesco_pagina_inicio('Mis proyectos','proyectos.php');
tesco_encabezado('Mis proyectos','Investigaciones y proyectos en los que participa el docente.');
?>
<div class="d-flex justify-content-between align-items-center mb-3">
 <span class="badge badge-light border p-3">Total histórico: <?=count($registros)?></span>
 <button class="btn btn-outline-primary" type="button" data-toggle="modal" data-target="#modalProyecto"><i class="fas fa-plus mr-2"></i>Nuevo proyecto</button>
</div>
<div class="row">
 <section class="col-12 col-lg-8">
 <?php tesco_tarjeta('Proyectos registrados','fas fa-project-diagram'); ?>
 <?php tesco_tabla_resultados($registros,[
  'Título'=>'Titulo',
  'Estado'=>'Estado',
  'Inicio'=>fn($r)=>tesco_fecha($r['Fecha']??null),
  'Conclusión'=>fn($r)=>tesco_fecha($r['FechaFin']??null)
 ]); ?>
 <?php tesco_tarjeta_fin(); ?>
 </section>
 <aside class="col-12 col-lg-4">
 <?php tesco_tarjeta('Seguimiento de proyectos','fas fa-info-circle'); ?>
 <p>Los proyectos se obtienen mediante la relación <strong>ProyectoParticipante</strong>, no únicamente por el usuario que los registró.</p>
 <p class="text-muted mb-0">Los proyectos con fechas incompletas se muestran, pero no se atribuyen a un año en Productividad.</p>
 <?php tesco_tarjeta_fin(); ?>
 <?php tesco_tarjeta('Estados observados','fas fa-chart-pie'); ?>
 <?php $estados=[]; foreach($registros as $r){$st=(string)($r['Estado']??'SIN ESTADO');$estados[$st]=($estados[$st]??0)+1;} ?>
 <?php if (!$estados):?><p class="text-muted">Sin datos.</p><?php else: ?>
 <?php foreach($estados as $e=>$n):?><div class="d-flex justify-content-between mb-2"><span><?=tesco_h($e)?></span><strong><?=$n?></strong></div><?php endforeach; ?><?php endif; ?>
 <?php tesco_tarjeta_fin(); ?>
 </aside>
</div>
<?php tesco_modal_inicio('modalProyecto','Registrar proyecto'); ?>
<?php tesco_formulario_inicio('proyecto'); ?>
<?php tesco_input('Título del proyecto','titulo','text','',true); ?>
<?php tesco_textarea('Descripción','descripcion'); ?>
<?php tesco_input('Fecha de inicio','fecha','date'); ?>
<p class="small text-muted">Al guardar se creará el proyecto y la asociación del docente como participante responsable, en una sola transacción.</p>
<?php tesco_formulario_fin(); tesco_modal_fin(); tesco_pagina_fin(); ?>
