<?php 

require_once __DIR__ . '/../includes/app.php';

use MVC\Router;
use Controllers\APIEventos;
use Controllers\APIPonentes;
use Controllers\APIRegalos;
use Controllers\AuthController;
use Controllers\EventosController;
use Controllers\PaginasController;
use Controllers\RegalosController;
use Controllers\PonentesController;
use Controllers\RegistroController;
use Controllers\DashboardController;
use Controllers\RegistradosController;
use Controllers\SearchController;
use Controllers\MarketplaceController;
use Controllers\ReputationController;
use Controllers\AdvisorController;
use Controllers\ReviewController;

$router = new Router();

// ══════════════════════════════════════════════
// AUTH ROUTES (Preserved from original)
// ══════════════════════════════════════════════
$router->get('/login', [AuthController::class, 'login']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);

$router->get('/registro', [AuthController::class, 'registro']);
$router->post('/registro', [AuthController::class, 'registro']);

$router->get('/olvide', [AuthController::class, 'olvide']);
$router->post('/olvide', [AuthController::class, 'olvide']);

$router->get('/reestablecer', [AuthController::class, 'reestablecer']);
$router->post('/reestablecer', [AuthController::class, 'reestablecer']);

$router->get('/mensaje', [AuthController::class, 'mensaje']);
$router->get('/confirmar-cuenta', [AuthController::class, 'confirmar']);

// ══════════════════════════════════════════════
// ADMIN ROUTES (Preserved from original)
// ══════════════════════════════════════════════
$router->get('/admin/dashboard', [DashboardController::class, 'index']);

$router->get('/admin/ponentes', [PonentesController::class, 'index']);
$router->get('/admin/ponentes/crear', [PonentesController::class, 'crear']);
$router->post('/admin/ponentes/crear', [PonentesController::class, 'crear']);
$router->get('/admin/ponentes/editar', [PonentesController::class, 'editar']);
$router->post('/admin/ponentes/editar', [PonentesController::class, 'editar']);
$router->post('/admin/ponentes/eliminar', [PonentesController::class, 'eliminar']);

$router->get('/admin/eventos', [EventosController::class, 'index']);
$router->get('/admin/eventos/crear', [EventosController::class, 'crear']);
$router->post('/admin/eventos/crear', [EventosController::class, 'crear']);
$router->get('/admin/eventos/editar', [EventosController::class, 'editar']);
$router->post('/admin/eventos/editar', [EventosController::class, 'editar']);
$router->post('/admin/eventos/eliminar', [EventosController::class, 'eliminar']);

$router->get('/api/eventos-horario', [APIEventos::class, 'index']);
$router->get('/api/ponentes', [APIPonentes::class, 'index']);
$router->get('/api/ponente', [APIPonentes::class, 'ponente']);
$router->get('/api/regalos', [APIRegalos::class, 'index']);

$router->get('/admin/registrados', [RegistradosController::class, 'index']);
$router->get('/admin/regalos', [RegalosController::class, 'index']);

// Registro de Usuarios (Preserved)
$router->get('/finalizar-registro', [RegistroController::class, 'crear']);
$router->post('/finalizar-registro/gratis', [RegistroController::class, 'gratis']);
$router->post('/finalizar-registro/pagar', [RegistroController::class, 'pagar']);
$router->get('/finalizar-registro/conferencias', [RegistroController::class, 'conferencias']);
$router->post('/finalizar-registro/conferencias', [RegistroController::class, 'conferencias']);

$router->get('/boleto', [RegistroController::class, 'boleto']);

// ══════════════════════════════════════════════
// BASE8 PRODUCTION ROUTES
// ══════════════════════════════════════════════

// Landing / Public
$router->get('/', [PaginasController::class, 'index']);
$router->get('/devwebcamp', [PaginasController::class, 'evento']);
$router->get('/paquetes', [PaginasController::class, 'paquetes']);
$router->get('/workshops-conferencias', [PaginasController::class, 'conferencias']);
$router->get('/404', [PaginasController::class, 'error']);

// Search Engine
$router->get('/buscar', [SearchController::class, 'index']);
$router->get('/buscar/resultados', [SearchController::class, 'results']);
$router->get('/api/search', [SearchController::class, 'api']);
$router->get('/api/careers', [SearchController::class, 'apiCareers']);

// Marketplace
$router->get('/marketplace', [MarketplaceController::class, 'index']);
$router->get('/marketplace/asset', [MarketplaceController::class, 'show']);
$router->get('/marketplace/subir', [MarketplaceController::class, 'upload']);
$router->post('/marketplace/subir', [MarketplaceController::class, 'upload']);
$router->post('/marketplace/comprar', [MarketplaceController::class, 'purchase']);
$router->get('/marketplace/mis-recursos', [MarketplaceController::class, 'myAssets']);
$router->get('/marketplace/mis-compras', [MarketplaceController::class, 'myPurchases']);

// Reputation & Prestige
$router->get('/leaderboard', [ReputationController::class, 'leaderboard']);
$router->get('/perfil/prestigio', [ReputationController::class, 'profile']);

// Advisors
$router->get('/asesores', [AdvisorController::class, 'index']);
$router->get('/asesores/perfil', [AdvisorController::class, 'profile']);

// Reviews
$router->post('/review/crear', [ReviewController::class, 'create']);

$router->comprobarRutas();