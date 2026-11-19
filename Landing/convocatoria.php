<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/tesco_landing_datos.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$convocatoria = null;
$error = false;
try { $convocatoria = $id ? tesco_landing_convocatoria((int)$id) : null; }
catch (Throwable $e) { error_log('TESCo: consulta pública de convocatoria no disponible'); $error = true; }
if ($error) http_response_code(503);
elseif (!$convocatoria) http_response_code(404);
?>
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Convocatoria | TESCo</title>
<link rel="stylesheet" href="../css/bootstrap.min.css"><link rel="stylesheet" href="../css/mdb.min.css">
<link rel="stylesheet" href="../css/style.css"></head><body>
<main class="container py-5" style="max-width:920px">
<a href="Inicio.php#convocatorias" class="d-block mb-3">&larr; Volver a convocatorias</a>
<?php if ($error): ?>
<div class="alert alert-warning" role="alert">Las convocatorias no están disponibles en este momento.</div>
<?php elseif (!$convocatoria): ?>
<div class="alert alert-info">La convocatoria no existe o ya no está vigente.</div>
<?php else: ?>
<article class="card shadow-sm"><div class="card-body p-4">
<h1 class="h3"><?=tesco_escape($convocatoria['Titulo'])?></h1>
<p class="text-muted">Publicada el <?=tesco_escape(tesco_landing_fecha($convocatoria['FechaPublicacion']))?>
 · Cierre: <?=tesco_escape(tesco_landing_fecha($convocatoria['FechaCierre']))?></p>
<p style="white-space:pre-wrap"><?=tesco_escape($convocatoria['Descripcion'] ?? 'Sin descripción adicional.')?></p>
<p class="small text-muted">Para participar, accede con tu cuenta institucional. La postulación desde Landing se integrará con el módulo correspondiente.</p>
<a href="acceso.php" class="btn btn-primary">Acceso institucional</a>
</div></article>
<?php endif; ?>
</main></body></html>
