<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/tesco_auth.php';
tesco_session_start();
header('Cache-Control: no-store');
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Método no permitido.');
}
if (!tesco_csrf_valid($_POST['csrf'] ?? null)) {
    http_response_code(403);
    exit('Token inválido.');
}
tesco_logout_local();
header('Location: Inicio.php', true, 303);
exit;
