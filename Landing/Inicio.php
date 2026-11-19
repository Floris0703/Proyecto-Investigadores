<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/tesco_landing_datos.php';
require_once __DIR__ . '/../includes/tesco_auth.php';
// Bienvenida protegida: la portada y el login preceden a esta pantalla.
$roles = tesco_require_login();
$rolActivo = (string)($_SESSION['tesco_rol_activo'] ?? '');
$necesitaElegirRol = !in_array($rolActivo, $roles, true);
$destinoPerfil = $necesitaElegirRol ? 'elegir_rol.php' : 'panel.php';
$nombreBienvenida = '';
try {
    $perfilBienvenida = tesco_db_one(
        'SELECT TOP (1) Nombre FROM plataforma.PerfilUsuario WHERE UsuarioId=?',
        [tesco_usuario_actual()]
    );
    $nombreBienvenida = trim((string)($perfilBienvenida['Nombre'] ?? ''));
} catch (Throwable $e) {
    error_log('TESCo: nombre de bienvenida no disponible');
}
$stats = null;
$produccionTreemap = null;
$convocatorias = [];
$anunciosError = false;
try { $stats = tesco_landing_metricas(); }
catch (Throwable $e) { error_log('TESCo: métricas públicas no disponibles'); }
try { $produccionTreemap = tesco_landing_treemap_produccion(); }
catch (Throwable $e) { error_log('TESCo: producción del treemap no disponible'); }
try { $convocatorias = tesco_landing_convocatorias(6); }
catch (Throwable $e) { error_log('TESCo: convocatorias públicas no disponibles'); $anunciosError = true; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Plataforma institucional para consultar proyectos, publicaciones y actividades académicas.">
  <title>Bienvenida | TESCo Investigadores</title>
  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../css/mdb.min.css">
  <link rel="stylesheet" href="../css/style.css?v=4">
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
              <button class="navbar-toggler d-block ml-2" id="tescoMenuToggle" type="button"
                      aria-label="Abrir menú de navegación" aria-expanded="false"
                      aria-controls="menuLateral" aria-haspopup="dialog">
                <span class="navbar-toggler-icon"></span>
              </button>
            </div>
          </nav>
        </div>
      </div>
    </div>
  </header>

  <!-- Menú público independiente del modal Bootstrap/MDB para evitar desplazar el documento. -->
  <div id="tescoMenuOverlay" class="tesco-menu-overlay" hidden>
    <div class="tesco-menu-backdrop" data-menu-close aria-hidden="true"></div>
    <aside id="menuLateral" class="tesco-menu-panel" role="dialog" aria-modal="true"
           aria-labelledby="tituloMenuLateral" aria-hidden="true" tabindex="-1">
      <div class="tesco-menu-header">
        <h2 class="h5 mb-0" id="tituloMenuLateral">
          <i class="fas fa-graduation-cap mr-2" aria-hidden="true"></i>Investigadores TESCo
        </h2>
        <button type="button" class="close text-white tesco-menu-close" data-menu-close aria-label="Cerrar menú">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="tesco-menu-body">
        <div>
          <small class="text-uppercase font-weight-bold d-block mb-2 tesco-public-menu-heading">Institucional</small>
          <nav class="nav flex-column" aria-label="Secciones públicas">
            <a href="Inicio.php" class="nav-link py-2 mb-1 rounded tesco-public-active" aria-current="page">
              <i class="fas fa-home mr-2" aria-hidden="true"></i>Inicio
            </a>
            <a href="#titulo-indicadores" class="nav-link py-2 mb-1 rounded text-dark" data-menu-close>
              <i class="fas fa-chart-bar mr-2" aria-hidden="true"></i>Estadísticas
            </a>
            <a href="#profesores" class="nav-link py-2 mb-1 rounded text-dark" data-menu-close>
              <i class="fas fa-project-diagram mr-2" aria-hidden="true"></i>Convocatorias
            </a>
            <a href="panel.php" class="nav-link py-2 mb-1 rounded text-dark">
              <i class="fas fa-chalkboard-teacher mr-2" aria-hidden="true"></i>Acceso al panel
            </a>
          </nav>
        </div>
        <div class="mt-auto pt-4">
          <hr class="tesco-public-menu-divider">
          <a href="<?=tesco_usuario_actual() ? 'panel.php' : 'acceso.php'?>" class="nav-link text-dark px-0">
            <i class="fas fa-sign-in-alt mr-2" aria-hidden="true"></i><?=tesco_usuario_actual() ? 'Mi panel' : 'Acceso a la plataforma'?>
          </a>
          <span class="nav-link text-muted px-0" aria-disabled="true">
            <i class="fas fa-cog mr-2" aria-hidden="true"></i>Configuración (próximamente)
          </span>
        </div>
      </div>
    </aside>
  </div>
  <main>
        <!-- Institucional -->
    <section class="section-padding section-muted">
      <div class="container">
        <div class="institution-block">
          <span class="section-kicker">BIENVENIDA INSTITUCIONAL</span>
          <h1 class="h2">Bienvenido<?= $nombreBienvenida !== '' ? ', ' . tesco_escape($nombreBienvenida) : '' ?> a TESCo Investigadores</h1>
          <p>Explora la actividad académica, los proyectos y las convocatorias de la institución. Cuando quieras continuar, entra a tu perfil con los permisos de tu cuenta.</p>
          <p class="small text-muted mb-3">Rol<?= count($roles) > 1 ? 'es' : '' ?> autorizado<?= count($roles) > 1 ? 's' : '' ?>: <?=tesco_escape(implode(', ', $roles))?></p>
          <div class="d-flex flex-wrap justify-content-center align-items-center">
            <a class="btn btn-primary mr-2 mb-2" href="<?=tesco_escape($destinoPerfil)?>">
              <i class="fas fa-user-circle mr-2" aria-hidden="true"></i><?= $necesitaElegirRol ? 'Elegir rol y entrar a mi perfil' : 'Entrar a mi perfil' ?>
            </a>
            <form action="cerrar_sesion.php" method="post" class="mb-2">
              <input type="hidden" name="csrf" value="<?=tesco_escape(tesco_csrf_public())?>">
              <button class="btn btn-outline-secondary" type="submit">
                <i class="fas fa-sign-out-alt mr-2" aria-hidden="true"></i>Cerrar sesión
              </button>
            </form>
          </div>
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
    <!-- Treemap: área de cada rectángulo proporcional a producción académica en BD. -->
    <div class="container mt-4">
      <div class="section-heading mb-3">
        <span class="section-kicker">PRODUCCIÓN ACADÉMICA</span>
        <h3 class="h4 mb-2">Distribución por tipo de producción</h3>
        <p>El área representa la proporción de registros institucionales por categoría; no es una puntuación de calidad ni participación de mercado.</p>
      </div>
      <?php if ($produccionTreemap === null): ?>
        <div class="alert alert-warning" role="status">No fue posible consultar la distribución de producción académica.</div>
      <?php elseif (!$produccionTreemap): ?>
        <div class="alert alert-light border" role="status">Todavía no hay producción académica registrada para mostrar.</div>
      <?php else: ?>
        <?php
          $totalProduccion = array_sum(array_column($produccionTreemap,'cantidad'));
          $treemapJson = json_encode(
              $produccionTreemap,
              JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_THROW_ON_ERROR
          );
        ?>
        <div class="tesco-treemap-resumen d-flex flex-wrap justify-content-between align-items-center mb-3">
          <span class="font-weight-bold"><i class="fas fa-chart-area text-success mr-2" aria-hidden="true"></i><?=number_format($totalProduccion,0,',','.')?> registros de producción</span>
          <span class="small text-muted">Porcentajes calculados sobre el total registrado</span>
        </div>
        <div id="tescoTreemap" class="tesco-treemap" role="img" aria-label="Mapa de áreas proporcionales de producción académica. La distribución detallada aparece debajo."></div>
        <div class="tesco-treemap-leyenda mt-3" aria-label="Detalle por categoría">
          <div class="row">
            <?php foreach ($produccionTreemap as $i=>$tipo): ?>
              <div class="col-12 col-sm-6 col-lg-4 mb-2">
                <div class="d-flex align-items-center">
                  <span class="tesco-treemap-punto tesco-treemap-color-<?=($i%9)+1?>" aria-hidden="true"></span>
                  <span class="small flex-grow-1"><?=tesco_escape($tipo['nombre'])?></span>
                  <strong class="small ml-2"><?=number_format($tipo['cantidad'],0,',','.')?></strong>
                  <span class="small text-muted ml-2"><?=number_format($tipo['cantidad']/$totalProduccion*100,1,',','.')?>%</span>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <script type="application/json" id="tescoTreemapData"><?=$treemapJson?></script>
        <noscript><p class="small text-muted">Activa JavaScript para ver el mapa de áreas; el desglose de cantidades y porcentajes permanece visible arriba.</p></noscript>
      <?php endif; ?>
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
  <script src="../js/landing-menu.js?v=4"></script>
</body>
</html>
