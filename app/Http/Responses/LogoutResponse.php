<?php
 
namespace App\Http\Responses;
 
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as Responsable;
use Illuminate\Http\RedirectResponse;
use Laravel\WorkOS\Http\Requests\AuthKitLogoutRequest;
 
class LogoutResponse implements Responsable
{
    public function toResponse($request)
    {
        // change this to your desired route
        $workOS = AuthKitLogoutRequest::createFrom($request);
        $workOS->logout();
        return redirect()->route('login');
        // $request->logout();
    }
}