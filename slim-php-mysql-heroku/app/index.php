<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request; 
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;
use Illuminate\Database\Capsule\Manager as Capsule;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';
require_once './middlewares/AutentificadorJWT.php';
require_once './middlewares/Historial.php';
require_once './middlewares/Logger.php'; 

require_once './controllers/UsuarioController.php';
require_once './controllers/MesaController.php';
require_once './controllers/PedidoController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/LoginController.php';
require_once './controllers/ArchivosController.php';
require_once './controllers/AuditoriaController.php';
require_once './controllers/ReportesController.php';
require_once './controllers/EncuestaController.php';
// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

$app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

$cointainer = $app->getContainer();

$capsule = new Capsule();

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => $_ENV['MYSQL_HOST'],
    'database'  => $_ENV['MYSQL_DB'],
    'username'  => $_ENV['MYSQL_USER'],
    'password'  => $_ENV['MYSQL_PASS'],
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();;


$app->group('/login', function (RouteCollectorProxy $group) {
    $group->post('[/]', \LoginController::class . ':access'); 
  })->add(\Historial::class . ':altaAccion');
  

// ------------------------// U S U A R I O S // ------------------------
$app->group('/usuarios', function (RouteCollectorProxy $group) 
{
    $group->get('[/]', \UsuarioController::class . ':TraerTodos')->add(\Logger::class . ':adminOrSocio');

    $group->get('/{IdUsuario}', \UsuarioController::class . ':TraerUno')->add(\Logger::class . ':adminOrSocio');
    
    $group->post('[/]', \UsuarioController::class . ':CargarUno')->add(\Logger::class . ':adminOrSocio'); //PERMISOS NECESARIOS

    $group->put('/{IdUsuario}', \UsuarioController::class. ':ModificarUno')->add(\Logger::class . ':adminOrSocio');
    $group->put('/suspender/{IdUsuario}', \UsuarioController::class. ':SuspenderUno')->add(\Logger::class . ':adminOrSocio');

    $group->delete('/{IdUsuario}', \UsuarioController::class . ':BorrarUno')->add(\Logger::class . ':adminOrSocio');
    })->add(\Historial::class . ':altaAccion');
// ------------------------// MESAS// ------------------------------
$app->group('/mesas', function (RouteCollectorProxy $group) 
{
    $group->get('[/]', \MesaController::class . ':TraerTodos')->add(\Logger::class . ':permisos');  

    $group->get('/estados', \MesaController::class . ':MesasEstados')->add(\Logger::class . ':permisos'); 
    $group->get('/{IdMesa}', \MesaController::class . ':TraerUno')->add(\Logger::class . ':permisos');  
    $group->post('[/]', \MesaController::class . ':CargarUno')->add(\Logger::class . ':permisos');  

    $group->put('/{IdMesa}', \MesaController::class. ':ModificarUno')->add(\Logger::class . ':permisos');  
    $group->put('/cerrar/{CodigoMesa}', \PedidoController::class. ':CerrarMesa')->add(\Logger::class . ':permisos');

    $group->delete('/{IdMesa}', \MesaController::class . ':BorrarUno')->add(\Logger::class . ':permisos');  
    })->add(\Historial::class . ':altaAccion');

// ------------------------// P R O D U C T O S // ------------------
$app->group('/productos', function (RouteCollectorProxy $group) 
{
    $group->get('[/]', \ProductoController::class . ':TraerTodos')->add(\Logger::class . ':permisos');  

    $group->get('/{IdProducto}', \ProductoController::class . ':TraerUno')->add(\Logger::class . ':permisos');  

    $group->post('[/]', \ProductoController::class . ':CargarUno')->add(\Logger::class . ':adminOrSocio');

    $group->put('/{IdProducto}', \ProductoController::class. ':ModificarUno')->add(\Logger::class . ':adminOrSocio');

    $group->delete('/{IdProducto}', \ProductoController::class . ':BorrarUno')->add(\Logger::class . ':adminOrSocio');
     })->add(\Historial::class . ':altaAccion');

// ------------------------// P E D I D O S // ------------------
$app->group('/pedidos', function (RouteCollectorProxy $group) 
{
    $group->get('[/]', \PedidoController::class . ':TraerTodos')->add(\Logger::class . ':permisos');  
    //$group->get('/preparando', \PedidoController::class . ':TraerTodosPreparando')->add(\Logger::class . ':permisos');  
    //$group->get('/preparados', \PedidoController::class . ':TraerTodosPreparados')->add(\Logger::class . ':permisos');  
    $group->get('/servidos', \PedidoController::class . ':TraerTodosServidos')->add(\Logger::class . ':permisos');  
    $group->get('/cobrados', \PedidoController::class . ':TraerTodosCobrados')->add(\Logger::class . ':permisos');  
    $group->get('/encuestados', \PedidoController::class . ':TraerTodosEncuestados')->add(\Logger::class . ':permisos');  
    $group->get('/cancelados', \PedidoController::class . ':TraerTodosCancelados')->add(\Logger::class . ':permisos');  
    $group->get('/sector', \PedidoController::class . ':PedidosSector')->add(\Logger::class . ':cocineroBarder');  
    $group->get('/preparacion', \PedidoController::class . ':PedidosEnPreparacion')->add(\Logger::class . ':cocineroBarder'); 
    
    
    $group->get('/{IdPedido}', \PedidoController::class . ':TraerUno')->add(\Logger::class . ':permisos');  
    $group->get('/{CodigoPedido}/{IdPedido}', \PedidoController::class . ':BuscarUno')->add(\Logger::class . ':permisos');  
    
    
    $group->post('[/]', \PedidoController::class . ':CargarUno')->add(\Logger::class . ':permisos');     
    $group->post('/foto', \PedidoController::class . ':CargarFoto')->add(\Logger::class . ':permisos');     
    
    $group->put('/actualizar', \PedidoController::class. ':ActualizarPedido')->add(\Logger::class . ':cocineroBarder');
    $group->put('/agregar/{CodigoPedido}', \PedidoController::class. ':ModificarUno')->add(\Logger::class . ':permisos');
    $group->put('/cancelar/{CodigoPedido}', \PedidoController::class. ':Cancelar')->add(\Logger::class . ':permisos');    
    $group->put('/servido', \PedidoController::class. ':Servido')->add(\Logger::class . ':permisos');   
    $group->put('/cobrado/{CodigoPedido}', \PedidoController::class. ':Cobrado')->add(\Logger::class . ':permisos');
    
    $group->delete('/{IdPedido}', \PedidoController::class . ':BorrarUno')->add(\Logger::class . ':permisos');  
    $group->delete('/cancelar/{CodigoPedido}', \PedidoController::class . ':CancelarUno')->add(\Logger::class . ':permisos');  

})->add(\Historial::class . ':altaAccion');

// ------------------------// E N C U E S T A S // ------------------
$app->group('/encuesta', function (RouteCollectorProxy $group) 
{
    $group->post('[/]', \EncuestaController::class. ':Encuesta'); 

});


//----------------------------------------------------------------

// ------------------------// R E P O R T E S // ------------------

$app->group('/reportes', function (RouteCollectorProxy $group) 
{   

    $group->get('/mesaMasUsada', \ReportesController::class . ':MesaMasUsada')->add(\Logger::class . ':adminOrSocio');
    $group->get('/productosMas', \ReportesController::class . ':ProductosMasVendidosAmenor')->add(\Logger::class . ':adminOrSocio');
    $group->get('/mejorComentarioUsuario', \ReportesController::class . ':MejorComentario')->add(\Logger::class . ':adminOrSocio');
    
})->add(\Historial::class . ':altaAccion');

//----------------------------------------------------------------
// ------------------------/ A U D I T O R I A // ------------------
$app->group('/auditoria', function (RouteCollectorProxy $group) 
{
    $group->get('/{IdUsuario}', \AuditoriaController::class . ':TraerUno')->add(\Logger::class . ':adminOrSocio');
    //$group->get('/{Nombre}', \AuditoriaController::class . ':TraerUno')->add(\Logger::class . ':adminOrSocio');
    $group->get('[/]', \AuditoriaController::class . ':TraerTodos')->add(\Logger::class . ':adminOrSocio');
    
});

$app->group('/clientes', function (RouteCollectorProxy $group) 
{
    $group->get('/{CodigoMesa}/{CodigoPedido}', \PedidoController::class . ':TiempoDemoraUsuario'); ;
});



//----------------------------------------------------------------
$app->get('[/]', function (Request $request, Response $response) 
{    
    $response->getBody()->write("Slim Framework 4s PHP");
    return $response;
});

$app->run();
