# TESCo Investigadores — Landing dinámica y autenticación (implementación supervisada)

## Flujo oficial de navegación
1. **Portada inicial:** `/` o `/Landing/` → `Landing/index - copia.html`.
2. **Inicio de sesión:** los botones Alumno, Profesor y Administrador van a `Landing/login.php` (el rol es verificado desde SQL Server, no elegido por la URL). `Landing/login.html`, `Landing/Inicio Sesion.html` y `Landing/acceso.php` son accesos compatibles al mismo formulario.
3. **Bienvenida:** tras autenticar correctamente, `Landing/autenticar.php` dirige a `Landing/Inicio.php`. Esta vista requiere sesión y presenta los indicadores reales y convocatorias de SQL Server, además de «Entrar a mi perfil».
4. **Panel:** `Landing/panel.php` redirige a `profesores/index.php` si corresponde al rol PROFESOR y existe el perfil docente. ALUMNO y ADMIN muestran la pantalla provisional protegida. Si el usuario tiene varios roles, `elegir_rol.php` permite seleccionar **solo** entre los roles asociados en BD, después de la bienvenida.
5. **Cerrar sesión:** vuelve a la portada original.

## Alcance
- Formulario seguro `login.php` → `autenticar.php` (POST con token CSRF).
- Identidad: `plataforma.Usuario` (correo, hash y estado), `UsuarioRol` + `Rol`.
- Destinos: `PROFESOR` → módulo PHP existente; `ALUMNO` y `ADMIN` → pantalla PHP protegida de espera, **no** módulos funcionales.
- Datos de bienvenida: contadores globales en cuatro entidades y `academico.Convocatoria` vigente, **visibles tras iniciar sesión**.
- Consultas sin escritura a SQL Server; no se ejecutan migraciones ni se crea ninguna cuenta automáticamente.

## Requisitos locales
- PHP 8.0 o posterior, Microsoft SQL Server (instancia predeterminada o LocalDB), extensión PHP `sqlsrv` y controlador ODBC.
- Base `TESCoInvestigadores` con esquema base 001 y catálogos `010_catalogos.sql`.
- Para mostrar datos reales se requieren filas en las tablas. Si faltan tablas o conexión, se presenta “no disponible”; nunca cifras ficticias.
- Por defecto se conecta a `(localdb)\\MSSQLLocalDB` y base `TESCoInvestigadores`.
- Variables de entorno opcionales: `TESCO_SQL_SERVER`, `TESCO_SQL_DATABASE`. No introducir contraseñas en el repositorio.

Desde la raíz de la aplicación:
```powershell
php -S 127.0.0.1:8000
```
Abrir **`http://127.0.0.1:8000/`**, que ahora comienza por la portada original. Usar PHP servido desde localhost: **no** abrir los archivos con `file://`.

## Preparación manual de usuario de prueba
El repositorio **no contiene cuentas de prueba ni contraseñas**. Un administrador de BD debe:
1. Comprobar que la tabla `plataforma.Rol` tiene `PROFESOR`, `ALUMNO` y `ADMIN` (scripts de catálogos).
2. Generar localmente un hash usando `password_hash(..., PASSWORD_DEFAULT)` desde PHP; no guardar ni registrar la contraseña en Git.
3. Insertar, de forma manual y autorizada, el correo único, hash y `EstadoCuenta='ACTIVO'` en `plataforma.Usuario`, junto con la asociación correspondiente en `plataforma.UsuarioRol`.
4. Para un profesor, asociar también el mismo `UsuarioId` a `academico.Profesor` con `NumeroEmpleado` único; sin esta asociación se deniega el panel.
5. Probar contraseñas incorrectas, usuarios PENDIENTE, roles no asignados y sesiones caducadas.

**No se almacenan contraseñas en texto plano.** El registro público no está habilitado; `Registro.html` informa que el alta es institucional.

## Datos mostrados en la bienvenida
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
- `conexion.php` y `includes/tesco_db.php` respetan las mismas variables `TESCO_SQL_SERVER` y `TESCO_SQL_DATABASE`, por lo que para pruebas locales en `PC_EMI` ambos módulos pueden usar la misma instancia sin modificar archivos.
- El módulo PHP de Profesores conserva su contrato `$_SESSION['usuario_id']`; el refuerzo de autorización de sus vistas está propuesto por separado en el PR #6 y debe revisarse antes de desplegar el sistema.
- El tablero no ofrece postulación pública real. Detalle informativo e inicio de sesión son las únicas acciones disponibles.

## Validación
```bash
php -l Landing/Inicio.php
php -l includes/tesco_auth.php
php tests/landing_smoke.php
```
El smoke test usa SQLSRV **simulado** y verifica enlaces del recorrido: valida la lógica de integración básica, NO demuestra que SQL Server real tenga usuarios o datos. Se requiere prueba manual de integración sobre base de demostración, en particular en formularios, redirecciones y permisos.

## Git y coordinación
Estos cambios se proponen en la rama existente `landing`. No hacer merge a `master` antes de revisar los PRs y realizar pruebas de integración. No se encienden agentes autónomos ni APIs de IA.


## Treemap institucional de producción académica (rama `landing-treemap`)

En `Landing/Inicio.php` los cards fotográficos Artículos, Congresos, Libros y Titulados se sustituyeron por un **treemap con superficies proporcionales al número de registros**. No usa librerías externas: `js/landing-treemap.js` y CSS aislado en `css/style.css`. El gráfico mantiene una leyenda legible con cantidades y porcentajes.

La fuente es `tesco_landing_treemap_produccion()` dentro de `includes/tesco_landing_datos.php`, que consulta SQL Server en modo lectura, agrupando:
- **Proyectos**: filas únicas de `academico.Proyecto`.
- **Publicaciones**: `academico.Publicacion`, desglosadas en artículos, libros, capítulos y otras publicaciones.
- **Trabajos de titulación**: productos cuyo `academico.TipoProducto.Nombre='TITULACION'`, excluyendo los que ya figuran como publicación. **No equivale a contar alumnos egresados**.
- **Otros productos**: registros de `academico.Producto` que no son publicaciones ni titulaciones.
- **Congresos**: filas únicas de `academico.Congreso`, es decir, eventos, no número de participaciones.
- **Otras actividades académicas**: filas de `academico.ActividadAcademica` sin registro asociado en `academico.Congreso`.

No se cuentan docentes, alumnos, convocatorias ni postulaciones como producción; sus indicadores permanecen en la parte superior. Cada entidad subyacente aporta **exactamente una unidad** a una categoría. Las categorías con cantidad cero no ocupan área; si todas están vacías aparece un aviso sin cifras inventadas.

El algoritmo local divide recursivamente la superficie de manera proporcional a los conteos, reacciona a los cambios de tamaño y no necesita CDN. Los porcentajes se redondean solo para su presentación; por ello podrían no sumar 100.0 % al mostrarse. En ausencia de JavaScript la leyenda con cantidad y porcentaje permanece disponible. El SQL no filtra por estado de validación: representa **todos los registros institucionales almacenados**, no solamente producción aprobada.

Pruebas: `php tests/landing_smoke.php` comprueba los nueve grupos, el total y que la portada emplea la visualización en lugar de los antiguos enlaces a archivos HTML de muestra. **Pendiente** verificar los conteos, permisos de lectura y comportamiento responsivo en SQL Server y navegadores reales antes de fusionar a `master`.
