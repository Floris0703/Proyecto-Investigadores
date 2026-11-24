<?php
/** Validaciones críticas puramente en PHP; no abre conexiones SQL Server. */
declare(strict_types=1);
require_once __DIR__.'/../includes/admin_datos.php';
$tests=0;
function rechaza(callable $fn,string $descripcion): void {
    global $tests;
    try {$fn();}
    catch (InvalidArgumentException) {$tests++;return;}
    throw new RuntimeException('Debió rechazar: '.$descripcion);
}
$admin=['UsuarioId'=>9];
rechaza(static fn()=>tesco_admin_estado_usuario($admin,['usuario_id'=>'9','estado'=>'INACTIVO']),'autodesactivación');
rechaza(static fn()=>tesco_admin_revision_habilidad($admin,[
    'usuario_id'=>'9','habilidad_id'=>'4','estado'=>'VALIDADA','evidencia'=>'folio'
]),'autoverificación');
rechaza(static fn()=>tesco_admin_revision_habilidad($admin,[
    'usuario_id'=>'7','habilidad_id'=>'4','estado'=>'VALIDADA','evidencia'=>''
]),'validación sin evidencia');
rechaza(static fn()=>tesco_admin_revision_habilidad($admin,[
    'usuario_id'=>'7','habilidad_id'=>'4','estado'=>'CUALQUIERA','evidencia'=>'folio'
]),'estado no permitido');
rechaza(static fn()=>tesco_admin_alta_usuario([
    'correo'=>'a@example.test','password'=>'ClaveSegura123!','nombre'=>'Prueba','apellido'=>'Institucional',
    'rol'=>'ADMIN','referencia'=>'ADM-001'
]),'alta de ADMIN desde cuentas ordinarias');
rechaza(static fn()=>tesco_admin_catalogo_habilidad(['accion'=>'nivel_nuevo','nombre'=>'Avanzado','orden'=>'101']),'orden inválido');
echo "OK: $tests validaciones críticas de Administración\n";
