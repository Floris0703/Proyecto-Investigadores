<?php
// Datos demostrativos hasta integrar SQL Server por sesión.
$tituloPagina = 'Titulación';
$paginaActiva = 'titulacion.php';
?>

<?php require __DIR__ . "/../includes/profesor_header.php"; ?>


    <!-- Contenido principal -->
    <main class="container-fluid mt-4 px-3 px-md-4">

        <!-- Encabezado -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">

            <div class="mb-3 mb-md-0">
                <h3 class="mb-1">
                    Proyectos de titulación
                </h3>

                <p class="text-muted mb-0">
                    Consulta y administra los trabajos de titulación
                    que diriges.
                </p>
            </div>

            <button type="button" class="btn btn-success"
                data-toggle="modal"
                data-target="#modalNuevaTitulacion">
                <i class="fas fa-plus mr-2"></i>
                Nuevo trabajo
            </button>

        </div>

        <!-- Indicadores -->
        <div class="row">

            <div class="col-12 col-sm-4 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h3 class="card-title mb-1">3</h3>
                        <p class="text-muted mb-0">
                            Trabajos registrados
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-4 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h3 class="card-title text-warning mb-1">2</h3>
                        <p class="text-muted mb-0">
                            En proceso
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-4 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h3 class="card-title text-success mb-1">1</h3>
                        <p class="text-muted mb-0">
                            Terminados
                        </p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Listado de trabajos -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">

                <h5 class="card-title mb-3">
                    <i class="fas fa-graduation-cap text-success mr-2"></i>
                    Trabajos de titulación registrados
                </h5>

                <!-- Pestañas -->
                <ul class="nav nav-tabs flex-nowrap overflow-auto mb-4"
                    role="tablist">

                    <li class="nav-item">
                        <a href="#todos"
                            class="nav-link active text-dark text-nowrap"
                            data-toggle="tab">
                            Todos
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#enProceso"
                            class="nav-link text-dark text-nowrap"
                            data-toggle="tab">
                            En proceso
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#terminados"
                            class="nav-link text-dark text-nowrap"
                            data-toggle="tab">
                            Terminados
                        </a>
                    </li>

                </ul>

                <!-- Contenido de pestañas -->
                <div class="tab-content">

                    <!-- Todos -->
                    <div class="tab-pane fade show active" id="todos">

                        <div class="row">

                            <!-- Trabajo 1 -->
                            <div class="col-12 col-md-6 col-xl-4 mb-3">
                                <div class="card h-100 border shadow-sm">

                                    <div class="card-body">

                                        <span class="badge badge-warning mb-2">
                                            En proceso
                                        </span>

                                        <span class="badge badge-info mb-2">
                                            Tesis
                                        </span>

                                        <h5 class="card-title">
                                            Sistema de seguimiento de investigaciones
                                        </h5>

                                        <p class="text-muted">
                                            Ingeniería en Sistemas Computacionales
                                        </p>

                                        <p class="card-text">
                                            Desarrollo de una plataforma web para
                                            consultar y dar seguimiento a
                                            proyectos académicos.
                                        </p>

                                        <p class="mb-1">
                                            <i class="fas fa-user-graduate mr-2"></i>
                                            Alumno 01
                                        </p>

                                        <p class="mb-1">
                                            <i class="fas fa-calendar-alt mr-2"></i>
                                            Entrega: 15/12/2026
                                        </p>

                                        <p class="text-muted mb-0">
                                            <small>
                                                Última revisión: 04/11/2026
                                            </small>
                                        </p>

                                    </div>

                                    <div class="card-footer bg-white">
                                        <button type="button"
                                            class="btn btn-outline-primary btn-sm"
                                            data-toggle="modal"
                                            data-target="#detalleTitulacion1">
                                            <i class="fas fa-eye mr-2"></i>
                                            Ver detalles
                                        </button>
                                    </div>

                                </div>
                            </div>

                            <!-- Trabajo 2 -->
                            <div class="col-12 col-md-6 col-xl-4 mb-3">
                                <div class="card h-100 border shadow-sm">

                                    <div class="card-body">

                                        <span class="badge badge-warning mb-2">
                                            En proceso
                                        </span>

                                        <span class="badge badge-info mb-2">
                                            Tesina
                                        </span>

                                        <h5 class="card-title">
                                            Análisis de datos académicos
                                        </h5>

                                        <p class="text-muted">
                                            Ingeniería en Sistemas Computacionales
                                        </p>

                                        <p class="card-text">
                                            Estudio sobre el uso de indicadores
                                            para analizar información y resultados
                                            académicos.
                                        </p>

                                        <p class="mb-1">
                                            <i class="fas fa-user-graduate mr-2"></i>
                                            Alumno 02
                                        </p>

                                        <p class="mb-1">
                                            <i class="fas fa-calendar-alt mr-2"></i>
                                            Entrega: 10/01/2027
                                        </p>

                                        <p class="text-muted mb-0">
                                            <small>
                                                Última revisión: 06/11/2026
                                            </small>
                                        </p>

                                    </div>

                                    <div class="card-footer bg-white">
                                        <button type="button"
                                            class="btn btn-outline-primary btn-sm"
                                            data-toggle="modal"
                                            data-target="#detalleTitulacion2">
                                            <i class="fas fa-eye mr-2"></i>
                                            Ver detalles
                                        </button>
                                    </div>

                                </div>
                            </div>

                            <!-- Trabajo 3 -->
                            <div class="col-12 col-md-6 col-xl-4 mb-3">
                                <div class="card h-100 border shadow-sm">

                                    <div class="card-body">

                                        <span class="badge badge-success mb-2">
                                            Terminado
                                        </span>

                                        <span class="badge badge-info mb-2">
                                            Tesis
                                        </span>

                                        <h5 class="card-title">
                                            Plataforma de gestión de laboratorios
                                        </h5>

                                        <p class="text-muted">
                                            Ingeniería en Sistemas Computacionales
                                        </p>

                                        <p class="card-text">
                                            Desarrollo de un sistema web para la
                                            administración de recursos y espacios
                                            de laboratorio.
                                        </p>

                                        <p class="mb-1">
                                            <i class="fas fa-user-graduate mr-2"></i>
                                            Alumno 03
                                        </p>

                                        <p class="mb-1">
                                            <i class="fas fa-calendar-check mr-2"></i>
                                            Finalizado: 20/10/2026
                                        </p>

                                        <p class="text-muted mb-0">
                                            <small>
                                                Última revisión: 18/10/2026
                                            </small>
                                        </p>

                                    </div>

                                    <div class="card-footer bg-white">
                                        <button type="button"
                                            class="btn btn-outline-primary btn-sm"
                                            data-toggle="modal"
                                            data-target="#detalleTitulacion3">
                                            <i class="fas fa-eye mr-2"></i>
                                            Ver detalles
                                        </button>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- En proceso -->
                    <div class="tab-pane fade" id="enProceso">

                        <div class="row">

                            <div class="col-12 col-md-6 mb-3">
                                <div class="card h-100 border">
                                    <div class="card-body">

                                        <span class="badge badge-warning mb-2">
                                            En proceso
                                        </span>

                                        <h5 class="card-title">
                                            Sistema de seguimiento de investigaciones
                                        </h5>

                                        <p class="text-muted">
                                            Tesis - Alumno 01
                                        </p>

                                        <p class="card-text">
                                            Desarrollo de una plataforma web
                                            para proyectos académicos.
                                        </p>

                                        <button type="button"
                                            class="btn btn-outline-primary btn-sm"
                                            data-toggle="modal"
                                            data-target="#detalleTitulacion1">
                                            Ver detalles
                                        </button>

                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 mb-3">
                                <div class="card h-100 border">
                                    <div class="card-body">

                                        <span class="badge badge-warning mb-2">
                                            En proceso
                                        </span>

                                        <h5 class="card-title">
                                            Análisis de datos académicos
                                        </h5>

                                        <p class="text-muted">
                                            Tesina - Alumno 02
                                        </p>

                                        <p class="card-text">
                                            Análisis de indicadores y resultados
                                            académicos.
                                        </p>

                                        <button type="button"
                                            class="btn btn-outline-primary btn-sm"
                                            data-toggle="modal"
                                            data-target="#detalleTitulacion2">
                                            Ver detalles
                                        </button>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Terminados -->
                    <div class="tab-pane fade" id="terminados">

                        <div class="row">

                            <div class="col-12 col-md-6 mb-3">
                                <div class="card h-100 border">
                                    <div class="card-body">

                                        <span class="badge badge-success mb-2">
                                            Terminado
                                        </span>

                                        <h5 class="card-title">
                                            Plataforma de gestión de laboratorios
                                        </h5>

                                        <p class="text-muted">
                                            Tesis - Alumno 03
                                        </p>

                                        <p class="card-text">
                                            Sistema web para administrar
                                            recursos de laboratorio.
                                        </p>

                                        <button type="button"
                                            class="btn btn-outline-primary btn-sm"
                                            data-toggle="modal"
                                            data-target="#detalleTitulacion3">
                                            Ver detalles
                                        </button>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </main>

    <!-- Botón flotante -->
    <div class="fixed-bottom d-flex justify-content-end p-4">
        <button type="button"
            class="btn btn-success rounded-circle shadow p-3"
            data-toggle="modal"
            data-target="#modalNuevaTitulacion"
            aria-label="Agregar trabajo de titulación"
            title="Agregar trabajo de titulación">

            <i class="fas fa-plus"></i>

        </button>
    </div>

    <!-- Modal detalle trabajo 1 -->
    <div class="modal fade" id="detalleTitulacion1"
        tabindex="-1" role="dialog"
        aria-labelledby="tituloTitulacion1"
        aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="tituloTitulacion1">
                        Sistema de seguimiento de investigaciones
                    </h5>

                    <button type="button" class="close"
                        data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <span class="badge badge-warning mb-3">
                        En proceso
                    </span>

                    <h6>Descripción</h6>
                    <p>
                        Desarrollo de una plataforma web destinada
                        a facilitar la consulta y seguimiento de
                        proyectos de investigación académica.
                    </p>

                    <hr>

                    <p><strong>Modalidad:</strong> Tesis</p>
                    <p><strong>Estudiante:</strong> Alumno 01</p>
                    <p><strong>Carrera:</strong> Ingeniería en Sistemas Computacionales</p>
                    <p><strong>Director:</strong> José Luis Camacho Campero</p>
                    <p><strong>Estado:</strong> En proceso</p>
                    <p><strong>Entrega estimada:</strong> 15/12/2026</p>
                    <p><strong>Última revisión:</strong> 04/11/2026</p>

                    <hr>

                    <h6>Observaciones</h6>
                    <p class="mb-0">
                        Pendiente de registrar observaciones
                        del seguimiento académico.
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

    <!-- Modal detalle trabajo 2 -->
    <div class="modal fade" id="detalleTitulacion2"
        tabindex="-1" role="dialog"
        aria-labelledby="tituloTitulacion2"
        aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="tituloTitulacion2">
                        Análisis de datos académicos
                    </h5>

                    <button type="button" class="close"
                        data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <span class="badge badge-warning mb-3">
                        En proceso
                    </span>

                    <h6>Descripción</h6>
                    <p>
                        Estudio de indicadores académicos mediante
                        técnicas de análisis de datos para facilitar
                        la interpretación de resultados.
                    </p>

                    <hr>

                    <p><strong>Modalidad:</strong> Tesina</p>
                    <p><strong>Estudiante:</strong> Alumno 02</p>
                    <p><strong>Carrera:</strong> Ingeniería en Sistemas Computacionales</p>
                    <p><strong>Director:</strong> José Luis Camacho Campero</p>
                    <p><strong>Estado:</strong> En proceso</p>
                    <p><strong>Entrega estimada:</strong> 10/01/2027</p>
                    <p><strong>Última revisión:</strong> 06/11/2026</p>

                    <hr>

                    <h6>Observaciones</h6>
                    <p class="mb-0">
                        Pendiente de registrar observaciones
                        del seguimiento académico.
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

    <!-- Modal detalle trabajo 3 -->
    <div class="modal fade" id="detalleTitulacion3"
        tabindex="-1" role="dialog"
        aria-labelledby="tituloTitulacion3"
        aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="tituloTitulacion3">
                        Plataforma de gestión de laboratorios
                    </h5>

                    <button type="button" class="close"
                        data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <span class="badge badge-success mb-3">
                        Terminado
                    </span>

                    <h6>Descripción</h6>
                    <p>
                        Desarrollo de un sistema web para administrar
                        recursos, equipos y espacios destinados
                        a prácticas de laboratorio.
                    </p>

                    <hr>

                    <p><strong>Modalidad:</strong> Tesis</p>
                    <p><strong>Estudiante:</strong> Alumno 03</p>
                    <p><strong>Carrera:</strong> Ingeniería en Sistemas Computacionales</p>
                    <p><strong>Director:</strong> José Luis Camacho Campero</p>
                    <p><strong>Estado:</strong> Terminado</p>
                    <p><strong>Fecha de finalización:</strong> 20/10/2026</p>
                    <p><strong>Última revisión:</strong> 18/10/2026</p>

                    <hr>

                    <h6>Documento final</h6>
                    <p class="text-muted mb-0">
                        Archivo pendiente de adjuntar.
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

    <!-- Modal nuevo trabajo de titulación -->
    <div class="modal fade" id="modalNuevaTitulacion"
        tabindex="-1" role="dialog"
        aria-labelledby="tituloNuevaTitulacion"
        aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header bg-success text-dark">
                    <h5 class="modal-title" id="tituloNuevaTitulacion">
                        Registrar trabajo de titulación
                    </h5>

                    <button type="button" class="close text-white"
                        data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <form>

                        <div class="form-group">
                            <label for="nombreTitulacion">
                                Título del trabajo
                            </label>

                            <input type="text" class="form-control"
                                id="nombreTitulacion"
                                placeholder="Título del proyecto de titulación">
                        </div>

                        <div class="form-row">

                            <div class="form-group col-12 col-md-6">
                                <label for="modalidadTitulacion">
                                    Modalidad
                                </label>

                                <select class="form-control" id="modalidadTitulacion">
                                    <option value="" selected>Seleccionar...</option>
                                    <option>Tesis</option>
                                    <option>Tesina</option>
                                </select>
                            </div>

                            <div class="form-group col-12 col-md-6">
                                <label for="estadoTitulacion">
                                    Estado
                                </label>

                                <select class="form-control" id="estadoTitulacion">
                                    <option value="" selected>Seleccionar...</option>
                                    <option>En proceso</option>
                                    <option>Terminado</option>
                                </select>
                            </div>

                        </div>

                        <div class="form-group">
                            <label for="alumnoTitulacion">
                                Alumno responsable
                            </label>

                            <input type="text" class="form-control"
                                id="alumnoTitulacion"
                                placeholder="Nombre del estudiante">
                        </div>

                        <div class="form-group">
                            <label for="carreraTitulacion">
                                Carrera
                            </label>

                            <select class="form-control" id="carreraTitulacion">
                                <option value="" selected>Seleccionar...</option>
                                <option>Ingeniería en Sistemas Computacionales</option>
                                <option>Otra carrera</option>
                            </select>
                        </div>

                        <div class="form-row">

                            <div class="form-group col-12 col-md-6">
                                <label for="inicioTitulacion">
                                    Fecha de inicio
                                </label>

                                <input type="date" class="form-control"
                                    id="inicioTitulacion">
                            </div>

                            <div class="form-group col-12 col-md-6">
                                <label for="entregaTitulacion">
                                    Fecha de entrega
                                </label>

                                <input type="date" class="form-control"
                                    id="entregaTitulacion">
                            </div>

                        </div>

                        <div class="form-group">
                            <label for="descripcionTitulacion">
                                Descripción del trabajo
                            </label>

                            <textarea class="form-control"
                                id="descripcionTitulacion"
                                rows="4"
                                placeholder="Objetivo y descripción general"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="observacionesTitulacion">
                                Observaciones
                            </label>

                            <textarea class="form-control"
                                id="observacionesTitulacion"
                                rows="3"
                                placeholder="Observaciones de seguimiento"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="archivoTitulacion">
                                Documento académico
                            </label>

                            <input type="file" class="form-control-file"
                                id="archivoTitulacion"
                                accept=".pdf,.doc,.docx">

                            <small class="form-text text-muted">
                                Selección visual. No se cargarán archivos.
                            </small>
                        </div>

                    </form>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="button" class="btn btn-primary" disabled>
                        <i class="fas fa-save mr-2"></i>
                        Registrar trabajo
                    </button>

                </div>

            </div>
        </div>
    </div>
<?php require __DIR__ . "/../includes/profesor_footer.php"; ?>
