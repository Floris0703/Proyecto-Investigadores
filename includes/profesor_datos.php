<?php
/**
 * TESCo Investigadores | Datos de profesores asociados al usuario autenticado.
 * Contrato oficial: plataforma.Usuario.UsuarioId en $_SESSION['usuario_id'].
 * El rol PROFESOR debe estar activo y vigente en SQL Server.
 */
declare(strict_types=1);
require_once __DIR__ . '/tesco_auth.php';
tesco_session_start();

function tesco_h(mixed $value): string {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function tesco_fecha(mixed $value, string $pattern='d/m/Y'): string {
    if ($value instanceof DateTimeInterface) return $value->format($pattern);
    if ($value === null || $value === '') return 'Sin fecha';
    try { return (new DateTimeImmutable((string)$value))->format($pattern); }
    catch (Throwable) { return 'Sin fecha'; }
}
function tesco_anio(mixed $fecha): ?int {
    if ($fecha === null || $fecha === '') return null;
    if ($fecha instanceof DateTimeInterface) return (int)$fecha->format('Y');
    $y=substr((string)$fecha, 0, 4);
    return ctype_digit($y) ? (int)$y : null;
}
function tesco_mes(mixed $fecha): ?int {
    if ($fecha === null || $fecha === '') return null;
    if ($fecha instanceof DateTimeInterface) return (int)$fecha->format('n');
    $m=substr((string)$fecha, 5, 2);
    return ctype_digit($m) ? (int)$m : null;
}
function tesco_query(string $sql, array $params=[]): mixed {
    $stmt=sqlsrv_query(tesco_db(),$sql,$params);
    if ($stmt === false) {
        error_log('TESCo SQLSRV: '.print_r(sqlsrv_errors(SQLSRV_ERR_ALL),true));
        throw new RuntimeException('No fue posible completar la consulta en SQL Server.');
    }
    return $stmt;
}
function tesco_filas(string $sql, array $params=[]): array {
    $stmt=tesco_query($sql,$params); $out=[];
    while (($row=sqlsrv_fetch_array($stmt,SQLSRV_FETCH_ASSOC))!==null) {
        if ($row === false) { sqlsrv_free_stmt($stmt); throw new RuntimeException('Error al leer los resultados.'); }
        $out[]=$row;
    }
    sqlsrv_free_stmt($stmt); return $out;
}
function tesco_uno(string $sql,array $params=[]): ?array {
    $rows=tesco_filas($sql,$params); return $rows[0] ?? null;
}
function tesco_ejecutar(string $sql,array $params=[]): int {
    $stmt=tesco_query($sql,$params);
    $n=sqlsrv_rows_affected($stmt); sqlsrv_free_stmt($stmt);
    return is_int($n) ? $n : 0;
}
function tesco_nuevo_id(string $sql,array $params=[]): int {
    $row=tesco_uno($sql,$params);
    if (!$row) throw new RuntimeException('No se recibió el identificador del nuevo registro.');
    return (int)reset($row);
}
function tesco_tabla(string $nombre): bool {
    static $cache=[];
    if (!isset($cache[$nombre])) {
        $res=tesco_uno("SELECT CASE WHEN OBJECT_ID(?, 'U') IS NULL THEN 0 ELSE 1 END AS Existe",[$nombre]);
        $cache[$nombre]=((int)($res['Existe'] ?? 0)===1);
    }
    return $cache[$nombre];
}
function tesco_columna(string $tabla,string $columna): bool {
    static $cache=[]; $key=$tabla.'.'.$columna;
    if (!isset($cache[$key])) {
        $r=tesco_uno("SELECT CASE WHEN COL_LENGTH(?, ?) IS NULL THEN 0 ELSE 1 END AS Existe",[$tabla,$columna]);
        $cache[$key]=((int)($r['Existe'] ?? 0)===1);
    }
    return $cache[$key];
}
function tesco_campo(string $campo,int $max=250,bool $obligatorio=false): string {
    $v=trim((string)($_POST[$campo] ?? ''));
    if ($obligatorio && $v==='') throw new InvalidArgumentException('Completa el campo '.$campo.'.');
    if ((function_exists('mb_strlen') ? mb_strlen($v,'UTF-8') : strlen($v))>$max) throw new InvalidArgumentException('El campo '.$campo.' supera el máximo permitido.');
    return $v;
}
function tesco_fecha_input(string $campo): ?string {
    $v=tesco_campo($campo,10);
    if ($v==='') return null;
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/D',$v)) throw new InvalidArgumentException('Fecha no válida: '.$campo);
    [$y,$m,$d]=array_map('intval',explode('-',$v));
    if (!checkdate($m,$d,$y)) throw new InvalidArgumentException('Fecha no válida: '.$campo);
    return $v;
}
function tesco_transaccion(callable $fn): mixed {
    $con=tesco_db();
    if (!sqlsrv_begin_transaction($con)) throw new RuntimeException('No se pudo abrir la transacción.');
    try { $out=$fn(); if (!sqlsrv_commit($con)) throw new RuntimeException('No se pudo confirmar la transacción.'); return $out; }
    catch (Throwable $e) { sqlsrv_rollback($con); throw $e; }
}
function tesco_catalogo(string $tabla,string $id,string $nombre): int {
    // Los tres nombres están definidos por código; no provienen de la petición.
    $r=tesco_uno("SELECT TOP (1) [$id] AS Id FROM [academico].[$tabla] WHERE Nombre=?",[$nombre]);
    if (!$r) throw new RuntimeException('Falta el catálogo '.$nombre.'; ejecuta los seeds oficiales.');
    return (int)$r['Id'];
}
/**
 * Identidad: no se acepta ProfesorId por GET, variable de entorno o valor fijo.
 * El módulo únicamente confía en la sesión validada por el login oficial.
 */
function tesco_profesor_actual(): array {
    $sesion=filter_var($_SESSION['usuario_id'] ?? null,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
    $ip=(string)($_SERVER['REMOTE_ADDR'] ?? '');
    $local=in_array($ip,['127.0.0.1','::1'],true);
    $demo=filter_var(getenv('TESCO_PROFESOR_ID_DEV') ?: null,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
    if ($sesion) {
        // Acceso de profesores: comprobar rol y cuenta directamente en SQL Server.
        // Los roles indicados por el cliente nunca conceden acceso por sí solos.
        if (isset($_SESSION['tesco_rol_activo']) && $_SESSION['tesco_rol_activo'] !== 'PROFESOR') {
            http_response_code(403);
            exit('No tienes permiso para acceder a Profesores con el rol actual.');
        }
        if (isset($_SESSION['tesco_login_at'])) {
            $ultimo=(int)($_SESSION['tesco_last_activity'] ?? $_SESSION['tesco_login_at']);
            if (time()-$ultimo > 1800) {
                $_SESSION=[];
                session_regenerate_id(true);
                http_response_code(403);
                exit('Tu sesión ha caducado.');
            }
            $_SESSION['tesco_last_activity']=time();
        }
        $permiso=tesco_uno("SELECT TOP (1) u.UsuarioId
            FROM plataforma.Usuario u
            JOIN plataforma.UsuarioRol ur ON ur.UsuarioId=u.UsuarioId
            JOIN plataforma.Rol r ON r.RolId=ur.RolId
            WHERE u.UsuarioId=?
              AND UPPER(LTRIM(RTRIM(u.EstadoCuenta))) IN ('ACTIVO','ACTIVA')
              AND UPPER(LTRIM(RTRIM(r.Nombre)))='PROFESOR'",[(int)$sesion]);
        if (!$permiso) {
            http_response_code(403);
            exit('Usuario sin rol docente vigente.');
        }
        $row=tesco_uno("SELECT p.ProfesorId,p.UsuarioId,p.Departamento,p.GradoAcademico,u.Correo,
                        per.Nombre,per.ApellidoPaterno,per.ApellidoMaterno,per.Biografia
                        FROM academico.Profesor p
                        JOIN plataforma.Usuario u ON u.UsuarioId=p.UsuarioId
                        LEFT JOIN plataforma.PerfilUsuario per ON per.UsuarioId=u.UsuarioId
                        WHERE p.UsuarioId=?",[(int)$sesion]);
        $origen='sesion';
    } elseif ($local && $demo) {
        $row=tesco_uno("SELECT p.ProfesorId,p.UsuarioId,p.Departamento,p.GradoAcademico,u.Correo,
                        per.Nombre,per.ApellidoPaterno,per.ApellidoMaterno,per.Biografia
                        FROM academico.Profesor p
                        JOIN plataforma.Usuario u ON u.UsuarioId=p.UsuarioId
                        LEFT JOIN plataforma.PerfilUsuario per ON per.UsuarioId=u.UsuarioId
                        WHERE p.ProfesorId=?",[(int)$demo]);
        $origen='demo';
    } else {
        http_response_code(403);
        exit('Acceso restringido. Para la demostración local: establecer TESCO_PROFESOR_ID_DEV=1 antes de iniciar PHP.');
    }

    // Verificación de los roles EN BD, no solo del contenido de la cookie.
    $roles=tesco_roles_actuales();
    if (!in_array('PROFESOR',$roles,true) ||
        ($_SESSION['tesco_rol_activo'] ?? null)!=='PROFESOR') {
        http_response_code(403);
        exit('Acceso exclusivo al rol PROFESOR activo. Regresa a la bienvenida y selecciona tu rol autorizado.');
    }

    $row=tesco_uno(
        "SELECT p.ProfesorId,p.UsuarioId,p.Departamento,p.GradoAcademico,u.Correo,
                per.Nombre,per.ApellidoPaterno,per.ApellidoMaterno,per.Biografia
         FROM academico.Profesor p
         JOIN plataforma.Usuario u ON u.UsuarioId=p.UsuarioId
         LEFT JOIN plataforma.PerfilUsuario per ON per.UsuarioId=u.UsuarioId
         WHERE p.UsuarioId=? AND UPPER(LTRIM(RTRIM(u.EstadoCuenta))) IN ('ACTIVO','ACTIVA')",
        [$usuarioId]
    );
    if (!$row) {
        http_response_code(403);
        exit('Tu cuenta tiene rol PROFESOR, pero no existe un perfil docente activo asociado en academico.Profesor.');
    }

    $row['NombreCompleto']=trim(implode(' ',array_filter([
        $row['Nombre'] ?? '',$row['ApellidoPaterno'] ?? '',$row['ApellidoMaterno'] ?? ''
    ],static fn($v)=>is_string($v)&&trim($v)!=='')));
    if ($row['NombreCompleto']==='') $row['NombreCompleto']=(string)$row['Correo'];
    header('Cache-Control: private, no-store');
    return $row;
}
function tesco_procesar_post(array $profesor): void {
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET')!=='POST') return;
    $pagina=basename((string)($_SERVER['SCRIPT_NAME'] ?? 'index.php'));
    $permitidas=['index.php','perfil.php','productividad.php','proyectos.php','publicaciones.php','congresos.php','titulacion.php','convocatorias.php','postulaciones.php'];
    if (!in_array($pagina,$permitidas,true)) { http_response_code(400); exit('Página inválida.'); }
    if (!hash_equals((string)($_SESSION['tesco_csrf']??''),(string)($_POST['csrf']??''))) {
        http_response_code(403);exit('Token de formulario inválido.');
    }
    $uid=(int)$profesor['UsuarioId']; $pid=(int)$profesor['ProfesorId'];
    try {
        switch ((string)($_POST['accion']??'')) {
            case 'perfil':
                $nombre=tesco_campo('nombre',100,true);$paterno=tesco_campo('apellido_paterno',100,true);
                $materno=tesco_campo('apellido_materno',100);$bio=tesco_campo('biografia',700);
                $dpto=tesco_campo('departamento',120);$grado=tesco_campo('grado',80);
                $orcid=tesco_campo('orcid',19);$ubicacion=tesco_campo('ubicacion',180);
                if ($orcid!=='' && !preg_match('/^\d{4}-\d{4}-\d{4}-[\dX]{4}$/D',$orcid)) {
                    throw new InvalidArgumentException('ORCID debe tener el formato 0000-0000-0000-0000.');
                }
                if (($orcid!==''&&!tesco_columna('academico.Profesor','ORCID'))||
                    ($ubicacion!==''&&!tesco_columna('academico.Profesor','UbicacionAtencion')))
                    throw new RuntimeException('Aplica primero la migración 004 para guardar ORCID o ubicación.');
                tesco_transaccion(function() use($uid,$pid,$nombre,$paterno,$materno,$bio,$dpto,$grado,$orcid,$ubicacion) {
                    $r=tesco_uno("SELECT UsuarioId FROM plataforma.PerfilUsuario WHERE UsuarioId=?",[$uid]);
                    if ($r) tesco_ejecutar("UPDATE plataforma.PerfilUsuario SET Nombre=?,ApellidoPaterno=?,ApellidoMaterno=?,Biografia=?,FechaActualizacion=SYSUTCDATETIME() WHERE UsuarioId=?",
                        [$nombre,$paterno,$materno?:null,$bio?:null,$uid]);
                    else tesco_ejecutar("INSERT INTO plataforma.PerfilUsuario (UsuarioId,Nombre,ApellidoPaterno,ApellidoMaterno,Biografia) VALUES (?,?,?,?,?)",
                        [$uid,$nombre,$paterno,$materno?:null,$bio?:null]);
                    tesco_ejecutar("UPDATE academico.Profesor SET Departamento=?,GradoAcademico=? WHERE ProfesorId=?",[$dpto?:null,$grado?:null,$pid]);
                    if (tesco_columna('academico.Profesor','ORCID')) tesco_ejecutar("UPDATE academico.Profesor SET ORCID=? WHERE ProfesorId=?",[$orcid?:null,$pid]);
                    if (tesco_columna('academico.Profesor','UbicacionAtencion')) tesco_ejecutar("UPDATE academico.Profesor SET UbicacionAtencion=? WHERE ProfesorId=?",[$ubicacion?:null,$pid]);
                });
                $msg='Perfil actualizado.';break;
            case 'proyecto':
                $titulo=tesco_campo('titulo',250,true);$descripcion=tesco_campo('descripcion',3000);$fecha=tesco_fecha_input('fecha');
                tesco_transaccion(function()use($uid,$titulo,$descripcion,$fecha) {
                    $cols='Titulo,Descripcion,Estado,FechaInicio';$values='?,?,?,?';$par=[$titulo,$descripcion?:null,'REGISTRADO',$fecha];
                    if(tesco_columna('academico.Proyecto','UsuarioCreadorId')){$cols.=',UsuarioCreadorId';$values.=',?';$par[]=$uid;}
                    $id=tesco_nuevo_id("INSERT INTO academico.Proyecto ($cols) OUTPUT INSERTED.ProyectoId VALUES ($values)",$par);
                    tesco_ejecutar("INSERT INTO academico.ProyectoParticipante (ProyectoId,UsuarioId,Rol) VALUES (?,?,?)",[$id,$uid,'RESPONSABLE']);
                });
                $msg='Proyecto registrado.';break;
            case 'publicacion':
                $titulo=tesco_campo('titulo',250,true);$tipo=tesco_campo('tipo',45,true);
                if (!in_array($tipo,['ARTICULO','LIBRO','CAPITULO','OTRA'],true))throw new InvalidArgumentException('Tipo de publicación inválido.');
                $fecha=tesco_fecha_input('fecha');$doi=tesco_campo('doi',150);$isbn=tesco_campo('isbn',40);
                tesco_transaccion(function()use($uid,$titulo,$tipo,$fecha,$doi,$isbn) {
                    $tipoId=tesco_catalogo('TipoProducto','TipoProductoId','PUBLICACION');
                    $cols='TipoProductoId,Titulo,FechaProducto';$values='?,?,?';$par=[$tipoId,$titulo,$fecha];
                    if(tesco_columna('academico.Producto','UsuarioCreadorId')){$cols.=',UsuarioCreadorId';$values.=',?';$par[]=$uid;}
                    $id=tesco_nuevo_id("INSERT INTO academico.Producto ($cols) OUTPUT INSERTED.ProductoId VALUES ($values)",$par);
                    tesco_ejecutar("INSERT INTO academico.Publicacion (ProductoId,TipoPublicacion,DOI,ISBN) VALUES (?,?,?,?)",
                        [$id,$tipo,$doi?:null,$isbn?:null]);
                    tesco_ejecutar("INSERT INTO academico.ContribucionProducto (ProductoId,UsuarioId,Rol) VALUES (?,?,?)",[$id,$uid,'AUTOR']);
                });
                $msg='Publicación registrada.';break;
            case 'congreso':
                $nombre=tesco_campo('nombre',250,true);$alcance=tesco_campo('alcance',25,true);
                if(!in_array($alcance,['NACIONAL','INTERNACIONAL','REGIONAL','LOCAL'],true))throw new InvalidArgumentException('Alcance inválido.');
                $fecha=tesco_fecha_input('fecha');$sede=tesco_campo('sede',150);$modalidad=tesco_campo('modalidad',60);
                tesco_transaccion(function()use($uid,$nombre,$alcance,$fecha,$sede,$modalidad) {
                    $tipo=tesco_catalogo('TipoActividad','TipoActividadId','CONGRESO');
                    $cols='TipoActividadId,Nombre,Alcance,FechaInicio';$values='?,?,?,?';$par=[$tipo,$nombre,$alcance,$fecha];
                    if(tesco_columna('academico.ActividadAcademica','UsuarioRegistradorId')){$cols.=',UsuarioRegistradorId';$values.=',?';$par[]=$uid;}
                    $id=tesco_nuevo_id("INSERT INTO academico.ActividadAcademica ($cols) OUTPUT INSERTED.ActividadId VALUES ($values)",$par);
                    tesco_ejecutar("INSERT INTO academico.Congreso (ActividadId,Modalidad,Sede) VALUES (?,?,?)",
                        [$id,$modalidad?:null,$sede?:null]);
                    tesco_ejecutar("INSERT INTO academico.ParticipacionActividad (ActividadId,UsuarioId,Rol) VALUES (?,?,?)",[$id,$uid,'PONENTE']);
                });
                $msg='Congreso registrado.';break;
            case 'titulacion':
                $titulo=tesco_campo('titulo',250,true);$modalidad=tesco_campo('modalidad',60,true);$fecha=tesco_fecha_input('fecha');
                tesco_transaccion(function()use($uid,$pid,$titulo,$modalidad,$fecha) {
                    $tipo=tesco_catalogo('TipoProducto','TipoProductoId','TITULACION');
                    $cols='TipoProductoId,Titulo,FechaProducto';$values='?,?,?';$par=[$tipo,$titulo,$fecha];
                    if(tesco_columna('academico.Producto','UsuarioCreadorId')){$cols.=',UsuarioCreadorId';$values.=',?';$par[]=$uid;}
                    $id=tesco_nuevo_id("INSERT INTO academico.Producto ($cols) OUTPUT INSERTED.ProductoId VALUES ($values)",$par);
                    tesco_ejecutar("INSERT INTO academico.TrabajoTitulacion (ProductoId,Modalidad,FechaInicio) VALUES (?,?,?)",[$id,$modalidad,$fecha]);
                    tesco_ejecutar("INSERT INTO academico.TitulacionDirector (ProductoId,ProfesorId,Funcion) VALUES (?,?,?)",[$id,$pid,'DIRECTOR']);
                });
                $msg='Trabajo de titulación registrado.';break;
            case 'convocatoria':
                $titulo=tesco_campo('titulo',200,true);$desc=tesco_campo('descripcion',3000);
                $fecha=tesco_fecha_input('fecha_cierre');$publicada=($_POST['publicada']??'')==='1';
                // La publicación se fecha en SQL Server: Landing compara con SYSDATETIME().
                // No utilizar date() de PHP: ambos entornos pueden tener distintas zonas horarias.
                tesco_ejecutar("INSERT INTO academico.Convocatoria (UsuarioPublicadorId,Titulo,Descripcion,Estado,FechaPublicacion,FechaCierre) VALUES (?,?,?,?,CASE WHEN ?=1 THEN SYSDATETIME() ELSE NULL END,?)",
                    [$uid,$titulo,$desc?:null,$publicada?'PUBLICADA':'BORRADOR',$publicada?1:0,$fecha]);
                $msg='Convocatoria registrada.';break;
            case 'postulacion_estado':
                $id=filter_var($_POST['postulacion_id']??null,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
                $nuevo=tesco_campo('estado',25,true);
                if(!$id||!in_array($nuevo,['PENDIENTE','EN_REVISION','ACEPTADA','RECHAZADA'],true))
                    throw new InvalidArgumentException('Estado o postulación inválidos.');
                tesco_transaccion(function()use($uid,$id,$nuevo) {
                    $anterior=tesco_uno("SELECT po.Estado FROM academico.Postulacion po
                        JOIN academico.Convocatoria c ON c.ConvocatoriaId=po.ConvocatoriaId
                        WHERE po.PostulacionId=? AND c.UsuarioPublicadorId=?",[$id,$uid]);
                    if(!$anterior) throw new RuntimeException('No se puede modificar una postulación ajena.');
                    if((string)$anterior['Estado']===$nuevo) return;
                    tesco_ejecutar("UPDATE academico.Postulacion SET Estado=? WHERE PostulacionId=?",[$nuevo,$id]);
                    if(tesco_tabla('academico.PostulacionMovimiento'))
                        tesco_ejecutar("INSERT INTO academico.PostulacionMovimiento (PostulacionId,UsuarioResponsableId,EstadoAnterior,EstadoNuevo) VALUES (?,?,?,?)",
                            [$id,$uid,(string)$anterior['Estado'],$nuevo]);
                });
                $msg='Estado de postulación actualizado.';break;
            default: throw new InvalidArgumentException('Operación no permitida.');
        }
        $_SESSION['tesco_flash']=['ok',$msg];
    } catch (Throwable $e) {
        error_log('TESCo POST: '.$e->getMessage());
        $_SESSION['tesco_flash']=['error',$e instanceof InvalidArgumentException ? $e->getMessage() : 'No fue posible guardar. Revisa los catálogos, las migraciones y los datos.'];
    }
    $qs=$pagina==='productividad.php'?'?anio='.urlencode((string)($_GET['anio']??date('Y'))):'';
    header('Location: '.$pagina.$qs, true,303); exit;
}
function tesco_lista(string $mod,array $p): array {
    $uid=(int)$p['UsuarioId'];$pid=(int)$p['ProfesorId'];
    switch($mod) {
        case 'proyectos': return tesco_filas("SELECT pr.ProyectoId AS Id,pr.Titulo,pr.Descripcion,pr.Estado,pr.FechaInicio AS Fecha,pr.FechaFin
            FROM academico.Proyecto pr WHERE EXISTS
            (SELECT 1 FROM academico.ProyectoParticipante x WHERE x.ProyectoId=pr.ProyectoId AND x.UsuarioId=?)
            ORDER BY pr.ProyectoId DESC",[$uid]);
        case 'publicaciones': return tesco_filas("SELECT prod.ProductoId AS Id,prod.Titulo,pub.TipoPublicacion AS Tipo,pub.DOI,
            pub.ISBN,prod.EstadoValidacion AS Estado,prod.FechaProducto AS Fecha
            FROM academico.Producto prod JOIN academico.Publicacion pub ON pub.ProductoId=prod.ProductoId
            WHERE EXISTS (SELECT 1 FROM academico.ContribucionProducto x WHERE x.ProductoId=prod.ProductoId AND x.UsuarioId=?)
            ORDER BY prod.ProductoId DESC",[$uid]);
        case 'congresos': return tesco_filas("SELECT a.ActividadId AS Id,a.Nombre AS Titulo,a.Alcance AS Tipo,a.FechaInicio AS Fecha,
            c.Modalidad,c.Sede FROM academico.ActividadAcademica a
            JOIN academico.Congreso c ON c.ActividadId=a.ActividadId
            WHERE EXISTS(SELECT 1 FROM academico.ParticipacionActividad x WHERE x.ActividadId=a.ActividadId AND x.UsuarioId=?)
            ORDER BY a.ActividadId DESC",[$uid]);
        case 'titulacion': return tesco_filas("SELECT prod.ProductoId AS Id,prod.Titulo,t.Modalidad AS Tipo,
            t.EstadoTrabajo AS Estado,t.FechaInicio AS Fecha,t.FechaConclusion
            FROM academico.TrabajoTitulacion t JOIN academico.Producto prod ON prod.ProductoId=t.ProductoId
            WHERE EXISTS(SELECT 1 FROM academico.TitulacionDirector x WHERE x.ProductoId=t.ProductoId AND x.ProfesorId=?)
            ORDER BY prod.ProductoId DESC",[$pid]);
        case 'convocatorias': return tesco_filas("SELECT c.ConvocatoriaId AS Id,c.Titulo,c.Descripcion,c.Estado,
            c.FechaPublicacion AS Fecha,c.FechaCierre
            FROM academico.Convocatoria c WHERE c.UsuarioPublicadorId=?
            ORDER BY c.ConvocatoriaId DESC",[$uid]);
        case 'postulaciones': return tesco_filas("SELECT po.PostulacionId AS Id,po.Estado,po.FechaPostulacion AS Fecha,
            c.Titulo AS Convocatoria,po.UsuarioId,
            COALESCE(NULLIF(LTRIM(RTRIM(CONCAT(per.Nombre,' ',per.ApellidoPaterno,' ',per.ApellidoMaterno))),''),u.Correo) AS Aspirante,
            u.Correo
            FROM academico.Postulacion po JOIN academico.Convocatoria c ON c.ConvocatoriaId=po.ConvocatoriaId
            JOIN plataforma.Usuario u ON u.UsuarioId=po.UsuarioId
            LEFT JOIN plataforma.PerfilUsuario per ON per.UsuarioId=u.UsuarioId
            WHERE c.UsuarioPublicadorId=? ORDER BY po.PostulacionId DESC",[$uid]);
        default: return [];
    }
}
function tesco_datos_perfil(array $p): array {
    $pid=(int)$p['ProfesorId'];$uid=(int)$p['UsuarioId'];$extra=[];
    if(tesco_columna('academico.Profesor','ORCID') || tesco_columna('academico.Profesor','UbicacionAtencion')) {
        $cols=[];
        if(tesco_columna('academico.Profesor','ORCID'))$cols[]='ORCID';
        if(tesco_columna('academico.Profesor','UbicacionAtencion'))$cols[]='UbicacionAtencion';
        $extra=tesco_uno('SELECT '.implode(',',$cols).' FROM academico.Profesor WHERE ProfesorId=?',[$pid]) ?? [];
    }
    $p=array_merge($p,$extra);
    $p['Formacion']=tesco_tabla('academico.FormacionAcademica')?
        tesco_filas("SELECT TOP (12) Grado,Programa,Institucion,Estado,FechaFin FROM academico.FormacionAcademica WHERE UsuarioId=? ORDER BY FormacionId DESC",[$uid]):[];
    $p['Experiencia']=tesco_tabla('academico.ExperienciaAcademica')?
        tesco_filas("SELECT TOP (12) Tipo,Cargo,Organizacion,FechaInicio FROM academico.ExperienciaAcademica WHERE UsuarioId=? ORDER BY ExperienciaId DESC",[$uid]):[];
    $p['Reconocimientos']=tesco_tabla('academico.Reconocimiento')?
        tesco_filas("SELECT TOP (12) Titulo,Otorgante,Fecha FROM academico.Reconocimiento WHERE UsuarioId=? ORDER BY ReconocimientoId DESC",[$uid]):[];
    $p['Horarios']=tesco_tabla('academico.ProfesorHorarioAtencion')?
        tesco_filas("SELECT DiaSemana,HoraInicio,HoraFin,Modalidad,Lugar FROM academico.ProfesorHorarioAtencion WHERE ProfesorId=? AND Activo=1 ORDER BY DiaSemana,HoraInicio",[$pid]):[];
    $p['Lineas']=tesco_tabla('academico.ProfesorLineaInvestigacion')?
        tesco_filas("SELECT l.Nombre FROM academico.ProfesorLineaInvestigacion pl JOIN academico.LineaInvestigacion l
              ON l.LineaInvestigacionId=pl.LineaInvestigacionId WHERE pl.ProfesorId=? ORDER BY l.Nombre",[$pid]):[];
    $p['Intereses']=tesco_filas("SELECT a.Nombre FROM academico.UsuarioInteres ui JOIN academico.AreaInvestigacion a
        ON a.AreaInvestigacionId=ui.AreaInvestigacionId WHERE ui.UsuarioId=? ORDER BY a.Nombre",[$uid]);
    return $p;
}
function tesco_metricas(array $p,int $anio): array {
    $tipos=['proyectos','publicaciones','congresos','convocatorias','titulacion'];
    $out=['anio'=>$anio,'listas'=>[],'series'=>[],'totales'=>[],'conteos'=>[]];
    foreach($tipos as $tipo) {
        $lista=tesco_lista($tipo,$p);$out['listas'][$tipo]=$lista;
        if($tipo==='convocatorias') $lista=array_values(array_filter($lista,fn($r)=>in_array(strtoupper((string)($r['Estado']??'')),['PUBLICADA','ABIERTA','CERRADA'],true)));
        $meses=array_fill(1,12,0);$count=0;
        foreach($lista as $r) {
            $y=tesco_anio($r['Fecha']??null);$m=tesco_mes($r['Fecha']??null);
            if($y===$anio && $m!==null){$count++;$meses[$m]++;}
        }
        $out['series'][$tipo]=$meses;
        $out['totales'][$tipo]=$count;
        $out['conteos'][$tipo]=count($lista);
    }
    $out['nacional']=array_fill(1,12,0);$out['internacional']=array_fill(1,12,0);
    foreach($out['listas']['congresos'] as $r) {
        if(tesco_anio($r['Fecha'])!==$anio)continue;
        $m=tesco_mes($r['Fecha']);
        if($m===null)continue;
        if(($r['Tipo']??'')==='NACIONAL')$out['nacional'][$m]++;
        if(($r['Tipo']??'')==='INTERNACIONAL')$out['internacional'][$m]++;
    }
    return $out;
}
if (empty($_SESSION['tesco_csrf'])) { $_SESSION['tesco_csrf']=bin2hex(random_bytes(24)); }
$tescoProfesor=tesco_profesor_actual();
tesco_procesar_post($tescoProfesor);
$tescoAnio=filter_var($_GET['anio']??date('Y'),FILTER_VALIDATE_INT,['options'=>['min_range'=>2000,'max_range'=>2100]]);
if (!$tescoAnio) $tescoAnio=(int)date('Y');
$tescoFlash=$_SESSION['tesco_flash']??null;
unset($_SESSION['tesco_flash']);
