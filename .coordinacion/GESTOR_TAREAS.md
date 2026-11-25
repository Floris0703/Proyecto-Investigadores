# Gestor de tareas gratuito (modo supervisado)

El CLI Python `gestor_tareas.py` usa solamente la biblioteca estándar de Python 3. No requiere API de IA, base de datos, credenciales ni servidor adicional.

## Comandos desde la raíz
```bash
python3 .coordinacion/gestor_tareas.py validate
python3 .coordinacion/gestor_tareas.py report
python3 .coordinacion/gestor_tareas.py suggest --paths profesores/index.php Landing/index.html database/migration.sql
python3 .coordinacion/gestor_tareas.py transition --id TESCO-APP-0001 --state pendiente
```
El último comando solo simula; añade `--write` para actualizar **un archivo local** y proponer el cambio mediante una rama y Pull Request. No modifica el repositorio remoto.

## Proceso de equipo
1. Crear una tarea JSON con identificador único en `tareas/`, responsable `agente_destino`, `estado`, `dependencias`, objetivo y criterios.
2. Los cambios en tareas disparan un workflow de validación y resumen en Actions; no ejecutan agentes.
3. El chat especializado consulta el repositorio o se le comparte el enlace a la tarea.
4. El especialista trabaja en una rama, documenta pruebas y crea un PR.
5. El chat coordinador revisa el PR y se solicita aprobación humana para merge.

Estados: propuesta → pendiente → en_progreso → completada. Se admite bloqueada/cancelada desde estados activos. Las dependencias de **otro repositorio** se gestionan manualmente: esta versión solo verifica dependencias locales.

## Límites
- Asignación sugerida por rutas, no análisis semántico del código.
- No notifica a chats de ChatGPT ni abre tareas automáticamente en otros repositorios.
- El interruptor `comunicacion_activa` permanece apagado hasta una autorización independiente.
- No se usa Codex API ni se ejecutan migraciones.
- Los jobs públicos pueden estar incluidos sin costo, pero los de repos privados dependen de la cuota de GitHub Actions del plan.
