<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/tesco_auth.php';
tesco_session_start();
if (tesco_usuario_actual()) {
    header('Location: Inicio.php', true, 303);
    exit;
}
$error = (string)($_SESSION['tesco_login_error'] ?? '');
unset($_SESSION['tesco_login_error']);
header('Cache-Control: no-store');
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login | Investigadores TESCo</title>
  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../css/mdb.min.css">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css">
  <style>
    body.tesco-login {
      min-height: 100vh;
      background-color: #172338;
    }
    body.tesco-login::before {
      content: "";
      position: fixed;
      inset: -8px;
      z-index: 0;
      background: linear-gradient(rgba(14, 24, 62, .55), rgba(14, 24, 62, .55)),
                  url('images/tesco%20banderas.jpeg') center center / cover no-repeat;
      filter: blur(3px);
      pointer-events: none;
    }
    .tesco-login main {
      position: relative;
      z-index: 1;
    }
    .tesco-login .card {
      background-color: rgba(255, 255, 255, .97);
      border-radius: .75rem;
    }
  </style>
</head>
<body class="tesco-login">
<main class="container min-vh-100 d-flex align-items-center justify-content-center py-5">
  <div class="row justify-content-center w-100"><div class="col-12 col-md-8 col-lg-5">
    <div class="card shadow-sm border-0"><div class="card-body p-4">
      <h1 class="h4 text-center mb-2"><i class="fas fa-graduation-cap mr-2"></i>Acceso institucional</h1>
      <p class="text-muted text-center">Inicia sesión para continuar a la bienvenida institucional. Tu rol se obtiene automáticamente de SQL Server.</p>
      <?php if ($error !== ''): ?><div class="alert alert-danger" role="alert"><?=tesco_escape($error)?></div><?php endif; ?>
      <form action="autenticar.php" method="post" autocomplete="on">
        <input type="hidden" name="csrf" value="<?=tesco_escape(tesco_csrf_public())?>">
        <div class="form-group">
          <label for="correo">Correo electrónico</label>
          <input class="form-control" id="correo" type="email" name="correo" maxlength="255" autocomplete="username" required>
        </div>
        <div class="form-group">
          <label for="contrasena">Contraseña</label>
          <input class="form-control" id="contrasena" type="password" name="contrasena" autocomplete="current-password" required>
        </div>
        <button class="btn btn-primary btn-block" type="submit">Iniciar sesión</button>
      </form>
      <p class="text-muted small mt-3 mb-0">Los permisos se obtienen de la base de datos, no se eligen en el formulario. Si no tienes una cuenta habilitada, contacta a la administración institucional.</p>
      <a class="d-block text-center mt-3" href="index%20-%20copia.html"><i class="fas fa-arrow-left mr-1"></i>Volver a la portada</a>
    </div></div>
  </div></div>
</main>
<script src="../js/jquery.min.js"></script><script src="../js/popper.min.js"></script><script src="../js/bootstrap.min.js"></script>
</body></html>
