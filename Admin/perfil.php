<?php
declare(strict_types=1);
require_once __DIR__.'/../includes/admin_layout.php';
[$admin,$error]=tesco_admin_contexto();
tesco_admin_inicio('Mi perfil administrativo','perfil.php',$admin,$error);
?>
<div class="row"><div class="col-12 col-lg-8">
<?php tesco_admin_tarjeta_inicio('Información personal e institucional','fas fa-user-shield'); ?>
<form method="post" autocomplete="off">
  <input type="hidden" name="csrf" value="<?=tesco_escape(tesco_csrf_public())?>">
  <input type="hidden" name="accion" value="perfil_admin">
  <div class="form-row">
    <div class="form-group col-md-6"><label for="nombre">Nombre *</label><input id="nombre" name="nombre" class="form-control" maxlength="100" required value="<?=tesco_escape($admin['Nombre']??'')?>"></div>
    <div class="form-group col-md-6"><label for="apellido">Apellido paterno *</label><input id="apellido" name="apellido" class="form-control" maxlength="100" required value="<?=tesco_escape($admin['ApellidoPaterno']??'')?>"></div>
  </div>
  <div class="form-group"><label for="materno">Apellido materno</label><input id="materno" name="materno" class="form-control" maxlength="100" value="<?=tesco_escape($admin['ApellidoMaterno']??'')?>"></div>
  <div class="form-group"><label for="correo">Correo institucional</label><input id="correo" class="form-control" readonly value="<?=tesco_escape($admin['Correo'])?>"></div>
  <div class="form-group"><label for="empleado">Número de empleado</label><input id="empleado" class="form-control" readonly value="<?=tesco_escape($admin['NumeroEmpleado'])?>"></div>
  <div class="form-row">
    <div class="form-group col-md-6"><label for="area">Área de adscripción</label><input id="area" name="area" class="form-control" maxlength="120" value="<?=tesco_escape($admin['AreaAdscripcion']??'')?>"></div>
    <div class="form-group col-md-6"><label for="cargo">Cargo</label><input id="cargo" name="cargo" class="form-control" maxlength="100" value="<?=tesco_escape($admin['Cargo']??'')?>"></div>
  </div>
  <div class="form-group"><label for="bio">Biografía</label><textarea id="bio" name="bio" maxlength="700" rows="3" class="form-control"><?=tesco_escape($admin['Biografia']??'')?></textarea></div>
  <button class="btn btn-primary" type="submit"><i class="fas fa-save mr-2"></i>Guardar perfil</button>
</form>
<?php tesco_admin_tarjeta_fin(); ?>
</div></div>
<?php tesco_admin_fin(); ?>
