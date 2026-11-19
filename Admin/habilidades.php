<?php
declare(strict_types=1);
require_once __DIR__.'/../includes/admin_layout.php';
[$admin,$error]=tesco_admin_contexto();
$habilidades=tesco_admin_habilidades();
$categorias=tesco_admin_categorias();
$niveles=tesco_admin_niveles();
tesco_admin_inicio('Habilidades y verificación','habilidades.php',$admin,$error);
?>
<div class="alert alert-info">
  <i class="fas fa-shield-alt mr-2"></i>Las habilidades declaradas por alumnos se revisan institucionalmente. La clasificación no es una verificación: para validar una habilidad primero debe pasar a <strong>EN_REVISION</strong> y registrar evidencia.
</div>
<?php tesco_admin_tarjeta_inicio('Habilidades de los estudiantes','fas fa-certificate'); ?>
<?php if (!$habilidades): ?>
  <?php tesco_admin_sin_datos('Ningún alumno ha declarado habilidades todavía.'); ?>
<?php else: ?>
  <div class="table-responsive"><table class="table table-striped table-hover tesco-admin-table">
    <thead class="thead-light"><tr><th>Alumno</th><th>Habilidad</th><th>Nivel / experiencia</th><th>Verificación</th><th>Acción institucional</th></tr></thead>
    <tbody>
    <?php foreach ($habilidades as $h): ?>
      <tr>
        <td><div><?=tesco_escape($h['Correo'])?></div><small class="text-muted">Usuario #<?= (int)$h['UsuarioId'] ?></small></td>
        <td><strong><?=tesco_escape($h['Habilidad'])?></strong><div class="small text-muted"><?=tesco_escape($h['Categoria'])?></div></td>
        <td><?=tesco_escape($h['Nivel'])?><div class="small text-muted"><?= $h['MesesExperiencia']===null?'Sin meses indicados':(int)$h['MesesExperiencia'].' meses' ?></div></td>
        <td><span class="badge badge-<?= $h['EstadoVerificacion']==='VALIDADA'?'success':($h['EstadoVerificacion']==='RECHAZADA'?'danger':'secondary') ?>"><?=tesco_escape($h['EstadoVerificacion'])?></span></td>
        <td>
          <?php if ($h['EstadoVerificacion']==='DECLARADA'): ?>
            <form method="post">
              <input type="hidden" name="csrf" value="<?=tesco_escape(tesco_csrf_public())?>">
              <input type="hidden" name="accion" value="revisar_habilidad">
              <input type="hidden" name="usuario_id" value="<?= (int)$h['UsuarioId'] ?>">
              <input type="hidden" name="habilidad_id" value="<?= (int)$h['HabilidadId'] ?>">
              <input type="hidden" name="estado" value="EN_REVISION">
              <button class="btn btn-outline-primary btn-sm" type="submit">Iniciar revisión</button>
            </form>
          <?php elseif ($h['EstadoVerificacion']==='EN_REVISION'): ?>
            <form method="post" class="tesco-admin-form-corto">
              <input type="hidden" name="csrf" value="<?=tesco_escape(tesco_csrf_public())?>">
              <input type="hidden" name="accion" value="revisar_habilidad">
              <input type="hidden" name="usuario_id" value="<?= (int)$h['UsuarioId'] ?>">
              <input type="hidden" name="habilidad_id" value="<?= (int)$h['HabilidadId'] ?>">
              <label class="small mb-1" for="evidencia-<?= (int)$h['UsuarioId'] ?>-<?= (int)$h['HabilidadId'] ?>">Referencia de evidencia (obligatoria al validar)</label>
              <input id="evidencia-<?= (int)$h['UsuarioId'] ?>-<?= (int)$h['HabilidadId'] ?>" name="evidencia" maxlength="500" class="form-control form-control-sm mb-1" placeholder="Folio, constancia o referencia">
              <select class="custom-select custom-select-sm mb-1" name="estado" aria-label="Resultado de revisión">
                <option value="VALIDADA">Validar</option>
                <option value="RECHAZADA">Rechazar</option>
              </select>
              <button type="submit" class="btn btn-outline-primary btn-sm">Confirmar revisión</button>
            </form>
          <?php else: ?>
            <span class="small text-muted">Revisión concluida</span>
            <?php if (!empty($h['EvidenciaReferencia'])): ?>
              <div class="small text-muted">Evidencia registrada</div>
            <?php endif; ?>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
  <p class="small text-muted">Se muestran hasta 100 relaciones usuario-habilidad. No se permite que un administrador valide habilidades propias ni que una cuenta de alumno modifique los campos de verificación.</p>
<?php endif; ?>
<?php tesco_admin_tarjeta_fin(); ?>
<div class="row">
  <div class="col-12 col-lg-6">
  <?php tesco_admin_tarjeta_inicio('Clasificar habilidad','fas fa-tags'); ?>
  <p class="small text-muted">Al asignar categoría se puede normalizar el nombre. Una clasificación no cambia el estado de verificación del alumno.</p>
  <?php if ($habilidades&&$categorias): ?>
  <form method="post">
    <input type="hidden" name="csrf" value="<?=tesco_escape(tesco_csrf_public())?>">
    <input type="hidden" name="accion" value="clasificar_habilidad">
    <div class="form-group"><label for="habilidad_id">Habilidad *</label>
      <select id="habilidad_id" name="habilidad_id" class="custom-select" required>
        <option value="">Selecciona la habilidad</option>
        <?php $vistas=[]; foreach($habilidades as $h): if (isset($vistas[(int)$h['HabilidadId']])) continue; $vistas[(int)$h['HabilidadId']]=true; ?>
          <option value="<?= (int)$h['HabilidadId'] ?>"><?=tesco_escape($h['Habilidad'])?> (ID <?= (int)$h['HabilidadId'] ?>)</option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group"><label for="habilidad_nombre">Nombre institucional *</label><input id="habilidad_nombre" name="nombre" class="form-control" maxlength="120" placeholder="Ej. Python" required></div>
    <div class="form-group"><label for="categoria_id">Categoría institucional *</label>
      <select id="categoria_id" name="categoria_id" class="custom-select" required>
        <option value="">Selecciona una categoría</option>
        <?php foreach($categorias as $c): if (!(int)$c['Activo']) continue; ?>
          <option value="<?= (int)$c['CategoriaId'] ?>"><?=tesco_escape($c['Nombre'])?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <button class="btn btn-outline-primary" type="submit">Guardar clasificación</button>
  </form>
  <?php else: tesco_admin_sin_datos('Necesitas habilidades declaradas y categorías configuradas.'); endif; ?>
  <?php tesco_admin_tarjeta_fin(); ?>
  <?php tesco_admin_tarjeta_inicio('Categorías de habilidades','fas fa-folder-open'); ?>
  <form method="post" class="mb-3">
    <input type="hidden" name="csrf" value="<?=tesco_escape(tesco_csrf_public())?>">
    <input type="hidden" name="accion" value="categoria_nueva">
    <div class="form-group"><label for="nombre_categoria">Nueva categoría</label><input id="nombre_categoria" name="nombre" class="form-control" maxlength="120" required></div>
    <button class="btn btn-outline-primary" type="submit">Agregar categoría</button>
  </form>
  <?php foreach($categorias as $c): ?>
    <div class="d-flex justify-content-between align-items-center border-top py-2">
      <span><?=tesco_escape($c['Nombre'])?> <small class="text-muted"><?= (int)$c['Activo']?'Activa':'Inactiva' ?></small></span>
      <form method="post">
        <input type="hidden" name="csrf" value="<?=tesco_escape(tesco_csrf_public())?>">
        <input type="hidden" name="accion" value="categoria_estado">
        <input type="hidden" name="id" value="<?= (int)$c['CategoriaId'] ?>">
        <input type="hidden" name="activo" value="<?= (int)$c['Activo']?'0':'1' ?>">
        <button class="btn btn-sm btn-outline-secondary" type="submit"><?= (int)$c['Activo']?'Deshabilitar':'Habilitar' ?></button>
      </form>
    </div>
  <?php endforeach; ?>
  <?php tesco_admin_tarjeta_fin(); ?>
  </div>
  <div class="col-12 col-lg-6">
  <?php tesco_admin_tarjeta_inicio('Niveles de dominio','fas fa-signal'); ?>
  <p class="small text-muted">Los niveles habilitados aparecen automáticamente en el formulario del CV del alumno.</p>
  <form method="post" class="mb-3">
    <input type="hidden" name="csrf" value="<?=tesco_escape(tesco_csrf_public())?>">
    <input type="hidden" name="accion" value="nivel_nuevo">
    <div class="form-row">
      <div class="form-group col-sm-8"><label for="nombre_nivel">Nombre *</label><input id="nombre_nivel" name="nombre" class="form-control" maxlength="60" required></div>
      <div class="form-group col-sm-4"><label for="orden_nivel">Orden (1-100) *</label><input id="orden_nivel" name="orden" type="number" min="1" max="100" class="form-control" required></div>
    </div>
    <button class="btn btn-outline-primary" type="submit">Agregar nivel</button>
  </form>
  <?php foreach($niveles as $n): ?>
    <div class="d-flex align-items-center justify-content-between border-top py-2">
      <span><?= (int)$n['Orden'] ?>. <?=tesco_escape($n['Nombre'])?><small class="text-muted ml-2"><?= (int)$n['Activo']?'Activo':'Inactivo' ?></small></span>
      <form method="post">
        <input type="hidden" name="csrf" value="<?=tesco_escape(tesco_csrf_public())?>">
        <input type="hidden" name="accion" value="nivel_estado">
        <input type="hidden" name="id" value="<?= (int)$n['NivelDominioId'] ?>">
        <input type="hidden" name="activo" value="<?= (int)$n['Activo']?'0':'1' ?>">
        <button type="submit" class="btn btn-sm btn-outline-secondary"><?= (int)$n['Activo']?'Deshabilitar':'Habilitar' ?></button>
      </form>
    </div>
  <?php endforeach; ?>
  <?php tesco_admin_tarjeta_fin(); ?>
  <div class="alert alert-light border">
    <i class="fas fa-info-circle mr-2"></i>La unificación automática de habilidades duplicadas no está habilitada para evitar perder estados o evidencias de verificación. Las clasificaciones se guardan sin eliminar historial.
  </div>
  </div>
</div>
<?php tesco_admin_fin(); ?>
