# Módulo Alumnos — TESCo Investigadores

## Flujo de ingreso
**Portada** `/Landing/index - copia.html` → **login** `/Landing/login.php` → **bienvenida** `/Landing/Inicio.php` → botón **Entrar a mi perfil** → `Landing/panel.php` → `Alumnos/index.php`.

El servidor valida el rol **ALUMNO** en `plataforma.UsuarioRol` y exige que el mismo `UsuarioId` tenga un registro en `academico.Alumno`. Para usuarios multirrol, se elige un rol autorizado en la bienvenida. No existe selección arbitraria de permisos en el formulario de login.

## Páginas
- `Alumnos/index.php`: resumen con matrícula, carrera, semestre, total de postulaciones, en revisión, aceptadas y convocatorias vigentes.
- `Alumnos/perfil.php`: consultar/editar nombres, carrera, semestre y biografía. No modifica correo ni matrícula institucionales.
- `Alumnos/postulaciones.php`: convocatorias publicadas vigentes de profesores, solicitud con CSRF y verificación SQL, historial y estado. La restricción única `UQ_Postulacion_ConvocatoriaUsuario` evita duplicados.
- `Alumnos/cv.php`: presentación, experiencia, formación académica y habilidades; permite agregar/eliminar formación y experiencia, además de declarar, actualizar y eliminar habilidades propias. El nombre se escribe libremente y el nivel se obtiene de `academico.NivelDominio` activo.
- `profesores/cv-postulante.php?postulacion_id=ID`: CV de un alumno asociado a una postulación de **una convocatoria cuyo publicador es el profesor autenticado**, con comprobación de rol y propiedad en SQL. En `profesores/postulaciones.php` se muestra el enlace «Ver CV».

Las URLs HTML anteriores de Alumnos redirigen a las páginas protegidas, no muestran datos simulados.

## Base de datos
Mínimo: modelo base SQL Server + migración `002_perfiles.sql`: `plataforma.Usuario`, `UsuarioRol`, `Rol`, `PerfilUsuario`, `academico.Alumno`, `Convocatoria`, `Postulacion`, `UsuarioHabilidad`, `Habilidad`, `NivelDominio`.

La migración existente `004_ampliacion_modelo_academico.sql` define `academico.ExperienciaAcademica` y `academico.FormacionAcademica`. **No se ejecutó automáticamente**. Si faltan, el CV muestra un aviso y deshabilita esos formularios, sin inventar datos.

Para probar un estudiante, un administrador debe crear **manualmente** una cuenta ACTIVA con contraseña generada usando `password_hash()`, asignarle rol ALUMNO, insertar `plataforma.PerfilUsuario` y `academico.Alumno` con una matrícula única. El estudiante debe tener `UsuarioId` distinto del docente de prueba si son personas diferentes. No guardar claves reales en GitHub.

## Entorno local
```powershell
git switch alumnos
git pull origin alumnos
$env:TESCO_SQL_SERVER = "PC_EMI"
$env:TESCO_SQL_DATABASE = "TESCoInvestigadores"
php -S 127.0.0.1:8000
```
Abrir `http://127.0.0.1:8000/`. Como el usuario local puede no tener la migración 004, verifique tablas en SSMS antes de probar la edición del CV.

## Seguridad y límites
- SQLSRV parametrizado, autorización del rol activo y cuenta ACTIVA, CSRF y formularios POST.
- La postulación a una convocatoria se comprueba nuevamente en el servidor (publicada, fecha de publicación pasada y no vencida).
- La lectura de CV por docentes exige que la convocatoria tenga al docente como `UsuarioPublicadorId`, y requiere que exista la postulación; no hay CV abierto al público.
- **Pendiente** integración real del flujo en SQL Server Windows, revisión de datos personales y pruebas de UI en navegador; los smoke usan SQLSRV simulado. Para producción, preparar políticas de privacidad, consentimiento y retención del CV.
- El CV leído por el profesor es el **CV vigente**, no una copia congelada al momento de postularse.
- Sin modificación de `master`, sin migraciones ejecutadas y sin integración de API de IA.

## Habilidades declaradas por alumnos y funcionalidades pendientes de Administración

- El alumno escribe un nombre libre (máximo 120 caracteres), selecciona un `NivelDominioId` **activo** y opcionalmente indica entre 0 y 1200 meses de experiencia.
- El backend busca una habilidad existente de igual nombre (sin distinguir mayúsculas/minúsculas ni espacios al inicio/final) y la reutiliza. Si no existe, crea un registro en `academico.Habilidad` bajo la categoría técnica **Pendiente de clasificación**; la categoría se crea de forma transaccional solo si falta.
- Se inserta o actualiza **solo la relación del alumno autenticado** en `academico.UsuarioHabilidad`, con estado `DECLARADA`; no se permite que el alumno fije `VALIDADA`, `EN_REVISION`, `VerificadoPorUsuarioId` ni la evidencia.
- Habilidades `DECLARADA` y `RECHAZADA` pueden volver a declararse o eliminarse; `EN_REVISION` y `VALIDADA` permanecen bloqueadas para modificaciones desde Alumnos.
- El CV que visualiza el profesor responsable de una postulación incluye nombre, nivel, experiencia declarada y estado de verificación.

**Backlog del futuro módulo Admin (no implementado aquí):**
1. Revisar propuestas de nombres, corregir duplicados semánticos y asignar la categoría definitiva; la categoría provisional no equivale a una validación.
2. Configurar catálogo institucional de niveles y categorías, y habilitar/deshabilitar entradas.
3. Tramitar solicitudes de verificación, registrar evidencia, persona verificadora y fecha, y controlar las transiciones `DECLARADA → EN_REVISION → VALIDADA/RECHAZADA` en el servidor.
4. Conservar trazabilidad y controles de permisos; evitar que una cuenta de alumno modifique los campos de verificación.

No se modifica el esquema SQL en este PR: se reutilizan `academico.CategoriaHabilidad`, `academico.Habilidad`, `academico.NivelDominio` y `academico.UsuarioHabilidad` existentes. Aún se requiere prueba manual en SQL Server real para verificar colaciones/duplicados y concurrencia.
