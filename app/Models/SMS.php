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
		$sid    = "AC91d9285115dbde467147601bf3018970"; 
		$token  = "8b016a2dac666a0ed9009f26e1ff183b"; 
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
									   "messagingServiceSid" => "MGb48a4db006f9739a7b00e1a0d20a194d",      
									   "body" => "Your order #".$orderid." status has been updated as ".$status 
								   ) 
						  ); 
						  print($message->sid);*/
	}
}
