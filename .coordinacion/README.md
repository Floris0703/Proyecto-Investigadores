# Coordinación TESCo Investigadores

## Objetivo
Compartir contexto y avisos **entre repositorios**, manteniendo aprobación humana para cambios de código, migraciones y merges. Los chats de ChatGPT no reciben ni ejecutan instrucciones automáticamente: este mecanismo intercambia señales mediante GitHub Actions.

## Carpetas
- `config.json`: interruptor y límites. `comunicacion_activa: false` por defecto.
- `tareas/`: solicitudes versionadas; cada tarea tiene un ID único, destinatario, origen, estado, dependencias y criterios de aceptación.
- `contexto/`: contratos y resúmenes; nunca credenciales ni datos privados.
- `resultados/`: reportes revisables de ejecuciones humanas o agentes externos.
- `logs/`: documentación histórica; el log operativo inicial vive en la pestaña Actions, no crea commits automáticos.

## Protocolo
1. Un especialista abre rama/PR con modificaciones y, cuando proceda, una tarea JSON.
2. GitHub Actions valida la sintaxis y estructura. En `master`, si está habilitado y existe el secreto `TESCO_COORDINATION_TOKEN`, notifica al repositorio par mediante `repository_dispatch`.
3. El repositorio receptor comprueba el interruptor de comunicación y el identificador de origen; solo entonces registra el aviso en el resumen de Actions. Cuando el origen es la BD privada, no se expone su SHA en el repositorio público.
4. El especialista responsable consulta el aviso y prepara PR, pero **no se despiertan chats automáticamente**.
5. El chat coordinador revisa PR y la persona usuaria decide el merge.

## Activación
- Tras aprobar/mezclar los PR en ambas ramas `master`, configurar en **cada** repositorio un secreto Actions `TESCO_COORDINATION_TOKEN`. Utilizar GitHub App installation token o fine-grained PAT con **Contents: write** en el repositorio destinatario; mínimo privilegio y rotación.
- Configurar `comunicacion_activa: true` mediante PR autorizado en cada repositorio. Inicialmente permanece apagado.
- Detener: establecer `false` y revocar el secreto cuando corresponda. El workflow comprueba el interruptor antes de enviar avisos, aunque no interrumpe ejecuciones ya iniciadas.
- El workflow verifica el origen declarado del evento, pero esta verificación no sustituye la autenticación de quien envía el evento. No se usa polling ni se sincroniza información sensible del repositorio privado al público. La señal desde la aplicación pública lleva su nombre y SHA de origen. Desde BD privada hacia la aplicación pública se utiliza solamente un identificador genérico, sin SHA privado.
- `repository_dispatch` y `workflow_dispatch` necesitan que el workflow receptor exista en la rama predeterminada. Las ejecuciones sobre PR validan, pero no envían avisos.
- Deshabilitar Actions en ajustes del repositorio si se necesita un corte de emergencia absoluto.

## Límites
La automatización es de **comunicación/detección**, no de edición automática por Codex. Un ejecutor externo necesitaría una implementación adicional y consentimiento explícito.
