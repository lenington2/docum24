<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UrlFavorita extends Model
{
    protected $fillable = [
        'user_id',
        'url',
        'nombre',
        'descripcion',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
