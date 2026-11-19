<?php
declare(strict_types=1);
require_once __DIR__.'/../includes/admin_layout.php';
[$admin,$error]=tesco_admin_contexto();
$datos=tesco_admin_produccion();
tesco_admin_inicio('Producción académica','produccion.php',$admin,$error);
?>
<div class="alert alert-info"><i class="fas fa-book-open mr-2"></i>Consulta institucional de registros existentes. La captura de nuevos proyectos, publicaciones y congresos permanece en el módulo del profesor.</div>
<?php tesco_admin_tarjeta_inicio('Proyectos','fas fa-project-diagram'); ?>
<?php if (!$datos['proyectos']): tesco_admin_sin_datos('No hay proyectos registrados.'); else: ?>
<div class="table-responsive"><table class="table table-striped table-hover tesco-admin-table">
<thead class="thead-light"><tr><th>ID</th><th>Título</th><th>Responsable</th><th>Estado</th><th>Inicio</th></tr></thead><tbody>
<?php foreach ($datos['proyectos'] as $v): ?>
<tr><td><?= (int)$v['ProyectoId'] ?></td><td><?=tesco_escape($v['Titulo'])?></td><td><?=tesco_escape($v['Responsable'])?></td><td><?=tesco_escape($v['Estado'])?></td><td><?=tesco_escape(tesco_admin_fecha($v['FechaInicio']))?></td></tr>
<?php endforeach; ?>
</tbody></table></div><?php endif; ?>
<?php tesco_admin_tarjeta_fin(); ?>
<?php tesco_admin_tarjeta_inicio('Publicaciones','fas fa-book'); ?>
<?php if (!$datos['publicaciones']): tesco_admin_sin_datos('No hay publicaciones registradas.'); else: ?>
<div class="table-responsive"><table class="table table-striped table-hover tesco-admin-table">
<thead class="thead-light"><tr><th>ID</th><th>Título</th><th>Tipo</th><th>Fecha</th></tr></thead><tbody>
<?php foreach ($datos['publicaciones'] as $v): ?>
<tr><td><?= (int)$v['ProductoId'] ?></td><td><?=tesco_escape($v['Titulo'])?></td><td><?=tesco_escape($v['TipoPublicacion'])?></td><td><?=tesco_escape(tesco_admin_fecha($v['FechaProducto']))?></td></tr>
<?php endforeach; ?>
</tbody></table></div><?php endif; ?>
<?php tesco_admin_tarjeta_fin(); ?>
<?php tesco_admin_tarjeta_inicio('Congresos','fas fa-calendar-check'); ?>
<?php if (!$datos['congresos']): tesco_admin_sin_datos('No hay congresos registrados.'); else: ?>
<div class="table-responsive"><table class="table table-striped table-hover tesco-admin-table">
<thead class="thead-light"><tr><th>ID</th><th>Nombre</th><th>Alcance</th><th>Modalidad</th><th>Sede</th><th>Inicio</th></tr></thead><tbody>
<?php foreach ($datos['congresos'] as $v): ?>
<tr><td><?= (int)$v['ActividadId'] ?></td><td><?=tesco_escape($v['Nombre'])?></td><td><?=tesco_escape($v['Alcance'])?></td><td><?=tesco_escape($v['Modalidad']??'—')?></td><td><?=tesco_escape($v['Sede']??'—')?></td><td><?=tesco_escape(tesco_admin_fecha($v['FechaInicio']))?></td></tr>
<?php endforeach; ?>
</tbody></table></div><?php endif; ?>
<?php tesco_admin_tarjeta_fin(); ?>
<?php tesco_admin_fin(); ?>
