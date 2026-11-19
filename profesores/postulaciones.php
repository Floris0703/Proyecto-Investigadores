<?php
require_once __DIR__.'/../includes/profesor_visual.php';
$registros=tesco_lista('postulaciones',$tescoProfesor);
tesco_pagina_inicio('Postulaciones','postulaciones.php');
tesco_encabezado('Postulaciones recibidas','Solicitudes de estudiantes a convocatorias publicadas por el investigador.');
?>
<div class="row">
<section class="col-12 col-lg-9">
<?php tesco_tarjeta('Solicitudes recibidas','fas fa-users'); ?>
<?php if(!$registros): ?>
<div class="alert alert-light border">Aún no hay postulaciones para las convocatorias de este profesor.</div>
<?php else: ?>
<div class="table-responsive"><table class="table table-striped table-hover">
<thead class="thead-light"><tr><th>Aspirante</th><th>Correo</th><th>Convocatoria</th><th>Fecha</th><th>Estado</th><th>Cambiar estado</th></tr></thead>
<tbody>
<?php foreach($registros as $fila): ?>
<tr>
 <td><?=tesco_h($fila['Aspirante'])?></td>
 <td><?=tesco_h($fila['Correo'])?></td>
 <td><?=tesco_h($fila['Convocatoria'])?></td>
 <td><?=tesco_h(tesco_fecha($fila['Fecha']))?></td>
 <td><span class="badge badge-warning p-2"><?=tesco_h($fila['Estado'])?></span></td>
 <td>
  <form method="post" class="form-inline" autocomplete="off">
   <input type="hidden" name="csrf" value="<?=tesco_h($_SESSION['tesco_csrf'])?>">
   <input type="hidden" name="accion" value="postulacion_estado">
   <input type="hidden" name="postulacion_id" value="<?=tesco_h($fila['Id'])?>">
   <select class="form-control form-control-sm mb-1 mr-1" name="estado" aria-label="Nuevo estado de postulación">
   <?php foreach(['PENDIENTE'=>'Pendiente','EN_REVISION'=>'En revisión','ACEPTADA'=>'Aceptada','RECHAZADA'=>'Rechazada'] as $codigo=>$nombre): ?>
    <option value="<?=tesco_h($codigo)?>" <?=$codigo===(string)$fila['Estado']?'selected':''?>><?=tesco_h($nombre)?></option>
   <?php endforeach; ?>
   </select>
   <button class="btn btn-outline-primary btn-sm mb-1" type="submit">Actualizar</button>
  </form>
 </td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<?php endif; ?>
<?php tesco_tarjeta_fin(); ?>
</section>
<aside class="col-12 col-lg-3">
<?php tesco_tarjeta('Seguimiento','fas fa-clipboard-check'); ?>
<?php $estados=[];foreach($registros as $r){$st=(string)($r['Estado']??'SIN ESTADO');$estados[$st]=($estados[$st]??0)+1;} ?>
<?php if(!$estados):?><p class="text-muted">Sin solicitudes.</p><?php else: ?>
<?php foreach($estados as $estado=>$n):?><div class="d-flex justify-content-between mb-2"><span><?=tesco_h($estado)?></span><strong><?=$n?></strong></div><?php endforeach; ?>
<?php endif; ?>
<p class="small text-muted mb-0">Solo se permite actualizar postulaciones de convocatorias publicadas por el profesor seleccionado. Con la migración 004 se registra también el historial de cambios.</p>
<?php tesco_tarjeta_fin(); ?>
</aside>
</div>
<?php tesco_pagina_fin(); ?>
