<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Filament\Http\Controllers\Auth\LogoutController;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse;
use Filament\Facades\Filament;
use Laravel\WorkOS\WorkOS;
use Illuminate\Support\Facades\Auth;
use WorkOS\UserManagement;

class CustomLogoutController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        Filament::auth()->logout();
        $accessToken = session()->get('workos_access_token');
        $workOsSession = $accessToken
            ? WorkOS::decodeAccessToken($accessToken)
            : false;

        Auth::guard('web')->logout();

        session()->invalidate();
        session()->regenerateToken();

        // return app(LogoutResponse::class);
        if (! $workOsSession) {
            return redirect()->route('login');
        }

        $logoutUrl = (new UserManagement)->getLogoutUrl(
            $workOsSession['sid'],
        );

        return class_exists(Inertia::class)
            ? Inertia::location($logoutUrl)
            : redirect($logoutUrl);
    }
}
