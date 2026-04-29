<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $fillable = [
        'tipologia_id',
        'proyecto_id',
        'categoria_id',
        'user_id',
        'nombre',
        'archivo',
        'mime_type',
        'descripcion',
        'fecha_documento',
        'contenido_texto',
        'resumen_ia',
        'categoria_ia',
        'tipologia_ia',
        'estado'
    ];

    public function tipologia()
    {
        return $this->belongsTo(Tipologia::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
