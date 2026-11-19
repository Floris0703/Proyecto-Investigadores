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

/**
 * Recuento de producción académica por tipo, sin duplicar entidades.
 * Cada proyecto, producto o actividad aparece en una sola categoría.
 * No incluye profesores, alumnos, postulaciones ni convocatorias:
 * no son productos académicos.
 */
function tesco_landing_treemap_produccion(): array {
    $rows = tesco_db_rows(
        "SELECT 'proyectos' AS Categoria, COUNT_BIG(*) AS Cantidad
         FROM academico.Proyecto
         UNION ALL
         SELECT CASE UPPER(LTRIM(RTRIM(pub.TipoPublicacion)))
              WHEN 'ARTICULO' THEN 'articulos'
              WHEN 'LIBRO' THEN 'libros'
              WHEN 'CAPITULO' THEN 'capitulos'
              ELSE 'otras_publicaciones' END AS Categoria,
              COUNT_BIG(*) AS Cantidad
         FROM academico.Publicacion pub
         GROUP BY CASE UPPER(LTRIM(RTRIM(pub.TipoPublicacion)))
              WHEN 'ARTICULO' THEN 'articulos'
              WHEN 'LIBRO' THEN 'libros'
              WHEN 'CAPITULO' THEN 'capitulos'
              ELSE 'otras_publicaciones' END
         UNION ALL
         SELECT CASE WHEN UPPER(LTRIM(RTRIM(tp.Nombre)))='TITULACION'
              THEN 'titulaciones' ELSE 'otros_productos' END AS Categoria,
              COUNT_BIG(*) AS Cantidad
         FROM academico.Producto p
         JOIN academico.TipoProducto tp ON tp.TipoProductoId=p.TipoProductoId
         LEFT JOIN academico.Publicacion pub ON pub.ProductoId=p.ProductoId
         WHERE pub.ProductoId IS NULL
         GROUP BY CASE WHEN UPPER(LTRIM(RTRIM(tp.Nombre)))='TITULACION'
              THEN 'titulaciones' ELSE 'otros_productos' END
         UNION ALL
         SELECT 'congresos' AS Categoria, COUNT_BIG(*) AS Cantidad
         FROM academico.Congreso
         UNION ALL
         SELECT 'otras_actividades' AS Categoria, COUNT_BIG(*) AS Cantidad
         FROM academico.ActividadAcademica a
         LEFT JOIN academico.Congreso c ON c.ActividadId=a.ActividadId
         WHERE c.ActividadId IS NULL"
    );

    $catalogo = [
        'proyectos' => 'Proyectos',
        'articulos' => 'Artículos',
        'libros' => 'Libros',
        'capitulos' => 'Capítulos de libro',
        'otras_publicaciones' => 'Otras publicaciones',
        'titulaciones' => 'Trabajos de titulación',
        'otros_productos' => 'Otros productos',
        'congresos' => 'Congresos',
        'otras_actividades' => 'Otras actividades académicas'
    ];
    $counts=array_fill_keys(array_keys($catalogo),0);
    foreach ($rows as $row) {
        $key=(string)($row['Categoria']??'');
        if (array_key_exists($key,$counts)) $counts[$key]+=(int)($row['Cantidad']??0);
    }
    $out=[];
    foreach ($catalogo as $key=>$label) {
        if ($counts[$key]>0) $out[]=['id'=>$key,'nombre'=>$label,'cantidad'=>$counts[$key]];
    }
    usort($out,static fn(array $a,array $b): int =>
        ($b['cantidad']<=>$a['cantidad']) ?: strcmp($a['id'],$b['id']));
    return $out;
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
