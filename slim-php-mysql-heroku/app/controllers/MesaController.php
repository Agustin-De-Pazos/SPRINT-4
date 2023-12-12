
<?php

require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';

use \App\Models\Mesa as Mesa;
use App\Models\AuditoriaAcciones;

class MesaController implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        try {
            $idUsuarioLogeado = AutentificadorJWT::GetUsuarioLogeado($request)->IdUsuario;
            $header = $request->getHeaderLine('Authorization');
            $parametros = $request->getParsedBody();

            $estado = 'Libre';
            $descripcion = $parametros['descripcion'];

            $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
            $codigoAleatorio = substr(str_shuffle($permitted_chars), 0, 10);

            $mesaCreada = new Mesa();

            $mesaCreada->Estado = $estado;
            $mesaCreada->Descripcion = $descripcion;
            $mesaCreada->Codigo = $codigoAleatorio;

            if (!$mesaCreada->save())
                throw new Exception('No se pudo guardar la mesa.');

            $payload = json_encode(
                array(
                    "IdUsuario" => strval($idUsuarioLogeado),
                    "IdRefUsuario" => null,
                    "IdAccion" =>  strval(AuditoriaAcciones::Alta),
                    "mensaje" => "Mesa creada con éxito",
                    "IdPedido" => null,
                    "Exito" => 1,
                    "IdPedidoDetalle" => null,
                    "IdMesa" => $mesaCreada->IdMesa,
                    "IdProducto" => null,
                    "IdArea" => null,
                    "Hora" => date('h:i:s')
                )
            );

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $response = $response->withStatus(401);
            $response->getBody()->write(json_encode(array('error' => $e->getMessage())));
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    public function BorrarUno($request, $response, $args)
    {
        try {
            $idUsuarioLogeado = AutentificadorJWT::GetUsuarioLogeado($request)->IdUsuario;
            $header = $request->getHeaderLine('Authorization');
            $idRecibida = $args['IdMesa'];

            $mesaEncontrada = Mesa::find($idRecibida);

            if ($mesaEncontrada != null) {

                $payload = json_encode(
                    array(
                        "IdUsuario" => strval($idUsuarioLogeado),
                        "IdRefUsuario" => null,
                        "IdAccion" =>  strval(AuditoriaAcciones::Baja),
                        "mensaje" => "Mesa eliminada con éxito",
                        "IdPedido" => null,
                        "IdPedidoDetalle" => null,
                        "IdMesa" => $mesaEncontrada->IdMesa,
                        "IdProducto" => null,
                        "IdArea" => null,
                        "Hora" => date('h:i:s')
                    )
                );
                $mesaEncontrada->delete();
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                throw new Exception("Mesa no encontrada.");
            }
        } catch (Exception $e) {
            $response = $response->withStatus(401);
            $response->getBody()->write(json_encode(array('error' => $e->getMessage())));
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    public function ModificarUno($request, $response, $args)
    {
        try {
            $id = $args['IdMesa'];
            $mesaEncontrada = Mesa::where('IdMesa', '=', $id)->first();
            $body = $request->getParsedBody();

            if ($mesaEncontrada != null) {
                $estado = $body['estado'];
                $descripcion = $body['descripcion'];

                $mesaEncontrada->Estado = $estado;
                $mesaEncontrada->Descripcion = $descripcion;

                $mesaEncontrada->save();
                $payload = json_encode(array("mensaje" => "Mesa modificada"));
            } else {
                throw new Exception('No se encontro la mesa buscada');
            }
        } catch (Exception $e) {
            $response = $response->withStatus(401);
            $response->getBody()->write(json_encode('Error al modificar'));
            return $response->withHeader('Content-Type', 'application/json');
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $listaMesas = Mesa::all();

        if ($listaMesas == null) {
            $payload = json_encode(array("mensaje" => "No hay ninguna mesa."));
        } else {
            $payload = json_encode(array("listaMesas" => $listaMesas));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $idRecibido = $args['IdMesa'];

        $listaMesas = Mesa::all();
        $mesaEncontrada = $listaMesas->find($idRecibido);

        if ($mesaEncontrada != null) {
            $payload = json_encode($mesaEncontrada);
        } else {
            $payload = json_encode(array("mensaje" => "Mesa no encontrada."));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function CerrarMesa($request, $response, $args)
    {
        try {
            $idUsuarioLogeado = AutentificadorJWT::GetUsuarioLogeado($request)->IdUsuario;
            $header = $request->getHeaderLine('Authorization');
            $codigo = $args['CodigoPedido'];

            $mesa = Mesa::where('CodigoPedido', '=', $codigo)->first();

            if ($mesa != null) {
                $mesa->Estado = 'Cerrada';
                if(!$mesa->save())
                throw new Exception('Error al cerrar la mesa.');

                $payload = json_encode(
                    array(
                        "IdUsuario" => strval($idUsuarioLogeado),
                        "IdRefUsuario" => $mesa->IdUsuario,
                        "IdAccion" =>  strval(AuditoriaAcciones::Baja),
                        "mensaje" => "Pedido cancelado con éxito",
                        "IdPedido" => $mesa->IdPedido,
                        "IdPedidoDetalle" => null,
                        "IdMesa" => $mesa->IdMesa,
                        "IdProducto" => null,
                        "IdArea" => null,
                        "Hora" => date('h:i:s')
                    )
                );
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $payload = json_encode(array("mensaje" => "Error al eliminar"));
            }
        } catch (Exception $e) {
            $response = $response->withStatus(401);
            $response->getBody()->write(json_encode(array('error' => $e->getMessage())));
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    public static function MesasEstados($request, $response, $args)
    {
        try {
            $idUsuarioLogeado = AutentificadorJWT::GetUsuarioLogeado($request)->IdUsuario;
            $header = $request->getHeaderLine('Authorization');

            $lista = Mesa::all();
            if ($lista != null) {
                $string = '';
                $cabecera = '---------------------------MESAS---------------------------' . '<br>' . 'Este es un informe detallado de nuestras mesas.' . '<br>';
                foreach ($lista as $mesa) {
                    $string = $string .
                        'IdMesa ->' . $mesa->IdMesa . '<br>' .
                        'Codigo -> ' . $mesa->Codigo . '<br>' .
                        'Estado ->' . $mesa->Estado . '<br>' .
                        'Descripcion ->' . $mesa->Descripcion . '<br>' .
                        '_______________________________________________<br>';
                }
                $payload = $cabecera . $string;
            } else {
                throw new Exception('Error al obtener el informe.');
            }
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $response = $response->withStatus(401);
            $response->getBody()->write(json_encode(array('error' => $e->getMessage())));
            return $response->withHeader('Content-Type', 'application/json');
        }
    }
}

?>