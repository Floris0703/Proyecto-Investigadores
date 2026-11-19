# TESCo Investigadores — Landing dinámica y autenticación (implementación supervisada)

## Alcance
- Entrada principal: `/Landing/Inicio.php`. `/Landing/Inicio.html` y `/index.html` redirigen a la versión PHP.
- Acceso: `/Landing/acceso.php` → `autenticar.php` (POST con token CSRF).
- Identidad: `plataforma.Usuario` (correo, hash y estado), `UsuarioRol` + `Rol`.
- Destinos: `PROFESOR` → módulo PHP existente; `ALUMNO` y `ADMIN` → pantalla PHP protegida de espera, **no** módulos funcionales.
- Datos públicos: contadores globales en cuatro entidades y `academico.Convocatoria` vigente.
- Consultas sin escritura a SQL Server; no se ejecutan migraciones ni se crea ninguna cuenta automáticamente.

## Requisitos locales
- PHP 8.0 o posterior, Microsoft SQL Server LocalDB, extensión PHP `sqlsrv` y controlador ODBC.
- Base `TESCoInvestigadores` con esquema base 001 y catálogos `010_catalogos.sql`.
- Para mostrar datos reales se requieren filas en las tablas. Si faltan tablas o conexión, se presenta “no disponible”; nunca cifras ficticias.
- Por defecto se conecta a `(localdb)\\MSSQLLocalDB` y base `TESCoInvestigadores`.
- Variables de entorno opcionales: `TESCO_SQL_SERVER`, `TESCO_SQL_DATABASE`. No introducir contraseñas en el repositorio.

Desde la raíz de la aplicación:
```powershell
php -S 127.0.0.1:8000
```
Abrir `http://127.0.0.1:8000/Landing/Inicio.php`. Usar PHP servido desde localhost: **no** abrir los archivos con `file://`.

## Preparación manual de usuario de prueba
El repositorio **no contiene cuentas de prueba ni contraseñas**. Un administrador de BD debe:
1. Comprobar que la tabla `plataforma.Rol` tiene `PROFESOR`, `ALUMNO` y `ADMIN` (scripts de catálogos).
2. Generar localmente un hash usando `password_hash(..., PASSWORD_DEFAULT)` desde PHP; no guardar ni registrar la contraseña en Git.
3. Insertar, de forma manual y autorizada, el correo único, hash y `EstadoCuenta='ACTIVO'` en `plataforma.Usuario`, junto con la asociación correspondiente en `plataforma.UsuarioRol`.
4. Para un profesor, asociar también el mismo `UsuarioId` a `academico.Profesor` con `NumeroEmpleado` único; sin esta asociación se deniega el panel.
5. Probar contraseñas incorrectas, usuarios PENDIENTE, roles no asignados y sesiones caducadas.

**No se almacenan contraseñas en texto plano.** El registro público no está habilitado; `Registro.html` informa que el alta es institucional.

## Datos públicos
- `Profesores`: COUNT(*) de `academico.Profesor`.
- `Proyectos`: COUNT(*) de `academico.Proyecto`.
- `Artículos`: COUNT(*) de `academico.Publicacion` donde `TipoPublicacion='ARTICULO'`.
- `Congresos`: COUNT(*) de `academico.Congreso` (eventos únicos, no participaciones).
- Convocatorias: `PUBLICADA` o `ABIERTA`, publicación no futura y cierre no pasado, máximo seis; se ordenan por fecha de publicación descendente.
- La consulta usa `SYSDATETIME()`, consistente con el horario local de SQL Server; el PHP de profesores actualmente inserta `date()`, por lo que **hay que verificar la zona horaria** de ambos servidores.
- La fecha de cierre NULL se considera sin fecha límite. Fechas y estados futuros pueden requerir una política institucional más detallada.

## Seguridad y límites
- `password_verify`, consultas parametrizadas, rotación de identificador de sesión, cookies HttpOnly, SameSite Lax, inactividad máxima de 30 minutos, tokens CSRF en formularios de autenticación y cierre.
- Límite básico de 5 intentos fallidos por 15 minutos **por sesión**. Para un despliegue público debe añadirse rate limiting persistente por cuenta/IP, HTTPS obligatorio, configuración segura del proxy y monitorización.
- Las sesiones comprueban periódicamente los roles y el estado de cuenta directamente en SQL Server al acceder al panel. El rol es dato de BD, no campo de confianza del formulario.
- `ALUMNO` y `ADMIN` carecen todavía de paneles PHP protegidos completos; la pantalla de espera **no reemplaza** sus desarrollos futuros.
- El módulo PHP de Profesores debe conservar su contrato `$_SESSION['usuario_id']`; protección por rol en las páginas de profesor se revisa en una rama específica de ese módulo.
- El tablero no ofrece postulación pública real. Detalle informativo e inicio de sesión son las únicas acciones disponibles.

## Validación
```bash
php -l Landing/Inicio.php
php -l includes/tesco_auth.php
php tests/landing_smoke.php
```
El smoke test usa SQLSRV **simulado**: valida la lógica de integración básica, NO demuestra que LocalDB real tenga usuarios o datos. Se requiere prueba manual de integración sobre base de demostración, en particular en formularios, redirecciones y permisos.

## Git y coordinación
Estos cambios se proponen en la rama existente `landing`. No hacer merge a `master` antes de revisar los PRs y realizar pruebas de integración. No se encienden agentes autónomos ni APIs de IA.
