<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    protected $fillable = ['user_id', 'nombre', 'descripcion'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categorias()
    {
        return $this->hasMany(Categoria::class);
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
