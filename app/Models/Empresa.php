<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $fillable = [
        'user_id', 'nombre', 'logo', 'direccion',
        'telefono', 'email', 'website', 'piva', 'descripcion',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logoUrl(): string
    {
        return $this->logo
            ? asset('storage/' . $this->logo)
            : '';
    }
}
