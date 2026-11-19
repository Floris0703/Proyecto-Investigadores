<?php
declare(strict_types=1);
require_once __DIR__.'/../includes/alumno_layout.php';
$alumno=tesco_alumno_requerir();
$error='';
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!tesco_csrf_valid($_POST['csrf'] ?? null)) {
        http_response_code(403);
        $error='El formulario caducó. Recarga la página e inténtalo de nuevo.';
    } else {
        try {
            tesco_alumno_guardar_perfil($alumno,$_POST);
            header('Location: perfil.php?guardado=1',true,303);
            exit;
        } catch (InvalidArgumentException $e) {
            $error=$e->getMessage();
        } catch (Throwable $e) {
            error_log('TESCo alumnos: error al guardar perfil');
            $error='No fue posible guardar los datos. Revisa la conexión y vuelve a intentarlo.';
        }
    }
}
tesco_alumno_encabezado('Mi perfil','perfil.php',$alumno);
if (isset($_GET['guardado'])) tesco_alumno_alerta('Tu perfil se actualizó correctamente.','success');
if ($error!=='') tesco_alumno_alerta($error,'danger');
?>
<div class="row">
  <div class="col-12 col-lg-8">
    <?php tesco_alumno_tarjeta_inicio('Datos personales y académicos','fas fa-id-card'); ?>
    <p class="text-muted">Estos datos ayudan a los docentes a identificarte cuando revisan una postulación.</p>
    <form method="post" autocomplete="on">
      <input type="hidden" name="csrf" value="<?=tesco_escape(tesco_csrf_public())?>">
      <div class="form-row">
        <div class="form-group col-md-6">
          <label for="nombre">Nombre *</label>
          <input id="nombre" class="form-control" name="nombre" maxlength="100" required value="<?=tesco_escape($alumno['Nombre'])?>">
        </div>
        <div class="form-group col-md-6">
          <label for="apellido_paterno">Apellido paterno *</label>
          <input id="apellido_paterno" class="form-control" name="apellido_paterno" maxlength="100" required value="<?=tesco_escape($alumno['ApellidoPaterno'])?>">
        </div>
        <div class="form-group col-md-6">
          <label for="apellido_materno">Apellido materno</label>
          <input id="apellido_materno" class="form-control" name="apellido_materno" maxlength="100" value="<?=tesco_escape($alumno['ApellidoMaterno'])?>">
        </div>
        <div class="form-group col-md-6">
          <label for="correo">Correo institucional</label>
          <input id="correo" class="form-control" value="<?=tesco_escape($alumno['Correo'])?>" readonly aria-describedby="correo-ayuda">
          <small id="correo-ayuda" class="form-text text-muted">El correo de acceso no se cambia desde este formulario.</small>
        </div>
        <div class="form-group col-md-6">
          <label for="matricula">Matrícula</label>
          <input id="matricula" class="form-control" value="<?=tesco_escape($alumno['Matricula'])?>" readonly>
        </div>
        <div class="form-group col-md-6">
          <label for="carrera">Carrera</label>
          <input id="carrera" name="carrera" class="form-control" maxlength="150" value="<?=tesco_escape($alumno['Carrera'])?>">
        </div>
        <div class="form-group col-md-6">
          <label for="semestre">Semestre</label>
          <select id="semestre" name="semestre" class="custom-select">
            <option value="">Sin especificar</option>
            <?php for($sem=1;$sem<=14;$sem++): ?>
              <option value="<?=$sem?>" <?= (int)$alumno['Semestre']===$sem ? 'selected' : '' ?>><?=$sem?>° semestre</option>
            <?php endfor; ?>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label for="biografia">Sobre mí (máximo 700 caracteres)</label>
        <textarea id="biografia" class="form-control" name="biografia" maxlength="700" rows="4" placeholder="Intereses académicos, habilidades y objetivos."><?=tesco_escape($alumno['Biografia'])?></textarea>
      </div>
      <button class="btn btn-primary" type="submit"><i class="fas fa-save mr-2"></i>Guardar perfil</button>
    </form>
    <?php tesco_alumno_tarjeta_fin(); ?>
  </div>
  <div class="col-12 col-lg-4">
    <?php tesco_alumno_tarjeta_inicio('Completa tu presentación','fas fa-lightbulb'); ?>
    <p>Tu perfil incluye tus datos institucionales. En la sección de CV puedes registrar tu experiencia, estudios y habilidades para respaldar tus postulaciones.</p>
    <a class="btn btn-outline-primary btn-block" href="cv.php"><i class="fas fa-file-alt mr-2"></i>Ir a mi CV</a>
    <?php tesco_alumno_tarjeta_fin(); ?>
  </div>
</div>
<?php tesco_alumno_pie(); ?>
