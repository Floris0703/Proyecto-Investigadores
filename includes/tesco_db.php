<?php
/** TESCo | conexión compartida de lectura para Landing y autenticación. */
declare(strict_types=1);

function tesco_db(): mixed {
    static $conn = null;
    if ($conn !== null) return $conn;
    if (!function_exists('sqlsrv_connect')) {
        error_log('TESCo: falta la extension PHP sqlsrv');
        throw new RuntimeException('Base de datos no disponible.');
    }
    $server = getenv('TESCO_SQL_SERVER') ?: '(localdb)\\MSSQLLocalDB';
    $name = getenv('TESCO_SQL_DATABASE') ?: 'TESCoInvestigadores';
    $options = ['Database' => $name, 'CharacterSet' => 'UTF-8', 'Encrypt' => false];
    $conn = @sqlsrv_connect($server, $options);
    if ($conn === false) {
        error_log('TESCo: error de conexion SQLSRV (revisar configuracion local)');
        throw new RuntimeException('Base de datos no disponible.');
    }
    return $conn;
}

/** Consulta parametrizada. No muestra errores SQL al visitante. */
function tesco_db_rows(string $sql, array $parameters = []): array {
    $stmt = @sqlsrv_query(tesco_db(), $sql, $parameters);
    if ($stmt === false) {
        error_log('TESCo: fallo en consulta SQLSRV');
        throw new RuntimeException('No fue posible consultar la base de datos.');
    }
    try {
        $rows = [];
        while (($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) !== null) {
            if ($row === false) throw new RuntimeException('No fue posible leer resultados.');
            $rows[] = $row;
        }
        return $rows;
    } finally {
        sqlsrv_free_stmt($stmt);
    }
}
function tesco_db_one(string $sql, array $parameters = []): ?array {
    return tesco_db_rows($sql, $parameters)[0] ?? null;
}
function tesco_escape(mixed $value): string {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
