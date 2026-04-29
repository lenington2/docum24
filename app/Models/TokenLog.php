<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TokenLog extends Model
{
    public $timestamps  = false;
    protected $fillable = ['user_id', 'suscripcion_id', 'tipo', 'tokens_input', 'tokens_output', 'tokens_total', 'modelo'];
    protected $casts    = ['created_at' => 'datetime'];

    public function user()        { return $this->belongsTo(User::class); }
    public function suscripcion() { return $this->belongsTo(Suscripcion::class); }
}
