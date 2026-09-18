<?php
/**
 * @author denis.chernonozhkin
 * @Date 18.09.2026 15:09
 */

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Mirror\Facades\Mirror;

class ImpersonationController extends Controller
{
    public function start(User $user): RedirectResponse
    {
        if (!auth()->user()->canImpersonate() || !$user->canBeImpersonated()) {
            abort(403, 'Вы не можете имперсонировать этого пользователя.');
        }

        Mirror::impersonate($user, null, ['reason' => 'admin panel']);

        return redirect()->route('home');
    }

    public function leave(): RedirectResponse
    {
        Mirror::leave();

        return redirect(config('mirror.redirect_leave'));
    }
}
