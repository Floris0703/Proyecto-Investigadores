<?php
/** Presentación común del alumnado, compatible con el diseño docente de Bootstrap 4.4.1. */
declare(strict_types=1);
require_once __DIR__ . '/alumno_datos.php';

function tesco_alumno_encabezado(string $titulo, string $activo, array $alumno): void {
    $items = [
        ['index.php','Resumen','fas fa-chart-bar'],
        ['perfil.php','Mi perfil','fas fa-user'],
        ['postulaciones.php','Convocatorias y postulaciones','fas fa-clipboard-check'],
        ['cv.php','CV y experiencia','fas fa-file-alt']
    ];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Panel del alumnado de TESCo Investigadores.">
  <title><?=tesco_escape($titulo)?> | Alumnos TESCo</title>
  <link rel="stylesheet" href="../css/bootstrap-tesco.min.css">
  <link rel="stylesheet" href="../css/alumnos.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css">
</head>
<body class="bg-light">
<header class="container-fluid px-0">
  <div class="bg-primary rounded-right pr-3"><div class="bg-success rounded-right pr-3">
    <nav class="navbar navbar-expand-lg navbar-light bg-white rounded-right shadow-sm" aria-label="Navegación del alumno">
      <div class="container-fluid">
        <a href="index.php" class="navbar-brand font-weight-bold text-primary">
          <i class="fas fa-graduation-cap mr-2" aria-hidden="true"></i>TESCo
        </a>
        <ul class="navbar-nav flex-row ml-auto align-items-center">
          <li class="nav-item"><a href="../Landing/Inicio.php" class="nav-link px-2" aria-label="Bienvenida" title="Bienvenida"><i class="fas fa-home"></i><span class="d-none d-lg-inline ml-1">Bienvenida</span></a></li>
          <li class="nav-item"><a href="index.php" class="nav-link px-2" aria-label="Resumen" title="Resumen"><i class="fas fa-chart-bar"></i><span class="d-none d-lg-inline ml-1">Resumen</span></a></li>
          <li class="nav-item"><a href="postulaciones.php" class="nav-link px-2" aria-label="Postulaciones" title="Postulaciones"><i class="fas fa-bell"></i></a></li>
          <li class="nav-item"><a href="perfil.php" class="nav-link px-2" aria-label="Perfil" title="Mi perfil"><i class="fas fa-user"></i></a></li>
        </ul>
        <button class="navbar-toggler d-block ml-2" type="button" data-toggle="modal" data-target="#menuLateral"
          aria-label="Abrir menú académico" aria-controls="menuLateral" aria-haspopup="dialog">
          <span class="navbar-toggler-icon"></span>
        </button>
      </div>
    </nav>
  </div></div>
</header>
<div class="modal fade" id="menuLateral" tabindex="-1" role="dialog" aria-labelledby="tituloMenuLateral" aria-hidden="true">
  <div class="modal-dialog m-0 h-100 tesco-alumno-menu-dialog" role="document">
    <div class="modal-content h-100 bg-warning text-dark rounded-0">
      <div class="modal-header bg-primary text-white">
        <h2 class="modal-title h5 mb-0" id="tituloMenuLateral"><i class="fas fa-graduation-cap mr-2"></i>Investigadores TESCo</h2>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar menú"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body d-flex flex-column overflow-auto">
        <div>
          <small class="text-uppercase font-weight-bold d-block mb-2 text-primary">Alumnado</small>
          <nav class="nav flex-column" aria-label="Secciones del alumno">
            <?php foreach ($items as [$ruta,$nombre,$icono]): ?>
            <a href="<?=tesco_escape($ruta)?>" class="nav-link py-2 mb-1 rounded <?= $ruta === $activo ? 'bg-primary text-white font-weight-bold' : 'text-dark' ?>"
               <?= $ruta === $activo ? 'aria-current="page"' : '' ?>>
              <i class="<?=tesco_escape($icono)?> mr-2" aria-hidden="true"></i><?=tesco_escape($nombre)?>
            </a>
            <?php endforeach; ?>
          </nav>
        </div>
        <div class="mt-auto pt-4">
          <hr class="border-primary">
          <p class="small text-muted mb-2"><?=tesco_escape(tesco_alumno_nombre($alumno))?></p>
          <a class="nav-link text-dark px-0" href="../Landing/Inicio.php"><i class="fas fa-home mr-2"></i>Volver a bienvenida</a>
          <form action="../Landing/cerrar_sesion.php" method="post">
            <input type="hidden" name="csrf" value="<?=tesco_escape(tesco_csrf_public())?>">
            <button class="btn btn-link text-dark px-0" type="submit"><i class="fas fa-sign-out-alt mr-2"></i>Cerrar sesión</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<main class="container-fluid mt-4 px-3 px-md-4 pb-5">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
      <h1 class="h3 text-primary mb-1"><?=tesco_escape($titulo)?></h1>
      <p class="text-muted mb-0">Plataforma de Investigación del Tecnológico de Estudios Superiores de Coacalco</p>
    </div>
    <span class="badge badge-light border px-3 py-2 mt-3 mt-md-0">
      <i class="fas fa-user-graduate text-success mr-2"></i>Alumno · <?=tesco_escape($alumno['Matricula'] ?? '')?>
    </span>
  </div>
<?php
}
function tesco_alumno_pie(): void { ?>
</main>
<footer class="bg-primary text-white py-3">
  <div class="container-fluid px-3 px-md-4 d-flex flex-wrap justify-content-between">
    <span>TESCo Investigadores · Módulo Alumnos</span>
    <a class="text-white" href="../Landing/Inicio.php">Bienvenida institucional</a>
  </div>
</footer>
<script src="../js/jquery.min.js"></script>
<script src="../js/popper.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
</body>
</html>
<?php }
function tesco_alumno_tarjeta_inicio(string $titulo,string $icono='fas fa-info-circle'): void { ?>
<section class="card shadow-sm border-0 mb-4">
  <div class="card-header bg-white"><h2 class="h5 mb-0"><i class="<?=tesco_escape($icono)?> text-success mr-2"></i><?=tesco_escape($titulo)?></h2></div>
  <div class="card-body">
<?php }
function tesco_alumno_tarjeta_fin(): void { echo '</div></section>'; }
function tesco_alumno_alerta(string $mensaje,string $tipo='info'): void {
    $allowed=['success','danger','warning','info']; if (!in_array($tipo,$allowed,true)) $tipo='info';
    echo '<div class="alert alert-'.$tipo.'" role="status">'.tesco_escape($mensaje).'</div>';
}
