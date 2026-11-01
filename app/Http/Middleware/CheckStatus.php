<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\UserLoggedDevices;

class CheckStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            
            $sessionId = $request->session()->getId();
            
            //create entry in log device table
            $logDevice = UserLoggedDevices::where([
                'user_id' => auth()->id(),
                'session_id' => $request->session()->getId(),
            ])->first();

            if($logDevice){
                if($logDevice->is_log_off==1){

                    //remove entry in log device table
                    UserLoggedDevices::where('id', $logDevice->id)->delete();

                    Auth::guard('web')->logout();
                    Session::flush();

                    return redirect('/login');
                }
            }
        }
        return $next($request);
    }
}
