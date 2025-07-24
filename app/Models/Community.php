<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'user_id',  //incluido
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Quem criou a comunidade (monitor responsável)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
