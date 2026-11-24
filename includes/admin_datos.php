<?php
/** TESCo Investigadores | Administración institucional. PHP 8.2 + SQLSRV. */
declare(strict_types=1);
require_once __DIR__ . '/tesco_auth.php';

function tesco_admin_actual(): array {
    if (!tesco_usuario_actual()) {
        header('Location: ../Landing/login.php', true, 303);
        exit;
    }
    $roles=tesco_roles_actuales();
    if (!in_array('ADMIN',$roles,true) || ($_SESSION['tesco_rol_activo'] ?? null)!=='ADMIN') {
        http_response_code(403);
        exit('Acceso exclusivo para el rol ADMIN activo.');
    }
    $row=tesco_db_one(
        "SELECT ad.AdministrativoId,ad.UsuarioId,ad.NumeroEmpleado,ad.AreaAdscripcion,ad.Cargo,
                u.Correo,p.Nombre,p.ApellidoPaterno,p.ApellidoMaterno,p.Biografia
         FROM plataforma.Administrativo ad
         JOIN plataforma.Usuario u ON u.UsuarioId=ad.UsuarioId
         LEFT JOIN plataforma.PerfilUsuario p ON p.UsuarioId=u.UsuarioId
         WHERE ad.UsuarioId=? AND UPPER(LTRIM(RTRIM(u.EstadoCuenta))) IN ('ACTIVO','ACTIVA')",
        [tesco_usuario_actual()]
    );
    if (!$row) {
        http_response_code(403);
        exit('La cuenta ADMIN necesita un registro en plataforma.Administrativo. Contacta al responsable de la base de datos.');
    }
    header('Cache-Control: private, no-store');
    return $row;
}
function tesco_admin_nombre(array $user): string {
    $nombre=trim(implode(' ',array_filter([
        $user['Nombre']??'',$user['ApellidoPaterno']??'',$user['ApellidoMaterno']??''
    ], static fn($s)=>is_string($s)&&trim($s)!=='')));
    return $nombre!==''?$nombre:(string)($user['Correo']??'Administración');
}
function tesco_admin_campo(array $post,string $campo,int $max,bool $required=false): string {
    $v=trim((string)($post[$campo]??''));
    $n=function_exists('mb_strlen')?mb_strlen($v,'UTF-8'):strlen($v);
    if (($required&&$v==='')||$n>$max) throw new InvalidArgumentException('Revisa el campo: '.$campo.'.');
    return $v;
}
function tesco_admin_id(mixed $v): int {
    $id=filter_var($v,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
    if (!$id) throw new InvalidArgumentException('Identificador inválido.');
    return (int)$id;
}
function tesco_admin_exec(string $sql,array $params=[]): int {
    $stmt=@sqlsrv_query(tesco_db(),$sql,$params);
    if ($stmt===false) {
        error_log('TESCo Administración: escritura SQLSRV fallida');
        throw new RuntimeException('No se pudo guardar el cambio en SQL Server.');
    }
    $n=sqlsrv_rows_affected($stmt);
    sqlsrv_free_stmt($stmt);
    return is_int($n)?$n:0;
}
function tesco_admin_tx(callable $fn): mixed {
    $conn=tesco_db();
    if (!sqlsrv_begin_transaction($conn)) throw new RuntimeException('No fue posible abrir una transacción.');
    try {
        $r=$fn();
        if (!sqlsrv_commit($conn)) throw new RuntimeException('No fue posible confirmar la transacción.');
        return $r;
    } catch (Throwable $e) {
        sqlsrv_rollback($conn);
        throw $e;
    }
}
function tesco_admin_fecha(mixed $v): string {
    if ($v instanceof DateTimeInterface) return $v->format('d/m/Y');
    if (!$v) return 'Sin fecha';
    try {return (new DateTimeImmutable((string)$v))->format('d/m/Y');}
    catch (Throwable) {return 'Sin fecha';}
}
function tesco_admin_resumen(): array {
    $sql="SELECT
      (SELECT COUNT(*) FROM plataforma.Usuario) AS Usuarios,
      (SELECT COUNT(*) FROM academico.Profesor) AS Profesores,
      (SELECT COUNT(*) FROM academico.Alumno) AS Alumnos,
      (SELECT COUNT(*) FROM academico.Proyecto) AS Proyectos,
      (SELECT COUNT(*) FROM academico.Publicacion) AS Publicaciones,
      (SELECT COUNT(*) FROM academico.Congreso) AS Congresos,
      (SELECT COUNT(*) FROM academico.Convocatoria) AS Convocatorias,
      (SELECT COUNT(*) FROM academico.Postulacion) AS Postulaciones,
      (SELECT COUNT(*) FROM academico.UsuarioHabilidad WHERE EstadoVerificacion IN ('DECLARADA','EN_REVISION')) AS HabilidadesPendientes";
    return tesco_db_one($sql)??[];
}
function tesco_admin_usuarios(string $filtro=''): array {
    return tesco_db_rows(
        "SELECT TOP (100) u.UsuarioId,u.Correo,u.EstadoCuenta,u.FechaRegistro,
           p.Nombre,p.ApellidoPaterno,al.Matricula,pr.NumeroEmpleado,
           CASE WHEN EXISTS (SELECT 1 FROM plataforma.UsuarioRol ur JOIN plataforma.Rol r ON r.RolId=ur.RolId
                       WHERE ur.UsuarioId=u.UsuarioId AND r.Nombre='ADMIN') THEN 1 ELSE 0 END AS EsAdmin,
           STUFF((SELECT ', '+r.Nombre FROM plataforma.UsuarioRol ur JOIN plataforma.Rol r ON r.RolId=ur.RolId
                  WHERE ur.UsuarioId=u.UsuarioId ORDER BY r.Nombre FOR XML PATH(''),TYPE).value('.','nvarchar(max)'),1,2,'') AS Roles
         FROM plataforma.Usuario u
         LEFT JOIN plataforma.PerfilUsuario p ON p.UsuarioId=u.UsuarioId
         LEFT JOIN academico.Alumno al ON al.UsuarioId=u.UsuarioId
         LEFT JOIN academico.Profesor pr ON pr.UsuarioId=u.UsuarioId
         WHERE (?='' OR u.Correo LIKE ? OR p.Nombre LIKE ? OR al.Matricula LIKE ? OR pr.NumeroEmpleado LIKE ?)
         ORDER BY u.UsuarioId DESC",
        [$filtro,'%'.$filtro.'%','%'.$filtro.'%','%'.$filtro.'%','%'.$filtro.'%']
    );
}
function tesco_admin_alta_usuario(array $post): void {
    $correo=tesco_admin_campo($post,'correo',255,true);
    if (!filter_var($correo,FILTER_VALIDATE_EMAIL)) throw new InvalidArgumentException('Ingresa un correo válido.');
    $clave=(string)($post['password']??'');
    if (strlen($clave)<12||strlen($clave)>200) throw new InvalidArgumentException('La contraseña inicial debe tener entre 12 y 200 caracteres.');
    $nombre=tesco_admin_campo($post,'nombre',100,true);
    $paterno=tesco_admin_campo($post,'apellido',100,true);
    $rol=(string)($post['rol']??'');
    if (!in_array($rol,['ALUMNO','PROFESOR'],true)) throw new InvalidArgumentException('Solo se permiten altas de ALUMNO o PROFESOR.');
    $referencia=tesco_admin_campo($post,'referencia',30,true);
    $carrera=tesco_admin_campo($post,'carrera',150);
    $semestre=trim((string)($post['semestre']??''));
    if ($rol==='ALUMNO'&&$semestre!==''&&(!ctype_digit($semestre)||(int)$semestre<1||(int)$semestre>14))
        throw new InvalidArgumentException('El semestre debe estar entre 1 y 14.');
    $depto=tesco_admin_campo($post,'departamento',120);
    $hash=password_hash($clave,PASSWORD_DEFAULT);
    if (!is_string($hash)) throw new RuntimeException('No fue posible proteger la contraseña.');
    tesco_admin_tx(static function() use($correo,$hash,$nombre,$paterno,$rol,$referencia,$carrera,$semestre,$depto): void {
        $r=tesco_db_one('SELECT RolId FROM plataforma.Rol WHERE Nombre=?',[$rol]);
        if (!$r) throw new RuntimeException('No existe el rol institucional: '.$rol);
        $user=tesco_db_one(
            'INSERT INTO plataforma.Usuario (Correo,PasswordHash,EstadoCuenta) OUTPUT INSERTED.UsuarioId VALUES (?,?,?)',
            [$correo,$hash,'ACTIVO']
        );
        if (!$user) throw new RuntimeException('No se recibió el identificador del usuario.');
        $id=(int)$user['UsuarioId'];
        tesco_admin_exec('INSERT INTO plataforma.UsuarioRol (UsuarioId,RolId) VALUES (?,?)',[$id,(int)$r['RolId']]);
        tesco_admin_exec(
            'INSERT INTO plataforma.PerfilUsuario (UsuarioId,Nombre,ApellidoPaterno) VALUES (?,?,?)',
            [$id,$nombre,$paterno]
        );
        if ($rol==='ALUMNO') tesco_admin_exec(
            'INSERT INTO academico.Alumno (UsuarioId,Matricula,Carrera,Semestre) VALUES (?,?,?,?)',
            [$id,$referencia,$carrera?:null,$semestre!==''?(int)$semestre:null]
        );
        else tesco_admin_exec(
            'INSERT INTO academico.Profesor (UsuarioId,NumeroEmpleado,Departamento) VALUES (?,?,?)',
            [$id,$referencia,$depto?:null]
        );
    });
}
function tesco_admin_estado_usuario(array $admin,array $post): void {
    $id=tesco_admin_id($post['usuario_id']??null);
    if ($id===(int)$admin['UsuarioId']) throw new InvalidArgumentException('No puedes desactivar tu propia cuenta.');
    $estado=(string)($post['estado']??'');
    if (!in_array($estado,['ACTIVO','INACTIVO'],true)) throw new InvalidArgumentException('Estado de cuenta inválido.');
    $updated=tesco_admin_exec(
        "UPDATE plataforma.Usuario SET EstadoCuenta=? WHERE UsuarioId=? AND NOT EXISTS
         (SELECT 1 FROM plataforma.UsuarioRol ur JOIN plataforma.Rol r ON r.RolId=ur.RolId
          WHERE ur.UsuarioId=plataforma.Usuario.UsuarioId AND r.Nombre='ADMIN')",
        [$estado,$id]
    );
    if ($updated!==1) throw new InvalidArgumentException('No se permite modificar esa cuenta.');
}
function tesco_admin_asignar_rol(array $post): void {
    $uid=tesco_admin_id($post['usuario_id']??null);
    $rol=(string)($post['rol']??'');
    $codigo=tesco_admin_campo($post,'referencia',30,true);
    if (!in_array($rol,['ALUMNO','PROFESOR'],true)) throw new InvalidArgumentException('Rol no permitido.');
    tesco_admin_tx(static function()use($uid,$rol,$codigo): void {
        $u=tesco_db_one('SELECT UsuarioId FROM plataforma.Usuario WITH (UPDLOCK,HOLDLOCK) WHERE UsuarioId=?',[$uid]);
        if (!$u) throw new InvalidArgumentException('Usuario inexistente.');
        $r=tesco_db_one('SELECT RolId FROM plataforma.Rol WHERE Nombre=?',[$rol]);
        if (!$r) throw new RuntimeException('Falta el rol en el catálogo.');
        $existente=tesco_db_one(
            'SELECT UsuarioId FROM plataforma.UsuarioRol WHERE UsuarioId=? AND RolId=?',
            [$uid,(int)$r['RolId']]
        );
        if ($existente) throw new InvalidArgumentException('El usuario ya tiene ese rol.');
        if ($rol==='ALUMNO') {
            $al=tesco_db_one('SELECT AlumnoId FROM academico.Alumno WHERE UsuarioId=?',[$uid]);
            if (!$al) tesco_admin_exec('INSERT INTO academico.Alumno (UsuarioId,Matricula) VALUES (?,?)',[$uid,$codigo]);
        } else {
            $pr=tesco_db_one('SELECT ProfesorId FROM academico.Profesor WHERE UsuarioId=?',[$uid]);
            if (!$pr) tesco_admin_exec('INSERT INTO academico.Profesor (UsuarioId,NumeroEmpleado) VALUES (?,?)',[$uid,$codigo]);
        }
        tesco_admin_exec('INSERT INTO plataforma.UsuarioRol (UsuarioId,RolId) VALUES (?,?)',[$uid,(int)$r['RolId']]);
    });
}
function tesco_admin_perfil(array $admin,array $post): void {
    $nombre=tesco_admin_campo($post,'nombre',100,true);
    $apellido=tesco_admin_campo($post,'apellido',100,true);
    $materno=tesco_admin_campo($post,'materno',100);
    $bio=tesco_admin_campo($post,'bio',700);
    $area=tesco_admin_campo($post,'area',120);
    $cargo=tesco_admin_campo($post,'cargo',100);
    $uid=(int)$admin['UsuarioId'];
    tesco_admin_tx(static function()use($nombre,$apellido,$materno,$bio,$area,$cargo,$uid): void {
        $existe=tesco_db_one('SELECT UsuarioId FROM plataforma.PerfilUsuario WHERE UsuarioId=?',[$uid]);
        if ($existe) tesco_admin_exec(
            'UPDATE plataforma.PerfilUsuario SET Nombre=?,ApellidoPaterno=?,ApellidoMaterno=?,Biografia=?,FechaActualizacion=SYSUTCDATETIME() WHERE UsuarioId=?',
            [$nombre,$apellido,$materno?:null,$bio?:null,$uid]
        );
        else tesco_admin_exec(
            'INSERT INTO plataforma.PerfilUsuario (UsuarioId,Nombre,ApellidoPaterno,ApellidoMaterno,Biografia) VALUES (?,?,?,?,?)',
            [$uid,$nombre,$apellido,$materno?:null,$bio?:null]
        );
        tesco_admin_exec('UPDATE plataforma.Administrativo SET AreaAdscripcion=?,Cargo=? WHERE UsuarioId=?',
            [$area?:null,$cargo?:null,$uid]);
    });
}
function tesco_admin_convocatorias(string $buscar='',string $estado=''): array {
    return tesco_db_rows(
        "SELECT TOP (100) c.ConvocatoriaId,c.Titulo,c.Estado,c.FechaPublicacion,c.FechaCierre,
                u.Correo AS Publicador,
                COALESCE(NULLIF(LTRIM(RTRIM(CONCAT(p.Nombre,' ',p.ApellidoPaterno))),''),u.Correo) AS Profesor,
                (SELECT COUNT(*) FROM academico.Postulacion po WHERE po.ConvocatoriaId=c.ConvocatoriaId) AS Postulaciones
         FROM academico.Convocatoria c
         JOIN plataforma.Usuario u ON u.UsuarioId=c.UsuarioPublicadorId
         LEFT JOIN plataforma.PerfilUsuario p ON p.UsuarioId=u.UsuarioId
         WHERE (?='' OR c.Titulo LIKE ? OR u.Correo LIKE ?)
           AND (?='' OR c.Estado=?)
         ORDER BY c.ConvocatoriaId DESC",
        [$buscar,'%'.$buscar.'%','%'.$buscar.'%',$estado,$estado]
    );
}
function tesco_admin_moderar_convocatoria(array $post): void {
    $id=tesco_admin_id($post['convocatoria_id']??null);
    $nuevo=(string)($post['estado']??'');
    if (!in_array($nuevo,['CERRADA','PUBLICADA'],true)) throw new InvalidArgumentException('Estado inválido.');
    $ant=$nuevo==='CERRADA'?'PUBLICADA':'CERRADA';
    $n=tesco_admin_exec(
        "UPDATE academico.Convocatoria SET Estado=? WHERE ConvocatoriaId=?
         AND Estado IN (?, CASE WHEN ?='CERRADA' THEN 'ABIERTA' ELSE '__INEXISTENTE__' END)
         AND FechaPublicacion IS NOT NULL",
        [$nuevo,$id,$ant,$nuevo]
    );
    if ($n!==1) throw new InvalidArgumentException('No se pudo modificar la convocatoria. Verifica su estado actual.');
}
function tesco_admin_postulaciones(string $estado=''): array {
    return tesco_db_rows(
        "SELECT TOP (100) po.PostulacionId,po.Estado,po.FechaPostulacion,c.Titulo,
                a.Matricula,ua.Correo AS Alumno,up.Correo AS Profesor
         FROM academico.Postulacion po
         JOIN academico.Convocatoria c ON c.ConvocatoriaId=po.ConvocatoriaId
         JOIN plataforma.Usuario ua ON ua.UsuarioId=po.UsuarioId
         JOIN plataforma.Usuario up ON up.UsuarioId=c.UsuarioPublicadorId
         LEFT JOIN academico.Alumno a ON a.UsuarioId=po.UsuarioId
         WHERE (?='' OR po.Estado=?)
         ORDER BY po.PostulacionId DESC",
        [$estado,$estado]
    );
}
function tesco_admin_produccion(string $buscar=''): array {
    return [
        'proyectos'=>tesco_db_rows(
            "SELECT TOP (60) p.ProyectoId,p.Titulo,p.Estado,p.FechaInicio,
                   COALESCE(u.Correo,'Sin autor asignado') AS Responsable
             FROM academico.Proyecto p
             LEFT JOIN academico.ProyectoParticipante pp ON pp.ProyectoId=p.ProyectoId AND pp.Rol='RESPONSABLE'
             LEFT JOIN plataforma.Usuario u ON u.UsuarioId=pp.UsuarioId
             WHERE (?='' OR p.Titulo LIKE ? OR u.Correo LIKE ?)
             ORDER BY p.ProyectoId DESC",
            [$buscar,'%'.$buscar.'%','%'.$buscar.'%']
        ),
        'publicaciones'=>tesco_db_rows(
            "SELECT TOP (60) pu.ProductoId,pr.Titulo,pu.TipoPublicacion,pr.FechaProducto
             FROM academico.Publicacion pu
             JOIN academico.Producto pr ON pr.ProductoId=pu.ProductoId
             WHERE (?='' OR pr.Titulo LIKE ?)
             ORDER BY pu.ProductoId DESC",
            [$buscar,'%'.$buscar.'%']
        ),
        'congresos'=>tesco_db_rows(
            "SELECT TOP (60) c.ActividadId,a.Nombre,a.Alcance,a.FechaInicio,c.Modalidad,c.Sede
             FROM academico.Congreso c
             JOIN academico.ActividadAcademica a ON a.ActividadId=c.ActividadId
             WHERE (?='' OR a.Nombre LIKE ?)
             ORDER BY c.ActividadId DESC",
            [$buscar,'%'.$buscar.'%']
        )
    ];
}
function tesco_admin_categorias(): array {
    return tesco_db_rows('SELECT CategoriaId,Nombre,Activo FROM academico.CategoriaHabilidad ORDER BY Nombre');
}
function tesco_admin_niveles(): array {
    return tesco_db_rows('SELECT NivelDominioId,Nombre,Orden,Activo FROM academico.NivelDominio ORDER BY Orden');
}
function tesco_admin_habilidades(): array {
    return tesco_db_rows(
        "SELECT TOP (100) uh.UsuarioId,uh.HabilidadId,h.Nombre AS Habilidad,uh.EstadoVerificacion,
                uh.MesesExperiencia,n.Nombre AS Nivel,u.Correo,
                c.Nombre AS Categoria,uh.EvidenciaReferencia
         FROM academico.UsuarioHabilidad uh
         JOIN academico.Habilidad h ON h.HabilidadId=uh.HabilidadId
         JOIN academico.CategoriaHabilidad c ON c.CategoriaId=h.CategoriaId
         JOIN academico.NivelDominio n ON n.NivelDominioId=uh.NivelDominioId
         JOIN plataforma.Usuario u ON u.UsuarioId=uh.UsuarioId
         ORDER BY CASE uh.EstadoVerificacion WHEN 'DECLARADA' THEN 0 WHEN 'EN_REVISION' THEN 1 ELSE 2 END,
                  uh.FechaActualizacion DESC,uh.UsuarioId DESC"
    );
}
function tesco_admin_revision_habilidad(array $admin,array $post): void {
    $uid=tesco_admin_id($post['usuario_id']??null);
    $hid=tesco_admin_id($post['habilidad_id']??null);
    $nuevo=(string)($post['estado']??'');
    if ($uid===(int)$admin['UsuarioId']) throw new InvalidArgumentException('No puedes verificar una habilidad propia.');
    if (!in_array($nuevo,['EN_REVISION','VALIDADA','RECHAZADA'],true))
        throw new InvalidArgumentException('Estado de verificación no permitido.');
    $evidencia=tesco_admin_campo($post,'evidencia',500);
    if ($nuevo==='VALIDADA'&&$evidencia==='') throw new InvalidArgumentException('Registra una referencia de evidencia para validar.');
    tesco_admin_tx(static function()use($uid,$hid,$nuevo,$evidencia,$admin): void {
        $actual=tesco_db_one(
            'SELECT EstadoVerificacion FROM academico.UsuarioHabilidad WITH (UPDLOCK,HOLDLOCK) WHERE UsuarioId=? AND HabilidadId=?',
            [$uid,$hid]
        );
        if (!$actual) throw new InvalidArgumentException('La habilidad declarada ya no existe.');
        $anterior=(string)$actual['EstadoVerificacion'];
        if (!(($anterior==='DECLARADA'&&$nuevo==='EN_REVISION')||
              ($anterior==='EN_REVISION'&&in_array($nuevo,['VALIDADA','RECHAZADA'],true)))) {
            throw new InvalidArgumentException('Transición inválida; primero pasa la habilidad a EN_REVISION.');
        }
        $cierre=in_array($nuevo,['VALIDADA','RECHAZADA'],true);
        $n=tesco_admin_exec(
            'UPDATE academico.UsuarioHabilidad
             SET EstadoVerificacion=?,VerificadoPorUsuarioId=?,FechaVerificacion='.
             ($cierre?'SYSUTCDATETIME()':'NULL').
             ',EvidenciaReferencia=?,FechaActualizacion=SYSUTCDATETIME()
             WHERE UsuarioId=? AND HabilidadId=? AND EstadoVerificacion=?',
            [$nuevo,$cierre?(int)$admin['UsuarioId']:null,$evidencia?:null,$uid,$hid,$anterior]
        );
        if ($n!==1) throw new RuntimeException('La verificación cambió antes de guardarse.');
    });
}
function tesco_admin_catalogo_habilidad(array $post): void {
    $accion=(string)($post['accion']??'');
    if ($accion==='categoria_nueva') {
        $nombre=tesco_admin_campo($post,'nombre',120,true);
        tesco_admin_exec('INSERT INTO academico.CategoriaHabilidad (Nombre,Activo) VALUES (?,1)',[$nombre]);
    } elseif ($accion==='nivel_nuevo') {
        $nombre=tesco_admin_campo($post,'nombre',60,true);
        $orden=tesco_admin_id($post['orden']??null);
        if ($orden>100) throw new InvalidArgumentException('Orden fuera de rango.');
        tesco_admin_exec('INSERT INTO academico.NivelDominio (Nombre,Orden,Activo) VALUES (?,?,1)',[$nombre,$orden]);
    } elseif ($accion==='clasificar_habilidad') {
        $hid=tesco_admin_id($post['habilidad_id']??null);
        $cat=tesco_admin_id($post['categoria_id']??null);
        $nombre=tesco_admin_campo($post,'nombre',120,true);
        $check=tesco_db_one('SELECT CategoriaId FROM academico.CategoriaHabilidad WHERE CategoriaId=? AND Activo=1',[$cat]);
        if (!$check) throw new InvalidArgumentException('La categoría no está activa.');
        if (tesco_admin_exec('UPDATE academico.Habilidad SET CategoriaId=?,Nombre=? WHERE HabilidadId=?',[$cat,$nombre,$hid])!==1)
            throw new InvalidArgumentException('Habilidad no encontrada.');
    } elseif (in_array($accion,['categoria_estado','nivel_estado'],true)) {
        $id=tesco_admin_id($post['id']??null);
        $activo=(string)($post['activo']??'');
        if (!in_array($activo,['0','1'],true)) throw new InvalidArgumentException('Estado inválido.');
        $tabla=$accion==='categoria_estado'?'academico.CategoriaHabilidad':'academico.NivelDominio';
        $pk=$accion==='categoria_estado'?'CategoriaId':'NivelDominioId';
        if (tesco_admin_exec("UPDATE $tabla SET Activo=? WHERE $pk=?",[(int)$activo,$id])!==1)
            throw new InvalidArgumentException('Registro inexistente.');
    } else throw new InvalidArgumentException('Acción de catálogo no permitida.');
}
function tesco_admin_procesar(array $admin,array $post): void {
    if (!tesco_csrf_valid($post['csrf']??null)) throw new InvalidArgumentException('Formulario caducado. Recarga la página.');
    $accion=(string)($post['accion']??'');
    switch ($accion) {
        case 'alta_usuario': tesco_admin_alta_usuario($post); break;
        case 'estado_usuario': tesco_admin_estado_usuario($admin,$post); break;
        case 'asignar_rol': tesco_admin_asignar_rol($post); break;
        case 'perfil_admin': tesco_admin_perfil($admin,$post); break;
        case 'moderar_convocatoria': tesco_admin_moderar_convocatoria($post); break;
        case 'revisar_habilidad': tesco_admin_revision_habilidad($admin,$post); break;
        case 'categoria_nueva':
        case 'nivel_nuevo':
        case 'clasificar_habilidad':
        case 'categoria_estado':
        case 'nivel_estado': tesco_admin_catalogo_habilidad($post); break;
        default: throw new InvalidArgumentException('Acción no permitida.');
    }
}
