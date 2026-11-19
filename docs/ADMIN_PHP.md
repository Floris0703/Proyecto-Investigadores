# TESCo Investigadores — Módulo Administración (rama admin)

## Flujo institucional

Portada → `Landing/login.php` → `Landing/Inicio.php` → **Entrar a mi perfil** → `Landing/panel.php` → `Admin/index.php`.

El usuario debe tener **las tres condiciones**:
1. `plataforma.Usuario.EstadoCuenta` = `ACTIVO` o `ACTIVA`.
2. Relación con rol **ADMIN** en `plataforma.UsuarioRol` + `plataforma.Rol` y rol activo de sesión `ADMIN`.
3. Registro institucional en `plataforma.Administrativo` vinculado al mismo `UsuarioId`, con número de empleado único.

No existe acceso anónimo, cuenta ADMIN por defecto ni modo demo que salte el login. Para multirrol se respeta `Landing/elegir_rol.php`.

## Estilo y navegación

- Se reutiliza `../css/bootstrap-tesco.min.css` (Bootstrap 4.4.1 con paleta TESCo) y los JS locales jQuery, Popper, Bootstrap.
- `css/admin.css` solo incorpora ajustes responsivos.
- Navbar de inicio, resumen, habilidades, perfil; menú lateral modal para el resto de vistas. Links al inicio institucional y cierre de sesión con CSRF.
- Plantillas compartidas en `includes/admin_layout.php`.

## Páginas funcionales

| Vista | Función |
|---|---|
| `Admin/index.php` | Nueve contadores agregados desde SQL Server, sin cifras simuladas |
| `Admin/usuarios.php` | Consultar/buscar usuarios (hasta 100), registrar ALUMNO o PROFESOR, asignar un rol adicional, activar/desactivar cuentas no ADMIN |
| `Admin/convocatorias.php` | Supervisión global, cierre/reactivación administrativa sin borrar solicitudes |
| `Admin/postulaciones.php` | Consulta global del estado de solicitudes, sin aceptar/rechazar desde Admin |
| `Admin/habilidades.php` | Revisar/validar/rechazar habilidades con evidencia, clasificar nombres/categorías, gestionar niveles y categorías activos |
| `Admin/produccion.php` | Consulta institucional de proyectos, publicaciones y congresos |
| `Admin/perfil.php` | Nombre, apellidos, biografía, área y cargo del administrativo |

## Contrato de habilidades

Se utilizan las entidades existentes `academico.CategoriaHabilidad`, `academico.Habilidad`, `academico.NivelDominio` y `academico.UsuarioHabilidad`.

- El alumno registra libremente el nombre y selecciona un `NivelDominioId` activo; la habilidad nueva entra en categoría provisional **Pendiente de clasificación**, con estado `DECLARADA`.
- Admin puede clasificar/renombrar el catálogo sin cambiar automáticamente la verificación de cada alumno.
- Transiciones permitidas desde Admin: `DECLARADA → EN_REVISION → VALIDADA/RECHAZADA`.
- Para `VALIDADA` se exige una referencia de evidencia (máximo 500 caracteres); se almacenan `VerificadoPorUsuarioId` y `FechaVerificacion` automáticamente. Para `RECHAZADA`, se registran igualmente verificador y fecha.
- `EN_REVISION` carece de verificador/fecha de cierre para cumplir los CHECK del esquema base.
- Está prohibido verificar una habilidad de la misma cuenta administrativa. El estudiante no puede editar estados bajo revisión o ya validados.
- Se evita la fusión automática de duplicados semánticos, porque podría sobrescribir evidencia o relaciones `UsuarioHabilidad` ya verificadas. La normalización y clasificación se realizan mediante actualización conservadora.

## Gestión de cuentas

- El registro valida datos, correo y longitud de contraseña; usa `password_hash(PASSWORD_DEFAULT)` y transacción SQL para insertar usuario, rol, perfil y registro de alumno/profesor.
- No muestra ni transmite contraseñas en listados. La contraseña inicial debe distribuirse por un canal institucional apropiado y cambiarse antes de producción.
- Las matrículas y números de empleado son únicos: duplicados generan rechazo por integridad de SQL Server.
- La asignación de rol adicional **no revoca** roles existentes ni otorga ADMIN; exige un identificador académico para el nuevo perfil.
- El propio usuario ADMIN no puede desactivarse desde esta vista, y ninguna cuenta que tenga rol ADMIN puede desactivarse desde ella.
- El sistema no contiene recuperación de contraseña, gestión de altas ADMIN ni mecanismo de consentimiento del alumno. Estos procesos deben contar con definición institucional antes de producción.

## Supervisión de convocatorias y postulaciones

- Admin puede cambiar convocatorias publicadas/abiertas a `CERRADA` y volver a `PUBLICADA` si fueron cerradas; se conserva la fecha de publicación y el historial de postulaciones.
- El profesor continúa siendo responsable de publicar nuevas convocatorias y de decidir el estado de sus propios aspirantes.
- Admin solo consulta postulaciones, sin poder acceder desde ese listado al CV privado ni alterar decisiones.
- La reactivación debe ser supervisada institucionalmente si `FechaCierre` ya venció: una reactivación no extiende el plazo existente.

## Prueba local

Desde la raíz del repositorio:

```powershell
git fetch origin
git switch admin
git pull origin admin
$env:TESCO_SQL_SERVER = "PC_EMI"
$env:TESCO_SQL_DATABASE = "TESCoInvestigadores"
php -S 127.0.0.1:8000
```

Abrir `http://127.0.0.1:8000/` y autenticar con usuario ADMIN institucional válido.

Para comprobar si existen usuarios ADMIN completos antes de acceder, ejecutar en SSMS **solo lectura**:

```sql
USE TESCoInvestigadores;
SELECT u.UsuarioId,u.Correo,u.EstadoCuenta,ad.NumeroEmpleado
FROM plataforma.Usuario u
JOIN plataforma.UsuarioRol ur ON ur.UsuarioId=u.UsuarioId
JOIN plataforma.Rol r ON r.RolId=ur.RolId AND r.Nombre='ADMIN'
LEFT JOIN plataforma.Administrativo ad ON ad.UsuarioId=u.UsuarioId;
```

Si no devuelve una cuenta con `NumeroEmpleado`, un responsable de BD deberá realizar el alta institucional con `PasswordHash` producido por `password_hash`, además de `plataforma.PerfilUsuario`; nunca se genera un administrador de forma automática.

## Pruebas y limitaciones

```bash
php -l includes/admin_datos.php
php -l includes/admin_layout.php
for page in index.php perfil.php usuarios.php convocatorias.php postulaciones.php habilidades.php produccion.php; do php tests/admin_smoke.php "$page"; done
```

- GitHub Actions realiza lint y render de cada vista con SQLSRV simulado.
- **Pendiente** prueba manual contra SQL Server real: altas, búsquedas, auditoría de permisos por rol y conflictos/concurrencia, cambios de estado, formularios móviles y revisión de evidencias.
- Los listados de gestión contienen un límite de 100 filas sin paginación persistente; serán necesarios índices/paginación para bases grandes.
- No hay bitácora independiente de cambios administrativos; conviene agregarla antes de operar datos de producción.
- El módulo no modifica el esquema ni ejecuta migraciones. Requiere las migraciones 002 y 004 ya aplicadas en el entorno de desarrollo.
- Preparar HTTPS, rate limiting persistente, un proceso de protección de datos y controles de acceso para producción.

**Control de Git:** trabajar en `admin`, revisión por PR; no actualizar `master` ni hacer merge sin autorización.
