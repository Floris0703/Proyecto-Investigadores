# TESCo Investigadores — Demostración PHP con base académica de 60 tablas

## Alcance
- Nueve módulos de profesores: resumen, perfil, productividad, proyectos, publicaciones, congresos, titulación, convocatorias y postulaciones.
- Se conserva el tema institucional de Bootstrap 4.4.1, el encabezado y el menú vertical desplegable por hamburguesa.
- Capa compartida de consultas y escrituras: `includes/profesor_datos.php`. Componentes visuales: `includes/profesor_visual.php`.
- **No se implementa ni modifica el login** del compañero. No se toca `Alumnos/`, `Landing/` o el esquema SQL.
- La aplicación detecta automáticamente tablas y columnas añadidas por la migración 004; los apartados ausentes indican que requieren dicha migración.
- Los formularios modifican **datos reales** de LocalDB. Usa exclusivamente una base de demostración.

## Preparación en Windows
1. Verificar que `TESCoInvestigadores` esté disponible en `(localdb)\MSSQLLocalDB` y que funcionen `sqlsrv` y ODBC.
2. Para habilitar **las 60 tablas**, aplicar la migración del repositorio `Floris0703/DB-InvestigadoresTESCo` siguiendo `database/docs/004_guia_migracion.md`, comenzando por una copia de prueba. Los archivos del repositorio no ejecutan migraciones automáticamente.
3. Abrir PowerShell en la raíz del proyecto.
4. Ejecutar:
   ```powershell
   $env:TESCO_PROFESOR_ID_DEV = "1"
   php -S 127.0.0.1:8000
   ```
5. Abrir `http://127.0.0.1:8000/profesores/index.php`.

## Contrato de identidad
- Demostración local: `TESCO_PROFESOR_ID_DEV` es **academico.Profesor.ProfesorId**. Solo se acepta si `REMOTE_ADDR` es loopback.
- Futuro login: `$_SESSION['usuario_id']` es **plataforma.Usuario.UsuarioId** y se resuelve contra `academico.Profesor.UsuarioId`.
- No se admite elegir profesor mediante parámetro GET.
- Si falla la identificación, el módulo rechaza el acceso.
- CSRF en formularios y consultas parametrizadas mediante SQLSRV.

## Contrato de lectura/escritura
| Pantalla | Lectura | Escritura |
|---|---|---|
| Resumen | Profesor, participante, contribuciones, actividad, convocatorias, titulación | Ninguna |
| Perfil | Usuario, PerfilUsuario, Profesor; 004 opcional: formación, experiencia, líneas, reconocimientos, horarios | PerfilUsuario + Profesor |
| Productividad | Agrupación anual de las entidades del profesor | Ninguna |
| Proyectos | Proyecto + ProyectoParticipante | INSERT relacionado, transacción |
| Publicaciones | Producto + Publicacion + ContribucionProducto | INSERT relacionado, transacción |
| Congresos | ActividadAcademica + Congreso + ParticipacionActividad | INSERT relacionado, transacción |
| Titulación | Producto + TrabajoTitulacion + TitulacionDirector | INSERT relacionado, transacción |
| Convocatorias | Convocatoria por UsuarioPublicadorId | INSERT con publicación/borrador |
| Postulaciones | Postulacion + Convocatoria + Usuario + PerfilUsuario | UPDATE de estado; con 004, historial PostulacionMovimiento |

## Criterios de conteo
Se consideran actividades con fecha asignable al año. Las convocatorias se cuentan como publicadas solo en estados PUBLICADA, ABIERTA o CERRADA, no borradores. Productividad no ofrece una calificación ni ponderación institucional. Los trabajos de titulación también se muestran en total histórico.

## Límites conocidos
- No incluye el acceso público, el login, el dashboard de alumnos o el módulo administrativo.
- No incluye subida de archivos adjuntos ni integración de los 60 módulos CRUD.
- Los datos vacíos son esperados cuando no existen registros vinculados al profesor de prueba; no se inventan registros.
- La migración 004 **no puede aplicarse desde GitHub** al equipo local. Es una ejecución separada en SSMS.
- Los formularios de demo no implementan edición/borrado completo de cada entidad ni la validación institucional.
- La extensión sqlsrv y el servidor LocalDB no están disponibles en CI; la ejecución real debe validarse en Windows antes de presentar.
