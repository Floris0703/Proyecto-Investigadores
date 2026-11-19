<?php
/** Datos y acciones del módulo Alumnos. SQLSRV, parámetros y autorización. */
declare(strict_types=1);
require_once __DIR__ . '/tesco_auth.php';

function tesco_alumno_requerir(): array {
    if (!tesco_usuario_actual()) {
        header('Location: ../Landing/login.php', true, 303);
        exit;
    }
    $roles = tesco_roles_actuales();
    if (!in_array('ALUMNO', $roles, true) || ($_SESSION['tesco_rol_activo'] ?? null) !== 'ALUMNO') {
        http_response_code(403);
        exit('Acceso restringido al rol ALUMNO. Regresa a la bienvenida y selecciona tu rol autorizado.');
    }
    $row = tesco_db_one(
        "SELECT a.AlumnoId,a.UsuarioId,a.Matricula,a.Carrera,a.Semestre,
                u.Correo,p.Nombre,p.ApellidoPaterno,p.ApellidoMaterno,p.Biografia
         FROM academico.Alumno a
         JOIN plataforma.Usuario u ON u.UsuarioId=a.UsuarioId
         LEFT JOIN plataforma.PerfilUsuario p ON p.UsuarioId=a.UsuarioId
         WHERE a.UsuarioId=? AND UPPER(LTRIM(RTRIM(u.EstadoCuenta))) IN ('ACTIVO','ACTIVA')",
        [tesco_usuario_actual()]
    );
    if (!$row) {
        http_response_code(403);
        exit('Tu cuenta tiene rol ALUMNO, pero requiere un registro en academico.Alumno. Contacta al administrador.');
    }
    header('Cache-Control: private, no-store');
    return $row;
}

function tesco_alumno_nombre(array $alumno): string {
    $nombre = trim(implode(' ', array_filter([
        $alumno['Nombre'] ?? '', $alumno['ApellidoPaterno'] ?? '', $alumno['ApellidoMaterno'] ?? ''
    ], static fn($v) => is_string($v) && trim($v) !== '')));
    return $nombre !== '' ? $nombre : (string)($alumno['Correo'] ?? 'Alumno');
}

function tesco_alumno_existe_tabla(string $nombre): bool {
    // Solo identificadores fijos del código; el usuario nunca elige la tabla.
    if (!in_array($nombre, ['academico.FormacionAcademica','academico.ExperienciaAcademica','academico.Reconocimiento'], true)) {
        return false;
    }
    $row = tesco_db_one("SELECT CASE WHEN OBJECT_ID(?, 'U') IS NOT NULL THEN 1 ELSE 0 END AS Disponible", [$nombre]);
    return (int)($row['Disponible'] ?? 0) === 1;
}

function tesco_alumno_cv(int $usuarioId): array {
    $formacionOk = tesco_alumno_existe_tabla('academico.FormacionAcademica');
    $experienciaOk = tesco_alumno_existe_tabla('academico.ExperienciaAcademica');
    $formacion = $formacionOk ? tesco_db_rows(
        "SELECT FormacionId,Grado,Programa,Institucion,FechaInicio,FechaFin,Estado,Descripcion
         FROM academico.FormacionAcademica WHERE UsuarioId=? ORDER BY FechaInicio DESC,FormacionId DESC",
        [$usuarioId]
    ) : [];
    $experiencia = $experienciaOk ? tesco_db_rows(
        "SELECT ExperienciaId,Tipo,Cargo,Organizacion,Descripcion,FechaInicio,FechaFin,EsActual
         FROM academico.ExperienciaAcademica WHERE UsuarioId=? ORDER BY FechaInicio DESC,ExperienciaId DESC",
        [$usuarioId]
    ) : [];
    $habilidades = tesco_db_rows(
        "SELECT h.HabilidadId,h.Nombre AS Habilidad,n.Nombre AS Nivel,uh.MesesExperiencia,uh.EstadoVerificacion
         FROM academico.UsuarioHabilidad uh
         JOIN academico.Habilidad h ON h.HabilidadId=uh.HabilidadId
         JOIN academico.NivelDominio n ON n.NivelDominioId=uh.NivelDominioId
         WHERE uh.UsuarioId=? ORDER BY h.Nombre",
        [$usuarioId]
    );
    return ['formacion'=>$formacion,'experiencia'=>$experiencia,'habilidades'=>$habilidades,
            'formacion_disponible'=>$formacionOk,'experiencia_disponible'=>$experienciaOk];
}

/** Las opciones de nivel proceden del catálogo institucional, no del formulario. */
function tesco_alumno_niveles(): array {
    return tesco_db_rows(
        'SELECT NivelDominioId,Nombre FROM academico.NivelDominio WHERE Activo=1 ORDER BY Orden,NivelDominioId'
    );
}

/** Altas y cambios de habilidades propias; la verificación queda reservada para Admin. */
function tesco_alumno_guardar_habilidad(int $uid, array $post): void {
    $accion = (string)($post['accion'] ?? '');
    if ($accion === 'eliminar_habilidad') {
        $id = filter_var($post['habilidad_id'] ?? null,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
        if (!$id) throw new InvalidArgumentException('Habilidad inválida.');
        $affected = tesco_alumno_exec(
            "DELETE FROM academico.UsuarioHabilidad
             WHERE UsuarioId=? AND HabilidadId=? AND EstadoVerificacion IN ('DECLARADA','RECHAZADA')",
            [$uid,(int)$id]
        );
        if ($affected !== 1) throw new InvalidArgumentException('La habilidad no existe o está bajo revisión institucional.');
        return;
    }
    if ($accion !== 'habilidad') throw new InvalidArgumentException('Acción no permitida.');
    $nombre = tesco_alumno_campo($post,'habilidad_nombre',120,true);
    // Normalización básica; no se permite crear un nombre en blanco o compuesto solo por espacios.
    $nombre = trim((string)preg_replace('/\\s+/u',' ',$nombre));
    if ($nombre === '' || (function_exists('mb_strlen') ? mb_strlen($nombre,'UTF-8') : strlen($nombre)) > 120) {
        throw new InvalidArgumentException('Ingresa un nombre de habilidad válido (máximo 120 caracteres).');
    }
    $nivel = filter_var($post['nivel_dominio_id'] ?? null,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
    if (!$nivel) throw new InvalidArgumentException('Selecciona un nivel institucional válido.');
    $mesesTexto = trim((string)($post['meses_experiencia'] ?? ''));
    if ($mesesTexto !== '' && (!ctype_digit($mesesTexto) || (int)$mesesTexto > 1200)) {
        throw new InvalidArgumentException('Los meses de experiencia deben estar entre 0 y 1200.');
    }
    $meses = $mesesTexto === '' ? null : (int)$mesesTexto;
    $conn = tesco_db();
    if (!sqlsrv_begin_transaction($conn)) throw new RuntimeException('No se pudo iniciar la transacción.');
    try {
        $valido = tesco_db_one(
            'SELECT NivelDominioId FROM academico.NivelDominio WHERE NivelDominioId=? AND Activo=1',
            [(int)$nivel]
        );
        if (!$valido) throw new InvalidArgumentException('El nivel seleccionado no está habilitado.');
        // Reutilizar una habilidad existente aunque esté en otra categoría.
        $habilidad = tesco_db_one(
            "SELECT TOP (1) h.HabilidadId
             FROM academico.Habilidad h WITH (UPDLOCK,HOLDLOCK)
             JOIN academico.CategoriaHabilidad c ON c.CategoriaId=h.CategoriaId
             WHERE UPPER(LTRIM(RTRIM(h.Nombre)))=UPPER(?)
               AND h.Activo=1
             ORDER BY CASE WHEN c.Nombre=N'Pendiente de clasificación' THEN 1 ELSE 0 END,
                      h.HabilidadId",
            [$nombre]
        );
        if (!$habilidad) {
            // Categoría provisional: clasificación y validación se harán en el módulo Admin.
            $categoria = tesco_db_one(
                "SELECT CategoriaId FROM academico.CategoriaHabilidad WITH (UPDLOCK,HOLDLOCK)
                 WHERE Nombre=N'Pendiente de clasificación'",
            );
            if (!$categoria) {
                tesco_alumno_exec(
                    "INSERT INTO academico.CategoriaHabilidad (Nombre,Descripcion,Activo)
                     VALUES (N'Pendiente de clasificación',
                             N'Habilidades declaradas por alumnos, pendientes de revisión institucional.',1)"
                );
                $categoria = tesco_db_one(
                    "SELECT CategoriaId FROM academico.CategoriaHabilidad
                     WHERE Nombre=N'Pendiente de clasificación'"
                );
            }
            if (!$categoria) throw new RuntimeException('No se pudo preparar la categoría.');
            tesco_alumno_exec(
                'INSERT INTO academico.Habilidad (CategoriaId,Nombre,Activo) VALUES (?,?,1)',
                [(int)$categoria['CategoriaId'],$nombre]
            );
            $habilidad = tesco_db_one(
                "SELECT TOP (1) HabilidadId FROM academico.Habilidad
                 WHERE CategoriaId=? AND UPPER(LTRIM(RTRIM(Nombre)))=UPPER(?)
                 ORDER BY HabilidadId DESC",
                [(int)$categoria['CategoriaId'],$nombre]
            );
        }
        if (!$habilidad) throw new RuntimeException('No se pudo registrar la habilidad.');
        $hid = (int)$habilidad['HabilidadId'];
        $existente = tesco_db_one(
            'SELECT EstadoVerificacion FROM academico.UsuarioHabilidad WITH (UPDLOCK,HOLDLOCK) WHERE UsuarioId=? AND HabilidadId=?',
            [$uid,$hid]
        );
        if ($existente) {
            // No permitir que una edición estudiantil borre una revisión/verificación realizada por la institución.
            if (!in_array((string)$existente['EstadoVerificacion'], ['DECLARADA','RECHAZADA'],true)) {
                throw new InvalidArgumentException('Esta habilidad está en revisión o validada; solicita un cambio a Administración.');
            }
            tesco_alumno_exec(
                "UPDATE academico.UsuarioHabilidad
                 SET NivelDominioId=?,MesesExperiencia=?,EstadoVerificacion='DECLARADA',
                     VerificadoPorUsuarioId=NULL,FechaVerificacion=NULL,
                     EvidenciaReferencia=NULL,FechaActualizacion=SYSUTCDATETIME()
                 WHERE UsuarioId=? AND HabilidadId=?",
                [(int)$nivel,$meses,$uid,$hid]
            );
        } else {
            tesco_alumno_exec(
                "INSERT INTO academico.UsuarioHabilidad
                 (UsuarioId,HabilidadId,NivelDominioId,MesesExperiencia,EstadoVerificacion)
                 VALUES (?,?,?,?,'DECLARADA')",
                [$uid,$hid,(int)$nivel,$meses]
            );
        }
        if (!sqlsrv_commit($conn)) throw new RuntimeException('No se pudo confirmar la habilidad.');
    } catch (Throwable $e) {
        sqlsrv_rollback($conn);
        throw $e;
    }
}

function tesco_alumno_fecha(mixed $fecha): string {
    if ($fecha instanceof DateTimeInterface) return $fecha->format('d/m/Y');
    if (!$fecha) return 'Sin especificar';
    try { return (new DateTimeImmutable((string)$fecha))->format('d/m/Y'); }
    catch (Throwable) { return 'No disponible'; }
}
function tesco_alumno_validar_fecha(string $value): ?string {
    if ($value === '') return null;
    $f = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
    if (!$f || $f->format('Y-m-d') !== $value) throw new InvalidArgumentException('Fecha inválida.');
    return $value;
}
function tesco_alumno_campo(array $data, string $name, int $max, bool $required = false): string {
    $value = trim((string)($data[$name] ?? ''));
    $long = function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
    if (($required && $value === '') || $long > $max) throw new InvalidArgumentException('Revisa el campo: '.$name.'.');
    return $value;
}
function tesco_alumno_exec(string $sql, array $params = []): int {
    $stmt = @sqlsrv_query(tesco_db(), $sql, $params);
    if ($stmt === false) {
        error_log('TESCo alumnos: error en escritura SQLSRV');
        throw new RuntimeException('No se pudo guardar la información.');
    }
    $rows = sqlsrv_rows_affected($stmt);
    sqlsrv_free_stmt($stmt);
    return is_int($rows) ? $rows : 0;
}

function tesco_alumno_guardar_perfil(array $alumno, array $post): void {
    $uid = (int)$alumno['UsuarioId'];
    $nombre = tesco_alumno_campo($post, 'nombre', 100, true);
    $apellido = tesco_alumno_campo($post, 'apellido_paterno', 100, true);
    $materno = tesco_alumno_campo($post, 'apellido_materno', 100);
    $bio = tesco_alumno_campo($post, 'biografia', 700);
    $carrera = tesco_alumno_campo($post, 'carrera', 150);
    $semestre = trim((string)($post['semestre'] ?? ''));
    if ($semestre !== '' && (!ctype_digit($semestre) || (int)$semestre < 1 || (int)$semestre > 14)) {
        throw new InvalidArgumentException('El semestre debe estar entre 1 y 14.');
    }
    $conn = tesco_db();
    if (!sqlsrv_begin_transaction($conn)) throw new RuntimeException('No se pudo iniciar la transacción.');
    try {
        tesco_alumno_exec(
            "UPDATE plataforma.PerfilUsuario
             SET Nombre=?,ApellidoPaterno=?,ApellidoMaterno=?,Biografia=?,FechaActualizacion=SYSUTCDATETIME()
             WHERE UsuarioId=?",
            [$nombre,$apellido,$materno ?: null,$bio ?: null,$uid]
        );
        if (tesco_db_one('SELECT UsuarioId FROM plataforma.PerfilUsuario WHERE UsuarioId=?', [$uid]) === null) {
            tesco_alumno_exec(
                "INSERT INTO plataforma.PerfilUsuario (UsuarioId,Nombre,ApellidoPaterno,ApellidoMaterno,Biografia)
                 VALUES (?,?,?,?,?)",
                [$uid,$nombre,$apellido,$materno ?: null,$bio ?: null]
            );
        }
        tesco_alumno_exec(
            'UPDATE academico.Alumno SET Carrera=?,Semestre=? WHERE UsuarioId=?',
            [$carrera ?: null,$semestre !== '' ? (int)$semestre : null,$uid]
        );
        if (!sqlsrv_commit($conn)) throw new RuntimeException('No se pudo confirmar el perfil.');
    } catch (Throwable $e) {
        sqlsrv_rollback($conn);
        throw $e;
    }
}

function tesco_alumno_agregar_cv(int $uid, array $post): void {
    $accion = (string)($post['accion'] ?? '');
    if (in_array($accion, ['habilidad','eliminar_habilidad'],true)) {
        tesco_alumno_guardar_habilidad($uid,$post);
        return;
    }
    if ($accion === 'experiencia') {
        if (!tesco_alumno_existe_tabla('academico.ExperienciaAcademica')) {
            throw new RuntimeException('Falta la tabla de experiencia de la migración 004.');
        }
        $tipo = (string)($post['tipo'] ?? '');
        if (!in_array($tipo, ['ACADEMICA','PROFESIONAL','INVESTIGACION','VOLUNTARIADO','PROYECTO'], true)) {
            throw new InvalidArgumentException('Selecciona un tipo de experiencia válido.');
        }
        $cargo = tesco_alumno_campo($post,'cargo',150,true);
        $organizacion = tesco_alumno_campo($post,'organizacion',180,true);
        $desc = tesco_alumno_campo($post,'descripcion',2000);
        $inicio = tesco_alumno_validar_fecha((string)($post['fecha_inicio'] ?? ''));
        $fin = tesco_alumno_validar_fecha((string)($post['fecha_fin'] ?? ''));
        $actual = ($post['es_actual'] ?? '') === '1';
        if (!$actual && $inicio !== null && $fin !== null && $fin < $inicio) {
            throw new InvalidArgumentException('La fecha de fin no puede ser anterior al inicio.');
        }
        tesco_alumno_exec(
            'INSERT INTO academico.ExperienciaAcademica (UsuarioId,Tipo,Cargo,Organizacion,Descripcion,FechaInicio,FechaFin,EsActual)
             VALUES (?,?,?,?,?,?,?,?)',
            [$uid,$tipo,$cargo,$organizacion,$desc ?: null,$inicio,$actual ? null : $fin,$actual ? 1 : 0]
        );
    } elseif ($accion === 'formacion') {
        if (!tesco_alumno_existe_tabla('academico.FormacionAcademica')) {
            throw new RuntimeException('Falta la tabla de formación de la migración 004.');
        }
        $grado = tesco_alumno_campo($post,'grado',80,true);
        $programa = tesco_alumno_campo($post,'programa',160,true);
        $institucion = tesco_alumno_campo($post,'institucion',180,true);
        $desc = tesco_alumno_campo($post,'descripcion',500);
        $inicio = tesco_alumno_validar_fecha((string)($post['fecha_inicio'] ?? ''));
        $fin = tesco_alumno_validar_fecha((string)($post['fecha_fin'] ?? ''));
        $estado = ($post['estado'] ?? '') === 'CONCLUIDA' ? 'CONCLUIDA' : 'EN_CURSO';
        if ($inicio !== null && $fin !== null && $fin < $inicio) throw new InvalidArgumentException('Fechas inconsistentes.');
        tesco_alumno_exec(
            'INSERT INTO academico.FormacionAcademica (UsuarioId,Grado,Programa,Institucion,FechaInicio,FechaFin,Estado,Descripcion)
             VALUES (?,?,?,?,?,?,?,?)',
            [$uid,$grado,$programa,$institucion,$inicio,$fin,$estado,$desc ?: null]
        );
    } elseif ($accion === 'eliminar_experiencia' || $accion === 'eliminar_formacion') {
        $id = filter_var($post['id'] ?? null,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
        if (!$id) throw new InvalidArgumentException('Registro inválido.');
        $table = $accion === 'eliminar_experiencia' ? 'academico.ExperienciaAcademica' : 'academico.FormacionAcademica';
        $key = $accion === 'eliminar_experiencia' ? 'ExperienciaId' : 'FormacionId';
        if (!tesco_alumno_existe_tabla($table)) throw new RuntimeException('Tabla aún no disponible.');
        tesco_alumno_exec("DELETE FROM $table WHERE $key=? AND UsuarioId=?",[(int)$id,$uid]);
    } else {
        throw new InvalidArgumentException('Acción no permitida.');
    }
}

function tesco_alumno_convocatorias(int $uid): array {
    return tesco_db_rows(
        "SELECT TOP (40) c.ConvocatoriaId,c.Titulo,c.Descripcion,c.FechaPublicacion,c.FechaCierre,
                COALESCE(NULLIF(LTRIM(RTRIM(CONCAT(p.Nombre,' ',p.ApellidoPaterno))),''),u.Correo) AS Profesor,
                po.PostulacionId AS MiPostulacionId
         FROM academico.Convocatoria c
         JOIN academico.Profesor pr ON pr.UsuarioId=c.UsuarioPublicadorId
         JOIN plataforma.Usuario u ON u.UsuarioId=c.UsuarioPublicadorId
         LEFT JOIN plataforma.PerfilUsuario p ON p.UsuarioId=c.UsuarioPublicadorId
         LEFT JOIN academico.Postulacion po ON po.ConvocatoriaId=c.ConvocatoriaId AND po.UsuarioId=?
         WHERE UPPER(LTRIM(RTRIM(c.Estado))) IN ('PUBLICADA','ABIERTA')
           AND c.FechaPublicacion IS NOT NULL AND c.FechaPublicacion <= SYSDATETIME()
           AND (c.FechaCierre IS NULL OR c.FechaCierre >= SYSDATETIME())
         ORDER BY c.FechaPublicacion DESC,c.ConvocatoriaId DESC",
        [$uid]
    );
}
function tesco_alumno_postulaciones(int $uid): array {
    return tesco_db_rows(
        "SELECT p.PostulacionId,p.Estado,p.FechaPostulacion,c.Titulo,c.ConvocatoriaId,
                c.FechaCierre
         FROM academico.Postulacion p
         JOIN academico.Convocatoria c ON c.ConvocatoriaId=p.ConvocatoriaId
         WHERE p.UsuarioId=? ORDER BY p.FechaPostulacion DESC,p.PostulacionId DESC",
        [$uid]
    );
}
function tesco_alumno_postular(int $uid, int $convocatoriaId): void {
    if ($convocatoriaId < 1) throw new InvalidArgumentException('Selecciona una convocatoria válida.');
    $affected = tesco_alumno_exec(
        "INSERT INTO academico.Postulacion (ConvocatoriaId,UsuarioId,Estado)
         SELECT c.ConvocatoriaId,?,'PENDIENTE'
         FROM academico.Convocatoria c
         JOIN academico.Profesor pr ON pr.UsuarioId=c.UsuarioPublicadorId
         WHERE c.ConvocatoriaId=?
           AND UPPER(LTRIM(RTRIM(c.Estado))) IN ('PUBLICADA','ABIERTA')
           AND c.FechaPublicacion IS NOT NULL AND c.FechaPublicacion <= SYSDATETIME()
           AND (c.FechaCierre IS NULL OR c.FechaCierre >= SYSDATETIME())
           AND NOT EXISTS (SELECT 1 FROM academico.Postulacion p WITH (UPDLOCK,HOLDLOCK)
                           WHERE p.ConvocatoriaId=c.ConvocatoriaId AND p.UsuarioId=?)",
        [$uid,$convocatoriaId,$uid]
    );
    if ($affected !== 1) throw new InvalidArgumentException('La convocatoria ya fue solicitada o no está vigente.');
}

function tesco_alumno_cv_para_profesor(int $postulacionId,int $profesorUid): ?array {
    return tesco_db_one(
        "SELECT po.PostulacionId,po.Estado AS EstadoPostulacion,c.Titulo AS Convocatoria,
                a.UsuarioId,a.Matricula,a.Carrera,a.Semestre,u.Correo,
                pf.Nombre,pf.ApellidoPaterno,pf.ApellidoMaterno,pf.Biografia
         FROM academico.Postulacion po
         JOIN academico.Convocatoria c ON c.ConvocatoriaId=po.ConvocatoriaId
         JOIN academico.Alumno a ON a.UsuarioId=po.UsuarioId
         JOIN plataforma.Usuario u ON u.UsuarioId=a.UsuarioId
         LEFT JOIN plataforma.PerfilUsuario pf ON pf.UsuarioId=a.UsuarioId
         WHERE po.PostulacionId=? AND c.UsuarioPublicadorId=?",
        [$postulacionId,$profesorUid]
    );
}
