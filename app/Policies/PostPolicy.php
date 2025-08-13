<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Post;
use App\Models\Community;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Community $community): bool
    {
        if ($user->role === 'student') {
            return true;
        }

        return $user->role === 'monitor'
            && $community->user_id === $user->id;
    }

    /**
     * Determine whether the user can update the model.
     * Permitir ao monitor da comunidade ou o autor do post editar.
     */
    public function update(User $user, Post $post): bool
    {
        // Se o usuário é o dono do post, ele pode editar.
        if ($user->id === $post->user_id) {
            return true;
        }
        
        // Se o usuário é um monitor e dono da comunidade, ele também pode editar.
        return $user->role === 'monitor'
            && $post->community->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     * Permitir ao monitor da comunidade ou o autor do post deletar.
     */
    public function delete(User $user, Post $post): bool
    {
        // A política de deleção agora está mais flexível:
        // O autor do post pode deletar o próprio post.
        if ($user->id === $post->user_id) {
            return true;
        }
        
        // O monitor dono da comunidade pode deletar posts.
        return $user->role === 'monitor'
            && $post->community->user_id === $user->id;
    }
}
