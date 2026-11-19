<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/tesco_landing_datos.php';
require_once __DIR__ . '/../includes/tesco_auth.php';
tesco_session_start();
$stats = null;
$convocatorias = [];
$anunciosError = false;
try { $stats = tesco_landing_metricas(); }
catch (Throwable $e) { error_log('TESCo: métricas públicas no disponibles'); }
try { $convocatorias = tesco_landing_convocatorias(6); }
catch (Throwable $e) { error_log('TESCo: convocatorias públicas no disponibles'); $anunciosError = true; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Plataforma institucional para consultar proyectos, publicaciones y actividades académicas.">
  <title>Inicio</title>
  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../css/mdb.min.css">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css">
</head>
<body>
  <!-- Navbar público: lenguaje visual del módulo Profesores, enlaces propios de Landing -->
  <header class="tesco-public-nav sticky-top">
    <div class="container-fluid px-0">
      <div class="tesco-public-nav-primary rounded-right pr-3">
        <div class="tesco-public-nav-success rounded-right pr-3">
          <nav class="navbar navbar-expand-lg navbar-light bg-white rounded-right shadow-sm" aria-label="Navegación principal">
            <div class="container-fluid">
              <a class="navbar-brand font-weight-bold tesco-public-brand" href="Inicio.php">
                <i class="fas fa-graduation-cap mr-2" aria-hidden="true"></i>TESCo
              </a>
              <ul class="navbar-nav flex-row ml-auto align-items-center">
                <li class="nav-item">
                  <a class="nav-link px-2" href="Inicio.php" aria-current="page" title="Inicio público">
                    <i class="fas fa-home" aria-hidden="true"></i><span class="d-none d-lg-inline ml-1">Inicio</span>
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link px-2" href="#titulo-indicadores" title="Estadísticas institucionales">
                    <i class="fas fa-chart-bar" aria-hidden="true"></i><span class="d-none d-lg-inline ml-1">Estadísticas</span>
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link px-2" href="#profesores" title="Próximos proyectos">
                    <i class="fas fa-bell" aria-hidden="true"></i><span class="sr-only">Próximos proyectos</span>
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link px-2" href="<?=tesco_usuario_actual() ? 'panel.php' : 'acceso.php'?>" aria-label="<?=tesco_usuario_actual() ? 'Mi panel' : 'Iniciar sesión'?>" title="Acceso institucional"><i class="fas fa-user" aria-hidden="true"></i></a>
                </li>
              </ul>
              <button class="navbar-toggler d-block ml-2" type="button" data-toggle="modal"
                      data-target="#menuLateral" aria-label="Abrir menú de navegación"
                      aria-controls="menuLateral" aria-haspopup="dialog">
                <span class="navbar-toggler-icon"></span>
              </button>
            </div>
          </nav>
        </div>
      </div>
    </div>
  </header>

  <!-- Menú lateral institucional, equivalente visual al menú PHP de Profesores -->
  <div class="modal fade tesco-public-menu" id="menuLateral" tabindex="-1" role="dialog"
       aria-labelledby="tituloMenuLateral" aria-hidden="true">
    <div class="modal-dialog m-0 h-100" role="document">
      <div class="modal-content h-100 rounded-0">
        <div class="modal-header text-white">
          <h5 class="modal-title" id="tituloMenuLateral">
            <i class="fas fa-graduation-cap mr-2" aria-hidden="true"></i>Investigadores TESCo
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar menú">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body d-flex flex-column overflow-auto">
          <div>
            <small class="text-uppercase font-weight-bold d-block mb-2 tesco-public-menu-heading">Institucional</small>
            <nav class="nav flex-column" aria-label="Secciones públicas">
              <a href="Inicio.php" class="nav-link py-2 mb-1 rounded tesco-public-active" aria-current="page">
                <i class="fas fa-home mr-2" aria-hidden="true"></i>Inicio
              </a>
              <a href="#titulo-indicadores" class="nav-link py-2 mb-1 rounded text-dark" data-dismiss="modal">
                <i class="fas fa-chart-bar mr-2" aria-hidden="true"></i>Estadísticas
              </a>
              <a href="#profesores" class="nav-link py-2 mb-1 rounded text-dark" data-dismiss="modal">
                <i class="fas fa-project-diagram mr-2" aria-hidden="true"></i>Próximos proyectos
              </a>
              <a href="panel.php" class="nav-link py-2 mb-1 rounded text-dark">
                <i class="fas fa-chalkboard-teacher mr-2" aria-hidden="true"></i>Acceso al panel
              </a>
            </nav>
          </div>
          <div class="mt-auto pt-4">
            <hr class="tesco-public-menu-divider">
            <a href="<?=tesco_usuario_actual() ? 'panel.php' : 'acceso.php'?>" class="nav-link text-dark px-0"><i class="fas fa-sign-in-alt mr-2" aria-hidden="true"></i><?=tesco_usuario_actual() ? 'Mi panel' : 'Acceso a la plataforma'?></a>
            <span class="nav-link text-muted px-0" aria-disabled="true">
              <i class="fas fa-cog mr-2" aria-hidden="true"></i>Configuración (próximamente)
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <main>
        <!-- Institucional -->
    <section class="section-padding section-muted">
      <div class="container">
        <div class="institution-block">
          <span class="section-kicker">INVESTIGACIÓN E INNOVACIÓN</span>
          <h2>El conocimiento también construye comunidad</h2>
          <p>Esta plataforma busca facilitar el acceso a la información sobre el trabajo académico de la institución, dar visibilidad a sus investigadores y contribuir al seguimiento de sus actividades y resultados.</p>
        </div>
      </div>
    </section>
    <!-- Acceso con credenciales reales disponible en acceso.php. -->
    <?php if ($stats === null || $anunciosError): ?>
      <div class="container mt-4"><div class="alert alert-warning" role="status">Parte de la información institucional no está disponible en este momento.</div></div>
    <?php endif; ?>

    <!-- Indicadores generales -->
    <section class="section-padding" aria-labelledby="titulo-indicadores">
      <div class="container">
        <div class="section-heading">
          <span class="section-kicker">EN CIFRAS</span>
          <h2 id="titulo-indicadores">Actividad académica institucional</h2>
          <p>Indicadores generales de la producción académica registrada.</p>
        </div>
        <div class="row">
          <?php foreach ([
              ['profesores','Profesores','Investigadores registrados'],
              ['proyectos','Proyectos','Proyectos registrados'],
              ['articulos','Artículos','Artículos académicos'],
              ['congresos','Congresos','Congresos registrados']
            ] as [$clave,$etiqueta,$nota]): ?>
            <div class="col-6 col-lg-3 mb-3"><article class="stat-card">
              <span class="stat-label"><?=tesco_escape($etiqueta)?></span>
              <strong><?= $stats === null ? '—' : number_format($stats[$clave],0,',','.') ?></strong>
              <span class="stat-note"><?=tesco_escape($nota)?></span>
            </article></div>
          <?php endforeach; ?>
        </div>
      </div>
    <style>
      .news-tile {
    position: relative;
    overflow: hidden;
    height: 100%;
}

.news-tile img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
    </style>
    <!-- Producción académica -->
      <div class="container">

<div class="row">

    <!-- ARTÍCULOS: columna izquierda -->
    <div class="col-lg-8 col-md-12 mb-4">
        <div class="news-tile view zoom z-depth-1 h-100">
            <a href="Historia.html" class="text-white">
                <img src="images/art inv.webp" class="img-fluid w-100" alt="Artículos">

                <div class="mask rgba-stylish-strong">
                    <div class="text-white text-center py-lg-5 py-0 my-0">
                        <h4 class="card-title font-weight-bold">
                            <strong>Artículos</strong>
                        </h4>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- COLUMNA DERECHA -->
    <div class="col-lg-4 col-md-12 mb-4 d-flex flex-column">

        <!-- CONGRESOS -->
        <div class="news-tile view zoom z-depth-1 flex-fill mb-3">
            <a href="Ofrendas.html" class="text-white">
                <img src="images/congrsos.jpg" class="img-fluid w-100" alt="Congresos">

                <div class="mask rgba-stylish-strong">
                    <div class="text-white text-center py-lg-5 py-0 my-0">
                        <h4 class="card-title font-weight-bold">
                            <strong>Congresos</strong>
                        </h4>
                    </div>
                </div>
            </a>
        </div>

        <!-- LIBROS Y TITULADOS -->
        <div class="row flex-fill">

            <div class="col-6">
                <div class="news-tile view zoom z-depth-1 h-100">
                    <a href="Vestimenta.html" class="text-white">
                        <img src="images/libros.jpg" class="img-fluid w-100" alt="Libros">

                        <div class="mask rgba-stylish-strong">
                            <div class="text-white text-center py-lg-5 py-0 my-0">
                                <h4 class="card-title font-weight-bold">
                                    <strong>Libros</strong>
                                </h4>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-6">
                <div class="news-tile view zoom z-depth-1 h-100">
                    <a href="Artesania.html" class="text-white">
                        <img src="images/tesis.jpg" class="img-fluid w-100" alt="Titulados">

                        <div class="mask rgba-stylish-strong">
                            <div class="text-white text-center py-lg-5 py-0 my-0">
                                <h4 class="card-title font-weight-bold">
                                    <strong>Titulados</strong>
                                </h4>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
      </div>
    </section>

    <!-- Convocatorias consultadas desde SQL Server -->
    <section id="profesores" class="section-padding section-muted" aria-labelledby="convocatorias">
      <div class="container">
        <div class="section-heading">
          <span class="section-kicker">OPORTUNIDADES</span>
          <h2 id="convocatorias">Convocatorias vigentes</h2>
          <p>Oportunidades de investigación publicadas por la comunidad TESCo.</p>
        </div>
        <?php if ($anunciosError): ?>
          <div class="alert alert-warning">No fue posible consultar las convocatorias.</div>
        <?php elseif (!$convocatorias): ?>
          <div class="alert alert-info">Actualmente no hay convocatorias publicadas y vigentes.</div>
        <?php else: ?>
          <div class="row">
            <?php foreach ($convocatorias as $aviso): ?>
              <div class="col-md-6 col-lg-4 mb-4">
                <article class="profile-card h-100 d-flex flex-column">
                  <span class="badge badge-success align-self-start mb-3">Convocatoria abierta</span>
                  <h3><?=tesco_escape($aviso['Titulo'])?></h3>
                  <p class="profile-area">Cierre: <?=tesco_escape(tesco_landing_fecha($aviso['FechaCierre']))?></p>
                  <p><?=tesco_escape(function_exists('mb_substr')
                       ? mb_substr((string)($aviso['Descripcion'] ?? 'Sin descripción adicional.'),0,180,'UTF-8')
                       : substr((string)($aviso['Descripcion'] ?? 'Sin descripción adicional.'),0,180))?></p>
                  <a class="text-link mt-auto" href="convocatoria.php?id=<?=urlencode((string)$aviso['ConvocatoriaId'])?>">Consultar detalles &rarr;</a>
                </article>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </section>



  </main>

  <footer class="footer">
    <div class="container">
      <div class="row">
        <div class="col-md-5 mb-4 mb-md-0"><a class="footer-brand" href="#inicio">Tesco</a><p class="mt-2">Plataforma institucional de producción académica e investigación.</p></div>
        <div class="col-md-3 mb-4 mb-md-0"><h2 class="h6">Navegación</h2><a href="#profesores">Profesores</a><a href="#produccion">Producción académica</a><a href="#estadisticas">Estadísticas</a></div>
        <div class="col-md-4"><h2 class="h6">Contacto institucional</h2><p>Correo: contacto@institucion.edu.mx</p><p>Teléfono: (000) 000 0000</p></div>
      </div>
      <div class="footer-bottom">&copy; 2026 Institución educativa. Todos los derechos reservados.</div>
    </div>
  </footer>

  <!-- Dependencias requeridas por Bootstrap 4.4.1: jQuery, Popper y Bootstrap -->
  <script src="../js/jquery.min.js"></script>
  <script src="../js/popper.min.js"></script>
  <script src="../js/bootstrap.min.js"></script>
  <script src="../js/main.js"></script>
</body>
</html>
