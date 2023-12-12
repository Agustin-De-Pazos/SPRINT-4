<?php

namespace app\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'IdProducto';
    protected $table = 'productos';
    public $incremeting = true;
    public $timestamps = true;

    const CREATED_AT = 'FechaAlta';
    const DELETED_AT = 'FechaBaja';
    const UPDATED_AT = 'FechaModificacion';

    public $fillable = [
        'Nombre','PrecioUnidad','TiempoEspera',
        'Area','TipoProducto','Stock','FechaAlta',
        'FechaBaja','FechaModificacion'
    ];
}
?>