# Migración del módulo de profesor a PHP

- Nueve pantallas PHP con componentes compartidos de Bootstrap 4.4.1.
- Menú de hamburguesa **vertical y desplegable** mediante modal y `nav flex-column`; no hay sidebar fija.
- Azul marino, verde lima y beige en `css/bootstrap-tesco.min.css`.
- HTML originales, Landing y Alumnos sin cambios.
- `profesores/index.php` consulta SQL Server LocalDB con la configuración de desarrollo en `conexion.php`. Las otras ocho pantallas contienen datos de demostración.

## Pruebas

`php -S localhost:8000` en la raíz. Abrir `/profesores/perfil.php` para comprobar diseño y menú; `/profesores/index.php` requiere SQLSRV y LocalDB.

## Seguridad

No hay autenticación, edición real ni SP. `conexion.php` no contiene contraseñas y su cifrado deshabilitado es solo para LocalDB. No poner en producción.
