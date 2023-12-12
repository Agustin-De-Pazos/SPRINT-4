<?php
date_default_timezone_set("America/Buenos_Aires");

require_once './models/Auditoria.php';
require_once './models/EnumGeneral.php';

use \App\Models\EnumGeneral as EnumGeneral;
use \App\Models\Auditoria as Auditoria;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class Historial
{
    public static function LogOperacion($request, $response, $next)
    {
        $retorno = $next($request, $response);
        return $retorno;
    }

    public static function altaAccion(Request $request, RequestHandler $handler)
    {
        try {
            $response = $handler->handle($request);

            $body = json_decode($response->getBody());
            
            if (isset($body->IdUsuario) && isset($body->IdAccion)) {
                $idAccion = intval($body->IdAccion);
                $idUser = intval($body->IdUsuario);
                $registro = new Auditoria();
                $registro->IdUsuario = $idUser;
                $registro->IdAccion = $idAccion;
                $registro->IdPedido = $body->IdPedido;
                $registro->IdProducto = $body->IdProducto;
                $registro->IdMesa = $body->IdMesa;
                $registro->IdRefUsuario = $body->IdRefUsuario;
                $registro->Hora = $body->Hora;
                if(!$registro->save())
                throw new Exception('Error al guardar la auditoria');
                
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(array("error" => $e->getMessage())));
            $response = $response->withStatus(404);
        }
        return $response;
    }
}
