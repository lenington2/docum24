<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $fillable = ['proyecto_id', 'nombre'];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function tipologias()
    {
        return $this->hasMany(Tipologia::class);
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class);
    }
}
