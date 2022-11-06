<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, "https://api-sandbox.ninjavan.co/SG/2.0/oauth/access_token");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);

	curl_setopt($ch, CURLOPT_POST, TRUE);

	curl_setopt($ch, CURLOPT_POSTFIELDS, "{
	  \"client_id\": \"2e64b30b998d42ad923bb4b6fab79e66\",
	  \"client_secret\": \"2650a1541d7f4a59bf3d5b59aaf6573f\",
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
	
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, "https://api-sandbox.ninjavan.co/SG/4.1/orders");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);

	curl_setopt($ch, CURLOPT_POST, TRUE);

	curl_setopt($ch, CURLOPT_POSTFIELDS, "{
	  \"service_type\": \"Parcel\",
	  \"service_level\": \"Standard\",
	  \"requested_tracking_number\": \"2\",
	  \"reference\": {
		\"merchant_order_number\": \"2\"
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
		\"name\": \"Balamurugan K\",
		\"phone_number\": \"9003830094\",
		\"email\": \"balamurugan.sk@gmail.com\",
		\"address\": {
		  \"address1\": \"North Street\",
		  \"address2\": \"North Street\",
		  \"city\": \"Singapore\",
		  \"province\": \"Singapore\",
		  \"country\": \"SG\",
		  \"postcode\": \"247964\"
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
		\"pickup_instructions\": \"Testing\",
		\"delivery_instructions\": \"Testing\",
		\"dimensions\": {
		  \"weight\" : \"\",
		  \"size\" : \"S\"
		},
		\"delivery_start_date\": \"".date("Y-m-d")."\",
		\"delivery_timeslot\": {
		  \"start_time\": \"09:00\",
		  \"end_time\": \"22:00\",
		  \"timezone\": \"Asia/Singapore\"
		}
	  }
	}");

	$header_str = "Authorization: bearer ".$authkey;

	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	  "Content-Type: application/json",
	  "Accept: application/json",
	  $header_str
	));

	$response = curl_exec($ch);
	curl_close($ch);
	
	$result = json_decode($response);

	$tracking_number = $result->tracking_number;
	echo "first" .$tracking_number."<br>";



?>