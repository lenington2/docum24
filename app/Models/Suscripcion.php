<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Suscripcion extends Model
{
    protected $table    = 'suscripciones';
    protected $fillable = ['user_id', 'plan_id', 'tokens_usados', 'tokens_limite', 'fecha_inicio', 'fecha_fin', 'estado','stripe_subscription_id', 'stripe_price_id'];
    protected $casts    = ['fecha_inicio' => 'date', 'fecha_fin' => 'date'];

    public function user()   { return $this->belongsTo(User::class); }
    public function plan()   { return $this->belongsTo(Plane::class, 'plan_id'); }
    public function logs()   { return $this->hasMany(TokenLog::class); }

    public function getPorcentajeUsadoAttribute(): float
    {
        if ($this->tokens_limite <= 0) return 0;
        return round(($this->tokens_usados / $this->tokens_limite) * 100, 1);
    }

    public function getTokensRestantesAttribute(): int
    {
        return max(0, $this->tokens_limite - $this->tokens_usados);
    }

    public function isAgotada(): bool
    {
        return $this->tokens_usados >= $this->tokens_limite;
    }
}
