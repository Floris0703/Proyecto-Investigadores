<?php
// Datos demostrativos hasta integrar SQL Server por sesión.
$tituloPagina = 'Productividad';
$paginaActiva = 'productividad.php';
?>

<?php require __DIR__ . "/../includes/profesor_header.php"; ?>


    <!-- Contenido principal -->
    <main class="container-fluid mt-4 px-3 px-md-4">

        <!-- Encabezado -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">

            <div class="mb-3 mb-md-0">
                <h3 class="mb-1">
                    Productividad académica
                </h3>

                <p class="text-muted mb-0">
                    Resumen de actividades e indicadores
                    del investigador.
                </p>
            </div>

            <div class="d-flex align-items-center">

                <span class="badge badge-light border p-3 mr-2">
                    Periodo de ejemplo: 2026
                </span>

                <button type="button"
                    class="btn btn-outline-primary"
                    data-toggle="modal"
                    data-target="#modalCriterios">
                    <i class="fas fa-info-circle mr-2"></i>
                    Criterios
                </button>

            </div>
        </div>
        <!-- Indicadores generales -->
        <div class="row">

            <div class="col-6 col-lg-3 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">

                        <i class="fas fa-project-diagram text-primary mb-3"></i>

                        <h3 class="card-title mb-1">3</h3>

                        <p class="text-muted mb-0">
                            Proyectos
                        </p>

                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">

                        <i class="fas fa-book-open text-success mb-3"></i>

                        <h3 class="card-title mb-1">3</h3>

                        <p class="text-muted mb-0">
                            Publicaciones
                        </p>

                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">

                        <i class="fas fa-globe-americas text-primary mb-3"></i>

                        <h3 class="card-title mb-1">3</h3>

                        <p class="text-muted mb-0">
                            Congresos
                        </p>

                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">

                        <i class="fas fa-bullhorn text-success mb-3"></i>

                        <h3 class="card-title mb-1">3</h3>

                        <p class="text-muted mb-0">
                            Convocatorias publicadas
                        </p>

                    </div>
                </div>
            </div>

        </div>

        <!-- Gráficas de productividad -->
        <div class="card shadow-sm mb-4">

            <div class="card-body">

                <h5 class="card-title mb-2">
                    <i class="fas fa-chart-bar text-success mr-2"></i>
                    Distribución mensual
                </h5>

                <p class="text-muted mb-4">
                    Cantidad de registros de ejemplo por categoría
                    y mes. Se incluyen actividades programadas.
                </p>

                <!-- Pestañas -->
                <ul class="nav nav-tabs flex-nowrap overflow-auto mb-4"
                    role="tablist">

                    <li class="nav-item">
                        <a href="#prodProyectos"
                            class="nav-link active text-dark text-nowrap"
                            data-toggle="tab">
                            Proyectos
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#prodConvocatorias"
                            class="nav-link text-dark text-nowrap"
                            data-toggle="tab">
                            Convocatorias
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#prodNacionales"
                            class="nav-link text-dark text-nowrap"
                            data-toggle="tab">
                            Congresos nacionales
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#prodInternacionales"
                            class="nav-link text-dark text-nowrap"
                            data-toggle="tab">
                            Congresos internacionales
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#prodPublicaciones"
                            class="nav-link text-dark text-nowrap"
                            data-toggle="tab">
                            Publicaciones
                        </a>
                    </li>

                </ul>

                <!-- Contenido de pestañas -->
                <div class="tab-content">

                    <!-- Productividad de proyectos -->
                    <div class="tab-pane fade show active"
                        id="prodProyectos">

                        <h6 class="mb-2">
                            Proyectos registrados
                        </h6>

                        <p class="text-muted">
                            Distribución de tres proyectos de ejemplo.
                        </p>

                        <!-- Septiembre -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Septiembre</span>
                                <strong>1</strong>
                            </div>

                            <div class="progress">
                                <div class="progress-bar bg-success w-25"
                                    role="progressbar"
                                    aria-valuenow="1"
                                    aria-valuemin="0"
                                    aria-valuemax="4">
                                </div>
                            </div>
                        </div>

                        <!-- Octubre -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Octubre</span>
                                <strong>1</strong>
                            </div>

                            <div class="progress">
                                <div class="progress-bar bg-success w-25"
                                    role="progressbar"
                                    aria-valuenow="1"
                                    aria-valuemin="0"
                                    aria-valuemax="4">
                                </div>
                            </div>
                        </div>

                        <!-- Noviembre -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Noviembre</span>
                                <strong>1</strong>
                            </div>

                            <div class="progress">
                                <div class="progress-bar bg-success w-25"
                                    role="progressbar"
                                    aria-valuenow="1"
                                    aria-valuemin="0"
                                    aria-valuemax="4">
                                </div>
                            </div>
                        </div>

                        <!-- Diciembre -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Diciembre</span>
                                <strong>0</strong>
                            </div>

                            <div class="progress"></div>
                        </div>

                        <a href="proyectos.php"
                            class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-folder-open mr-2"></i>
                            Consultar proyectos
                        </a>

                    </div>

                    <!-- Productividad de convocatorias -->
                    <div class="tab-pane fade"
                        id="prodConvocatorias">

                        <h6 class="mb-2">
                            Convocatorias publicadas
                        </h6>

                        <p class="text-muted">
                            Dos convocatorias abiertas y una cerrada.
                            El borrador no se contabiliza.
                        </p>

                        <!-- Septiembre -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Septiembre</span>
                                <strong>0</strong>
                            </div>

                            <div class="progress"></div>
                        </div>

                        <!-- Octubre -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Octubre</span>
                                <strong>1</strong>
                            </div>

                            <div class="progress">
                                <div class="progress-bar bg-primary w-25"
                                    role="progressbar"
                                    aria-valuenow="1"
                                    aria-valuemin="0"
                                    aria-valuemax="4">
                                </div>
                            </div>
                        </div>

                        <!-- Noviembre -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Noviembre</span>
                                <strong>2</strong>
                            </div>

                            <div class="progress">
                                <div class="progress-bar bg-primary w-50"
                                    role="progressbar"
                                    aria-valuenow="2"
                                    aria-valuemin="0"
                                    aria-valuemax="4">
                                </div>
                            </div>
                        </div>

                        <!-- Diciembre -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Diciembre</span>
                                <strong>0</strong>
                            </div>

                            <div class="progress"></div>
                        </div>

                        <a href="convocatorias.php"
                            class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-bullhorn mr-2"></i>
                            Consultar convocatorias
                        </a>

                    </div>

                    <!-- Congresos nacionales -->
                    <div class="tab-pane fade"
                        id="prodNacionales">

                        <h6 class="mb-2">
                            Congresos nacionales
                        </h6>

                        <p class="text-muted">
                            Participaciones registradas, incluidas
                            las programadas para fechas futuras.
                        </p>

                        <!-- Septiembre -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Septiembre</span>
                                <strong>0</strong>
                            </div>

                            <div class="progress"></div>
                        </div>

                        <!-- Octubre -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Octubre</span>
                                <strong>1</strong>
                            </div>

                            <div class="progress">
                                <div class="progress-bar bg-success w-25"
                                    role="progressbar"
                                    aria-valuenow="1"
                                    aria-valuemin="0"
                                    aria-valuemax="4">
                                </div>
                            </div>
                        </div>

                        <!-- Noviembre -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Noviembre</span>
                                <strong>0</strong>
                            </div>

                            <div class="progress"></div>
                        </div>

                        <!-- Diciembre -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Diciembre</span>
                                <strong>1</strong>
                            </div>

                            <div class="progress">
                                <div class="progress-bar bg-success w-25"
                                    role="progressbar"
                                    aria-valuenow="1"
                                    aria-valuemin="0"
                                    aria-valuemax="4">
                                </div>
                            </div>
                        </div>

                        <a href="congresos.php"
                            class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-globe-americas mr-2"></i>
                            Consultar congresos
                        </a>

                    </div>

                    <!-- Congresos internacionales -->
                    <div class="tab-pane fade"
                        id="prodInternacionales">

                        <h6 class="mb-2">
                            Congresos internacionales
                        </h6>

                        <p class="text-muted">
                            Participaciones internacionales
                            registradas en el periodo de ejemplo.
                        </p>

                        <!-- Septiembre -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Septiembre</span>
                                <strong>0</strong>
                            </div>

                            <div class="progress"></div>
                        </div>

                        <!-- Octubre -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Octubre</span>
                                <strong>0</strong>
                            </div>

                            <div class="progress"></div>
                        </div>

                        <!-- Noviembre -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Noviembre</span>
                                <strong>1</strong>
                            </div>

                            <div class="progress">
                                <div class="progress-bar bg-primary w-25"
                                    role="progressbar"
                                    aria-valuenow="1"
                                    aria-valuemin="0"
                                    aria-valuemax="4">
                                </div>
                            </div>
                        </div>

                        <!-- Diciembre -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Diciembre</span>
                                <strong>0</strong>
                            </div>

                            <div class="progress"></div>
                        </div>

                        <a href="congresos.php"
                            class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-globe-americas mr-2"></i>
                            Consultar congresos
                        </a>

                    </div>

                    <!-- Publicaciones -->
                    <div class="tab-pane fade"
                        id="prodPublicaciones">

                        <h6 class="mb-2">
                            Producción bibliográfica
                        </h6>

                        <p class="text-muted">
                            Un libro, un capítulo de libro y
                            un artículo de investigación de ejemplo.
                        </p>

                        <!-- Septiembre -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Septiembre</span>
                                <strong>1</strong>
                            </div>

                            <div class="progress">
                                <div class="progress-bar bg-success w-25"
                                    role="progressbar"
                                    aria-valuenow="1"
                                    aria-valuemin="0"
                                    aria-valuemax="4">
                                </div>
                            </div>
                        </div>

                        <!-- Octubre -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Octubre</span>
                                <strong>1</strong>
                            </div>

                            <div class="progress">
                                <div class="progress-bar bg-success w-25"
                                    role="progressbar"
                                    aria-valuenow="1"
                                    aria-valuemin="0"
                                    aria-valuemax="4">
                                </div>
                            </div>
                        </div>

                        <!-- Noviembre -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Noviembre</span>
                                <strong>1</strong>
                            </div>

                            <div class="progress">
                                <div class="progress-bar bg-success w-25"
                                    role="progressbar"
                                    aria-valuenow="1"
                                    aria-valuemin="0"
                                    aria-valuemax="4">
                                </div>
                            </div>
                        </div>

                        <!-- Diciembre -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Diciembre</span>
                                <strong>0</strong>
                            </div>

                            <div class="progress"></div>
                        </div>

                        <a href="publicaciones.php"
                            class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-book-open mr-2"></i>
                            Consultar publicaciones
                        </a>

                    </div>

                </div>

                <hr class="my-4">

                <small class="text-muted">
                    Escala visual uniforme de 0 a 4 registros.
                    Cada unidad equivale al 25% de la barra.
                    Los meses y cantidades son ficticios y
                    no representan una evaluación de desempeño.
                </small>

            </div>
        </div>

        <!-- Desglose académico -->
        <div class="row">

            <!-- Tabla de resumen -->
            <section class="col-12 col-lg-8 mb-4">

                <div class="card shadow-sm h-100">

                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="fas fa-table text-success mr-2"></i>
                            Desglose de actividades
                        </h5>
                    </div>

                    <div class="card-body">

                        <p class="text-muted">
                            Resumen de los registros académicos
                            representados en los demás módulos.
                        </p>

                        <div class="table-responsive">

                            <table class="table table-striped table-hover">

                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">Actividad</th>
                                        <th scope="col">Categoría</th>
                                        <th scope="col">Cantidad</th>
                                        <th scope="col">Consultar</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <tr>
                                        <td>Investigaciones</td>
                                        <td>Proyectos</td>
                                        <td>3</td>
                                        <td>
                                            <a href="proyectos.php"
                                                class="btn btn-outline-primary btn-sm">
                                                Ver
                                            </a>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Producción bibliográfica</td>
                                        <td>Libros</td>
                                        <td>1</td>
                                        <td>
                                            <a href="publicaciones.php"
                                                class="btn btn-outline-primary btn-sm">
                                                Ver
                                            </a>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Producción bibliográfica</td>
                                        <td>Capítulos de libros</td>
                                        <td>1</td>
                                        <td>
                                            <a href="publicaciones.php"
                                                class="btn btn-outline-primary btn-sm">
                                                Ver
                                            </a>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Producción bibliográfica</td>
                                        <td>Artículos</td>
                                        <td>1</td>
                                        <td>
                                            <a href="publicaciones.php"
                                                class="btn btn-outline-primary btn-sm">
                                                Ver
                                            </a>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Congresos</td>
                                        <td>Nacionales</td>
                                        <td>2</td>
                                        <td>
                                            <a href="congresos.php"
                                                class="btn btn-outline-primary btn-sm">
                                                Ver
                                            </a>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Congresos</td>
                                        <td>Internacionales</td>
                                        <td>1</td>
                                        <td>
                                            <a href="congresos.php"
                                                class="btn btn-outline-primary btn-sm">
                                                Ver
                                            </a>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Convocatorias</td>
                                        <td>Publicadas</td>
                                        <td>3</td>
                                        <td>
                                            <a href="convocatorias.php"
                                                class="btn btn-outline-primary btn-sm">
                                                Ver
                                            </a>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Titulación</td>
                                        <td>Trabajos registrados</td>
                                        <td>3</td>
                                        <td>
                                            <a href="titulacion.php"
                                                class="btn btn-outline-primary btn-sm">
                                                Ver
                                            </a>
                                        </td>
                                    </tr>

                                </tbody>

                            </table>

                        </div>

                        <small class="text-muted">
                            Las categorías pueden contener registros
                            relacionados entre sí. No se asigna
                            una puntuación total.
                        </small>

                    </div>
                </div>
            </section>

            <!-- Resumen de titulación -->
            <aside class="col-12 col-lg-4 mb-4">

                <div class="card shadow-sm h-100">

                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="fas fa-graduation-cap text-success mr-2"></i>
                            Dirección de trabajos
                        </h5>
                    </div>

                    <div class="card-body">

                        <p class="text-muted">
                            Seguimiento de los proyectos de titulación
                            dirigidos por el investigador.
                        </p>

                        <div class="d-flex justify-content-between mb-3">
                            <span>Trabajos registrados</span>
                            <strong>3</strong>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span>En proceso</span>
                            <span class="badge badge-warning p-2">2</span>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span>Terminados</span>
                            <span class="badge badge-success p-2">1</span>
                        </div>

                        <hr>

                        <p class="text-muted">
                            Este resumen se presenta por separado
                            porque el conteo mensual de trabajos
                            dependerá de la fecha y del criterio
                            académico que se establezca.
                        </p>

                        <a href="titulacion.php"
                            class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-graduation-cap mr-2"></i>
                            Consultar titulación
                        </a>

                    </div>
                </div>

            </aside>

        </div>

        <!-- Muestra de actividad -->
        <div class="card shadow-sm mb-4">

            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-history text-success mr-2"></i>
                    Muestra de actividad académica
                </h5>
            </div>

            <div class="card-body">

                <p class="text-muted">
                    Algunos registros ficticios utilizados
                    para representar la actividad del profesor.
                </p>

                <div class="list-group list-group-flush">

                    <div class="list-group-item px-0">

                        <div class="d-flex flex-column flex-md-row justify-content-between">

                            <div>
                                <h6 class="mb-1">
                                    Análisis de datos académicos
                                </h6>

                                <p class="text-muted mb-1">
                                    Convocatoria de investigación
                                </p>

                                <small class="text-muted">
                                    Publicada: 05/11/2026
                                </small>
                            </div>

                            <div class="mt-2 mt-md-0">
                                <span class="badge badge-success">
                                    Abierta
                                </span>
                            </div>

                        </div>

                    </div>

                    <div class="list-group-item px-0">

                        <div class="d-flex flex-column flex-md-row justify-content-between">

                            <div>
                                <h6 class="mb-1">
                                    Encuentro Nacional de Investigación Tecnológica
                                </h6>

                                <p class="text-muted mb-1">
                                    Congreso nacional
                                </p>

                                <small class="text-muted">
                                    18/10/2026
                                </small>
                            </div>

                            <div class="mt-2 mt-md-0">
                                <span class="badge badge-primary">
                                    Finalizado
                                </span>
                            </div>

                        </div>

                    </div>

                    <div class="list-group-item px-0">

                        <div class="d-flex flex-column flex-md-row justify-content-between">

                            <div>
                                <h6 class="mb-1">
                                    Plataforma de gestión de laboratorios
                                </h6>

                                <p class="text-muted mb-1">
                                    Trabajo de titulación
                                </p>

                                <small class="text-muted">
                                    Finalizado: 20/10/2026
                                </small>
                            </div>

                            <div class="mt-2 mt-md-0">
                                <span class="badge badge-success">
                                    Terminado
                                </span>
                            </div>

                        </div>

                    </div>

                    <div class="list-group-item px-0">

                        <div class="d-flex flex-column flex-md-row justify-content-between">

                            <div>
                                <h6 class="mb-1">
                                    Simposio Internacional de Sistemas Inteligentes
                                </h6>

                                <p class="text-muted mb-1">
                                    Congreso internacional
                                </p>

                                <small class="text-muted">
                                    Programado: 21/11/2026
                                </small>
                            </div>

                            <div class="mt-2 mt-md-0">
                                <span class="badge badge-warning">
                                    Próximo
                                </span>
                            </div>

                        </div>

                    </div>

                </div>

            </div>
        </div>

    </main>

    <!-- Modal de criterios -->
    <div class="modal fade" id="modalCriterios"
        tabindex="-1" role="dialog"
        aria-labelledby="tituloCriterios"
        aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered"
            role="document">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title" id="tituloCriterios">
                        Criterios de productividad académica
                    </h5>

                    <button type="button" class="close"
                        data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>

                <div class="modal-body">


                    <h6>Aspectos pendientes</h6>

                    <ul>
                        <li>
                            Determinar qué actividades cuentan
                            como productividad académica.
                        </li>

                        <li>
                            Definir si se contabilizan registros
                            creados, publicados o concluidos.
                        </li>

                        <li>
                            Establecer los periodos de evaluación.
                        </li>

                        <li>
                            Definir los criterios de calidad
                            para cada tipo de actividad.
                        </li>

                        <li>
                            Validar si existirá un sistema
                            de puntuación o ponderación.
                        </li>
                    </ul>

                    <hr>

                    <h6>Implementación posterior</h6>

                    <p class="mb-0">
                        Las estadísticas serán calculadas mediante
                        procedimientos almacenados y presentadas
                        en este panel cuando se integre el backend.
                    </p>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">
                        Cerrar
                    </button>

                </div>

            </div>
        </div>
    </div>
<?php require __DIR__ . "/../includes/profesor_footer.php"; ?>
