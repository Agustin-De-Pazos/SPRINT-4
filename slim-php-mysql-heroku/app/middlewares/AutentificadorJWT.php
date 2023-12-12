<?php

use Firebase\JWT\JWT;

require_once './models/Usuario.php';

use \App\Models\Usuario as Usuario;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AutentificadorJWT
{
    private static $claveSecreta = 'T3sT$JWT';
    private static $tipoEncriptacion = ['HS256'];

    public static function CrearToken($datos)
    {
        $ahora = time();
        $payload = array(
            'iat' => $ahora,
            'exp' => $ahora + (60000 * 24 * 7 * 30 * 2),
            'aud' => self::Aud(),
            'data' => $datos,
            'app' => "Test JWT"
        );
        return JWT::encode($payload, self::$claveSecreta);
    }

    public static function VerificarToken($token)
    {
        if (empty($token)) {
            throw new Exception("token vacío");
        }

        try {
            $decodificado = JWT::decode(
                $token,
                self::$claveSecreta,
                self::$tipoEncriptacion
            );
        } catch (Exception $e) {
            throw $e;
        }

        if ($decodificado->aud !== self::Aud()) {
            throw new Exception("Usuario invalido");
        }
    }

    public static function ObtenerPayLoad($token)
    {
        if (empty($token)) {
            throw new Exception("El token esta vacío");
        }
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        );
    }

    public static function ObtenerData($token)
    {
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        )->data;
    }

    private static function Aud()
    {
        $aud = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }

        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return sha1($aud);
    }

    static public function GetUsuarioLogeado($request)
    {
        try {
            $header = $request->getHeaderLine('Authorization');

            $response = new Response();

            if (empty($header)) {
                throw new Exception('Es necesario Token para acceso');
            }
            $token = trim(explode("Bearer", $header)[1]);

            self::VerificarToken($token);
            $data = self::ObtenerData($token);

            $obj = Usuario::find($data->idUsuario);

            return $obj;
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(array("error" => $e->getMessage())));
            $response = $response->withStatus(401);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}
