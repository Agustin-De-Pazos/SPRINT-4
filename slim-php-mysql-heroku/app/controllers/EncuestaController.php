<?php
date_default_timezone_set("America/Buenos_Aires");
require_once './interfaces/IApiUsable.php';
require_once './models/Pedido.php';
require_once './models/PedidoDetalle.php';
require_once './models/Producto.php';

use \App\Models\Pedido as Pedido;
use \App\Models\PedidoDetalle as PedidoDetalle;
use \App\Models\AuditoriaAcciones as AuditoriaAcciones;
use \App\Models\Producto as Producto;
use \App\Models\Usuario as Usuario;
use \App\Models\Mesa as Mesa;


class EncuestaController
{
    public function Encuesta($request, $response, $args)
    {
        try {
            $header = $request->getHeaderLine('Authorization');
            $idUsuarioLogeado = AutentificadorJWT::GetUsuarioLogeado($request)->IdUsuario;

            $body = $request->getParsedBody();
            if (!isset($body['Comentario']) || !isset($body['PuntuacionMesa']) || !isset($body['PuntuacionMozo'])
                || !isset($body['PuntuacionRestaurante']) || !isset($body['PuntuacionCocinero']) || !isset($body['CodigoPedido'])
            )
                throw new Exception("Datos invalidos");

                $puntuacionMesa = $body['PuntuacionMesa'];
                $puntuacionRestaurante = $body['PuntuacionRestaurante'];
                $puntuacionMozo = $body['PuntuacionMozo'];
                $puntuacionCocinero = $body['PuntuacionCocinero'];
            if (($puntuacionMesa > 0 && $puntuacionMesa <= 10) && ($puntuacionRestaurante > 0 && $puntuacionRestaurante <= 10) && 
                 ($puntuacionMozo > 0 && $puntuacionMozo <= 10) && ($puntuacionCocinero > 0 && $puntuacionCocinero <= 10)) {
                $comentario = $body['Comentario'];
                $CodigoPedido = $body['CodigoPedido'];
                $pedido = Pedido::where('CodigoPedido', '=', $CodigoPedido)->first();
                //$mesa = Mesa::find($pedido->IdMesa)->first();

                $listaDetalles = PedidoDetalle::all();
                if ($pedido != null && $listaDetalles != null && $pedido->Estado == 'Cobrado') {
                    $pedido->PuntuacionMesa = $puntuacionMesa;
                    $pedido->PuntuacionCocinero = $puntuacionCocinero;
                    $pedido->PuntuacionMozo = $puntuacionMozo;
                    $pedido->PuntuacionRestaurante = $puntuacionRestaurante;
                    $pedido->Comentario = $comentario;
                    $pedido->Estado = 'CobradoEncuestado';
                    $pedido->save();

                    $payload = json_encode(
                        array(
                            "IdUsuario" => strval($idUsuarioLogeado),
                            "IdRefUsuario" => $pedido->IdUsuario,
                            "IdAccion" =>  strval(AuditoriaAcciones::Encuesta),
                            "mensaje" => "Encuesta realizada con éxito",
                            "IdPedido" => $pedido->IdPedido,
                            "IdPedidoDetalle" => null,
                            "IdMesa" => $pedido->IdMesa,
                            "IdProducto" => null,
                            "IdArea" => null,
                            "Hora" => date('h:i:s')
                        )
                    );
                    $response->getBody()->write($payload);
                    return $response->withHeader('Content-Type', 'application/json');
                } else {
                    throw new Exception("Debe cobrar antes de realizar la encuesta o pedido no disponible.");
                }
                } else {
                    throw new Exception("Puntuacion/es invalida/s.");
                }
        } catch (Exception $e) {
            $response = $response->withStatus(401);
            $response->getBody()->write(json_encode(array('error' => $e->getMessage())));
            return $response->withHeader('Content-Type', 'application/json');
        }
    }
}
