<?php

namespace App\Livewire\Admin\Concerns;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

trait AuthorizesContentEditors
{
    protected function authorizeContentEditor(): User
    {
        $user = Auth::user();

        abort_unless(
            $user instanceof User && $user->isEditor() && $user->hasVerifiedEmail(),
            Response::HTTP_FORBIDDEN,
        );

        return $user;
    }
}
