<?php
require_once './models/Auditoria.php';
require_once './interfaces/IApiUsable.php';

use \App\Models\Auditoria as Auditoria;
use App\Models\AuditoriaAcciones;
use App\Models\UsuarioTipo;

class AuditoriaController
{
    public function TraerTodos($request, $response, $args)
    {
        try {
            $lista = Auditoria::all();
            if (count($lista) > 0) {
                $titulo =  '----AUDITORIA----' . '<br>';
                $string = '';
                foreach ($lista as $auditoria) {
                        $string = $string .
                            'IdAuditoria ->' . $auditoria->IdAuditoria . '<br>' .
                            'IdResponsable ->' . $auditoria->IdUsuario . '<br>' .
                            'Hora ->' . $auditoria->Hora . '<br>' .
                            'IdAccion ->' . $auditoria->IdAccion . '<br>';
                        if (isset($auditoria->idRefUsuario)) {
                            $string = $string . 'IdUsuario ->' . $auditoria->idRefUsuario . '<br>';
                        }
                        if (isset($auditoria->IdProducto)) {
                            $string = $string . 'IdProducto ->' . $auditoria->IdProducto . '<br>';
                        }
                        if (isset($auditoria->IdPedido)) {
                            $string = $string . 'IdPedido ->' . $auditoria->IdPedido . '<br>';
                        }
                        if (isset($auditoria->IdMesa)) {
                            $string = $string . 'IdMesa ->' . $auditoria->IdMesa . '<br>';
                        }

                        $string = $string . '---------------------------------------<br>';
                }
                $payload = json_encode($titulo . $string);
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $response->getBody()->write('Auditoria vacia..');
                return $response->withHeader('Content-Type', 'application/json');
            }
        } catch (Exception $e) {
            $response = $response->withStatus(401);
            $response->getBody()->write(json_encode(array('error' => $e->getMessage())));
            return $response->withHeader('Content-Type', 'application/json');
        }
    }
    

    public function TraerUno($request, $response, $args)
    {
        try {
            $id = $args['IdUsuario'];
            $lista = Auditoria::all();

            if (count($lista) > 0) {
                $titulo =  '----AUDITORIA----' . '<br>';
                $string = '';
                foreach ($lista as $auditoria) {
                    if ($auditoria->IdUsuario == $id) {

                        $string = $string .
                            'IdAuditoria ->' . $auditoria->IdAuditoria . '<br>' .
                            'IdResponsable ->' . $auditoria->IdUsuario . '<br>' .
                            'Hora ->' . $auditoria->Hora . '<br>' .
                            'IdAccion ->' . $auditoria->IdAccion . '<br>';

                        if (isset($auditoria->idRefUsuario)) {
                            $string = $string . 'IdUsuario ->' . $auditoria->idRefUsuario . '<br>';
                        }
                        if (isset($auditoria->IdProducto)) {
                            $string = $string . 'IdProducto ->' . $auditoria->IdProducto . '<br>';
                        }
                        if (isset($auditoria->IdPedido)) {
                            $string = $string . 'IdPedido ->' . $auditoria->IdPedido . '<br>';
                        }
                        if (isset($auditoria->IdMesa)) {
                            $string = $string . 'IdMesa ->' . $auditoria->IdMesa . '<br>';
                        }

                        $string = $string . '---------------------------------------<br>';
                    }
                }
                $payload = json_encode($titulo . $string);
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $response->getBody()->write('Auditoria vacia..');
                return $response->withHeader('Content-Type', 'application/json');
            }
        } catch (Exception $e) {
            $response = $response->withStatus(401);
            $response->getBody()->write(json_encode(array('error' => $e->getMessage())));
            return $response->withHeader('Content-Type', 'application/json');
        }
    }
}
