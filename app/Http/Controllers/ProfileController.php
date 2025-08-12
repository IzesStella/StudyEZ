<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Display the user's profile.
     */
    public function show(Request $request): Response
    {
        $user = $request->user();
        
        $profileData = [
            'user' => $user->only(['id', 'name', 'email', 'role', 'profile_photo', 'bio']),
            'isOwnProfile' => true, // É o próprio perfil
        ];

        // Se for monitor, incluir comunidades criadas
        if ($user->role === 'monitor') {
            $profileData['communities'] = $user->hasMany(\App\Models\Community::class, 'user_id')
                ->with(['posts'])
                ->get()
                ->map(function ($community) {
                    return [
                        'id' => $community->id,
                        'name' => $community->name,
                        'description' => $community->description,
                        'posts_count' => $community->posts->count(),
                        'created_at' => $community->created_at->format('d/m/Y'),
                    ];
                });
        }

        // Posts do usuário (monitor ou aluno)
        $profileData['posts'] = $user->posts()
            ->with(['community:id,name', 'comments'])
            ->latest()
            ->get()
            ->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'content' => $post->content,
                    'community' => $post->community,
                    'comments_count' => $post->comments->count(),
                    'created_at' => $post->created_at->format('d/m/Y H:i'),
                ];
            });

        // Comentários do usuário (monitor ou aluno)
        $profileData['comments'] = $user->comments()
            ->with(['post:id,title,community_id', 'post.community:id,name'])
            ->latest()
            ->get()
            ->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'post' => $comment->post,
                    'community' => $comment->post->community ?? null,
                    'created_at' => $comment->created_at->format('d/m/Y H:i'),
                ];
            });

        return Inertia::render('Profile', $profileData);
    }

    /**
     * Display another user's profile.
     */
    public function showUser(User $user): Response
    {
        $profileData = [
            'user' => $user->only(['id', 'name', 'email', 'role', 'profile_photo', 'bio']),
            'isOwnProfile' => false, // Indica que não é o próprio perfil
        ];

        // Se for monitor, incluir comunidades criadas
        if ($user->role === 'monitor') {
            $profileData['communities'] = $user->hasMany(\App\Models\Community::class, 'user_id')
                ->with(['posts'])
                ->get()
                ->map(function ($community) {
                    return [
                        'id' => $community->id,
                        'name' => $community->name,
                        'description' => $community->description,
                        'posts_count' => $community->posts->count(),
                        'created_at' => $community->created_at->format('d/m/Y'),
                    ];
                });
        }

        // Posts do usuário (monitor ou aluno)
        $profileData['posts'] = $user->posts()
            ->with(['community:id,name', 'comments'])
            ->latest()
            ->get()
            ->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'content' => $post->content,
                    'community' => $post->community,
                    'comments_count' => $post->comments->count(),
                    'created_at' => $post->created_at->format('d/m/Y H:i'),
                ];
            });

        // Comentários do usuário (monitor ou aluno)
        $profileData['comments'] = $user->comments()
            ->with(['post:id,title,community_id', 'post.community:id,name'])
            ->latest()
            ->get()
            ->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'post' => $comment->post,
                    'community' => $comment->post->community ?? null,
                    'created_at' => $comment->created_at->format('d/m/Y H:i'),
                ];
            });

        return Inertia::render('Profile', $profileData);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.show')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Update the user's profile avatar.
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'photo' => ['required', 'image', 'max:2048'],
        ]);

        $user = $request->user();

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $path = $photo->store('profile_photos', 'public');

            // Delete old avatar if exists
            if ($user->profile_photo) {
                \Storage::disk('public')->delete($user->profile_photo);
            }

            $user->profile_photo = $path;
            $user->save();
        }

        return Redirect::route('profile.show')->with('status', 'profile-photo-updated');
    }
}