<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrabPayFunctions extends Model
{
    public function getAuthorizeLink($request = '')
    {

        $scope = ['payment.one_time_charge'];
        $response_type = 'code';
        $redirect_uri = CONST_REDIRECT_URI;

        $nonce = $this->generateRandomString(32);
        $state = $this->generateRandomString(14);
        $code_challenge_method = 'S256';
        $code_verifier = $this->generateCodeVerifier(64);
        $code_challenge = $this->generateCodeChallengeForAuthorization($code_verifier);
        $countryCode = "SG";
        $currencyCode = "SGD";

        setcookie('code_verifier', $code_verifier, time() + 36000);
        setcookie('code_challenge', $code_challenge, time() + 36000);

        $str = http_build_query([
            'acr_values' => "consent_ctx:countryCode=" . $countryCode . ",currency=" . $currencyCode,
            'client_id' => CONST_CLIENT_ID,
            'code_challenge' => $code_challenge,
            'code_challenge_method' => $code_challenge_method,
            'nonce' => $nonce,
            'request' => $request,
            'response_type' => $response_type,
            'scope' => 'payment.one_time_charge',
            'state' => $state,
            'redirect_uri' => $redirect_uri,
        ]);
        // You should get this URL from service discovery
        $url = ENDPOINT_URL . 'grabid/v1/oauth2/authorize?' . $str;
        return $url;
    }

    public function generateRandomString($length = 16)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function generateCodeVerifier($len)
    {
        return bin2hex(random_bytes($len));
    }

    public function computeHmac256($message, $secret)
    {
        $s = hash_hmac('sha256', $message, $secret, true);
        return $this->base64UrlEncodeNormal($s);
    }

    public function generateCodeChallenge($codeVerifier)
    {
        $hash = hash('sha256', $codeVerifier, true);
        $code_challenge = base64_encode($hash);
        return $code_challenge;
    }

    public function generateCodeChallengeForAuthorization($codeVerifier)
    {
        $hash = hash('sha256', $codeVerifier, true);
        $code_challenge = $this->base64_encode_url($hash);
        return $code_challenge;
    }

    public function base64_encode_url($string)
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($string));
    }

    public function base64UrlEncodeUpdated($url)
    {
        return str_replace(['=', '+', '/'], ['', '-', '_'], base64_encode($url));
    }

    public function ComputeHMAC($dateString, $payloadtoSign)
    {

        $mdStr = $this->generateCodeChallenge($payloadtoSign);

        $stringToSign = "POST" . "\n";
        $stringToSign .= "application/json" . "\n";
        $stringToSign .= $dateString . "\n";
        $stringToSign .= "/grabpay/partner/v2/charge/init" . "\n";
        $stringToSign .= $mdStr . "\n";

        $partner_secret = CONST_PARTNER_SECRET;

        $shaSignature = $this->computeHmac256($stringToSign, $partner_secret);

        // echo "<br/><u><b>HMAC Signature</b></u><br/>".$shaSignature."<br/>";

        $partnerId = CONST_PARTNER_ID;

        setcookie('hmacSignature', $shaSignature, time() + 36000);

        $hmacSignature = $partnerId . ':' . $shaSignature;

        return $hmacSignature;
    }

    public function base64UrlEncodeNormal($url)
    {
        return base64_encode($url);
    }

    public function base64UrlEncode($url)
    {
        return base64_encode($url);
    }
}
