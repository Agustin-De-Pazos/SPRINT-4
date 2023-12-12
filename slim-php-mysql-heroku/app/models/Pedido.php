<?php

namespace app\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pedido extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'IdPedido';
    protected $table = 'pedidos';
    public $incremeting = true;
    public $timestamps = true;

    const CREATED_AT = 'FechaAlta';
    const DELETED_AT = 'FechaBaja';
    const UPDATED_AT = 'FechaModificacion';

    public function usuario()
    {
        return $this->hasOne(Usuario::class,'IdUsuario');
    }

    public function mesa()
    {
        return $this->hasOne(Mesa::class,'IdMesa');
    }

    public $fillable = [
        'CodigoPedido','ImporteTotal',
        'Estado','TiempoPreparacion','NombreCliente',
        'PathFoto','IdMesa','IdUsuario','FechaAlta','FechaBaja','FechaModificacion','Puntuacion','Comentario'
    ]; 

}
?>