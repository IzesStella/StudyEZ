<?php
// app/Policies/PostPolicy.php

namespace App\Policies;

use App\Models\Post;
use App\Models\Community;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    /**
     * Criação de post:
     * - Alunos podem postar em qualquer comunidade.
     * - Monitores apenas na comunidade que criaram.
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
     * Atualização de post:
     * só monitor dono da comunidade pode editar.
     */
    public function update(User $user, Post $post): bool
    {
        return $user->role === 'monitor'
            && $post->community->user_id === $user->id;
    }

    /**
     * Exclusão de post:
     * só monitor dono da comunidade pode excluir.
     */
    public function delete(User $user, Post $post): bool
    {
        return $this->update($user, $post);
    }
}
