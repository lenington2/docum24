<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversacion extends Model
{
    protected $table = 'conversaciones';

    protected $fillable = [
        'user_id',
        'session_id',
        'titulo',
        'historial',
        'ultimo_mensaje_at',
    ];

    protected $casts = [
        'historial'          => 'array',
        'ultimo_mensaje_at'  => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Genera título automático del primer mensaje del usuario
    public static function tituloDesde(string $mensaje): string
    {
        return mb_substr($mensaje, 0, 60) . (mb_strlen($mensaje) > 60 ? '...' : '');
    }
}
