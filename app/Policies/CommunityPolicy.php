<?php
// app/Policies/CommunityPolicy.php

namespace App\Policies;

use App\Models\Community;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommunityPolicy
{
    use HandlesAuthorization;

    /**
     * Só monitores podem criar comunidades.
     */
    public function create(User $user): bool
    {
        return $user->role === 'monitor';
    }

    /**
     * Só o monitor dono pode atualizar a comunidade.
     */
    public function update(User $user, Community $community): bool
    {
        return $user->role === 'monitor'
            && $community->user_id === $user->id;
    }

    /**
     * Só o monitor dono pode excluir a comunidade.
     */
    public function delete(User $user, Community $community): bool
    {
        return $this->update($user, $community);
    }

    /**
     * Só alunos podem se inscrever (join/leave).
     */
    public function join(User $user, Community $community): bool
    {
        return $user->role === 'student';
    }
}
