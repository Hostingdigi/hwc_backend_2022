<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

class GrabPayService
{
    protected $partnetId;
    protected $partnerSecret;
    protected $clientId;
    protected $clientSecret;
    protected $merchantId;
    protected $baseUrl;
    protected $grabDiscovery = null;

    public function __construct()
    {
        $this->partnetId = '5e315b18-7bbf-4859-8ba0-366970d8226b';
        $this->partnerSecret = '06tpTQyeKUUdm3R7';
        $this->clientId = '6cdc4e582e1149c9ab5db8dbd27bfe09';
        $this->clientSecret = 'K5j-oqJQzDo-Ikao';
        $this->merchantId = '5dab3868-b5fe-4592-97ab-22fa24756ecc';
        $this->baseUrl = 'https://partner-api.grab.com'; // Sandbox URL
    }
    
    public function grabIdDiscovery()
    {
        $this->grabIdDiscoveryS();
        $this->createCharge();
        //
        // $this->generateAuthToken();
    }

    public function ComputeHMAC($dateString, $payloadtoSign)
    {
        $mdStr = base64_encode(hash('sha256', $payloadtoSign, true));

        $stringToSign = "POST" . "\n";
        $stringToSign .= "application/json" . "\n";
        $stringToSign .= $dateString . "\n";
        $stringToSign .= "/grabpay/partner/v2/charge/init" . "\n";
        $stringToSign .= $mdStr . "\n";

        $shaSignature = base64_encode(hash_hmac('sha256', $stringToSign, $this->partnerSecret, true));

        setcookie('hmacSignature', $shaSignature, time() + 36000);

        return $shaSignature;
    }
    
    public function createCharge()
    {
        $url = $this->baseUrl."/grabpay/partner/v2/charge/init";
        $gmDate = gmdate("D, d M Y H:i:s \G\M\T");
        

        $orderid = 123;

        $partnerTxID = "ORD_" . $orderid;
        $grandtotal=0.5;
        setcookie('partnerTxID', $partnerTxID, time() + 36000);
        
        $postData = [
            'partnerTxID' => $partnerTxID,
            'partnerGroupTxID' => "ORD_" . $orderid,
            'amount' => $grandtotal * 100,
            'currency' => 'SGD',
            'merchantID' => $this->merchantId,
            'description' => "Order from HardwareCity",
        ];
    
        $payLoad = json_encode($postData);
        echo $payLoad;
        die();
        $sha256code = $this->ComputeHMAC($gmDate,$payLoad);

        $headers = [
            'Authorization: '.$this->partnetId.':'.$sha256code,
            'Content-Type: application/json',
            'Date: '.$gmDate,
        ];
        
        print_r($headers);

        print_r($postData);
        
        $curl = curl_init($url);
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postData)); // JSON payload
        
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($curl);
        
        if (curl_errno($curl)) $error_msg = curl_error($curl);
        
        curl_close($curl);
        
        $data = json_decode($response, true);
        print_r($data);
    }
    
    public function generateAuthToken(){
        echo 123;
    }
    
    public function grabIdDiscoveryS()
    {
        $url = $this->baseUrl.'/grabid/v1/oauth2/.well-known/openid-configuration';
        // Initialize cURL session
        $curl = curl_init($url);
        $headers = [
            'Content-Type: application/json'
        ];
        
        // Set cURL options
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        // Execute the request
        $response = curl_exec($curl);
        
        // Check for errors
        if(curl_errno($curl)) $error_msg = curl_error($curl);
    
        // Close the session
        curl_close($curl);
        
        $this->grabDiscovery=json_decode($response, true);
        // echo $this->grabDiscovery['authorization_endpoint'];
        // Response data
        
    }

    // Example: Create payment
    public function createPayment($amount, $orderId, $returnUrl)
    {
        $payload = [
            'amount' => $amount,
            'merchantTransactionId' => $orderId,
            'returnUrl' => $returnUrl,
            // Add other required fields per API spec
        ];

        $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
            ->post("{$this->baseUrl}/payment/v1/payments", $payload);

        if ($response->successful()) {
            return $response->json();
        }

        // Handle errors
        return null;
    }

    // Add other API methods: refund, get status, webhook validation, etc.
}
