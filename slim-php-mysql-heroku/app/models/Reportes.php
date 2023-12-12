<?php

require_once './models/Producto.php';

use \App\Models\Producto as Producto;

use \App\Models\Mesa as Mesa;
use \App\Models\Pedido as Pedido;
use \App\Models\Usuario as Usuario;
use \App\Models\PedidoDetalle as PedidoDetalle;
use \App\Models\Auditoria as Auditoria;
use App\Models\AuditoriaAcciones;

class Reportes
{
    public static function Pedidos()
    {
        $retorno = null;
            $cabecera = '---------------------------RESTAURANT EL PHP----------------------------' . '<br>' .
                'Este es un informe detallado de los ultimos 30 dias.' . '<br>' .
                '<br>' . 'TOTAL VENTAS: $' . self::TotalVendido() . '<br>';

            $retorno =//  $cabecera . self::PedidoMasCaro() . self::PedidoMasBarato() . self::PedidoCancelados() .
                 self::ProductosMasVendidos();
           
        

        return json_encode($retorno);
    }
    public static function ProductosMasVendidos(){
        $pedido = PedidoDetalle::all()->query('select sum(pd.Cantidad) as Cantidad, prod.Nombre as Nombre from pedidosdetalle pd 
        inner join pedidos p on p.IdPedido = pd.IdPedido 
        inner join productos prod on pd.IdProducto = prod.IdProducto 
        GROUP by prod.Nombre 
        order by sum(pd.Cantidad) desc;');
      

        echo count($pedido);

        foreach ($pedido as $key) {
            $string =   '----------------------------PRODUCTO MAS VENDIDO---------------------------' . '<br>' .
            'El producto mas caro cuesta $' . $pedido->Cantidad . ' por unidad' . '<br>' .

        $corte = '------------------------------------------------------------------------' . '<br>';
        }

        $producto = Producto::find($pedido->IdProducto);
 
        var_dump($producto);


        return $string.$corte;
    }
    public static function InformeMasCaro()
    {
        $precio = Producto::all()->max('PrecioUnidad');
        $producto = Producto::where('PrecioUnidad', '=', $precio)->first();
        $string =   '----------------------------PRODUCTO MAS CARO---------------------------' . '<br>' .
            'El producto mas caro cuesta $' . $precio . ' por unidad' . '<br>' .
            'IdProducto: ' . $producto->IdProducto . '<br>' .
            'Nombre: ' . $producto->Nombre . '<br>' .
            'Stock: ' . $producto->Stock . '<br>' .
            'Tiempo estimado de espera: ' . $producto->TiempoEspera . '(minutos)' . '<br>' .
            'Area: ' . $producto->Area . '<br>';
        $corte = '------------------------------------------------------------------------' . '<br>';

        return $string . $corte;
    }
    public static function PedidoMasCaro()
    {
        $max = Pedido::all()->max('ImporteTotal');
        $pedido = Pedido::where('ImporteTotal', '=', $max)->first();

        if ($max > 0 && $pedido != null) {
            $string =   '---------------------------PEDIDO MAS CARO------------------------------' . '<br>' .
                'El pedido mas caro costo $' . $pedido->ImporteTotal . '<br>' .
                'IdPedido: ' . $pedido->IdPedido . '<br>' .
                'IdMesa: ' . $pedido->IdMesa . '<br>' .
                'Codigo: ' . $pedido->CodigoPedido . '<br>' .
                'Foto: ' . $pedido->PathFoto . '<br>' .
                'Nombre Cliente: ' . $pedido->NombreCliente . '<br>' .
                'Tiempo preparacion: ' . $pedido->TiempoPreparacion . '(minutos)' . '<br>';
            $corte = '-------------------------------------------------------------------------' . '<br>';
            $string . $corte;
        }
        return "#";
    }
    public static function PedidoMasBarato()
    {
        $max = Pedido::all()->min('ImporteTotal');
        $pedido = Pedido::where('ImporteTotal', '=', $max)->first();

        $string =   '---------------------------PEDIDO MAS BARATO------------------------------' . '<br>' .
            'El pedido mas caro costo $' . $pedido->ImporteTotal . '<br>' .
            'IdPedido: ' . $pedido->IdPedido . '<br>' .
            'IdMesa: ' . $pedido->IdMesa . '<br>' .
            'Codigo: ' . $pedido->CodigoPedido . '<br>' .
            'Foto: ' . $pedido->PathFoto . '<br>' .
            'Nombre Cliente: ' . $pedido->NombreCliente . '<br>' .
            'Tiempo preparacion: ' . $pedido->TiempoPreparacion . '(minutos)' . '<br>';
        $corte = '-------------------------------------------------------------------------' . '<br>';

        return $string . $corte;
    }

    public static function PedidoCancelados()
    {

        $pedidos = Pedido::all();
        $string =   '---------------------------PEDIDOS CANCELADOS------------------------------' . '<br>';
        foreach ($pedidos as $detalle) {
            # code...
            if ($detalle->Estado == 'Cancelado') {
                $string = $string . 'Costo $' . $detalle->ImporteTotal . '<br>' .
                    'IdPedido: ' . $detalle->IdPedido . '<br>' .
                    'IdMesa: ' . $detalle->IdMesa . '<br>' .
                    'Codigo: ' . $detalle->CodigoPedido . '<br>' .
                    'Foto: ' . $detalle->PathFoto . '<br>' .
                    'Nombre Cliente: ' . $detalle->NombreCliente . '<br>' .
                    'Tiempo preparacion: ' . $detalle->TiempoPreparacion . '(minutos)' . '<br>' .
                    '------------------------------------------------' . '<br>';
            }
        }
        return $string;
    }
    public static function TotalVendido()
    {
        $max = Pedido::all()->sum('ImporteTotal');
        $string =  $max . '<br>';
        $corte = '-------------------------------------------------------------------------';
        return $string . $corte;
    }

    //*************************REPORTES MESAS******************************* */
    public static function Mesas()
    {

            $cabecera = '---------------------------RESTAURANT EL PHP----------------------------' . '<br>' .
                'Este es un informe detallado de nuestras mesas de los ultimos 30 dias.' . '<br>' .
                '<br>' . 'TOTAL VENTAS: $' . self::TotalVendido() . '<br>';

            $retorno = self::MejorImporteMesa().self::PeorImporteMesa().self::MejorComentario().self::PeorComentario(); //$string . $corte.'<br>')
        return $retorno;
    }

    public static function MejorComentario()
    {
        $max = Pedido::all()->max('PuntuacionMozo');
       
        $pedido = Pedido::where('PuntuacionMozo', '=', $max)->first();
        if ($pedido != null) {
            $string =   '---------------------------MEJOR COMENTARIO------------------------------' . '<br>' .
                'Este es un informe sobre el mejor comentario del mes!' . '<br>' .
                'IdPedido: ' . $pedido->IdPedido . '<br>' .
                'IdMesa: ' . $pedido->IdMesa . '<br>' .
                'Nombre Cliente: ' . $pedido->NombreCliente . '<br>' .
                'Puntuacion de mesa :'.$pedido->PuntuacionMesa . '<br>'.
                'Puntuacion de mozo :'.$pedido->PuntuacionMozo . '<br>'.
                'Puntuacion de cocinero :'.$pedido->PuntuacionCocinero . '<br>'.
                'Puntuacion de restaurante :'.$pedido->PuntuacionRestaurante . '<br>'.
                'Comentario: '.$pedido->Comentario . '<br>';
            $corte = '-------------------------------------------------------------------------' . '<br>';
           return $string . $corte;
        }
        return "#";
    }
    public static function PeorComentario()
    {
        $min = Pedido::all()->min('PuntuacionMozo');
        $pedido = Pedido::where('PuntuacionMozo', '=', $min)->first();
        if ($pedido != null) {
            $string =   '---------------------------PEOR COMENTARIO------------------------------' . '<br>' .
                'Este es un informe sobre el peor comentario del mes' . '<br>' .
                'IdPedido: ' . $pedido->IdPedido . '<br>' .
                'IdMesa: ' . $pedido->IdMesa . '<br>' .
                'Nombre Cliente: ' . $pedido->NombreCliente . '<br>' .
                'Puntuacion de mesa :'.$pedido->PuntuacionMesa . '<br>'.
                'Puntuacion de mozo :'.$pedido->PuntuacionMozo . '<br>'.
                'Puntuacion de cocinero :'.$pedido->PuntuacionCocinero . '<br>'.
                'Puntuacion de restaurante :'.$pedido->PuntuacionRestaurante . '<br>'.
                'Comentario: '.$pedido->Comentario . '<br>';
            $corte = '-------------------------------------------------------------------------' . '<br>';
           return $string . $corte;
        }
        return "#";
    }
    public static function MejorImporteMesa()
    {
        $max = Pedido::all()->max('ImporteTotal');
        $pedido = Pedido::where('ImporteTotal', '=', $max)->first();

        if ($pedido != null) {
            $string =   '---------------------------FACTURA MAS CARA------------------------------' . '<br>' .
                'La factura de mayor costo fue $' . $pedido->ImporteTotal . '<br>' .
                'IdMesa: ' . $pedido->IdMesa . '<br>' .
                'Codigo: ' . $pedido->CodigoPedido . '<br>' .
                'Nombre Cliente: ' . $pedido->NombreCliente . '<br>';
            $corte = '-------------------------------------------------------------------------' . '<br>';
            return $string . $corte;
        }
        return "#";
    }
    public static function PeorImporteMesa()
    {
        $max = Pedido::all()->min('ImporteTotal');
        $pedido = Pedido::where('ImporteTotal', '=', $max)->first();

        if ($pedido != null) {
            $string =   '---------------------------FACTURA MAS BAJA------------------------------' . '<br>' .
                'La factura de menor costo fue $' . $pedido->ImporteTotal . '<br>' .
                'IdMesa: ' . $pedido->IdMesa . '<br>' .
                'Codigo: ' . $pedido->CodigoPedido . '<br>' .
                'Nombre Cliente: ' . $pedido->NombreCliente . '<br>';
            $corte = '-------------------------------------------------------------------------' . '<br>';
            return $string . $corte;
        }
        return "#";
    }
    public static function MesaMasUsada()
    {
        $max = Pedido::all()->count('IdMesa');
        $pedido = Pedido::where('ImporteTotal', '=', $max)->first();

        if ($pedido != null) {
            $string =   '---------------------------FACTURA MAS CARA------------------------------' . '<br>' .
                'La factura de mayor costo fue $' . $pedido->ImporteTotal . '<br>' .
                'IdMesa: ' . $pedido->IdMesa . '<br>' .
                'Codigo: ' . $pedido->CodigoPedido . '<br>' .
                'Nombre Cliente: ' . $pedido->NombreCliente . '<br>';
            $corte = '-------------------------------------------------------------------------' . '<br>';
            return $string . $corte;
        }
        return "#";
    }
//************************************************************************************* */
    //*************************REPORTES EMPLEADOS******************************* */
    public static function Empleados()
    {
        $escrituraSalioBien = true;

            $cabecera = '---------------------------RESTAURANT EL PHP----------------------------' . '<br>' .
                'Este es un informe detallado de la actividad de los usuarios de los ultimos 30 dias.' . '<br>' ;

            return $cabecera.self::Logeos(); //$string . $corte.'<br>');
    }

//     public static function CantidadOperaciones()
//     {
//         $historial = Auditoria::all();
//         foreach ($historial as $obj) {
//             if($obj != null){
//                 $usuario = Usuario::where('IdUsuario','=',$obj->IdUsuario)->first();
                
//                 $string =   '' . '<br>' .
//                 'Usuario: ' . $usuario->Usuario . ' - Estado: ' .$usuario->Estado . '<br>' .
//                 'Nombre: ' . $obj->Nombre . '<br>' .
//                 'Cantidad de acciones: ' . $obj->Stock . '<br>' .
//                 'Tiempo estimado de espera: ' . $obj->TiempoEspera . '(minutos)' . '<br>' .
//                 'Area: ' . $obj->Area . '<br>';
//                 $corte = '------------------------------------------------------------------------' . '<br>';
                
//             }
//         }
//         return '----------------------------OPERACIONES USUARIOS---------------------------'.$string . $corte;
// }

public static function Logeos(){
    $historial = Auditoria::all();
    $string = '';
    foreach ($historial as $obj) {
        if($obj != null){
            if($obj->IdAccion == AuditoriaAcciones::Login){

                $usuario = Usuario::where('IdUsuario','=',$obj->IdUsuario)->first();
                
                $string =  $string . '' . '<br>' .
                'Usuario: ' . $usuario->Usuario . ' - Estado: ' .$usuario->Estado . '<br>' .
                'Nombre: ' . $obj->Nombre . '<br>' .
                'Fecha : ' . $obj->FechaAlta . '<br>' .
                'Hora:  ' . $obj->Hora . '<br>' .
                '------------------------------------------------------------------------' . '<br>';
                
            }
        }
    }
    return '----------------------------LOGEOS USUARIOS---------------------------'.$string ;
}
}
