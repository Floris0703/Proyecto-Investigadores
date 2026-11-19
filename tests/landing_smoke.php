<?php
/** Pruebas deterministas de Landing y login usando SQLSRV simulado; no conectan a LocalDB. */
declare(strict_types=1);
define('SQLSRV_FETCH_ASSOC', 2);
$GLOBALS['tesco_test_state'] = 'ACTIVO';

function sqlsrv_connect(string $server, array $options): object { return (object)['connected' => true]; }
function sqlsrv_query(object $conn, string $sql, array $params = []): object {
    $rows = [];
    if (str_contains($sql, "SELECT 'proyectos' AS Categoria") && str_contains($sql, 'UNION ALL')) {
        $rows = [
            ['Categoria'=>'proyectos','Cantidad'=>5],
            ['Categoria'=>'articulos','Cantidad'=>3],
            ['Categoria'=>'libros','Cantidad'=>2],
            ['Categoria'=>'capitulos','Cantidad'=>1],
            ['Categoria'=>'otras_publicaciones','Cantidad'=>1],
            ['Categoria'=>'titulaciones','Cantidad'=>2],
            ['Categoria'=>'otros_productos','Cantidad'=>1],
            ['Categoria'=>'congresos','Cantidad'=>4],
            ['Categoria'=>'otras_actividades','Cantidad'=>1]
        ];
    } elseif (str_contains($sql, 'COUNT_BIG(*)')) {
        $rows = [['Profesores'=>2, 'Proyectos'=>3, 'Articulos'=>4, 'Congresos'=>5]];
    } elseif (str_contains($sql, 'FROM academico.Convocatoria')) {
        if (!str_contains($sql, "'PUBLICADA','ABIERTA'") || !str_contains($sql, 'FechaCierre')) {
            throw new RuntimeException('El filtro de convocatorias debe aplicar vigencia y publicación.');
        }
        $rows = [['ConvocatoriaId'=>4, 'Titulo'=>'Convocatoria de prueba',
                  'Descripcion'=>'Prueba SQL', 'FechaPublicacion'=>new DateTimeImmutable(),
                  'FechaCierre'=>null]];
    } elseif (str_contains($sql, 'SELECT UsuarioId,PasswordHash,EstadoCuenta')) {
        if (($params[0] ?? '') === 'docente@tesco.mx') {
            $rows = [['UsuarioId'=>17,
                      'PasswordHash'=>password_hash('secreto-de-prueba', PASSWORD_DEFAULT),
                      'EstadoCuenta'=>$GLOBALS['tesco_test_state']]];
        }
    } elseif (str_contains($sql, 'FROM plataforma.Usuario u')) {
        $rows = $GLOBALS['tesco_test_state'] === 'ACTIVO' ? [['Nombre'=>'PROFESOR']] : [];
    }
    return (object)['rows'=>$rows, 'at'=>0];
}
function sqlsrv_fetch_array(object $stmt, int $mode): array|null {
    return $stmt->rows[$stmt->at++] ?? null;
}
function sqlsrv_free_stmt(object $stmt): bool { return true; }

require_once __DIR__ . '/../includes/tesco_landing_datos.php';
require_once __DIR__ . '/../includes/tesco_auth.php';
function tesco_check(bool $condition, string $message): void {
    if (!$condition) throw new RuntimeException('Falló: '.$message);
}
$metricas = tesco_landing_metricas();
tesco_check($metricas === ['profesores'=>2,'proyectos'=>3,'articulos'=>4,'congresos'=>5], 'métricas globales');
$treemap=tesco_landing_treemap_produccion();
tesco_check(count($treemap)===9, 'treemap cubre todas las categorias de produccion');
tesco_check(array_sum(array_column($treemap,'cantidad'))===20, 'treemap suma registros sin duplicar');
tesco_check($treemap[0]['id']==='proyectos' && $treemap[0]['cantidad']===5, 'orden descendente por cantidad');
tesco_check($treemap[1]['id']==='congresos' && $treemap[1]['cantidad']===4, 'congresos únicos');
tesco_check(count(tesco_landing_convocatorias()) === 1, 'convocatorias visibles');
tesco_check(tesco_landing_convocatoria(4)['ConvocatoriaId'] === 4, 'detalle convocatorias');
tesco_check(tesco_landing_convocatoria(0) === null, 'ID inválido');
tesco_check(tesco_escape('<script>') === '&lt;script&gt;', 'escape HTML');
tesco_check(tesco_landing_fecha(null) === 'Sin fecha de cierre', 'fecha nula');
tesco_session_start();
tesco_check(tesco_csrf_valid(tesco_csrf_public()), 'CSRF válido');
tesco_check(!tesco_csrf_valid('token-falso'), 'CSRF inválido');
tesco_check(!tesco_login('docente@tesco.mx','incorrecta'), 'contraseña incorrecta');
tesco_check(tesco_login('docente@tesco.mx','secreto-de-prueba'), 'login correcto');
tesco_check(tesco_usuario_actual() === 17, 'sesión con UsuarioId');
tesco_check(tesco_roles_actuales() === ['PROFESOR'], 'roles desde BD');
$GLOBALS['tesco_test_state'] = 'PENDIENTE';
tesco_check(tesco_roles_actuales() === [], 'cuenta deshabilitada invalida sesión');
tesco_check(tesco_usuario_actual() === null, 'sesión revocada');
// Verificación estática de la secuencia de rutas; no depende de SQL Server.
$raiz = dirname(__DIR__);
$portada = file_get_contents($raiz . '/Landing/index - copia.html');
$raizHtml = file_get_contents($raiz . '/index.html');
$login = file_get_contents($raiz . '/Landing/login.php');
$procesoLogin = file_get_contents($raiz . '/includes/tesco_auth.php');
$bienvenida = file_get_contents($raiz . '/Landing/Inicio.php');
$salida = file_get_contents($raiz . '/Landing/cerrar_sesion.php');
$conexionCompartida = file_get_contents($raiz . '/conexion.php');
tesco_check(str_contains($raizHtml, 'Landing/index%20-%20copia.html'), 'portada como entrada');
tesco_check(substr_count($portada, 'href="login.php"') >= 3, 'tres accesos desde portada al login');
tesco_check(str_contains($login, 'action="autenticar.php"') && str_contains($login, 'name="csrf"'), 'login con POST y CSRF');
tesco_check(str_contains($procesoLogin, "header('Location: Inicio.php', true, 303)"), 'redirección tras login a bienvenida');
tesco_check(str_contains($bienvenida, '$roles = tesco_require_login();'), 'bienvenida protegida');
tesco_check(str_contains($bienvenida, 'id="tescoTreemap"') && str_contains($bienvenida, 'landing-treemap.js'), 'treemap integrado en bienvenida');
tesco_check(!str_contains($bienvenida, 'news-tile') && !str_contains($bienvenida, 'href="Historia.html"'), 'cards y enlaces heredados sustituidos');
tesco_check(str_contains($bienvenida, 'tesco_landing_treemap_produccion()'), 'treemap alimentado por BD');
$scriptTreemap=file_get_contents($raiz.'/js/landing-treemap.js');
tesco_check(str_contains($scriptTreemap, 'layout(indexed') && str_contains($scriptTreemap, 'Number(item.cantidad)'), 'áreas proporcionales calculadas con JS local');
tesco_check(str_contains($bienvenida, "elegir_rol.php") && str_contains($bienvenida, "panel.php") && str_contains($bienvenida, 'Entrar a mi perfil'), 'CTA hacia panel o rol');
tesco_check(str_contains($salida, 'index%20-%20copia.html'), 'logout retorna a portada');
tesco_check(str_contains($conexionCompartida, "getenv('TESCO_SQL_SERVER')"), 'conexión de Profesores configurable');
$profesorDatos = file_get_contents($raiz . '/includes/profesor_datos.php');
tesco_check(str_contains($profesorDatos, 'CASE WHEN ?=1 THEN SYSDATETIME() ELSE NULL END'), 'convocatorias usan el reloj de SQL Server');
tesco_check(!str_contains($profesorDatos, '$publicada?date(\'Y-m-d H:i:s\'):null'), 'no usar hora PHP para publicar');
echo "OK: Landing, secuencia de navegación y autenticación simulada\n";
