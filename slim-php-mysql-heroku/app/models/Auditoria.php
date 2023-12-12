<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Auditoria extends Model
{
    use SoftDeletes;

    protected $table = 'auditoria';
    protected $primaryKey = 'IdAuditoria';
    public $incrementing = true;
    //public $timestamps = false;

    const CREATED_AT = 'FechaAlta';
    const DELETED_AT = 'FechaBaja';
    const UPDATED_AT = 'FechaModificacion';

    protected $fillable = [
        'IdUsuario', 
        'IdUsuarioAccionTipo', 
        'IdPedido', 
        'IdMesa', 
        'IdProducto', 
        'IdArea', 
        'Hora', 
        'IdRefUsuario',
        'FechaAlta', 'FechaModificacion', 'FechaBaja'
    ];

    public function AuditoriaAcciones()
    {
        return $this->belongsTo(AuditoriaAcciones::class,'IdAccion');
    }

    public function Pedido()
    {
        return $this->belongsTo(Pedido::class, 'IdPedido');
    }
    public function Usuario()
    {
        return $this->belongsTo(Usuario::class, 'IdUsuario');
    }
    public function RefUsuario()
    {
        return $this->belongsTo(Usuario::class, 'IdRefUsuario');
    }

    public function Mesa()
    {
        return $this->belongsTo(Mesa::class, 'IdMesa');
    }
    
    public function Producto()
    {
        return $this->belongsTo(Producto::class, 'IdProducto');
    }

    public function Area()
    {
        return $this->belongsTo(Area::class, 'IdArea');
    }

}