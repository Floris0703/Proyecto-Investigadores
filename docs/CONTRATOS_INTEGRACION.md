# Contratos de integración — TESCo Investigadores

## Repositorios
- Aplicación: `Floris0703/Proyecto-Investigadores` (público).
- Base de datos: `Floris0703/DB-InvestigadoresTESCo` (privado).

## Módulos
- Frontend: campos, rutas HTTP y presentaciones.
- Backend: seguridad, validación, consultas SQLSRV y operaciones.
- Base de Datos: nombres reales de esquemas/tablas/columnas, restricciones y migraciones.
- GitHub: valida dependencias y coordina merges.

## Reglas de cambios
- Cambiar tabla o columna: registrar migración, compatibilidad y consulta PHP afectada.
- Cambiar ruta o campo de formulario: actualizar el contrato esperado del Backend.
- Cambiar endpoints o sesiones: notificar a Frontend y BD si cambia la persistencia.
- Cambiar estructura compartida: PR con sección de impacto interdisciplinar.
- No declarar una migración aplicada en LocalDB por el hecho de existir en GitHub.

## Contrato de notificaciones
Evento: `repository_dispatch`, tipo `tesco_context_update`.
Campos de payload: `source_repo`, `source_sha`, `change_type`. No enviar diffs ni contenidos privados.
Envío condicionado a `comunicacion_activa` y secreto `TESCO_COORDINATION_TOKEN`. No hay acciones automáticas sobre código en el receptor.
