<?php
/** Componentes visuales del módulo Administración — Bootstrap 4.4.1 TESCo. */
declare(strict_types=1);
require_once __DIR__.'/admin_datos.php';

function tesco_admin_contexto(): array {
    $admin=tesco_admin_actual();
    $error='';
    if (($_SERVER['REQUEST_METHOD']??'GET')==='POST') {
        try {
            tesco_admin_procesar($admin,$_POST);
            $pagina=basename((string)($_SERVER['SCRIPT_NAME']??'index.php'));
            $validas=['perfil.php','usuarios.php','convocatorias.php','habilidades.php'];
            header('Location: '.(in_array($pagina,$validas,true)?$pagina:'index.php').'?guardado=1',true,303);
            exit;
        } catch (InvalidArgumentException $e) {
            $error=$e->getMessage();
        } catch (Throwable $e) {
            error_log('TESCo admin: '.get_class($e).' durante acción de panel');
            $error='No se pudo realizar la operación. Revisa datos, duplicados o restricciones de SQL Server.';
        }
    }
    return [$admin,$error];
}
function tesco_admin_inicio(string $titulo,string $activa,array $admin,string $error=''): void {
    $items=[
        ['index.php','Resumen','fas fa-chart-bar'],
        ['usuarios.php','Usuarios y roles','fas fa-users'],
        ['convocatorias.php','Convocatorias','fas fa-bullhorn'],
        ['postulaciones.php','Postulaciones','fas fa-clipboard-check'],
        ['habilidades.php','Habilidades','fas fa-certificate'],
        ['produccion.php','Producción académica','fas fa-book'],
        ['perfil.php','Mi perfil','fas fa-user']
    ];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Panel institucional de Administración TESCo Investigadores.">
  <title><?=tesco_escape($titulo)?> | Administración TESCo</title>
  <link rel="stylesheet" href="../css/bootstrap-tesco.min.css">
  <link rel="stylesheet" href="../css/admin.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css">
</head>
<body class="bg-light">
<header class="container-fluid px-0">
  <div class="bg-primary rounded-right pr-3"><div class="bg-success rounded-right pr-3">
    <nav class="navbar navbar-expand-lg navbar-light bg-white rounded-right shadow-sm" aria-label="Navegación administrativa">
      <div class="container-fluid">
        <a href="index.php" class="navbar-brand font-weight-bold text-primary"><i class="fas fa-graduation-cap mr-2" aria-hidden="true"></i>TESCo</a>
        <ul class="navbar-nav flex-row ml-auto align-items-center">
          <li class="nav-item"><a href="../Landing/Inicio.php" class="nav-link px-2" title="Inicio" aria-label="Inicio"><i class="fas fa-home"></i><span class="d-none d-lg-inline ml-1">Inicio</span></a></li>
          <li class="nav-item"><a href="index.php" class="nav-link px-2" title="Resumen" aria-label="Resumen"><i class="fas fa-chart-bar"></i><span class="d-none d-lg-inline ml-1">Resumen</span></a></li>
          <li class="nav-item"><a href="habilidades.php" class="nav-link px-2" title="Habilidades" aria-label="Habilidades"><i class="fas fa-certificate"></i></a></li>
          <li class="nav-item"><a href="perfil.php" class="nav-link px-2" title="Perfil" aria-label="Mi perfil"><i class="fas fa-user"></i></a></li>
        </ul>
        <button class="navbar-toggler d-block ml-2" type="button" data-toggle="modal" data-target="#menuLateral"
                aria-label="Abrir menú de administración" aria-controls="menuLateral" aria-haspopup="dialog">
          <span class="navbar-toggler-icon"></span>
        </button>
      </div>
    </nav>
  </div></div>
</header>
<div class="modal fade" id="menuLateral" tabindex="-1" role="dialog" aria-labelledby="tituloMenuLateral" aria-hidden="true">
  <div class="modal-dialog m-0 h-100 tesco-admin-menu-dialog" role="document">
    <div class="modal-content h-100 bg-warning text-dark rounded-0">
      <div class="modal-header bg-primary text-white">
        <h2 class="modal-title h5 mb-0" id="tituloMenuLateral"><i class="fas fa-graduation-cap mr-2"></i>Investigadores TESCo</h2>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar menú"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body d-flex flex-column overflow-auto">
        <div>
          <small class="text-uppercase font-weight-bold d-block mb-2 text-primary">Administración</small>
          <nav class="nav flex-column" aria-label="Secciones de administración">
            <?php foreach ($items as [$ruta,$nombre,$icono]): ?>
              <a href="<?=tesco_escape($ruta)?>" class="nav-link py-2 mb-1 rounded <?=$ruta===$activa?'bg-primary text-white font-weight-bold':'text-dark'?>"
                 <?=$ruta===$activa?'aria-current="page"':''?>>
                <i class="<?=tesco_escape($icono)?> mr-2" aria-hidden="true"></i><?=tesco_escape($nombre)?>
              </a>
            <?php endforeach; ?>
          </nav>
        </div>
        <div class="mt-auto pt-4">
          <hr class="border-primary">
          <p class="small text-muted mb-2"><?=tesco_escape(tesco_admin_nombre($admin))?></p>
          <a class="nav-link text-dark px-0" href="../Landing/Inicio.php"><i class="fas fa-home mr-2"></i>Volver a Inicio</a>
          <form action="../Landing/cerrar_sesion.php" method="post">
            <input type="hidden" name="csrf" value="<?=tesco_escape(tesco_csrf_public())?>">
            <button type="submit" class="btn btn-link text-dark px-0"><i class="fas fa-sign-out-alt mr-2"></i>Cerrar sesión</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<main class="container-fluid mt-4 px-3 px-md-4 pb-5">
  <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
    <div><h1 class="h3 text-primary mb-1"><?=tesco_escape($titulo)?></h1>
      <p class="text-muted mb-0">Plataforma de Investigación del Tecnológico de Estudios Superiores de Coacalco</p>
    </div>
    <span class="badge badge-light border px-3 py-2 mt-3 mt-md-0"><i class="fas fa-user-shield text-success mr-2"></i>Administración · <?=tesco_escape($admin['NumeroEmpleado'])?></span>
  </div>
  <?php if (isset($_GET['guardado'])): ?>
    <div class="alert alert-success" role="status">La operación se completó correctamente.</div>
  <?php endif; ?>
  <?php if ($error!==''): ?>
    <div class="alert alert-danger" role="alert"><?=tesco_escape($error)?></div>
  <?php endif; ?>
<?php }
function tesco_admin_fin(): void { ?>
</main>
<footer class="bg-primary text-white py-3">
  <div class="container-fluid px-3 px-md-4 d-flex flex-wrap justify-content-between">
    <span>TESCo Investigadores · Módulo Administración</span>
    <a href="../Landing/Inicio.php" class="text-white">Inicio institucional</a>
  </div>
</footer>
<script src="../js/jquery.min.js"></script>
<script src="../js/popper.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
</body>
</html>
<?php }
function tesco_admin_tarjeta_inicio(string $titulo,string $icono='fas fa-list'): void { ?>
<section class="card shadow-sm border-0 mb-4">
  <div class="card-header bg-white"><h2 class="h5 mb-0"><i class="<?=tesco_escape($icono)?> text-success mr-2"></i><?=tesco_escape($titulo)?></h2></div>
  <div class="card-body">
<?php }
function tesco_admin_tarjeta_fin(): void { echo '</div></section>'; }
function tesco_admin_sin_datos(string $mensaje='No hay registros disponibles.'): void {
    echo '<p class="text-muted mb-0">'.tesco_escape($mensaje).'</p>';
}
