<?php
/** TESCo | indicadores y convocatorias públicas: solo consultas de lectura. */
declare(strict_types=1);
require_once __DIR__ . '/tesco_db.php';

function tesco_landing_metricas(): array {
    $row = tesco_db_one(
        "SELECT
          (SELECT COUNT_BIG(*) FROM academico.Profesor) AS Profesores,
          (SELECT COUNT_BIG(*) FROM academico.Proyecto) AS Proyectos,
          (SELECT COUNT_BIG(*) FROM academico.Publicacion
            WHERE UPPER(LTRIM(RTRIM(TipoPublicacion))) = 'ARTICULO') AS Articulos,
          (SELECT COUNT_BIG(*) FROM academico.Congreso) AS Congresos"
    );
    if (!$row) throw new RuntimeException('No se recibieron métricas.');
    return [
        'profesores' => (int)$row['Profesores'],
        'proyectos' => (int)$row['Proyectos'],
        'articulos' => (int)$row['Articulos'],
        'congresos' => (int)$row['Congresos']
    ];
}

/** Solo publicadas y vigentes; BORRADOR y CERRADA no aparecen. */
function tesco_landing_convocatorias(int $limit = 6): array {
    $limit = max(1, min($limit, 12));
    return tesco_db_rows(
        "SELECT TOP ($limit) c.ConvocatoriaId,c.Titulo,c.Descripcion,
                c.FechaPublicacion,c.FechaCierre
         FROM academico.Convocatoria c
         WHERE c.Estado IN ('PUBLICADA','ABIERTA')
           AND c.FechaPublicacion IS NOT NULL
           AND c.FechaPublicacion <= SYSDATETIME()
           AND (c.FechaCierre IS NULL OR c.FechaCierre >= SYSDATETIME())
         ORDER BY c.FechaPublicacion DESC,c.ConvocatoriaId DESC"
    );
}
function tesco_landing_convocatoria(int $id): ?array {
    if ($id < 1) return null;
    return tesco_db_one(
        "SELECT c.ConvocatoriaId,c.Titulo,c.Descripcion,c.FechaPublicacion,c.FechaCierre
         FROM academico.Convocatoria c
         WHERE c.ConvocatoriaId=?
           AND c.Estado IN ('PUBLICADA','ABIERTA')
           AND c.FechaPublicacion IS NOT NULL
           AND c.FechaPublicacion <= SYSDATETIME()
           AND (c.FechaCierre IS NULL OR c.FechaCierre >= SYSDATETIME())",
        [$id]
    );
}
function tesco_landing_fecha(mixed $value): string {
    if ($value instanceof DateTimeInterface) return $value->format('d/m/Y');
    if (!is_string($value) || $value === '') return 'Sin fecha de cierre';
    try { return (new DateTimeImmutable($value))->format('d/m/Y'); }
    catch (Exception) { return 'Fecha no disponible'; }
}
