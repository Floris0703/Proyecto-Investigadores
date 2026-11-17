
<?php

// Importamos la conexión a SQL Server.
require_once __DIR__ . '/../conexion.php';

// Consultamos el primer profesor de prueba.
$sql = "
    SELECT TOP (1)
        p.ProfesorId,
        pr.Nombre,
        pr.ApellidoPaterno,
        pr.ApellidoMaterno,
        p.Departamento,
        u.Correo
    FROM academico.Profesor AS p
    INNER JOIN plataforma.Usuario AS u
        ON p.UsuarioId = u.UsuarioId
    LEFT JOIN plataforma.PerfilUsuario AS pr
        ON p.UsuarioId = pr.UsuarioId
    ORDER BY p.ProfesorId
";

// Ejecutamos la consulta.
$resultado = sqlsrv_query($con, $sql);

if ($resultado === false) {
    error_log(print_r(sqlsrv_errors(), true));
    http_response_code(500);
    exit("No fue posible consultar los profesores.");
}

// Recuperamos el registro.
$profesor = sqlsrv_fetch_array(
    $resultado,
    SQLSRV_FETCH_ASSOC
);

if (!$profesor) {
    exit("No hay profesores registrados.");
}

// Preparamos los datos para HTML.
$nombreProfesor = trim(
    ($profesor["Nombre"] ?? "") . " " .
    ($profesor["ApellidoPaterno"] ?? "") . " " .
    ($profesor["ApellidoMaterno"] ?? "")
);

$departamento = $profesor["Departamento"]
    ?? "Sin departamento registrado";

$correoProfesor = $profesor["Correo"]
    ?? "Sin correo registrado";

sqlsrv_free_stmt($resultado);
sqlsrv_close($con);


$tituloPagina = 'Resumen docente';
$paginaActiva = 'index.php';
?>


<?php require __DIR__ . "/../includes/profesor_header.php"; ?>


    <!--Resumen-->
    <div class="container-fluid mt-4">
        <div class="row">
            <section class="col-12 col-lg-5">
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <div class="media align-items-center mb-3">
                            <div class="rounded-circle bg-light p-3 mr-3">
                                <i class="fas fa-user fa-2x text-secondary"></i>
                            </div>
                            <div class="media-body">
                                <h5 class="card-title">
                                    <?= htmlspecialchars($nombreProfesor,ENT_QUOTES, 'UTF-8') ?>
                                </h5>
                                <small class="text-muted">
                                    Docente investigador
                                </small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <p>
                                    <strong>Correo:</strong><br>
                                    <?= htmlspecialchars($correoProfesor, ENT_QUOTES, 'UTF-8') ?>
                                </p>
                            </div>
                            <div class="col-sm-6">
                                <p>
                                    <strong>Horario de atención:</strong><br>
                                    9:00 am - 12:00 pm
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <strong>Ubicación:</strong><br>
                                CCAI
                            </div>
                            <div class="col-sm-6">
                                <strong>ORCID:</strong><br>
                                82929292
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <strong>Áreas:</strong><br>
                                <?= htmlspecialchars($departamento,ENT_QUOTES,'UTF-8') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="col-12 col-lg-7">
                <div class="row">
                    <div class="col-6 mb-3">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h3 class="card-title mb-1">10</h3>
                                <p class="card-text text-muted">
                                    Proyectos
                                </p>
                            </div>
                        </div>
                    </div>
                            
                    <div class="col-12 col-sm-6 mb-3">
                        <div class="card h-100 shadow-sm h-100">
                            <div class="card-body">
                                <h3 class="card-title mb-1">8</h3>
                                <p class="card-text text-muted">
                                    Proyectos de titulación
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-sm-6 mb-3">
                        <div class="card h-100 shadow-sm h-100">
                            <div class="card-body">
                                <h3 class="card-title mb-1">8</h3>
                                <p class="card-text text-muted">
                                    Artículos de investigación
                                </p>
                            </div>
                        </div>
                    </div>
                            
                    <div class="col-12 col-sm-6 mb-3">
                        <div class="card h-100 shadow-sm h-100">
                            <div class="card-body">
                                <h3 class="card-title mb-1">8</h3>
                                <p class="card-text text-muted">
                                    Proyectos de titulación
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-sm-6 mb-3">
                            <div class="card h-100 shadow-sm h-100">
                                <div class="card-body">
                                    <h3 class="card-title mb-1">5</h3>
                                    <p class="card-text text-muted">
                                        Convocatorias
                                    </p>
                                </div>
                            </div>
                        </div>
                            
                        <div class="col-12 col-sm-6 mb-3">
                            <div class="card h-100 shadow-sm h-100">
                                <div class="card-body">
                                    <h3 class="card-title mb-1">2</h3>
                                    <p class="card-text text-muted">
                                        Congresos nacionales
                                    </p>
                                </div>
                            </div>
                        </div>

                            
                        <div class="col-12 col-sm-6 mb-3">
                            <div class="card h-100 shadow-sm h-100">
                                <div class="card-body">
                                    <h3 class="card-title mb-1">8</h3>
                                    <p class="card-text text-muted">
                                        Publicaciones
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 mb-3">
                            <div class="card h-100 shadow-sm h-100">
                                <div class="card-body">
                                    <h3 class="card-title mb-1">8</h3>
                                    <p class="card-text text-muted">
                                        Concursos
                                    </p>
                                </div>
                            </div>
                        </div>
                </div>
            </section>
        </div>
    </div>

    <!--Producción estadística-->
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12 mb-3">
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h3 class="card-title mb-3">
                            Productividad mensual
                        </h3>
                        <ul class="nav nav-tabs flex-nowrap overflow-auto mb-3" role="tablist">
                            <li class="nav-item">
                                <a href="#proyectos" class="nav-link active text-dark text-nowrap" data-toggle="tab">
                                    Proyectos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#convocatorias" class="nav-link text-dark text-nowrap" data-toggle="tab">
                                    Convocatorias
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#congresosN" class="nav-link text-dark text-nowrap" data-toggle="tab">
                                    Congresos nacionales
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#congresosI" class="nav-link text-dark text-nowrap" data-toggle="tab">
                                    Congresos internacionales
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#publicaciones" class="nav-link text-dark text-nowrap" data-toggle="tab">
                                    Publicaciones
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="proyectos">        
                                <small class="text-muted d-block mb-1">
                                    Septiembre
                                </small>
                                <div class="progress">
                                    <div class="progress-bar bg-success w-50"
                                        role="progressbar"
                                        aria-valuenow="2"
                                        aria-valuemin="0"
                                        aria-valuemax="4">
                                        1
                                    </div>
                                </div>
                                <small class="text-muted d-block mb-1">
                                    Octubre
                                </small>
                                <div class="progress">
                                    <div class="progress-bar bg-success w-75"
                                        role="progressbar"
                                        aria-valuenow="3"
                                        aria-valuemin="0"
                                        aria-valuemax="4">
                                        3
                                    </div>
                                </div>
                                <small class="text-muted d-block mb-1">
                                    Noviembre
                                </small>
                                <div class="progress">
                                    <div class="progress-bar bg-success w-25"
                                        role="progressbar"
                                        aria-valuenow="1"
                                        aria-valuemin="0"
                                        aria-valuemax="4">
                                        1
                                    </div>
                                </div>        
                                <small class="text-muted d-block mb-1">
                                    Diciembre
                                </small>
                                <div class="progress">
                                    <div class="progress-bar bg-success w-100"
                                        role="progressbar"
                                        aria-valuenow="4"
                                        aria-valuemin="0"
                                        aria-valuemax="4">
                                        4
                                    </div>
                                </div>
                            </div>
                            
                            <div class="tab-pane fade" id="convocatorias">        
                                <small class="text-muted d-block mb-1">
                                    Septiembre
                                </small>
                                <div class="progress">
                                    <div class="progress-bar bg-success w-50"
                                        role="progressbar"
                                        aria-valuenow="2"
                                        aria-valuemin="0"
                                        aria-valuemax="4">
                                        1
                                    </div>
                                </div>
                                <small class="text-muted d-block mb-1">
                                    Octubre
                                </small>
                                <div class="progress">
                                    <div class="progress-bar bg-success w-75"
                                        role="progressbar"
                                        aria-valuenow="3"
                                        aria-valuemin="0"
                                        aria-valuemax="4">
                                        3
                                    </div>
                                </div>
                                <small class="text-muted d-block mb-1">
                                    Noviembre
                                </small>
                                <div class="progress">
                                    <div class="progress-bar bg-success w-25"
                                        role="progressbar"
                                        aria-valuenow="1"
                                        aria-valuemin="0"
                                        aria-valuemax="4">
                                        1
                                    </div>
                                </div>        
                                <small class="text-muted d-block mb-1">
                                    Diciembre
                                </small>
                                <div class="progress">
                                    <div class="progress-bar bg-success w-100"
                                        role="progressbar"
                                        aria-valuenow="4"
                                        aria-valuemin="0"
                                        aria-valuemax="4">
                                        4
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="congresosN">        
                                <small class="text-muted d-block mb-1">
                                    Septiembre
                                </small>
                                <div class="progress">
                                    <div class="progress-bar bg-success w-50"
                                        role="progressbar"
                                        aria-valuenow="2"
                                        aria-valuemin="0"
                                        aria-valuemax="4">
                                        1
                                    </div>
                                </div>
                                <small class="text-muted d-block mb-1">
                                    Octubre
                                </small>
                                <div class="progress">
                                    <div class="progress-bar bg-success w-75"
                                        role="progressbar"
                                        aria-valuenow="3"
                                        aria-valuemin="0"
                                        aria-valuemax="4">
                                        3
                                    </div>
                                </div>
                                <small class="text-muted d-block mb-1">
                                    Noviembre
                                </small>
                                <div class="progress">
                                    <div class="progress-bar bg-success w-25"
                                        role="progressbar"
                                        aria-valuenow="1"
                                        aria-valuemin="0"
                                        aria-valuemax="4">
                                        1
                                    </div>
                                </div>        
                                <small class="text-muted d-block mb-1">
                                    Diciembre
                                </small>
                                <div class="progress">
                                    <div class="progress-bar bg-success w-100"
                                        role="progressbar"
                                        aria-valuenow="4"
                                        aria-valuemin="0"
                                        aria-valuemax="4">
                                        4
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="congresosI">        
                                <small class="text-muted d-block mb-1">
                                    Septiembre
                                </small>
                                <div class="progress">
                                    <div class="progress-bar bg-success w-50"
                                        role="progressbar"
                                        aria-valuenow="2"
                                        aria-valuemin="0"
                                        aria-valuemax="4">
                                        1
                                    </div>
                                </div>
                                <small class="text-muted d-block mb-1">
                                    Octubre
                                </small>
                                <div class="progress">
                                    <div class="progress-bar bg-success w-75"
                                        role="progressbar"
                                        aria-valuenow="3"
                                        aria-valuemin="0"
                                        aria-valuemax="4">
                                        3
                                    </div>
                                </div>
                                <small class="text-muted d-block mb-1">
                                    Noviembre
                                </small>
                                <div class="progress">
                                    <div class="progress-bar bg-success w-25"
                                        role="progressbar"
                                        aria-valuenow="1"
                                        aria-valuemin="0"
                                        aria-valuemax="4">
                                        1
                                    </div>
                                </div>        
                                <small class="text-muted d-block mb-1">
                                    Diciembre
                                </small>
                                <div class="progress">
                                    <div class="progress-bar bg-success w-100"
                                        role="progressbar"
                                        aria-valuenow="4"
                                        aria-valuemin="0"
                                        aria-valuemax="4">
                                        4
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="publicaciones">        
                                <small class="text-muted d-block mb-1">
                                    Septiembre
                                </small>
                                <div class="progress">
                                    <div class="progress-bar bg-success w-50"
                                        role="progressbar"
                                        aria-valuenow="2"
                                        aria-valuemin="0"
                                        aria-valuemax="4">
                                        1
                                    </div>
                                </div>
                                <small class="text-muted d-block mb-1">
                                    Octubre
                                </small>
                                <div class="progress">
                                    <div class="progress-bar bg-success w-75"
                                        role="progressbar"
                                        aria-valuenow="3"
                                        aria-valuemin="0"
                                        aria-valuemax="4">
                                        3
                                    </div>
                                </div>
                                <small class="text-muted d-block mb-1">
                                    Noviembre
                                </small>
                                <div class="progress">
                                    <div class="progress-bar bg-success w-25"
                                        role="progressbar"
                                        aria-valuenow="1"
                                        aria-valuemin="0"
                                        aria-valuemax="4">
                                        1
                                    </div>
                                </div>        
                                <small class="text-muted d-block mb-1">
                                    Diciembre
                                </small>
                                <div class="progress">
                                    <div class="progress-bar bg-success w-100"
                                        role="progressbar"
                                        aria-valuenow="4"
                                        aria-valuemin="0"
                                        aria-valuemax="4">
                                        4
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
            
        </div>
        
    </div>

    <!--Producción académica-->
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12 mb-3">
                <div class="card shadow-sm mb-3"> 
                    <div class="card-body">
                        <h3 class="card-title mb-3">
                            Producción Académica
                        </h3>
                        <ul class="nav nav-tabs flex-nowrap overflow-auto mb-3" role="tablist">
                            <li class="nav-item">
                                <a href="#prodAvisos" class="nav-link active text-dark text-nowrap" data-toggle="tab">
                                    Avisos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#prodConvocatorias" class="nav-link text-dark text-nowrap" data-toggle="tab">
                                    Convocatorias
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#prodCongresos" class="nav-link text-dark text-nowrap" data-toggle="tab">
                                    Congresos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#prodPublicaciones" class="nav-link text-dark text-nowrap" data-toggle="tab">
                                    Publicaciones
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="prodAvisos">
                                <div class="card border mb-3">
                                    <div class="card-body">

                                        <h5 class="card-title">
                                            Aviso a investigadores
                                        </h5>

                                        <p class="card-text">
                                            Los alumnos con proyectos activos deberán
                                            acudir a jefatura para revisar sus avances.
                                        </p>

                                        <small class="text-muted d-block mb-3">
                                            Publicado: 25 de septiembre de 2026
                                        </small>

                                        <button type="button" class="btn btn-primary btn-sm"
                                            data-toggle="modal"
                                            data-target="#modalAviso">
                                            <i class="fas fa-eye mr-2"></i> Leer
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="tab-pane fade" id="prodConvocatorias">
                                2
                            </div>

                            
                            <div class="tab-pane fade" id="prodCongresos">
                                3
                            </div>

                            
                            <div class="tab-pane fade" id="prodPublicaciones">
                                4
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAviso"
        tabindex="-1" role="dialog"
        aria-labelledby="tituloModalAviso"
        aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered" role="document">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tituloModalAviso">
                        Aviso a investigadores
                    </h5>

                    <button type="button" class="close"
                        data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        Los almnos con proyectos activos deberán acudir a jefatura para revisar sus avances
                    </p>
                    <hr>
                    <p class="text-muted mb-1">
                        <i class="fas fa-calendar-alt mr-3"></i>
                        Publicado: 25 de septiembre de 2026
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
</div>
<?php require __DIR__ . "/../includes/profesor_footer.php"; ?>
