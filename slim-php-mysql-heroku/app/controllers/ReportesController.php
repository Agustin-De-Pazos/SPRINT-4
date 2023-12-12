
<?php
require_once './models/Mesa.php';
require_once './models/Producto.php';
require_once './models/Usuario.php';
require_once './models/Pedido.php';
require_once './models/Archivos.php';
require_once './models/Reportes.php';
require_once './models/Producto.php';
date_default_timezone_set("America/Buenos_Aires");
use App\Models\UsuarioTipo as UsuarioTipo;
use App\Models\Pedido as Pedido;
use App\Models\Mesa as Mesa;
use App\Models\Producto as Producto;

class ReportesController
{
    
    public static function MesaMasUsada($request, $response, $args)
    {
        $maxUsedMesaId = Pedido::groupBy('IdMesa')
        ->selectRaw('IdMesa, COUNT(IdMesa) as mesaCount')
        ->orderByDesc('mesaCount')
        ->value('IdMesa');
        $mesa = Mesa::find($maxUsedMesaId);

        if ($maxUsedMesaId != null) {
            $payload = json_encode(array(
                "La Mesa Mas Usada" => $mesa
            ));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    public static function ProductosMasVendidosAmenor($request, $response, $args)
    {

        $result = Producto::select(
            'productos.*',
            DB::raw('COUNT(pedidosdetalle.IdProducto) as totalVentas')
        )
        ->leftJoin('pedidosdetalle', 'pedidosdetalle.IdProducto', '=', 'productos.IdProducto')
        ->groupBy('productos.IdProducto')
        ->orderBy('totalVentas', 'desc')
        ->get();

        if ($result != null) {
            $payload = json_encode(array(
                "Productos Vendidos: " => $result
            ));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function MejorComentario($request, $response, $args)
    {
        $max = Pedido::all()->max('PuntuacionMozo');
       
        $pedido = Pedido::where('PuntuacionMozo', '=', $max)->first();
        if ($pedido != null) {
            $payload =   '---------------------------MEJOR COMENTARIO------------------------------' . '<br>' .
                'IdPedido: ' . $pedido->IdPedido . '<br>' .
                'IdMesa: ' . $pedido->IdMesa . '<br>' .
                'Nombre Cliente: ' . $pedido->NombreCliente . '<br>' .
                'Puntuacion de mesa :'.$pedido->PuntuacionMesa . '<br>'.
                'Puntuacion de mozo :'.$pedido->PuntuacionMozo . '<br>'.
                'Puntuacion de cocinero :'.$pedido->PuntuacionCocinero . '<br>'.
                'Puntuacion de restaurante :'.$pedido->PuntuacionRestaurante . '<br>'.
                'Comentario: '.$pedido->Comentario . '<br>';
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}

?>
