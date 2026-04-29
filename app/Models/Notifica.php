<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifica extends Model
{
    protected $table = 'notifiche';
    
    protected $fillable = [
        'user_id',
        'documento_id',
        'fecha_scadenza',
        'dias_antes',
        'email',
        'estado',
        'enviada_at',
    ];

    protected function casts(): array
    {
        return [
            'fecha_scadenza' => 'date',
            'enviada_at'     => 'datetime',
        ];
    }

    public function documento()
    {
        return $this->belongsTo(Documento::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
