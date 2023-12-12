<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Session;

class Order extends Model
{
    use HasFactory;
    public $table = 'order_master';
	
	public function getAuthKey() {
		$ch = curl_init();
		//curl_setopt($ch, CURLOPT_URL, "https://api-sandbox.ninjavan.co/SG/2.0/oauth/access_token");
		curl_setopt($ch, CURLOPT_URL, "https://api.ninjavan.co/SG/2.0/oauth/access_token");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);

		curl_setopt($ch, CURLOPT_POSTFIELDS, "{
		  \"client_id\": \"0e2374fa2a294f39898ff1ff47342872\",
		  \"client_secret\": \"d80bf70bdba04e1d9a14c6a34b6ae91a\",
		  \"grant_type\": \"client_credentials\"
		}");

		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		  "Content-Type: application/json",
		  "Accept: application/json"
		));


		$response = curl_exec($ch);
		curl_close($ch);
		
		var_dump($response);

		$ninja_obj = json_decode($response, true);		
				
		$authkey = $ninja_obj['access_token'];
		
		Session::put('ninja_authkey', $authkey);
		
		return $authkey;
		
	}
	
	public function getTrackingNumber($authkey = '', $orderid = 0, $deliveryname = '', $deliveryemail = '', $shipmobile = '', $shipadd1 = '', $shipadd2 = '', $shipzip = '', $pickup = '', $delivery = '', $weight = '', $size = '') {
		
		
		$tracking_number = '';
		if($orderid > 0 && $authkey != '') {
			date_default_timezone_set('Asia/Singapore');
				
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, "https://api.ninjavan.co/SG/4.1/orders");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_POST, TRUE);

			//$rand = rand(0000, 1111);
			
			curl_setopt($ch, CURLOPT_POSTFIELDS, "{
			  \"service_type\": \"Parcel\",
			  \"service_level\": \"Standard\",
			  \"requested_tracking_number\": \"".$orderid."\",
			  \"reference\": {
				\"merchant_order_number\": \"".$orderid."\"
			  },
			  \"from\": {
				\"name\": \"HardwareCity Superstore\",
				\"phone_number\": \"+65 67691517\",
				\"email\": \"sales@hardwarecity.com.sg\",
				\"address\": {
				  \"address1\": \"Shophouse 204 – 204A – 206 \",
				  \"address2\": \"208 Choa Chu Kang Avenue 1\",
				  \"city\": \"Singapore\",
				  \"state\": \"Singapore\",
				  \"country\": \"SG\",
				  \"postcode\": \"689473\"
				}
			  },
			  \"to\": {
				\"name\": \"".$deliveryname."\",
				\"phone_number\": \"".$shipmobile."\",
				\"email\": \"".$deliveryemail."\",
				\"address\": {
				  \"address1\": \"".$shipadd1."\",
				  \"address2\": \"".$shipadd2."\",
				  \"city\": \"Singapore\",
				  \"province\": \"Singapore\",
				  \"country\": \"SG\",
				  \"postcode\": \"".$shipzip."\"
				}
			  },
			  \"parcel_job\": {
				\"is_pickup_required\": true,
				\"pickup_address_id\":234567,
				\"pickup_service_type\": \"Scheduled\",
				\"pickup_service_level\": \"Standard\",
				\"pickup_date\": \"".date("Y-m-d")."\",
				\"pickup_timeslot\": {
				  \"start_time\": \"09:00\",
				  \"end_time\": \"12:00\",
				  \"timezone\": \"Asia/Singapore\"
				},    
				\"pickup_instructions\": \"".$pickup."\",
				\"delivery_instructions\": \"".$delivery."\",
				\"dimensions\": {
				  \"weight\" : \"".$weight."\",
				  \"size\" : \"".$size."\"
				},
				\"delivery_start_date\": \"".date("Y-m-d")."\",
				\"delivery_timeslot\": {
				  \"start_time\": \"09:00\",
				  \"end_time\": \"22:00\",
				  \"timezone\": \"Asia/Singapore\"
				}
			  }
			}");

			$header_str = "Authorization: Bearer ".$authkey;

			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			  "Content-Type: application/json",
			  "Accept: application/json",
			  $header_str
			));
			
			$response = curl_exec($ch);
			print_r($response);
			curl_close($ch);
			if($response) {
				$result = json_decode($response);
				
				$tracking_number = $result->tracking_number;				
			}
		}
		return $tracking_number;
	}
	
	public function TrackOrder($authkey = '', $trackingnumber = '') {
		$ch = curl_init();
		$url = "https://api.ninjavan.co/SG/2.0/reports/waybill?tids=".$trackingnumber."&h=67691517";

		curl_setopt($ch, CURLOPT_URL, $url );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);

		$header_str = "Authorization: Bearer ".$authkey;

		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		  "Content-Type: application/json",
		  "Accept: application/json",
		  $header_str
		));
		$response = curl_exec($ch);
		curl_close($ch);

		header('Cache-Control: public'); 
		header('Content-type: application/pdf');
		header('Content-Disposition: inline; filename="'.$trackingnumber.'.pdf"');
		header('Content-Length: '.strlen($response));
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		ob_clean();
		flush();
		echo $response;
	}
}
