<?php
declare(strict_types=1);
require_once __DIR__.'/../includes/admin_layout.php';
[$admin,$error]=tesco_admin_contexto();
$buscar=trim((string)($_GET['q']??''));
if (strlen($buscar)>100) $buscar=substr($buscar,0,100);
$estado=(string)($_GET['estado']??'');
if (!in_array($estado,['','BORRADOR','PUBLICADA','ABIERTA','CERRADA'],true)) $estado='';
$convocatorias=tesco_admin_convocatorias($buscar,$estado);
tesco_admin_inicio('Supervisión de convocatorias','convocatorias.php',$admin,$error);
?>
<div class="alert alert-info"><i class="fas fa-info-circle mr-2"></i>Los profesores publican y gestionan sus convocatorias. Administración puede cerrar preventivamente una publicación y reactivarla, pero no crea ni modifica sus contenidos.</div>
<form method="get" class="form-row align-items-end mb-3">
  <div class="form-group col-md-6"><label for="q">Buscar título o profesor</label><input id="q" name="q" class="form-control" maxlength="100" value="<?=tesco_escape($buscar)?>"></div>
  <div class="form-group col-md-4"><label for="estado">Estado</label>
    <select name="estado" id="estado" class="custom-select">
      <?php foreach([''=>'Todos','BORRADOR'=>'Borrador','PUBLICADA'=>'Publicada','ABIERTA'=>'Abierta','CERRADA'=>'Cerrada'] as $valor=>$etiqueta): ?>
        <option value="<?=tesco_escape($valor)?>" <?=$estado===$valor?'selected':''?>><?=tesco_escape($etiqueta)?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="form-group col-md-2"><button class="btn btn-outline-primary btn-block my-0" type="submit">Buscar</button></div>
</form>
<?php tesco_admin_tarjeta_inicio('Convocatorias institucionales','fas fa-bullhorn'); ?>
<?php if (!$convocatorias): tesco_admin_sin_datos(); else: ?>
<div class="table-responsive"><table class="table table-striped table-hover tesco-admin-table">
<thead class="thead-light"><tr><th>ID</th><th>Título</th><th>Profesor</th><th>Publicado</th><th>Cierre</th><th>Postulaciones</th><th>Estado</th><th>Moderación</th></tr></thead>
<tbody>
<?php foreach ($convocatorias as $c): ?>
<tr>
 <td><?= (int)$c['ConvocatoriaId'] ?></td>
 <td><strong><?=tesco_escape($c['Titulo'])?></strong></td>
 <td><?=tesco_escape($c['Profesor'])?><div class="small text-muted"><?=tesco_escape($c['Publicador'])?></div></td>
 <td><?=tesco_escape(tesco_admin_fecha($c['FechaPublicacion']))?></td>
 <td><?=tesco_escape(tesco_admin_fecha($c['FechaCierre']))?></td>
 <td><?= (int)$c['Postulaciones'] ?></td>
 <td><span class="badge badge-light border"><?=tesco_escape($c['Estado'])?></span></td>
 <td>
 <?php if (in_array((string)$c['Estado'],['PUBLICADA','ABIERTA','CERRADA'],true)): ?>
  <form method="post">
    <input type="hidden" name="csrf" value="<?=tesco_escape(tesco_csrf_public())?>">
    <input type="hidden" name="accion" value="moderar_convocatoria">
    <input type="hidden" name="convocatoria_id" value="<?= (int)$c['ConvocatoriaId'] ?>">
    <?php $cerrar=(string)$c['Estado']!=='CERRADA'; ?>
    <input type="hidden" name="estado" value="<?=$cerrar?'CERRADA':'PUBLICADA'?>">
    <button class="btn btn-sm <?=$cerrar?'btn-outline-danger':'btn-outline-success'?>" type="submit">
      <?=$cerrar?'Cerrar':'Reactivar'?>
    </button>
  </form>
 <?php else: ?><span class="text-muted small">Sin acción</span><?php endif; ?>
 </td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<p class="small text-muted">La página muestra los últimos 100 registros. Una convocatoria cerrada deja de aparecer entre las convocatorias vigentes para nuevos postulantes; las solicitudes existentes se conservan.</p>
<?php endif; ?>
<?php tesco_admin_tarjeta_fin(); ?>
<?php tesco_admin_fin(); ?>
