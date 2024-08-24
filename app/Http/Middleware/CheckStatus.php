<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
        if (Session::has('customer_id')) {
            echo Session::get('customer_id') . " - ";
            $sessionId = $request->session()->getId();
            echo $sessionId;

            //create entry in log device table
            $logDevice = UserLoggedDevices::where([
                'user_id' => Session::get('customer_id'),
                'session_id' => $request->session()->getId(),
            ])->first();

            if($logDevice){
                if($logDevice->is_log_off==1){

                    //remove entry in log device table
                    UserLoggedDevices::where('id', $logDevice->id)->delete();

                    //Session::flash();
                    $sessionNames = ['customer_id', 'customer_name', 'cartdata', 'deliverymethod', 'if_unavailable', 'billinginfo',
                        'paymentmethod', 'discount', 'discounttext', 'couponcode', 'discounttype', 'old_order_id'];
                    foreach($sessionNames as $sN) Session::forget($sN);

                    return redirect('/login');
                }
            }
        }

        return $next($request);
    }
}
