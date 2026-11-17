<?php
// Datos demostrativos hasta integrar SQL Server por sesión.
$tituloPagina = 'Publicaciones';
$paginaActiva = 'publicaciones.php';
?>

<?php require __DIR__ . "/../includes/profesor_header.php"; ?>


    <!-- Contenido principal -->
    <main class="container-fluid mt-4 px-3 px-md-4">

        <!-- Encabezado -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">

            <div class="mb-3 mb-md-0">
                <h3 class="mb-1">
                    Mis publicaciones académicas
                </h3>

                <p class="text-muted mb-0">
                    Consulta libros, capítulos y artículos de investigación.
                </p>
            </div>

            <button type="button" class="btn btn-success"
                data-toggle="modal"
                data-target="#modalNuevaPublicacion">
                <i class="fas fa-plus mr-2"></i>
                Nueva publicación
            </button>

        </div>

        <!-- Indicadores generales -->
        <div class="row">

            <div class="col-12 col-sm-4 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h3 class="card-title mb-1">1</h3>
                        <p class="text-muted mb-0">
                            Libros
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-4 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h3 class="card-title text-success mb-1">1</h3>
                        <p class="text-muted mb-0">
                            Capítulos de libros
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-4 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h3 class="card-title text-primary mb-1">1</h3>
                        <p class="text-muted mb-0">
                            Artículos de investigación
                        </p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Listado de publicaciones -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">

                <h5 class="card-title mb-3">
                    <i class="fas fa-book-open text-success mr-2"></i>
                    Producción bibliográfica
                </h5>

                <!-- Pestañas -->
                <ul class="nav nav-tabs flex-nowrap overflow-auto mb-4"
                    role="tablist">

                    <li class="nav-item">
                        <a href="#libros"
                            class="nav-link active text-dark text-nowrap"
                            data-toggle="tab">
                            Libros
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#capitulos"
                            class="nav-link text-dark text-nowrap"
                            data-toggle="tab">
                            Capítulos de libros
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#articulos"
                            class="nav-link text-dark text-nowrap"
                            data-toggle="tab">
                            Artículos de investigación
                        </a>
                    </li>

                </ul>

                <!-- Contenido de pestañas -->
                <div class="tab-content">

                    <!-- Libros -->
                    <div class="tab-pane fade show active" id="libros">

                        <div class="row">

                            <div class="col-12 col-md-6 col-xl-4 mb-3">
                                <div class="card h-100 border shadow-sm">

                                    <div class="card-body">

                                        <span class="badge badge-success mb-2">
                                            Libro académico
                                        </span>

                                        <h5 class="card-title">
                                            Fundamentos de ingeniería de software
                                        </h5>

                                        <p class="text-muted">
                                            Autor: Docente investigador
                                        </p>

                                        <p class="card-text">
                                            Publicación de ejemplo sobre los
                                            fundamentos del análisis y desarrollo
                                            de sistemas informáticos.
                                        </p>

                                        <p class="mb-1">
                                            <i class="fas fa-calendar-alt mr-2"></i>
                                            2026
                                        </p>

                                        <p class="text-muted mb-0">
                                            <small>
                                                Editorial: Información de ejemplo
                                            </small>
                                        </p>

                                    </div>

                                    <div class="card-footer bg-white">
                                        <button type="button"
                                            class="btn btn-outline-primary btn-sm"
                                            data-toggle="modal"
                                            data-target="#detalleLibro">
                                            <i class="fas fa-eye mr-2"></i>
                                            Ver detalles
                                        </button>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Capítulos de libros -->
                    <div class="tab-pane fade" id="capitulos">

                        <div class="row">

                            <div class="col-12 col-md-6 col-xl-4 mb-3">
                                <div class="card h-100 border shadow-sm">

                                    <div class="card-body">

                                        <span class="badge badge-info mb-2">
                                            Capítulo de libro
                                        </span>

                                        <h5 class="card-title">
                                            Inteligencia artificial en la educación
                                        </h5>

                                        <p class="text-muted">
                                            Autor: Docente investigador
                                        </p>

                                        <p class="card-text">
                                            Capítulo de ejemplo sobre las
                                            aplicaciones de inteligencia artificial
                                            en entornos educativos.
                                        </p>

                                        <p class="mb-1">
                                            <i class="fas fa-book mr-2"></i>
                                            Obra colectiva de investigación
                                        </p>

                                        <p class="text-muted mb-0">
                                            <small>
                                                Publicación: Octubre de 2026
                                            </small>
                                        </p>

                                    </div>

                                    <div class="card-footer bg-white">
                                        <button type="button"
                                            class="btn btn-outline-primary btn-sm"
                                            data-toggle="modal"
                                            data-target="#detalleCapitulo">
                                            <i class="fas fa-eye mr-2"></i>
                                            Ver detalles
                                        </button>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Artículos de investigación -->
                    <div class="tab-pane fade" id="articulos">

                        <div class="row">

                            <div class="col-12 col-md-6 col-xl-4 mb-3">
                                <div class="card h-100 border shadow-sm">

                                    <div class="card-body">

                                        <span class="badge badge-primary mb-2">
                                            Artículo de investigación
                                        </span>

                                        <h5 class="card-title">
                                            Análisis de sistemas inteligentes
                                        </h5>

                                        <p class="text-muted">
                                            Autor: Docente investigador
                                        </p>

                                        <p class="card-text">
                                            Artículo de ejemplo sobre métodos
                                            de análisis de datos y sistemas
                                            inteligentes.
                                        </p>

                                        <p class="mb-1">
                                            <i class="fas fa-newspaper mr-2"></i>
                                            Revista académica de ejemplo
                                        </p>

                                        <p class="text-muted mb-0">
                                            <small>
                                                Publicación: Septiembre de 2026
                                            </small>
                                        </p>

                                    </div>

                                    <div class="card-footer bg-white">
                                        <button type="button"
                                            class="btn btn-outline-primary btn-sm"
                                            data-toggle="modal"
                                            data-target="#detalleArticulo">
                                            <i class="fas fa-eye mr-2"></i>
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
            data-target="#modalNuevaPublicacion"
            aria-label="Agregar publicación"
            title="Agregar publicación">

            <i class="fas fa-plus"></i>

        </button>
    </div>

    <!-- Modal detalle del libro -->
    <div class="modal fade" id="detalleLibro"
        tabindex="-1" role="dialog"
        aria-labelledby="tituloDetalleLibro"
        aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="tituloDetalleLibro">
                        Fundamentos de ingeniería de software
                    </h5>

                    <button type="button" class="close"
                        data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <span class="badge badge-success mb-3">
                        Libro académico
                    </span>

                    <h6>Descripción</h6>
                    <p>
                        Publicación ficticia utilizada para mostrar
                        el registro y la consulta de libros académicos.
                    </p>

                    <hr>

                    <p><strong>Autor:</strong> Docente investigador</p>
                    <p><strong>Editorial:</strong> Información de ejemplo</p>
                    <p><strong>Año:</strong> 2026</p>
                    <p><strong>ISBN:</strong> Pendiente de registrar</p>
                    <p><strong>Área:</strong> Ingeniería de software</p>

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

    <!-- Modal detalle del capítulo -->
    <div class="modal fade" id="detalleCapitulo"
        tabindex="-1" role="dialog"
        aria-labelledby="tituloDetalleCapitulo"
        aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="tituloDetalleCapitulo">
                        Inteligencia artificial en la educación
                    </h5>

                    <button type="button" class="close"
                        data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <span class="badge badge-info mb-3">
                        Capítulo de libro
                    </span>

                    <h6>Descripción</h6>
                    <p>
                        Ejemplo de capítulo académico sobre
                        aplicaciones de inteligencia artificial
                        en procesos educativos.
                    </p>

                    <hr>

                    <p><strong>Autor:</strong> Docente investigador</p>
                    <p><strong>Libro:</strong> Obra colectiva de investigación</p>
                    <p><strong>Fecha:</strong> Octubre de 2026</p>
                    <p><strong>Páginas:</strong> Pendiente de registrar</p>
                    <p><strong>ISBN:</strong> Pendiente de registrar</p>

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

    <!-- Modal detalle del artículo -->
    <div class="modal fade" id="detalleArticulo"
        tabindex="-1" role="dialog"
        aria-labelledby="tituloDetalleArticulo"
        aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="tituloDetalleArticulo">
                        Análisis de sistemas inteligentes
                    </h5>

                    <button type="button" class="close"
                        data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <span class="badge badge-primary mb-3">
                        Artículo de investigación
                    </span>

                    <h6>Resumen</h6>
                    <p>
                        Publicación ficticia sobre análisis de datos
                        y sistemas inteligentes, utilizada para
                        demostrar la consulta de artículos científicos.
                    </p>

                    <hr>

                    <p><strong>Autor:</strong> Docente investigador</p>
                    <p><strong>Revista:</strong> Revista académica de ejemplo</p>
                    <p><strong>Fecha:</strong> Septiembre de 2026</p>
                    <p><strong>DOI:</strong> Pendiente de registrar</p>
                    <p><strong>Área:</strong> Sistemas computacionales</p>

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

    <!-- Modal nueva publicación -->
    <div class="modal fade" id="modalNuevaPublicacion"
        tabindex="-1" role="dialog"
        aria-labelledby="tituloNuevaPublicacion"
        aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header bg-success text-dark">
                    <h5 class="modal-title" id="tituloNuevaPublicacion">
                        Registrar nueva publicación
                    </h5>

                    <button type="button" class="close text-white"
                        data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">



                    <form>

                        <div class="form-group">
                            <label for="tituloPublicacion">
                                Título de la publicación
                            </label>

                            <input type="text" class="form-control"
                                id="tituloPublicacion"
                                placeholder="Título de la obra o artículo">
                        </div>

                        <div class="form-row">

                            <div class="form-group col-12 col-md-6">
                                <label for="tipoPublicacion">
                                    Tipo de publicación
                                </label>

                                <select class="form-control" id="tipoPublicacion">
                                    <option value="" selected>Seleccionar...</option>
                                    <option>Libro</option>
                                    <option>Capítulo de libro</option>
                                    <option>Artículo de investigación</option>
                                </select>
                            </div>

                            <div class="form-group col-12 col-md-6">
                                <label for="fechaPublicacion">
                                    Fecha de publicación
                                </label>

                                <input type="date" class="form-control"
                                    id="fechaPublicacion">
                            </div>

                        </div>

                        <div class="form-group">
                            <label for="autoresPublicacion">
                                Autores
                            </label>

                            <input type="text" class="form-control"
                                id="autoresPublicacion"
                                placeholder="Autores de la publicación">
                        </div>

                        <div class="form-group">
                            <label for="medioPublicacion">
                                Editorial, revista u obra de origen
                            </label>

                            <input type="text" class="form-control"
                                id="medioPublicacion"
                                placeholder="Nombre de la editorial, revista o libro">
                        </div>

                        <div class="form-row">

                            <div class="form-group col-12 col-md-6">
                                <label for="isbnPublicacion">
                                    ISBN
                                </label>

                                <input type="text" class="form-control"
                                    id="isbnPublicacion"
                                    placeholder="Si corresponde">
                            </div>

                            <div class="form-group col-12 col-md-6">
                                <label for="doiPublicacion">
                                    DOI
                                </label>

                                <input type="text" class="form-control"
                                    id="doiPublicacion"
                                    placeholder="Si corresponde">
                            </div>

                        </div>

                        <div class="form-group">
                            <label for="descripcionPublicacion">
                                Descripción o resumen
                            </label>

                            <textarea class="form-control"
                                id="descripcionPublicacion"
                                rows="4"
                                placeholder="Descripción de la publicación"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="urlPublicacion">
                                Enlace de consulta
                            </label>

                            <input type="url" class="form-control"
                                id="urlPublicacion"
                                placeholder="https://">
                        </div>

                        <div class="form-group">
                            <label for="archivoPublicacion">
                                Documento adjunto
                            </label>

                            <input type="file" class="form-control-file"
                                id="archivoPublicacion">

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
                        Registrar publicación
                    </button>

                </div>

            </div>
        </div>
    </div>
<?php require __DIR__ . "/../includes/profesor_footer.php"; ?>
