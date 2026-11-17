<?php
// Datos demostrativos hasta integrar SQL Server por sesión.
$tituloPagina = 'Postulaciones';
$paginaActiva = 'postulaciones.php';
?>

<?php require __DIR__ . "/../includes/profesor_header.php"; ?>


    <!-- Contenido principal -->
    <main class="container-fluid mt-4 px-3 px-md-4">

        <!-- Encabezado -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">

            <div class="mb-3 mb-md-0">
                <h3 class="mb-1">
                    Postulaciones recibidas
                </h3>

                <p class="text-muted mb-0">
                    Consulta y revisa las solicitudes de estudiantes
                    interesados en tus convocatorias.
                </p>
            </div>

            <a href="convocatorias.php" class="btn btn-outline-primary">
                <i class="fas fa-bullhorn mr-2"></i>
                Ver convocatorias
            </a>

        </div>

        <!-- Indicadores -->
        <div class="row">

            <div class="col-6 col-lg-3 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h3 class="card-title mb-1">11</h3>
                        <p class="text-muted mb-0">
                            Total de solicitudes
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h3 class="card-title text-warning mb-1">5</h3>
                        <p class="text-muted mb-0">
                            Pendientes
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h3 class="card-title text-success mb-1">4</h3>
                        <p class="text-muted mb-0">
                            Aceptadas
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h3 class="card-title text-danger mb-1">2</h3>
                        <p class="text-muted mb-0">
                            Rechazadas
                        </p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Solicitudes por estado -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">

                <h5 class="card-title mb-3">
                    <i class="fas fa-user-check text-success mr-2"></i>
                    Revisión de postulaciones
                </h5>

                <p class="text-muted">
                    Selecciona el estado de las solicitudes para
                    consultar la información de los estudiantes.
                </p>

                <!-- Pestañas -->
                <ul class="nav nav-tabs flex-nowrap overflow-auto mb-4"
                    role="tablist">

                    <li class="nav-item">
                        <a href="#pendientes"
                            class="nav-link active text-dark text-nowrap"
                            data-toggle="tab">
                            Pendientes
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#aceptadas"
                            class="nav-link text-dark text-nowrap"
                            data-toggle="tab">
                            Aceptadas
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#rechazadas"
                            class="nav-link text-dark text-nowrap"
                            data-toggle="tab">
                            Rechazadas
                        </a>
                    </li>

                </ul>
                
                <!-- Contenido de pestañas -->
                <div class="tab-content">

                    <!-- Pendientes -->
                    <div class="tab-pane fade show active" id="pendientes">

                        <div class="row">

                            <div class="col-12 col-lg-6 mb-3">

                                <div class="card h-100 border shadow-sm">

                                    <div class="card-body">

                                        <div class="media align-items-center mb-3">

                                            <div class="rounded-circle bg-light p-3 mr-3">
                                                <i class="fas fa-user-graduate fa-2x text-secondary"></i>
                                            </div>

                                            <div class="media-body">
                                                <h5 class="mt-0 mb-1">
                                                    Alumno 01
                                                </h5>

                                                <p class="text-muted mb-0">
                                                    Ingeniería en Sistemas Computacionales
                                                </p>

                                                <small class="text-muted">
                                                    Estudiante TESCo
                                                </small>
                                            </div>

                                        </div>

                                        <span class="badge badge-warning mb-3">
                                            Pendiente de revisión
                                        </span>

                                        <hr>

                                        <h6>Convocatoria</h6>

                                        <p class="card-text">
                                            Desarrollo de sistemas de monitoreo
                                        </p>

                                        <p class="mb-2">
                                            <i class="fas fa-calendar-alt mr-2 text-secondary"></i>
                                            Postulación: 07/11/2026
                                        </p>

                                        <p class="mb-0">
                                            <i class="fas fa-clock mr-2 text-secondary"></i>
                                            En espera de respuesta
                                        </p>

                                    </div>

                                    <div class="card-footer bg-white">

                                        <button type="button"
                                            class="btn btn-outline-primary btn-sm"
                                            data-toggle="modal"
                                            data-target="#detallePostulacion1">
                                            <i class="fas fa-eye mr-2"></i>
                                            Revisar solicitud
                                        </button>

                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>

                    <!-- Aceptadas -->
                    <div class="tab-pane fade" id="aceptadas">

                        <div class="row">

                            <div class="col-12 col-lg-6 mb-3">

                                <div class="card h-100 border shadow-sm">

                                    <div class="card-body">

                                        <div class="media align-items-center mb-3">

                                            <div class="rounded-circle bg-light p-3 mr-3">
                                                <i class="fas fa-user-graduate fa-2x text-secondary"></i>
                                            </div>

                                            <div class="media-body">
                                                <h5 class="mt-0 mb-1">
                                                    Alumno 02
                                                </h5>

                                                <p class="text-muted mb-0">
                                                    Ingeniería en Sistemas Computacionales
                                                </p>

                                                <small class="text-muted">
                                                    Estudiante TESCo
                                                </small>
                                            </div>

                                        </div>

                                        <span class="badge badge-success mb-3">
                                            Aceptada
                                        </span>

                                        <hr>

                                        <h6>Convocatoria</h6>

                                        <p class="card-text">
                                            Plataforma de gestión de inventarios
                                        </p>

                                        <p class="mb-2">
                                            <i class="fas fa-calendar-alt mr-2 text-secondary"></i>
                                            Postulación: 15/10/2026
                                        </p>

                                        <p class="mb-0">
                                            <i class="fas fa-check-circle mr-2 text-success"></i>
                                            Resolución favorable
                                        </p>

                                    </div>

                                    <div class="card-footer bg-white">

                                        <button type="button"
                                            class="btn btn-outline-primary btn-sm"
                                            data-toggle="modal"
                                            data-target="#detallePostulacion2">
                                            <i class="fas fa-eye mr-2"></i>
                                            Ver solicitud
                                        </button>

                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>

                    <!-- Rechazadas -->
                    <div class="tab-pane fade" id="rechazadas">

                        <div class="row">

                            <div class="col-12 col-lg-6 mb-3">

                                <div class="card h-100 border shadow-sm">

                                    <div class="card-body">

                                        <div class="media align-items-center mb-3">

                                            <div class="rounded-circle bg-light p-3 mr-3">
                                                <i class="fas fa-user-graduate fa-2x text-secondary"></i>
                                            </div>

                                            <div class="media-body">
                                                <h5 class="mt-0 mb-1">
                                                    Alumno 03
                                                </h5>

                                                <p class="text-muted mb-0">
                                                    Ingeniería en Sistemas Computacionales
                                                </p>

                                                <small class="text-muted">
                                                    Estudiante TESCo
                                                </small>
                                            </div>

                                        </div>

                                        <span class="badge badge-danger mb-3">
                                            Rechazada
                                        </span>

                                        <hr>

                                        <h6>Convocatoria</h6>

                                        <p class="card-text">
                                            Plataforma de gestión de inventarios
                                        </p>

                                        <p class="mb-2">
                                            <i class="fas fa-calendar-alt mr-2 text-secondary"></i>
                                            Postulación: 16/10/2026
                                        </p>

                                        <p class="mb-0">
                                            <i class="fas fa-times-circle mr-2 text-danger"></i>
                                            Solicitud no seleccionada
                                        </p>

                                    </div>

                                    <div class="card-footer bg-white">

                                        <button type="button"
                                            class="btn btn-outline-primary btn-sm"
                                            data-toggle="modal"
                                            data-target="#detallePostulacion3">
                                            <i class="fas fa-eye mr-2"></i>
                                            Ver solicitud
                                        </button>

                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Información complementaria -->
        <div class="card shadow-sm mb-4">

            <div class="card-body">

                <h5 class="card-title">
                    <i class="fas fa-info-circle text-primary mr-2"></i>
                    Gestión de postulaciones
                </h5>

                <p class="text-muted mb-0">
                    Las solicitudes recibidas estarán asociadas
                    a una convocatoria y a un estudiante.
                    Las decisiones de aceptación o rechazo
                    quedarán disponibles cuando se implemente
                    el sistema de usuarios y permisos.
                </p>

            </div>

        </div>

    </main>

    <!-- Modal: solicitud pendiente -->
    <div class="modal fade" id="detallePostulacion1"
        tabindex="-1" role="dialog"
        aria-labelledby="tituloPostulacion1"
        aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable"
            role="document">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title" id="tituloPostulacion1">
                        Revisión de solicitud — Alumno 01
                    </h5>

                    <button type="button" class="close"
                        data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>

                <div class="modal-body">

                    <span class="badge badge-warning mb-3">
                        Pendiente de revisión
                    </span>

                    <h6>Información del estudiante</h6>

                    <p><strong>Nombre:</strong> Alumno 01</p>
                    <p><strong>Institución:</strong> TESCo</p>
                    <p><strong>Carrera:</strong> Ingeniería en Sistemas Computacionales</p>
                    <p><strong>Semestre:</strong> No especificado</p>

                    <hr>

                    <h6>Información de la postulación</h6>

                    <p>
                        <strong>Convocatoria:</strong>
                        Desarrollo de sistemas de monitoreo
                    </p>

                    <p>
                        <strong>Fecha de solicitud:</strong>
                        07/11/2026
                    </p>

                    <p>
                        <strong>Estado:</strong>
                        Pendiente
                    </p>

                    <hr>

                    <h6>Motivación del estudiante</h6>

                    <p>
                        Me interesa participar en el desarrollo
                        de sistemas de monitoreo y adquirir
                        experiencia práctica en investigación
                        y programación.
                    </p>

                    <h6>Conocimientos declarados</h6>

                    <ul>
                        <li>Programación básica</li>
                        <li>Desarrollo web</li>
                        <li>Bases de datos</li>
                    </ul>

                    <hr>

                    <h6>Documentos adjuntos</h6>

                    <p class="text-muted">
                        No se muestran archivos reales en
                        esta etapa de maquetado.
                    </p>

                    <hr>

                    <h6>Evaluación del profesor</h6>

                    <div class="form-group">

                        <label for="observacionPendiente">
                            Observaciones
                        </label>

                        <textarea class="form-control"
                            id="observacionPendiente"
                            rows="3"
                            placeholder="Escribe una observación de ejemplo..."></textarea>

                        <small class="form-text text-muted">
                            Las observaciones no se guardarán.
                        </small>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">
                        Cerrar
                    </button>

                    <button type="button"
                        class="btn btn-outline-danger" disabled>
                        <i class="fas fa-times mr-2"></i>
                        Rechazar
                    </button>

                    <button type="button"
                        class="btn btn-success" disabled>
                        <i class="fas fa-check mr-2"></i>
                        Aceptar
                    </button>

                </div>

            </div>
        </div>
    </div>

    <!-- Modal: solicitud aceptada -->
    <div class="modal fade" id="detallePostulacion2"
        tabindex="-1" role="dialog"
        aria-labelledby="tituloPostulacion2"
        aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered"
            role="document">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title" id="tituloPostulacion2">
                        Solicitud aceptada — Alumno 02
                    </h5>

                    <button type="button" class="close"
                        data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>

                <div class="modal-body">

                    <span class="badge badge-success mb-3">
                        Aceptada
                    </span>

                    <h6>Información del estudiante</h6>

                    <p><strong>Nombre:</strong> Alumno 02</p>
                    <p><strong>Carrera:</strong> Ingeniería en Sistemas Computacionales</p>
                    <p><strong>Institución:</strong> TESCo</p>

                    <hr>

                    <h6>Información de la solicitud</h6>

                    <p>
                        <strong>Convocatoria:</strong>
                        Plataforma de gestión de inventarios
                    </p>

                    <p>
                        <strong>Fecha de postulación:</strong>
                        15/10/2026
                    </p>

                    <p>
                        <strong>Estado:</strong>
                        Aceptada
                    </p>

                    <hr>

                    <h6>Motivación del estudiante</h6>

                    <p>
                        Me gustaría colaborar en un proyecto
                        que me permita aplicar conocimientos
                        de desarrollo de software y bases de datos.
                    </p>

                    <div class="alert alert-success mb-0">
                        Esta solicitud fue seleccionada para participar
                        en la convocatoria.
                    </div>

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

    <!-- Modal: solicitud rechazada -->
    <div class="modal fade" id="detallePostulacion3"
        tabindex="-1" role="dialog"
        aria-labelledby="tituloPostulacion3"
        aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered"
            role="document">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title" id="tituloPostulacion3">
                        Solicitud rechazada — Alumno 03
                    </h5>

                    <button type="button" class="close"
                        data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>

                <div class="modal-body">

                    <span class="badge badge-danger mb-3">
                        Rechazada
                    </span>

                    <h6>Información del estudiante</h6>

                    <p><strong>Nombre:</strong> Alumno 03</p>
                    <p><strong>Carrera:</strong> Ingeniería en Sistemas Computacionales</p>
                    <p><strong>Institución:</strong> TESCo</p>

                    <hr>

                    <h6>Información de la solicitud</h6>

                    <p>
                        <strong>Convocatoria:</strong>
                        Plataforma de gestión de inventarios
                    </p>

                    <p>
                        <strong>Fecha de postulación:</strong>
                        16/10/2026
                    </p>

                    <p>
                        <strong>Estado:</strong>
                        Rechazada
                    </p>

                    <hr>

                    <h6>Motivación del estudiante</h6>

                    <p>
                        Estoy interesado en mejorar mis habilidades
                        de programación y colaborar en
                        proyectos académicos.
                    </p>

                    <div class="alert alert-secondary mb-0">
                        La solicitud no fue seleccionada.
                        No se ha registrado un motivo de rechazo.
                    </div>

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
