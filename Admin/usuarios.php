<?php
declare(strict_types=1);
require_once __DIR__.'/../includes/admin_layout.php';
[$admin,$error]=tesco_admin_contexto();
$buscar=trim((string)($_GET['q']??''));
if (strlen($buscar)>100) $buscar=substr($buscar,0,100);
$usuarios=tesco_admin_usuarios($buscar);
tesco_admin_inicio('Usuarios y roles','usuarios.php',$admin,$error);
?>
<div class="alert alert-info"><i class="fas fa-lock mr-2"></i>El alta es institucional: los alumnos y profesores no se registran públicamente. Los administradores no pueden desactivar otras cuentas ADMIN desde este panel.</div>
<div class="row">
 <div class="col-12 col-xl-5">
 <?php tesco_admin_tarjeta_inicio('Registrar alumno o profesor','fas fa-user-plus'); ?>
 <form method="post" autocomplete="off">
  <input type="hidden" name="csrf" value="<?=tesco_escape(tesco_csrf_public())?>">
  <input type="hidden" name="accion" value="alta_usuario">
  <div class="form-group"><label for="correo">Correo *</label><input id="correo" name="correo" type="email" class="form-control" maxlength="255" required></div>
  <div class="form-row">
    <div class="form-group col-sm-6"><label for="nombre">Nombre *</label><input id="nombre" name="nombre" class="form-control" maxlength="100" required></div>
    <div class="form-group col-sm-6"><label for="apellido">Apellido paterno *</label><input id="apellido" name="apellido" class="form-control" maxlength="100" required></div>
  </div>
  <div class="form-group"><label for="password">Contraseña inicial *</label><input id="password" name="password" type="password" class="form-control" minlength="12" maxlength="200" autocomplete="new-password" required><small class="form-text text-muted">Se guarda únicamente su hash PHP, nunca la contraseña en texto plano.</small></div>
  <div class="form-row">
    <div class="form-group col-sm-6"><label for="rol">Rol *</label>
      <select id="rol" name="rol" class="custom-select" required><option value="ALUMNO">Alumno</option><option value="PROFESOR">Profesor</option></select>
    </div>
    <div class="form-group col-sm-6"><label for="referencia">Matrícula / Núm. empleado *</label><input id="referencia" name="referencia" class="form-control" maxlength="30" required></div>
  </div>
  <div class="form-row">
    <div class="form-group col-sm-7"><label for="carrera">Carrera (alumnos)</label><input id="carrera" name="carrera" class="form-control" maxlength="150"></div>
    <div class="form-group col-sm-5"><label for="semestre">Semestre (alumnos)</label><input id="semestre" type="number" min="1" max="14" name="semestre" class="form-control"></div>
  </div>
  <div class="form-group"><label for="departamento">Departamento (profesores)</label><input id="departamento" name="departamento" class="form-control" maxlength="120"></div>
  <button class="btn btn-primary btn-block" type="submit"><i class="fas fa-user-plus mr-2"></i>Registrar usuario</button>
 </form>
 <?php tesco_admin_tarjeta_fin(); ?>
 <?php tesco_admin_tarjeta_inicio('Asignar un rol adicional','fas fa-user-tag'); ?>
 <p class="small text-muted">Utiliza el identificador del usuario de la tabla. No modifica sus roles actuales. El código es su matrícula o número de empleado para el rol nuevo.</p>
 <form method="post">
  <input type="hidden" name="csrf" value="<?=tesco_escape(tesco_csrf_public())?>">
  <input type="hidden" name="accion" value="asignar_rol">
  <div class="form-group"><label for="uidrol">ID de usuario *</label><input id="uidrol" name="usuario_id" type="number" min="1" class="form-control" required></div>
  <div class="form-group"><label for="nuevorol">Rol adicional *</label><select id="nuevorol" name="rol" class="custom-select"><option value="ALUMNO">Alumno</option><option value="PROFESOR">Profesor</option></select></div>
  <div class="form-group"><label for="refrol">Matrícula / Núm. empleado *</label><input id="refrol" name="referencia" class="form-control" maxlength="30" required></div>
  <button class="btn btn-outline-primary" type="submit">Asignar rol</button>
 </form>
 <?php tesco_admin_tarjeta_fin(); ?>
 </div>
 <div class="col-12 col-xl-7">
 <?php tesco_admin_tarjeta_inicio('Directorio institucional','fas fa-users'); ?>
 <form method="get" class="form-row align-items-end mb-3">
  <div class="form-group col-9 mb-0"><label for="q">Filtrar por correo, nombre o matrícula</label><input id="q" name="q" class="form-control" value="<?=tesco_escape($buscar)?>" maxlength="100"></div>
  <div class="form-group col-3 mb-0"><button class="btn btn-outline-primary btn-block my-0" type="submit">Buscar</button></div>
 </form>
 <?php if (!$usuarios): tesco_admin_sin_datos('No se encontraron cuentas.'); else: ?>
 <div class="table-responsive"><table class="table table-striped table-hover table-sm tesco-admin-table">
  <thead class="thead-light"><tr><th scope="col">ID</th><th scope="col">Usuario</th><th scope="col">Rol</th><th scope="col">Identificador</th><th scope="col">Estado</th><th scope="col">Acción</th></tr></thead>
  <tbody>
  <?php foreach ($usuarios as $u): ?>
    <tr>
      <td><?= (int)$u['UsuarioId'] ?></td>
      <td><strong><?=tesco_escape(trim(($u['Nombre']??'').' '.($u['ApellidoPaterno']??'')))?></strong><div class="small text-muted"><?=tesco_escape($u['Correo'])?></div></td>
      <td><?=tesco_escape($u['Roles']??'Sin rol')?></td>
      <td><?=tesco_escape($u['Matricula']??$u['NumeroEmpleado']??'—')?></td>
      <td><span class="badge badge-light border"><?=tesco_escape($u['EstadoCuenta'])?></span></td>
      <td>
      <?php if ((int)$u['EsAdmin']===1): ?>
        <small class="text-muted">Protegido</small>
      <?php else: ?>
        <form method="post" class="d-flex flex-column">
          <input type="hidden" name="csrf" value="<?=tesco_escape(tesco_csrf_public())?>">
          <input type="hidden" name="accion" value="estado_usuario">
          <input type="hidden" name="usuario_id" value="<?= (int)$u['UsuarioId'] ?>">
          <?php $estaActivo=in_array(strtoupper((string)$u['EstadoCuenta']),['ACTIVO','ACTIVA'],true); ?>
          <input type="hidden" name="estado" value="<?=$estaActivo?'INACTIVO':'ACTIVO'?>">
          <button type="submit" class="btn btn-sm <?=$estaActivo?'btn-outline-danger':'btn-outline-success'?>"
                  aria-label="<?=$estaActivo?'Desactivar':'Activar'?> cuenta"><?= $estaActivo?'Desactivar':'Activar' ?></button>
        </form>
      <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody></table></div>
  <p class="text-muted small mb-0">Se muestran hasta 100 resultados por búsqueda. No se muestran hashes ni contraseñas.</p>
 <?php endif; ?>
 <?php tesco_admin_tarjeta_fin(); ?>
 </div>
</div>
<?php tesco_admin_fin(); ?>
