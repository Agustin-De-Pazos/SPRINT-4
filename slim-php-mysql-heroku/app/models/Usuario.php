<?php

namespace app\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Usuario extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'IdUsuario';
    protected $table = 'usuarios';
    public $incremeting = true;
    public $timestamps = true;

    const CREATED_AT = 'FechaAlta';
    const DELETED_AT = 'FechaBaja';
    const UPDATED_AT = 'FechaModificacion';

    public $fillable = [
        'Usuario','Clave','Nombre',
        'Apellido','Estado','IdUsuarioTipo',
        'Puesto','FechaAlta','FechaBaja','FechaModificacion'
    ]; 

    public function MostrarUsuario()
    {
        echo '-------------------------';
        echo PHP_EOL;
        echo 'USUARIO: '. $this->Usuario;
        echo PHP_EOL;
        echo 'NOMBRE: '. $this->Nombre;
        echo PHP_EOL;
        echo 'APELLIDO: '. $this->Apellido;
        echo PHP_EOL;
        echo 'PUESTO: '. $this->Puesto;
        echo PHP_EOL;
        echo 'ESTADO: '. $this->Estado;
        echo PHP_EOL;
        echo '-------------------------';
        echo PHP_EOL;
        echo PHP_EOL;
    }
    static public function ExisteUsuario($usuario)
    {
      if(Usuario::where('Usuario', '=', $usuario)->first() != null) {
         return true; 
        }

      return false;
    }
}

?>