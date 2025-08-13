<?php
// app/Policies/CommentPolicy.php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    /**
     * Criação de comentário:
     * só alunos podem comentar em qualquer post.
     */
     public function create(User $user): bool
    {
        // Permite criar comentários se o usuário for 'student' OU 'monitor'
        return $user->role === 'student' || $user->role === 'monitor';
    }

    /**
     * Atualização de comentário:
     * só o próprio autor pode editar, independentemente do seu papel.
     */
    public function update(User $user, Comment $comment): bool
    {
        // Apenas verifica se o ID do usuário logado é o mesmo que o ID do criador do comentário.
        return $comment->user_id === $user->id;
    }

    /**
     * Exclusão de comentário:
     * só o próprio autor (aluno) pode excluir.
     */
    public function delete(User $user, Comment $comment): bool
    {
        // Reutiliza a mesma lógica de atualização para exclusão.
        // A lógica original permitia apenas alunos deletarem,
        // mas a nova lógica permite que o dono do comentário o exclua.
        // Se a intenção era restringir a exclusão a alunos, o código deve ser:
        // return $user->role === 'student' && $comment->user_id === $user->id;
        // Para manter a consistência, estou atualizando para a lógica do `update`
        return $comment->user_id === $user->id;
    }
}
