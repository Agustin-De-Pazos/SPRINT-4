<?php

namespace app\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PedidoDetalle extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'IdPedidoDetalle';
    protected $table = 'pedidosdetalle';
    public $incremeting = true;
    public $timestamps = true;

    const CREATED_AT = 'FechaAlta';
    const DELETED_AT = 'FechaBaja';
    const UPDATED_AT = 'FechaModificacion';

    public function pedido()
    {
        return $this->belongsTo(Pedido::class,'IdPedido');
    }

    public function producto()
    {
        return $this->hasOne(Producto::class,'IdProducto');
    }

    public $fillable = [
        'Cantidad','Estado','IdProducto','IdPedido', 'FechaAlta','FechaBaja','FechaModificacion'
    ]; 
}


?>