<?php
/** Componentes de presentación: Bootstrap institucional del proyecto. */
declare(strict_types=1);
require_once __DIR__ . '/profesor_datos.php';

function tesco_encabezado(string $titulo,string $subtitulo): void { ?>
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div><h3 class="mb-1"><?=tesco_h($titulo)?></h3><p class="text-muted mb-0"><?=tesco_h($subtitulo)?></p></div>
    <span class="badge badge-light border px-3 py-2 mt-2 mt-md-0"><i class="fas fa-database mr-2 text-success"></i>Datos desde SQL Server</span>
  </div>
<?php }
function tesco_estado(): void {
    global $tescoFlash, $tescoProfesor;
    if($tescoFlash) { ?>
      <div class="alert alert-<?=($tescoFlash[0]??'')==='ok'?'success':'danger'?> alert-dismissible fade show" role="alert">
        <?=tesco_h($tescoFlash[1]??'')?> <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span>&times;</span></button>
      </div>
    <?php }
    if(($tescoProfesor['origen']??'')==='demo') { ?>
      <div class="alert alert-warning py-2 small">
        <i class="fas fa-flask mr-2"></i><strong>Demostración local.</strong>
        Los cambios realizados con los formularios se guardan en la base de pruebas del equipo.
        ProfesorId <?=tesco_h($tescoProfesor['ProfesorId'])?>.
      </div>
    <?php }
}
function tesco_formulario_inicio(string $accion): void { ?>
    <form method="post" autocomplete="off">
      <input type="hidden" name="csrf" value="<?=tesco_h($_SESSION['tesco_csrf']??'')?>">
      <input type="hidden" name="accion" value="<?=tesco_h($accion)?>">
<?php }
function tesco_input(string $etiqueta,string $name,string $tipo='text',string $valor='',bool $requerido=false): void {
    $id='tesco-'.preg_replace('/[^a-z0-9_-]/i','-',$name); ?>
    <div class="form-group"><label for="<?=tesco_h($id)?>"><?=tesco_h($etiqueta)?></label>
      <input class="form-control" id="<?=tesco_h($id)?>" type="<?=tesco_h($tipo)?>" name="<?=tesco_h($name)?>" value="<?=tesco_h($valor)?>" <?=$requerido?'required':''?>>
    </div>
<?php }
function tesco_textarea(string $etiqueta,string $name,string $valor=''): void {
    $id='tesco-'.preg_replace('/[^a-z0-9_-]/i','-',$name); ?>
    <div class="form-group"><label for="<?=tesco_h($id)?>"><?=tesco_h($etiqueta)?></label>
      <textarea class="form-control" rows="3" id="<?=tesco_h($id)?>" name="<?=tesco_h($name)?>"><?=tesco_h($valor)?></textarea>
    </div>
<?php }
function tesco_formulario_fin(): void { ?>
      <button class="btn btn-primary" type="submit"><i class="fas fa-save mr-2"></i>Guardar</button>
    </form>
<?php }
function tesco_tarjeta(string $titulo,string $icono='fas fa-list'): void { ?>
    <div class="card shadow-sm mb-4">
      <div class="card-header bg-white"><h5 class="mb-0"><i class="<?=tesco_h($icono)?> text-success mr-2"></i><?=tesco_h($titulo)?></h5></div>
      <div class="card-body">
<?php }
function tesco_tarjeta_fin(): void { ?> </div></div> <?php }
function tesco_tabla_resultados(array $filas,array $cols): void { ?>
    <?php if(!$filas): ?>
      <div class="alert alert-light border mb-0"><i class="far fa-folder-open mr-2"></i>No hay registros asociados al profesor seleccionado.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-striped table-hover table-sm">
          <thead class="thead-light"><tr>
            <?php foreach($cols as $label=>$campo): ?><th scope="col"><?=tesco_h($label)?></th><?php endforeach; ?>
          </tr></thead>
          <tbody>
            <?php foreach($filas as $fila): ?><tr>
              <?php foreach($cols as $label=>$campo): ?>
                <td><?=tesco_h(is_callable($campo)?$campo($fila):($fila[$campo]??'Sin registrar'))?></td>
              <?php endforeach; ?>
            </tr><?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
<?php }
function tesco_modal_inicio(string $id,string $titulo): void { ?>
  <div class="modal fade" id="<?=tesco_h($id)?>" tabindex="-1" role="dialog" aria-labelledby="<?=tesco_h($id)?>-titulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header bg-success text-dark">
          <h5 class="modal-title" id="<?=tesco_h($id)?>-titulo"><?=tesco_h($titulo)?></h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Cerrar"><span>&times;</span></button>
        </div>
        <div class="modal-body">
<?php }
function tesco_modal_fin(): void { ?> </div></div></div></div> <?php }
function tesco_pagina_inicio(string $titulo,string $pagina): void {
    $tituloPagina=$titulo; $paginaActiva=$pagina;
    require __DIR__.'/profesor_header.php';
    echo '<main class="container-fluid mt-4 px-3 px-md-4">';
    tesco_estado();
}
function tesco_pagina_fin(): void {
    echo '</main>';
    require __DIR__.'/profesor_footer.php';
}
