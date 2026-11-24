<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tituloPagina ?? 'Investigadores TESCo', ENT_QUOTES, 'UTF-8') ?> | Investigadores TESCo</title>
    <!-- Bootstrap 4.4.1 con paleta institucional; sin CSS por página -->
    <link rel="stylesheet" href="../css/bootstrap-tesco.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css">
</head>
<body class="bg-light">
<!-- Barra superior: menú vertical desplegable, no sidebar fija -->
<div class="container-fluid px-0">
  <div class="bg-primary rounded-right pr-3">
    <div class="bg-success rounded-right pr-3">
      <nav class="navbar navbar-expand-lg navbar-light bg-white rounded-right shadow-sm">
        <div class="container-fluid">
          <a class="navbar-brand font-weight-bold text-primary" href="index.php"><i class="fas fa-graduation-cap mr-2"></i>TESCo</a>
          <ul class="navbar-nav flex-row ml-auto align-items-center">
            <li class="nav-item"><a class="nav-link px-2" href="../Landing/Inicio.php" title="Inicio público" aria-label="Inicio público"><i class="fas fa-home"></i><span class="d-none d-lg-inline ml-1">Inicio</span></a></li>
            <li class="nav-item"><a class="nav-link px-2" href="index.php" title="Resumen"><i class="fas fa-chart-bar"></i><span class="d-none d-lg-inline ml-1">Resumen</span></a></li>
            <li class="nav-item"><a class="nav-link px-2" href="convocatorias.php" aria-label="Convocatorias" title="Convocatorias"><i class="fas fa-bell"></i></a></li>
            <li class="nav-item"><a class="nav-link px-2" href="perfil.php" aria-label="Mi perfil" title="Mi perfil"><i class="fas fa-user"></i></a></li>
          </ul>
          <button class="navbar-toggler d-block ml-2" type="button" data-toggle="modal" data-target="#menuLateral"
                  aria-label="Abrir menú académico" aria-controls="menuLateral" aria-haspopup="dialog">
            <span class="navbar-toggler-icon"></span>
          </button>
        </div>
      </nav>
    </div>
  </div>
</div>
<?php require __DIR__ . '/profesor_menu.php'; ?>
