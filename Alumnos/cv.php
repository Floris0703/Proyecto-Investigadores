<?php
declare(strict_types=1);
require_once __DIR__.'/../includes/alumno_layout.php';
$alumno=tesco_alumno_requerir();
$uid=(int)$alumno['UsuarioId'];
$error='';
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!tesco_csrf_valid($_POST['csrf'] ?? null)) {
        http_response_code(403);
        $error='Formulario inválido. Recarga la página.';
    } else {
        try {
            tesco_alumno_agregar_cv($uid,$_POST);
            header('Location: cv.php?guardado=1',true,303);
            exit;
        } catch (InvalidArgumentException $e) {
            $error=$e->getMessage();
        } catch (Throwable $e) {
            error_log('TESCo alumnos: error al guardar CV');
            $error='No se pudo actualizar el CV. Verifica los datos y las tablas disponibles.';
        }
    }
}
$cv=tesco_alumno_cv($uid);
$niveles=tesco_alumno_niveles();
tesco_alumno_encabezado('CV y experiencia','cv.php',$alumno);
if (isset($_GET['guardado'])) tesco_alumno_alerta('Los datos de tu CV se actualizaron correctamente.','success');
if ($error!=='') tesco_alumno_alerta($error,'danger');
?>
<div class="alert alert-info">
  <i class="fas fa-user-shield mr-2"></i>
  Los profesores podrán consultar este CV <strong>al revisar una postulación que les hayas enviado</strong>. Se mostrará la versión actual de tu perfil.
</div>
<div class="row">
  <div class="col-12 col-lg-7">
    <?php tesco_alumno_tarjeta_inicio('Presentación académica','fas fa-user-graduate'); ?>
    <h3 class="h5"><?=tesco_escape(tesco_alumno_nombre($alumno))?></h3>
    <p class="mb-1"><strong>Carrera:</strong> <?=tesco_escape($alumno['Carrera'] ?: 'Pendiente')?></p>
    <p class="mb-1"><strong>Semestre:</strong> <?=tesco_escape($alumno['Semestre'] ?: 'Pendiente')?></p>
    <p class="mb-0"><?=tesco_escape($alumno['Biografia'] ?: 'Completa tu biografía en Mi perfil para presentarte ante los investigadores.')?></p>
    <a href="perfil.php" class="btn btn-outline-primary btn-sm mt-3"><i class="fas fa-edit mr-2"></i>Editar presentación</a>
    <?php tesco_alumno_tarjeta_fin(); ?>

    <?php tesco_alumno_tarjeta_inicio('Experiencia','fas fa-briefcase'); ?>
    <?php if (!$cv['experiencia_disponible']): ?>
      <p class="alert alert-warning">La tabla de experiencia todavía no está disponible. El administrador debe verificar la migración académica 004.</p>
    <?php elseif (!$cv['experiencia']): ?>
      <p class="text-muted">Aún no has registrado experiencia académica o profesional.</p>
    <?php endif; ?>
    <?php foreach($cv['experiencia'] as $exp): ?>
      <div class="tesco-cv-item border-bottom py-3">
        <div class="d-flex justify-content-between align-items-start flex-wrap">
          <div>
            <h3 class="h6 mb-1"><?=tesco_escape($exp['Cargo'])?></h3>
            <p class="mb-1"><?=tesco_escape($exp['Organizacion'])?> · <span class="text-primary"><?=tesco_escape($exp['Tipo'])?></span></p>
            <p class="small text-muted mb-1"><?=tesco_escape(tesco_alumno_fecha($exp['FechaInicio']))?> – <?= !empty($exp['EsActual']) ? 'Actualidad' : tesco_escape(tesco_alumno_fecha($exp['FechaFin']))?></p>
          </div>
          <form method="post">
            <input type="hidden" name="csrf" value="<?=tesco_escape(tesco_csrf_public())?>">
            <input type="hidden" name="accion" value="eliminar_experiencia">
            <input type="hidden" name="id" value="<?= (int)$exp['ExperienciaId'] ?>">
            <button type="submit" class="btn btn-outline-danger btn-sm" aria-label="Eliminar experiencia"><i class="fas fa-trash-alt"></i></button>
          </form>
        </div>
        <?php if ($exp['Descripcion']): ?><p class="mb-0"><?=nl2br(tesco_escape($exp['Descripcion']))?></p><?php endif; ?>
      </div>
    <?php endforeach; ?>
    <?php tesco_alumno_tarjeta_fin(); ?>

    <?php tesco_alumno_tarjeta_inicio('Formación académica','fas fa-graduation-cap'); ?>
    <?php if (!$cv['formacion_disponible']): ?>
      <div class="alert alert-warning">La tabla de formación todavía no está disponible. El administrador debe verificar la migración académica 004.</div>
    <?php elseif (!$cv['formacion']): ?>
      <p class="text-muted">Aún no has registrado estudios o formación adicional.</p>
    <?php endif; ?>
    <?php foreach($cv['formacion'] as $f): ?>
      <div class="border-bottom py-3">
        <div class="d-flex justify-content-between align-items-start flex-wrap">
          <div>
            <h3 class="h6 mb-1"><?=tesco_escape($f['Programa'])?></h3>
            <p class="mb-1"><?=tesco_escape($f['Grado'])?> · <?=tesco_escape($f['Institucion'])?></p>
            <p class="small text-muted mb-1"><?=tesco_escape(tesco_alumno_fecha($f['FechaInicio']))?> – <?=tesco_escape(tesco_alumno_fecha($f['FechaFin']))?> · <?=tesco_escape($f['Estado'])?></p>
          </div>
          <form method="post">
            <input type="hidden" name="csrf" value="<?=tesco_escape(tesco_csrf_public())?>">
            <input type="hidden" name="accion" value="eliminar_formacion">
            <input type="hidden" name="id" value="<?= (int)$f['FormacionId'] ?>">
            <button type="submit" class="btn btn-outline-danger btn-sm" aria-label="Eliminar formación"><i class="fas fa-trash-alt"></i></button>
          </form>
        </div>
        <?php if ($f['Descripcion']): ?><p class="mb-0"><?=tesco_escape($f['Descripcion'])?></p><?php endif; ?>
      </div>
    <?php endforeach; ?>
    <?php tesco_alumno_tarjeta_fin(); ?>

    <?php tesco_alumno_tarjeta_inicio('Habilidades registradas','fas fa-tools'); ?>
    <p class="text-muted small">Las habilidades que registres aparecen como <strong>declaradas</strong>. Solo Administración podrá revisarlas y validarlas.</p>
    <?php if (!$cv['habilidades']): ?>
      <p class="text-muted">Aún no has registrado habilidades. Añade una desde el formulario de esta página.</p>
    <?php else: ?>
      <div class="list-group list-group-flush">
        <?php foreach($cv['habilidades'] as $h): ?>
          <div class="list-group-item px-0">
            <div class="d-flex flex-wrap justify-content-between align-items-start">
              <div class="mb-2">
                <strong class="d-block"><?=tesco_escape($h['Habilidad'])?></strong>
                <span class="small text-muted"><?=tesco_escape($h['Nivel'])?>
                  · <?= $h['MesesExperiencia'] === null ? 'Experiencia no indicada' : (int)$h['MesesExperiencia'].' meses de experiencia' ?>
                </span>
                <span class="badge badge-<?= $h['EstadoVerificacion'] === 'VALIDADA' ? 'success' : ($h['EstadoVerificacion'] === 'RECHAZADA' ? 'danger' : 'secondary') ?> ml-2">
                  <?=tesco_escape($h['EstadoVerificacion'])?>
                </span>
              </div>
              <?php if (in_array((string)$h['EstadoVerificacion'],['DECLARADA','RECHAZADA'],true)): ?>
                <form method="post" class="ml-2">
                  <input type="hidden" name="csrf" value="<?=tesco_escape(tesco_csrf_public())?>">
                  <input type="hidden" name="accion" value="eliminar_habilidad">
                  <input type="hidden" name="habilidad_id" value="<?= (int)$h['HabilidadId'] ?>">
                  <button class="btn btn-outline-danger btn-sm" type="submit"
                          aria-label="Eliminar habilidad <?=tesco_escape($h['Habilidad'])?>">Eliminar</button>
                </form>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <?php tesco_alumno_tarjeta_fin(); ?>
  </div>
  <div class="col-12 col-lg-5">
    <?php tesco_alumno_tarjeta_inicio('Añadir o actualizar habilidad','fas fa-plus-circle'); ?>
    <p class="small text-muted">Escribe el nombre de tu habilidad. El nivel se selecciona del catálogo institucional. Si ya la declaraste, se actualizará.</p>
    <?php if (!$niveles): ?>
      <div class="alert alert-warning">No hay niveles activos en el catálogo. Solicita a Administración que configure los niveles de dominio.</div>
    <?php else: ?>
      <form method="post">
        <input type="hidden" name="csrf" value="<?=tesco_escape(tesco_csrf_public())?>">
        <input type="hidden" name="accion" value="habilidad">
        <div class="form-group">
          <label for="habilidad_nombre">Nombre de la habilidad *</label>
          <input id="habilidad_nombre" name="habilidad_nombre" maxlength="120" class="form-control"
                 placeholder="Ej. Python, análisis de datos, redacción académica" required>
        </div>
        <div class="form-group">
          <label for="nivel_dominio_id">Nivel de dominio *</label>
          <select id="nivel_dominio_id" name="nivel_dominio_id" class="custom-select" required>
            <option value="">Selecciona tu nivel</option>
            <?php foreach($niveles as $nivel): ?>
              <option value="<?= (int)$nivel['NivelDominioId'] ?>"><?=tesco_escape($nivel['Nombre'])?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="meses_experiencia">Meses de experiencia (opcional)</label>
          <input id="meses_experiencia" type="number" name="meses_experiencia" class="form-control"
                 min="0" max="1200" step="1" placeholder="Ej. 6">
        </div>
        <p class="small text-muted mb-3"><i class="fas fa-info-circle mr-1"></i>La habilidad quedará como DECLARADA y, si es nueva, pendiente de clasificación institucional.</p>
        <button class="btn btn-primary btn-block" type="submit">Guardar habilidad</button>
      </form>
    <?php endif; ?>
    <?php tesco_alumno_tarjeta_fin(); ?>
    <?php if ($cv['experiencia_disponible']): ?>
    <?php tesco_alumno_tarjeta_inicio('Añadir experiencia','fas fa-plus-circle'); ?>
    <form method="post">
      <input type="hidden" name="csrf" value="<?=tesco_escape(tesco_csrf_public())?>">
      <input type="hidden" name="accion" value="experiencia">
      <div class="form-group"><label for="tipo">Tipo de experiencia</label>
        <select class="custom-select" id="tipo" name="tipo" required>
          <option value="ACADEMICA">Académica</option>
          <option value="PROFESIONAL">Profesional</option>
          <option value="INVESTIGACION">Investigación</option>
          <option value="VOLUNTARIADO">Voluntariado</option>
          <option value="PROYECTO">Proyecto</option>
        </select>
      </div>
      <div class="form-group"><label for="cargo">Actividad o cargo *</label><input id="cargo" name="cargo" class="form-control" maxlength="150" required></div>
      <div class="form-group"><label for="organizacion">Institución u organización *</label><input id="organizacion" name="organizacion" class="form-control" maxlength="180" required></div>
      <div class="form-row">
        <div class="form-group col-sm-6"><label for="inicio-exp">Inicio</label><input id="inicio-exp" type="date" name="fecha_inicio" class="form-control"></div>
        <div class="form-group col-sm-6"><label for="fin-exp">Fin</label><input id="fin-exp" type="date" name="fecha_fin" class="form-control"></div>
      </div>
      <div class="form-group"><label><input type="checkbox" name="es_actual" value="1"> Actualmente participo</label></div>
      <div class="form-group"><label for="desc-exp">Descripción</label><textarea id="desc-exp" class="form-control" name="descripcion" rows="3" maxlength="2000"></textarea></div>
      <button class="btn btn-primary btn-block" type="submit">Guardar experiencia</button>
    </form>
    <?php tesco_alumno_tarjeta_fin(); ?>
    <?php endif; ?>
    <?php if ($cv['formacion_disponible']): ?>
    <?php tesco_alumno_tarjeta_inicio('Añadir formación','fas fa-plus-circle'); ?>
    <form method="post">
      <input type="hidden" name="csrf" value="<?=tesco_escape(tesco_csrf_public())?>">
      <input type="hidden" name="accion" value="formacion">
      <div class="form-group"><label for="grado">Grado o tipo de formación *</label><input id="grado" name="grado" maxlength="80" class="form-control" required></div>
      <div class="form-group"><label for="programa">Programa o curso *</label><input id="programa" name="programa" maxlength="160" class="form-control" required></div>
      <div class="form-group"><label for="institucion">Institución *</label><input id="institucion" name="institucion" maxlength="180" class="form-control" required></div>
      <div class="form-row">
        <div class="form-group col-sm-6"><label for="inicio-form">Inicio</label><input id="inicio-form" type="date" name="fecha_inicio" class="form-control"></div>
        <div class="form-group col-sm-6"><label for="fin-form">Fin</label><input id="fin-form" type="date" name="fecha_fin" class="form-control"></div>
      </div>
      <div class="form-group"><label for="estado">Estado</label>
        <select class="custom-select" id="estado" name="estado"><option value="EN_CURSO">En curso</option><option value="CONCLUIDA">Concluida</option></select>
      </div>
      <div class="form-group"><label for="desc-form">Descripción breve</label><textarea id="desc-form" name="descripcion" maxlength="500" rows="2" class="form-control"></textarea></div>
      <button class="btn btn-primary btn-block" type="submit">Guardar formación</button>
    </form>
    <?php tesco_alumno_tarjeta_fin(); ?>
    <?php endif; ?>
  </div>
</div>
<?php tesco_alumno_pie(); ?>
