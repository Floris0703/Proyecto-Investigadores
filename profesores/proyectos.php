<?php
// Datos demostrativos hasta integrar SQL Server por sesión.
$tituloPagina = 'Mis proyectos';
$paginaActiva = 'proyectos.php';
?>

<?php require __DIR__ . "/../includes/profesor_header.php"; ?>


    <!-- Contenido principal -->
    <main class="container-fluid mt-4 px-3 px-md-4">

        <!-- Encabezado -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">

            <div class="mb-3 mb-md-0">
                <h3 class="mb-1">
                    Mis proyectos de investigación
                </h3>

                <p class="text-muted mb-0">
                    Consulta y administra tus proyectos académicos.
                </p>
            </div>

            <button type="button" class="btn btn-success"
                data-toggle="modal"
                data-target="#modalNuevoProyecto">
                <i class="fas fa-plus mr-2"></i>
                Nuevo proyecto
            </button>

        </div>

        <!-- Indicadores -->
        <div class="row">

            <div class="col-12 col-sm-4 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h3 class="card-title mb-1">3</h3>
                        <p class="text-muted mb-0">
                            Proyectos registrados
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-4 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h3 class="card-title text-success mb-1">2</h3>
                        <p class="text-muted mb-0">
                            En proceso
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-4 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h3 class="card-title text-primary mb-1">1</h3>
                        <p class="text-muted mb-0">
                            Terminados
                        </p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Listado de proyectos -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">

                <h5 class="card-title mb-3">
                    <i class="fas fa-folder-open text-success mr-2"></i>
                    Proyectos registrados
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
                        <a href="#personales"
                            class="nav-link text-dark text-nowrap"
                            data-toggle="tab">
                            Personales
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#conAlumnos"
                            class="nav-link text-dark text-nowrap"
                            data-toggle="tab">
                            Con alumnos
                        </a>
                    </li>

                </ul>

                <!-- Contenido de pestañas -->
                <div class="tab-content">

                    <!-- Todos -->
                    <div class="tab-pane fade show active" id="todos">

                        <div class="row">

                            <!-- Proyecto 1 -->
                            <div class="col-12 col-md-6 col-xl-4 mb-3">
                                <div class="card h-100 border shadow-sm">

                                    <div class="card-body">

                                        <span class="badge badge-success mb-2">
                                            En proceso
                                        </span>

                                        <h5 class="card-title">
                                            Sistema de monitoreo inteligente
                                        </h5>

                                        <p class="text-muted">
                                            Proyecto con alumnos
                                        </p>

                                        <p class="card-text">
                                            Desarrollo de un sistema para el
                                            monitoreo de información mediante
                                            herramientas tecnológicas.
                                        </p>

                                        <p class="mb-1">
                                            <i class="fas fa-users mr-2"></i>
                                            3 estudiantes
                                        </p>

                                        <p class="text-muted mb-0">
                                            <small>Entrega: 30/11/2026</small>
                                        </p>

                                    </div>

                                    <div class="card-footer bg-white">
                                        <button type="button"
                                            class="btn btn-outline-primary btn-sm"
                                            data-toggle="modal"
                                            data-target="#detalleProyecto1">
                                            <i class="fas fa-eye mr-2"></i>
                                            Ver detalles
                                        </button>
                                    </div>

                                </div>
                            </div>

                            <!-- Proyecto 2 -->
                            <div class="col-12 col-md-6 col-xl-4 mb-3">
                                <div class="card h-100 border shadow-sm">

                                    <div class="card-body">

                                        <span class="badge badge-info mb-2">
                                            Personal
                                        </span>

                                        <h5 class="card-title">
                                            Análisis de datos académicos
                                        </h5>

                                        <p class="text-muted">
                                            Proyecto personal
                                        </p>

                                        <p class="card-text">
                                            Investigación sobre el análisis
                                            de indicadores académicos y
                                            herramientas de visualización.
                                        </p>

                                        <p class="mb-1">
                                            <i class="fas fa-user mr-2"></i>
                                            Investigador responsable
                                        </p>

                                        <p class="text-muted mb-0">
                                            <small>Entrega: 15/12/2026</small>
                                        </p>

                                    </div>

                                    <div class="card-footer bg-white">
                                        <button type="button"
                                            class="btn btn-outline-primary btn-sm"
                                            data-toggle="modal"
                                            data-target="#detalleProyecto2">
                                            <i class="fas fa-eye mr-2"></i>
                                            Ver detalles
                                        </button>
                                    </div>

                                </div>
                            </div>

                            <!-- Proyecto 3 -->
                            <div class="col-12 col-md-6 col-xl-4 mb-3">
                                <div class="card h-100 border shadow-sm">

                                    <div class="card-body">

                                        <span class="badge badge-primary mb-2">
                                            Terminado
                                        </span>

                                        <h5 class="card-title">
                                            Sistema de inventarios
                                        </h5>

                                        <p class="text-muted">
                                            Proyecto con alumnos
                                        </p>

                                        <p class="card-text">
                                            Desarrollo de una solución para
                                            registrar y consultar recursos
                                            mediante una plataforma web.
                                        </p>

                                        <p class="mb-1">
                                            <i class="fas fa-users mr-2"></i>
                                            2 estudiantes
                                        </p>

                                        <p class="text-muted mb-0">
                                            <small>Finalizado: 20/10/2026</small>
                                        </p>

                                    </div>

                                    <div class="card-footer bg-white">
                                        <button type="button"
                                            class="btn btn-outline-primary btn-sm"
                                            data-toggle="modal"
                                            data-target="#detalleProyecto3">
                                            <i class="fas fa-eye mr-2"></i>
                                            Ver detalles
                                        </button>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Personales -->
                    <div class="tab-pane fade" id="personales">

                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">

                                <div class="card h-100 border">
                                    <div class="card-body">

                                        <span class="badge badge-info mb-2">
                                            Personal
                                        </span>

                                        <h5 class="card-title">
                                            Análisis de datos académicos
                                        </h5>

                                        <p class="card-text">
                                            Investigación sobre indicadores
                                            y visualización de información.
                                        </p>

                                        <p class="text-muted">
                                            Entrega: 15/12/2026
                                        </p>

                                        <button type="button"
                                            class="btn btn-outline-primary btn-sm"
                                            data-toggle="modal"
                                            data-target="#detalleProyecto2">
                                            Ver detalles
                                        </button>

                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                    <!-- Con alumnos -->
                    <div class="tab-pane fade" id="conAlumnos">

                        <div class="row">

                            <div class="col-12 col-md-6 mb-3">
                                <div class="card h-100 border">
                                    <div class="card-body">

                                        <span class="badge badge-success mb-2">
                                            En proceso
                                        </span>

                                        <h5 class="card-title">
                                            Sistema de monitoreo inteligente
                                        </h5>

                                        <p class="text-muted">
                                            3 estudiantes participantes
                                        </p>

                                        <p class="card-text">
                                            Desarrollo de herramientas de monitoreo.
                                        </p>

                                        <button type="button"
                                            class="btn btn-outline-primary btn-sm"
                                            data-toggle="modal"
                                            data-target="#detalleProyecto1">
                                            Ver detalles
                                        </button>

                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 mb-3">
                                <div class="card h-100 border">
                                    <div class="card-body">

                                        <span class="badge badge-primary mb-2">
                                            Terminado
                                        </span>

                                        <h5 class="card-title">
                                            Sistema de inventarios
                                        </h5>

                                        <p class="text-muted">
                                            2 estudiantes participantes
                                        </p>

                                        <p class="card-text">
                                            Plataforma de registro de recursos.
                                        </p>

                                        <button type="button"
                                            class="btn btn-outline-primary btn-sm"
                                            data-toggle="modal"
                                            data-target="#detalleProyecto3">
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
            data-target="#modalNuevoProyecto"
            aria-label="Agregar proyecto"
            title="Agregar proyecto">

            <i class="fas fa-plus"></i>

        </button>
    </div>

    <!-- Modal detalle proyecto 1 -->
    <div class="modal fade" id="detalleProyecto1"
        tabindex="-1" role="dialog"
        aria-labelledby="tituloProyecto1">

        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="tituloProyecto1">
                        Sistema de monitoreo inteligente
                    </h5>

                    <button type="button" class="close"
                        data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <span class="badge badge-success mb-3">
                        En proceso
                    </span>

                    <h6>Descripción</h6>
                    <p>
                        Investigación orientada al desarrollo de un
                        sistema para monitorear información mediante
                        herramientas tecnológicas.
                    </p>

                    <hr>

                    <p><strong>Tipo:</strong> Con alumnos</p>
                    <p><strong>Responsable:</strong> José Luis Camacho Campero</p>
                    <p><strong>Participantes:</strong> 3 estudiantes</p>
                    <p><strong>Entrega:</strong> 30/11/2026</p>
                    <p><strong>Última revisión:</strong> 03/11/2026</p>

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

    <!-- Modal detalle proyecto 2 -->
    <div class="modal fade" id="detalleProyecto2"
        tabindex="-1" role="dialog"
        aria-labelledby="tituloProyecto2">

        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="tituloProyecto2">
                        Análisis de datos académicos
                    </h5>

                    <button type="button" class="close"
                        data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <span class="badge badge-info mb-3">
                        Personal
                    </span>

                    <h6>Descripción</h6>
                    <p>
                        Investigación sobre el análisis de indicadores
                        académicos y la representación visual de
                        información institucional.
                    </p>

                    <hr>

                    <p><strong>Tipo:</strong> Personal</p>
                    <p><strong>Responsable:</strong> José Luis Camacho Campero</p>
                    <p><strong>Estado:</strong> En proceso</p>
                    <p><strong>Entrega:</strong> 15/12/2026</p>
                    <p><strong>Última revisión:</strong> 04/11/2026</p>

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

    <!-- Modal detalle proyecto 3 -->
    <div class="modal fade" id="detalleProyecto3"
        tabindex="-1" role="dialog"
        aria-labelledby="tituloProyecto3">

        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="tituloProyecto3">
                        Sistema de inventarios
                    </h5>

                    <button type="button" class="close"
                        data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <span class="badge badge-primary mb-3">
                        Terminado
                    </span>

                    <h6>Descripción</h6>
                    <p>
                        Desarrollo de una plataforma web para el
                        registro y consulta de recursos académicos.
                    </p>

                    <hr>

                    <p><strong>Tipo:</strong> Con alumnos</p>
                    <p><strong>Responsable:</strong> José Luis Camacho Campero</p>
                    <p><strong>Participantes:</strong> 2 estudiantes</p>
                    <p><strong>Finalizado:</strong> 20/10/2026</p>
                    <p><strong>Última revisión:</strong> 21/10/2026</p>

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

    <!-- Modal nuevo proyecto -->
    <div class="modal fade" id="modalNuevoProyecto"
        tabindex="-1" role="dialog"
        aria-labelledby="tituloNuevoProyecto">

        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header bg-success text-dark">

                    <h5 class="modal-title" id="tituloNuevoProyecto">
                        Registrar nuevo proyecto
                    </h5>

                    <button type="button" class="close text-white"
                        data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>

                <div class="modal-body">

                    <form>

                        <div class="form-group">
                            <label for="nombreProyecto">
                                Nombre del proyecto
                            </label>

                            <input type="text" class="form-control"
                                id="nombreProyecto"
                                placeholder="Nombre de la investigación">
                        </div>

                        <div class="form-row">

                            <div class="form-group col-12 col-md-6">
                                <label for="tipoProyecto">
                                    Tipo de proyecto
                                </label>

                                <select class="form-control" id="tipoProyecto">
                                    <option selected>Seleccionar...</option>
                                    <option>Personal</option>
                                    <option>Con alumnos</option>
                                </select>
                            </div>

                            <div class="form-group col-12 col-md-6">
                                <label for="estadoProyecto">
                                    Estado
                                </label>

                                <select class="form-control" id="estadoProyecto">
                                    <option selected>Seleccionar...</option>
                                    <option>En proceso</option>
                                    <option>Terminado</option>
                                </select>
                            </div>

                        </div>

                        <div class="form-group">
                            <label for="descripcionProyecto">
                                Descripción
                            </label>

                            <textarea class="form-control"
                                id="descripcionProyecto"
                                rows="4"
                                placeholder="Descripción del proyecto"></textarea>
                        </div>

                        <div class="form-row">

                            <div class="form-group col-12 col-md-6">
                                <label for="inicioProyecto">
                                    Fecha de inicio
                                </label>

                                <input type="date" class="form-control"
                                    id="inicioProyecto">
                            </div>

                            <div class="form-group col-12 col-md-6">
                                <label for="entregaProyecto">
                                    Fecha estimada de entrega
                                </label>

                                <input type="date" class="form-control"
                                    id="entregaProyecto">
                            </div>

                        </div>

                        <div class="form-group">
                            <label for="participantesProyecto">
                                Participantes
                            </label>

                            <textarea class="form-control"
                                id="participantesProyecto"
                                rows="2"
                                placeholder="Alumnos o investigadores participantes"></textarea>
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
                        Registrar proyecto
                    </button>

                </div>

            </div>
        </div>
    </div>
<?php require __DIR__ . "/../includes/profesor_footer.php"; ?>
