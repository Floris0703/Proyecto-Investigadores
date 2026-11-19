#!/usr/bin/env python3
"""Gestor local de tareas TESCo: sin APIs, sin credenciales y sin escrituras remotas."""
import argparse
import json
import pathlib
import sys

ROOT = pathlib.Path(__file__).resolve().parent
TASKS = ROOT / "tareas"
STATES = {"propuesta", "pendiente", "en_progreso", "bloqueada", "completada", "cancelada"}
AGENTS = {"frontend", "backend", "base_datos", "github"}
ALLOWED = {
    "propuesta": {"pendiente", "cancelada"},
    "pendiente": {"en_progreso", "bloqueada", "cancelada"},
    "en_progreso": {"completada", "bloqueada", "cancelada"},
    "bloqueada": {"pendiente", "cancelada"},
    "completada": set(),
    "cancelada": set(),
}

def load():
    config = json.loads((ROOT / "config.json").read_text(encoding="utf-8"))
    if not isinstance(config.get("comunicacion_activa"), bool):
        raise ValueError("config.json: comunicacion_activa debe ser booleano")
    if config.get("ejecucion_automatica") is not False or config.get("integracion_automatica_master") is not False:
        raise ValueError("El modo supervisado requiere ejecución y merge automáticos desactivados")
    tasks = {}
    for path in sorted(TASKS.glob("*.json")):
        task = json.loads(path.read_text(encoding="utf-8"))
        for key in ("id", "titulo", "agente_destino", "repositorio_origen", "estado", "criterios_aceptacion"):
            if key not in task:
                raise ValueError(f"{path.name}: falta {key}")
        if task["id"] in tasks or path.stem != task["id"]:
            raise ValueError(f"{path.name}: ID duplicado o diferente al nombre del archivo")
        if task["agente_destino"] not in AGENTS or task["estado"] not in STATES:
            raise ValueError(f"{path.name}: agente o estado inválido")
        if not isinstance(task["criterios_aceptacion"], list) or not all(isinstance(x, str) for x in task["criterios_aceptacion"]):
            raise ValueError(f"{path.name}: criterios_aceptacion debe ser lista de textos")
        if not isinstance(task.get("dependencias", []), list):
            raise ValueError(f"{path.name}: dependencias debe ser lista")
        tasks[task["id"]] = (task, path)
    for key, (task, _) in tasks.items():
        for dep in task.get("dependencias", []):
            if dep == key:
                raise ValueError(f"{key}: dependencia circular propia")
            if dep in tasks and dep == key:
                raise ValueError(f"{key}: dependencia propia")
        seen = set()
        def visit(node):
            if node in seen:
                return
            seen.add(node)
            for dep in tasks[node][0].get("dependencias", []):
                if dep in tasks:
                    if dep == key:
                        raise ValueError(f"{key}: ciclo de dependencias")
                    visit(dep)
        visit(key)
    return config, tasks

def suggested_agent(path):
    normalized = path.lower().replace("\\", "/")
    if normalized.endswith(".sql") or normalized.startswith("database/"):
        return "base_datos"
    if normalized.endswith(".php") or normalized.startswith(("includes/", "profesores/")):
        return "backend"
    if normalized.endswith((".html", ".css", ".js")) or normalized.startswith(("landing/", "alumnos/")):
        return "frontend"
    if normalized.startswith((".github/", ".coordinacion/")) or normalized == "agents.md":
        return "github"
    return None

def main():
    ap = argparse.ArgumentParser()
    ap.add_argument("command", choices=["validate", "report", "suggest", "transition"])
    ap.add_argument("--paths", nargs="*", default=[])
    ap.add_argument("--id")
    ap.add_argument("--state", choices=sorted(STATES))
    ap.add_argument("--write", action="store_true", help="Escribir solo en el archivo local; requiere PR humano")
    args = ap.parse_args()
    _, tasks = load()
    if args.command == "validate":
        print(f"OK: {len(tasks)} tareas validadas")
    elif args.command == "report":
        for key, (task, _) in sorted(tasks.items()):
            blockers = [d for d in task.get("dependencias", []) if d in tasks and tasks[d][0]["estado"] != "completada"]
            suffix = f" | dependencias pendientes: {','.join(blockers)}" if blockers else ""
            print(f"{key} | {task['agente_destino']} | {task['estado']} | {task['titulo']}{suffix}")
    elif args.command == "suggest":
        grouped = {}
        for path in args.paths:
            agent = suggested_agent(path)
            if agent:
                grouped.setdefault(agent, []).append(path)
        for agent, paths in sorted(grouped.items()):
            print(f"REVISAR {agent}: {', '.join(paths)}")
        if not grouped:
            print("Sin rutas clasificadas: requiere revisión manual")
    else:
        if not args.id or not args.state or args.id not in tasks:
            ap.error("transition requiere --id existente y --state")
        task, path = tasks[args.id]
        current = task["estado"]
        if args.state not in ALLOWED[current]:
            raise ValueError(f"Transición no autorizada: {current} -> {args.state}")
        if args.state in ("en_progreso", "completada"):
            unresolved = [d for d in task.get("dependencias", []) if d not in tasks or tasks[d][0]["estado"] != "completada"]
            if unresolved:
                raise ValueError(f"Dependencias sin completar o externas no verificadas: {unresolved}")
        if not args.write:
            print(f"SIMULACIÓN {args.id}: {current} -> {args.state}; usar --write para editar el archivo local")
            return
        task["estado"] = args.state
        path.write_text(json.dumps(task, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")
        print(f"Archivo local actualizado: {path.name}; requiere revisión y PR")
if __name__ == "__main__":
    try:
        main()
    except (ValueError, OSError, json.JSONDecodeError) as exc:
        print(f"ERROR: {exc}", file=sys.stderr)
        sys.exit(1)
