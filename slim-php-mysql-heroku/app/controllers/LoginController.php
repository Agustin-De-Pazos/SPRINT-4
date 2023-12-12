<?php
date_default_timezone_set("America/Buenos_Aires");
require_once './models/Usuario.php';
require_once './models/EnumGeneral.php';

use App\Models\AuditoriaAcciones;
use \App\Models\Usuario as Usuario;
use \App\Models\EnumGeneral as EnumGeneral;
use App\Models\UsuarioTipo;

class LoginController
{

    private static function ValidarDatosLogin($usuario, $clave)
    {
        if (!isset($usuario) || !isset($clave)) {
            throw new Exception("Usuario o Clave indefinidos.");
        }
    }

    public function access($request, $response, $args)
    {
        try {
            $params = $request->getParsedBody();
            $usuario = $params['Usuario'];
            $clave = $params['Clave'];


            self::ValidarDatosLogin($usuario, $clave);

            $obj = Usuario::where('Usuario','=', $usuario)->first();

          
            if ($obj == null) {
                throw new Exception("No existe Usuario con nombre '$usuario'");
            }
            if ($obj->Clave !== $clave) {
                throw new Exception("Clave incorrecta");
            }

            $datos = array('idUsuario' => $obj->IdUsuario,'IdUsuarioTipo' => $obj->IdUsuarioTipo);
            $token = AutentificadorJWT::CrearToken($datos);

            $obj->MostrarUsuario();
            $payload = json_encode(
                array(
                    'jwt' => $token,
                    "IdUsuario" => strval($obj->IdUsuario),
                    "IdAccion" => strval(AuditoriaAcciones::Login),
                    "mensaje" => "Login Usuario con éxito",
                    "Hora" => date('h:i:s'),
                    "IdPedido" => null,
                    "IdRefUsuario" => null,
                    "IdProducto" => null,
                    "IdMesa" => null,

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
}
