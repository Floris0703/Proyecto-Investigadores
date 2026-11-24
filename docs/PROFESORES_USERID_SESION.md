# Profesores — identidad dinámica por UsuarioId

## Estado y alcance
- Nueve vistas del profesor utilizan `includes/profesor_datos.php` con el login institucional existente.
- La identidad proviene de `tesco_usuario_actual()`, leído de `$_SESSION['usuario_id']` por `includes/tesco_auth.php`.
- El sistema vuelve a verificar roles activos desde `plataforma.UsuarioRol` + `plataforma.Rol` y `EstadoCuenta IN ('ACTIVO','ACTIVA')`.
- Exige `$_SESSION['tesco_rol_activo'] === 'PROFESOR'` y perfil asociado en `academico.Profesor.UsuarioId`.
- Los datos de las páginas se filtran por **UsuarioId** o por el **ProfesorId** derivado de ese registro; no se acepta ninguno de ellos del navegador.
- Eliminado el mecanismo de identidad fija `TESCO_PROFESOR_ID_DEV`; sin sesión se redirige al login.
- La misma conexión `tesco_db()` del módulo de autenticación se reutiliza en todas las consultas y transacciones docentes.
- Se conserva Bootstrap TESCo y el menú vertical desplegable. Su cierre de sesión realiza POST con CSRF al endpoint oficial `Landing/cerrar_sesion.php`.
- La cabecera dirige a la bienvenida protegida `Landing/Inicio.php`.

## Flujo esperado
1. Iniciar servidor desde raíz: `php -S 127.0.0.1:8000`.
2. Abrir `http://127.0.0.1:8000/Landing/login.php`.
3. Acceder con una cuenta válida, activa y rol PROFESOR.
4. Después de la bienvenida usar «Entrar a mi perfil». Para multirrol, elegir explícitamente PROFESOR.
5. `Landing/panel.php` verifica que exista un registro docente y redirige a `profesores/index.php`.
6. Las nueve pantallas heredan el usuario autenticado y sus datos asociados.

## Contrato SQL
```sql
SELECT p.ProfesorId,p.UsuarioId,p.Departamento,p.GradoAcademico,u.Correo,
       per.Nombre,per.ApellidoPaterno,per.ApellidoMaterno,per.Biografia
FROM academico.Profesor p
JOIN plataforma.Usuario u ON u.UsuarioId=p.UsuarioId
LEFT JOIN plataforma.PerfilUsuario per ON per.UsuarioId=u.UsuarioId
WHERE p.UsuarioId=?
  AND UPPER(LTRIM(RTRIM(u.EstadoCuenta))) IN ('ACTIVO','ACTIVA')
```
El parámetro es el `UsuarioId` de la sesión, nunca el ID ingresado por URL.

## Pruebas
- `php -l includes/profesor_datos.php` y prueba sintáctica de los archivos PHP.
- `php tests/profesor_smoke.php index.php` para un usuario docente simulado.
- `php tests/profesor_smoke.php index.php uid12` verifica otro UsuarioId y otro nombre.
- El CI comprueba denegación de permisos por rol incorrecto, cuenta inactiva o perfil inexistente.
- El CI no se conecta a LocalDB. Verificar en Windows con DOS cuentas institucionales para asegurar el aislamiento de datos.

## Sin cambios
No se modificaron `Landing/`, `Alumnos/`, `Admin/`, `conexion.php` ni el DDL.
