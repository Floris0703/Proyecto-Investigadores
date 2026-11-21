
<?php

// Esta prueba solamente debe ejecutarse desde la terminal.
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Prueba disponible solamente en terminal.");
}

// Reutilizar la conexión.
require_once __DIR__ . '/conexion.php';

// Consulta de solo lectura.
$sql = "
    SELECT
        DB_NAME() AS BaseActual,
        COUNT(*) AS TotalProfesores
    FROM academico.Profesor
";

$resultado = sqlsrv_query($con, $sql);

if ($resultado === false) {
    print_r(sqlsrv_errors());
    exit(1);
}

// Obtener una fila.
$fila = sqlsrv_fetch_array(
    $resultado,
    SQLSRV_FETCH_ASSOC
);

echo "Conexión exitosa" . PHP_EOL;
echo "Base de datos: " . $fila["BaseActual"] . PHP_EOL;
echo "Total de profesores: " . $fila["TotalProfesores"] . PHP_EOL;

// Liberar recursos.
sqlsrv_free_stmt($resultado);
sqlsrv_close($con);
