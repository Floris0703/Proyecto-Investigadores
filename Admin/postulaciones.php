<?php
declare(strict_types=1);
require_once __DIR__.'/../includes/admin_layout.php';
[$admin,$error]=tesco_admin_contexto();
$estado=(string)($_GET['estado']??'');
if (!in_array($estado,['','PENDIENTE','EN_REVISION','ACEPTADA','RECHAZADA'],true)) $estado='';
$postulaciones=tesco_admin_postulaciones($estado);
tesco_admin_inicio('Supervisión de postulaciones','postulaciones.php',$admin,$error);
?>
<div class="alert alert-info"><i class="fas fa-eye mr-2"></i>Vista de seguimiento institucional. La aceptación o rechazo corresponde al profesor responsable de cada convocatoria, no a Administración.</div>
<form method="get" class="form-inline mb-3">
  <label for="estado" class="mr-2">Estado de postulación</label>
  <select name="estado" id="estado" class="custom-select mr-2 mb-2">
    <?php foreach([''=>'Todos','PENDIENTE'=>'Pendiente','EN_REVISION'=>'En revisión','ACEPTADA'=>'Aceptada','RECHAZADA'=>'Rechazada'] as $valor=>$etiqueta): ?>
      <option value="<?=tesco_escape($valor)?>" <?=$estado===$valor?'selected':''?>><?=tesco_escape($etiqueta)?></option>
    <?php endforeach; ?>
  </select>
  <button type="submit" class="btn btn-outline-primary mb-2">Filtrar</button>
</form>
<?php tesco_admin_tarjeta_inicio('Postulaciones registradas','fas fa-clipboard-check'); ?>
<?php if (!$postulaciones): tesco_admin_sin_datos('Todavía no hay postulaciones registradas.'); else: ?>
<div class="table-responsive"><table class="table table-striped table-hover tesco-admin-table">
<thead class="thead-light"><tr><th>ID</th><th>Alumno</th><th>Matrícula</th><th>Convocatoria</th><th>Profesor</th><th>Fecha</th><th>Estado</th></tr></thead><tbody>
<?php foreach ($postulaciones as $p): ?>
<tr>
<td><?= (int)$p['PostulacionId'] ?></td>
<td><?=tesco_escape($p['Alumno'])?></td>
<td><?=tesco_escape($p['Matricula']??'Sin matrícula')?></td>
<td><?=tesco_escape($p['Titulo'])?></td>
<td><?=tesco_escape($p['Profesor'])?></td>
<td><?=tesco_escape(tesco_admin_fecha($p['FechaPostulacion']))?></td>
<td><span class="badge badge-light border"><?=tesco_escape($p['Estado'])?></span></td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<p class="small text-muted">Se muestran las 100 postulaciones más recientes; los estados se leen directamente de SQL Server.</p>
<?php endif; ?>
<?php tesco_admin_tarjeta_fin(); ?>
<?php tesco_admin_fin(); ?>
