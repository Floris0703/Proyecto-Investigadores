<!-- Menú desplegable VERTICAL por hamburguesa, usando Modal de Bootstrap 4.4.1 -->
<div class="modal fade" id="menuLateral" tabindex="-1" role="dialog"
     aria-labelledby="tituloMenuLateral" aria-hidden="true">
  <div class="modal-dialog m-0 h-100" role="document">
    <div class="modal-content h-100 bg-warning text-dark rounded-0">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="tituloMenuLateral"><i class="fas fa-graduation-cap mr-2"></i>Investigadores TESCo</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar menú"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body d-flex flex-column overflow-auto">
        <div>
          <small class="text-uppercase font-weight-bold d-block mb-2 text-primary">Académico</small>
          <nav class="nav flex-column" aria-label="Navegación de profesor">
            <?php
            $seccionesProfesor = [
              ['index.php', 'Resumen', 'fas fa-chalkboard-teacher'],
              ['perfil.php', 'Mi perfil', 'fas fa-user'],
              ['productividad.php', 'Productividad', 'fas fa-chart-bar'],
              ['proyectos.php', 'Mis proyectos', 'fas fa-project-diagram'],
              ['publicaciones.php', 'Publicaciones', 'fas fa-book-open'],
              ['congresos.php', 'Congresos', 'fas fa-globe-americas'],
              ['titulacion.php', 'Titulación', 'fas fa-graduation-cap'],
              ['convocatorias.php', 'Convocatorias', 'fas fa-bullhorn'],
              ['postulaciones.php', 'Postulaciones', 'fas fa-users']
            ];
            foreach ($seccionesProfesor as [$ruta, $etiqueta, $icono]):
              $seleccionado = ($paginaActiva ?? '') === $ruta;
            ?>
            <a href="<?= htmlspecialchars($ruta, ENT_QUOTES, 'UTF-8') ?>"
               class="nav-link py-2 mb-1 rounded <?= $seleccionado ? 'bg-primary text-white font-weight-bold' : 'text-dark' ?>"
               <?= $seleccionado ? 'aria-current="page"' : '' ?>>
              <i class="<?= htmlspecialchars($icono, ENT_QUOTES, 'UTF-8') ?> mr-2"></i><?= htmlspecialchars($etiqueta, ENT_QUOTES, 'UTF-8') ?>
            </a>
            <?php endforeach; ?>
          </nav>
        </div>
        <div class="mt-auto pt-4">
          <hr class="border-primary">
          <nav class="nav flex-column" aria-label="Opciones pendientes de implementar">
            <span class="nav-link text-muted"><i class="fas fa-cog mr-2"></i>Configuración (próximamente)</span>
            <span class="nav-link text-muted"><i class="fas fa-sign-out-alt mr-2"></i>Cerrar sesión (sin autenticación aún)</span>
          </nav>
        </div>
      </div>
    </div>
  </div>
</div>
