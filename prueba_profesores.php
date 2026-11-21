<?php

// Solo ejecutaremos esta prueba desde la terminal.
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Prueba disponible solo en terminal.");
}

// Reutilizamos la conexión que ya funciona.
require_once __DIR__ . '/conexion.php';

// Consultamos los perfiles de los profesores.
$sql = "
    SELECT
        p.ProfesorId,
        pr.Nombre,
        pr.ApellidoPaterno,
        pr.ApellidoMaterno,
        u.Correo,
        p.Departamento,
        p.GradoAcademico
    FROM academico.Profesor AS p
    INNER JOIN plataforma.Usuario AS u
        ON p.UsuarioId = u.UsuarioId
    LEFT JOIN plataforma.PerfilUsuario AS pr
        ON p.UsuarioId = pr.UsuarioId
    ORDER BY p.ProfesorId
";

// Ejecutamos la consulta.
$resultado = sqlsrv_query($con, $sql);

if ($resultado === false) {
    print_r(sqlsrv_errors());
    exit(1);
}

// Recorremos los registros obtenidos.
while ($profesor = sqlsrv_fetch_array(
    $resultado,
    SQLSRV_FETCH_ASSOC
)) {

    echo "------------------------" . PHP_EOL;

    echo "ID: " . $profesor["ProfesorId"] . PHP_EOL;

    echo "Nombre: "
        . ($profesor["Nombre"] ?? "")
        . " "
        . ($profesor["ApellidoPaterno"] ?? "")
        . " "
        . ($profesor["ApellidoMaterno"] ?? "")
        . PHP_EOL;

    echo "Correo: "
        . ($profesor["Correo"] ?? "")
        . PHP_EOL;

    echo "Departamento: "
        . ($profesor["Departamento"] ?? "Sin registrar")
        . PHP_EOL;
}

// Cerramos recursos.
sqlsrv_free_stmt($resultado);
sqlsrv_close($con);
?>
