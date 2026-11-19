<?php
declare(strict_types=1);
require_once __DIR__.'/../includes/admin_layout.php';
[$admin,$error]=tesco_admin_contexto();
$postulaciones=tesco_admin_postulaciones();
tesco_admin_inicio('Supervisión de postulaciones','postulaciones.php',$admin,$error);
?>
<div class="alert alert-info"><i class="fas fa-eye mr-2"></i>Vista de seguimiento institucional. La aceptación o rechazo corresponde al profesor responsable de cada convocatoria, no a Administración.</div>
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
