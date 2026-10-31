<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Twilio\Rest\Client;
use App\Models\CountryCodes;
class SMS extends Model
{
    use HasFactory;
	protected $table = 'sms';
	
	public function sendSMS($mobile = '', $country = '', $orderid = 0, $status = '') {
		// From .env (TWILIO_SID / TWILIO_TOKEN); they used to be hardcoded here
		$sid    = (string) config('services.twilio.sid');
		$token  = (string) config('services.twilio.token');
		if ($sid === '' || $token === '') {
			return;
		}
		$twilio = new Client($sid, $token); 
		
		$countrycodes = CountryCodes::where('iso', $country)->select('phonecode')->first();
		if($countrycodes) {
			$mobile = '+'.$countrycodes->phonecode.$mobile;
		}
		/*$phone_number = $twilio->lookups->v1->phoneNumbers($mobile)->fetch(["countryCode" => $country]);

		print_r($phone_number); exit;*/
		/*
		$message = $twilio->messages 
						  ->create($mobile, // to 
								   array(  
									   "messagingServiceSid" => config('services.twilio.messaging_service'),      
									   "body" => "Your order #".$orderid." status has been updated as ".$status 
								   ) 
						  ); 
						  print($message->sid);*/
	}
}
