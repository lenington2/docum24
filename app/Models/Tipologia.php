<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tipologia extends Model
{
    protected $fillable = ['categoria_id', 'nombre'];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class);
    }
}
