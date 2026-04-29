<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = ['user_id', 'name', 'personal_team'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function proyectos()
    {
        return $this->hasMany(Proyecto::class);
    }

    public function invitations()
    {
        return $this->hasMany(TeamInvitation::class);
    }

    public function hasUser(User $user): bool
    {
        return $this->users()->where('user_id', $user->id)->exists()
            || $this->user_id === $user->id;
    }

    public function userRole(User $user): ?string
    {
        if ($this->user_id === $user->id) return 'admin';
        return $this->users()->where('user_id', $user->id)->first()?->pivot->role;
    }
}
