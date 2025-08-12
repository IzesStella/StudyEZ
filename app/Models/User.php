<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Chat;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile_photo',
        'bio',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        // Remova o cast 'password' => 'hashed'
    ];

    public function communities(): BelongsToMany
    {
        return $this->belongsToMany(Community::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function getAvatarAttribute()
    {
        return $this->profile_photo ? "/storage/{$this->profile_photo}" : '/images/default-avatar.svg';
    }

    public function planners()
    {
        return $this->hasMany(Planner::class);
    }

    /**
     * Define o relacionamento Many-to-Many com o modelo Chat.
     * Isso é essencial para que o ChatController possa buscar
     * os chats de um usuário.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function chats(): BelongsToMany
    {
        return $this->belongsToMany(Chat::class);
    }
}
