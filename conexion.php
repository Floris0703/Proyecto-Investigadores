<?php
// servidor de SQL Server
$servidor = getenv('TESCO_SQL_SERVER') ?: '(localdb)\\MSSQLLocalDB';

//opciones de conexion
$opciones = [
    "DataBase" => (getenv('TESCO_SQL_DATABASE') ?: "TESCoInvestigadores"),
    "CharacterSet" => "UTF-8",
    "Encrypt" => false
];

//establecer conexion
$con = sqlsrv_connect($servidor, $opciones);

//comprobar conexion
if ($con === false) {

    // Mostrar detalles únicamente desde la terminal.
    if (PHP_SAPI === 'cli') {
        echo "ERROR DE CONEXION SQL SERVER:" . PHP_EOL;
        print_r(sqlsrv_errors(SQLSRV_ERR_ALL));
    }

    exit("No fue posible conectar con TESCoInvestigadores.");
}
