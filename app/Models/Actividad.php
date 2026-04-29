<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    protected $table = 'actividades';
    
    protected $fillable = ['user_id', 'proyecto_id', 'tipo', 'descripcion'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public static function registrar(int $userId, string $tipo, string $descripcion, ?int $proyectoId = null): void
    {
        self::create([
            'user_id'    => $userId,
            'proyecto_id' => $proyectoId,
            'tipo'       => $tipo,
            'descripcion' => $descripcion,
        ]);
    }
}
