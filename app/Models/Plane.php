<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Plane extends Model
{
    protected $table    = 'planes';
    protected $fillable = ['nombre', 'precio_euros', 'tokens_mes', 'duracion_dias', 'activo'];

    public function suscripciones()
    {
        return $this->hasMany(Suscripcion::class, 'plan_id');
    }
}
