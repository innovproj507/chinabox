<?php
// Archivo central de rutas

// Auth
$router->get('/', 'AuthController', 'login');
$router->post('/', 'AuthController', 'login');
$router->post('/logout', 'AuthController', 'logout');
$router->get('/logout', 'AuthController', 'logout'); // helper

// Dashboard
$router->get('/dashboard', 'DashboardController', 'index');

// Facturas
$router->get('/facturas', 'FacturaController', 'lista');
$router->get('/facturas/crear', 'FacturaController', 'crear');
$router->post('/facturas/crear', 'FacturaController', 'crear');
$router->get('/facturas/{id}', 'FacturaController', 'detalle');
$router->get('/facturas/{id}/editar', 'FacturaController', 'editar');
$router->post('/facturas/{id}/editar', 'FacturaController', 'editar');
$router->post('/facturas/{id}/eliminar', 'FacturaController', 'eliminar');
$router->get('/facturas/{id}/imprimir', 'FacturaController', 'imprimir');
$router->get('/facturas/{id}/pdf', 'FacturaController', 'pdf');
$router->get('/facturas-exportar', 'FacturaController', 'exportar');
$router->get('/facturas/{id}/json', 'FacturaController', 'datosJson');
$router->post('/facturas/{id}/whatsapp', 'FacturaController', 'whatsapp');
$router->post('/facturas/{id}/anular', 'FacturaController', 'anular');
$router->post('/facturas/{id}/abrir-app', 'FacturaController', 'abrirApp');

// Clientes
$router->get('/clientes', 'ClienteController', 'lista');
$router->get('/clientes/crear', 'ClienteController', 'crear');
$router->post('/clientes/crear', 'ClienteController', 'crear');
$router->get('/clientes-importar', 'ClienteController', 'importar');
$router->post('/clientes-importar', 'ClienteController', 'importar');
$router->get('/buscar-clientes', 'ClienteController', 'buscar'); // API helper
$router->get('/clientes/{id}', 'ClienteController', 'detalle');
$router->get('/clientes/{id}/editar', 'ClienteController', 'editar');
$router->post('/clientes/{id}/editar', 'ClienteController', 'editar');
$router->post('/clientes/{id}/eliminar', 'ClienteController', 'eliminar');
$router->get('/clientes/{id}/exportar-facturas', 'ClienteController', 'exportarFacturas');
$router->get('/clientes-exportar', 'ClienteController', 'exportarClientes');

// Servicios
$router->get('/servicios', 'ServicioController', 'lista');
$router->get('/servicios/crear', 'ServicioController', 'crear');
$router->post('/servicios/crear', 'ServicioController', 'crear');
$router->get('/servicios/{id}', 'ServicioController', 'detalle');
$router->get('/servicios/{id}/editar', 'ServicioController', 'editar');
$router->post('/servicios/{id}/editar', 'ServicioController', 'editar');
$router->post('/servicios/{id}/eliminar', 'ServicioController', 'eliminar');

// Configuracion
$router->get('/configuracion', 'ConfiguracionController', 'sistema');
$router->post('/configuracion', 'ConfiguracionController', 'sistema');

$router->get('/configuracion/usuarios', 'ConfiguracionController', 'usuarios');
$router->get('/configuracion/usuarios/crear', 'ConfiguracionController', 'usuarioCrear');
$router->post('/configuracion/usuarios/crear', 'ConfiguracionController', 'usuarioCrear');
$router->get('/configuracion/usuarios/{id}/editar', 'ConfiguracionController', 'usuarioEditar');
$router->post('/configuracion/usuarios/{id}/editar', 'ConfiguracionController', 'usuarioEditar');
$router->post('/configuracion/usuarios/{id}/eliminar', 'ConfiguracionController', 'usuarioEliminar');
$router->post('/configuracion/usuarios/{id}/toggle', 'ConfiguracionController', 'usuarioToggle');

$router->get('/configuracion/roles', 'ConfiguracionController', 'roles');
