<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Services\CartServices;
use App\Models\PunchoutSession;
use App\Models\Category;
use App\Models\Product;
use App\Models\Country;
use Session;

class PunchoutController extends Controller
{
    protected $cartServices = null;
    public function __construct(CartServices $cartServices)
    {
        $this->cartServices = $cartServices;
    }

    public function setup(Request $request)
    {
        $xml = @simplexml_load_string($request->getContent());
        if ($xml === false || !isset($xml->Request->PunchOutSetupRequest)) {
            return $this->cxmlStatus(400, 'Bad Request');
        }

        // The buyer's procurement system must send the shared secret agreed with
        // them. Without this check anyone could open a punchout session, which also
        // switches the cart to 0% tax.
        $expected = (string) config('services.punchout.shared_secret');
        $given = (string) ($xml->Header->Sender->Credential->SharedSecret ?? '');
        if ($expected === '' || !hash_equals($expected, $given)) {
            return $this->cxmlStatus(401, 'Unauthorized');
        }

        // Extract BuyerCookie
        $buyerCookie = (string) $xml->Request->PunchOutSetupRequest->BuyerCookie;
        $payloadId   = (string) $xml['payloadID'];
        $deployment  = (string) $xml->Request['deploymentMode'];
        $browserUrl  = (string) $xml->Request->PunchOutSetupRequest->BrowserFormPost->URL;
        $supplierUrl = (string) $xml->Request->PunchOutSetupRequest->SupplierSetup->URL;

        // Save to punchout_sessions table
        PunchoutSession::create([
            'buyer_cookie' => $buyerCookie,
            'payload_id'   => $payloadId,
            'deployment'   => $deployment,
            'browser_form_post_url' => $browserUrl,
            'supplier_setup_url'    => $supplierUrl,
            'cxml' => $request->getContent()
        ]);

        // Build PunchOutSetupResponse
        $response = '<?xml version="1.0" encoding="UTF-8"?>';
        $response .= '<cXML payloadID="'.uniqid().'" timestamp="'.now()->toIso8601String().'">';
        $response .= '<Response><Status code="200" text="OK"/>';
        $response .= '<PunchOutSetupResponse><StartPage><URL>'
                . url('/punchout/login?buyer='.Crypt::encryptString($buyerCookie))
                . '</URL></StartPage></PunchOutSetupResponse>';
        $response .= '</Response></cXML>';

        return response($response, 200)->header('Content-Type', 'text/xml');
    }

    /** A cXML reply carrying only a status (cXML reports errors in-band). */
    private function cxmlStatus(int $code, string $text)
    {
        $response = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<cXML payloadID="' . uniqid() . '" timestamp="' . now()->toIso8601String() . '">'
            . '<Response><Status code="' . $code . '" text="' . e($text) . '"/></Response></cXML>';

        return response($response, 200)->header('Content-Type', 'text/xml');
    }

    public function login(Request $request)
    {
        if(!$request->filled('buyer')) abort(401, 'Unauthorized punchout request');

        // $encryptedId = Crypt::encryptString(25436);
        try {
            $buyerId = Crypt::decryptString(trim($request->buyer));
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            abort(401, 'Unauthorized punchout request');
        }

        $punchoutUser = PunchoutSession::where(['buyer_cookie' => $buyerId, 'status' => 1])->latest()->first();

        if ($punchoutUser) {
            session([
                'punchout' => true,
                'punchout_id' => $punchoutUser->id,
                'buyer_cookie' => $buyerId,
            ]);
            return redirect('/');
        }

        abort(401, 'Unauthorized punchout request');
    }

    public function checkout(Request $request)
    {
        if (!session()->has('punchout')) {
            DB::table('cart_details')->where('user_token',session()->getId())->delete();
            abort(401, 'Unauthorized punchout checkout');
        }

        $cartItems = $this->cartServices->cartItems($billinginfo['ship_country'] ?? null);
        $cartdata = $cartItems['cartItems'];
        $subtotal = $cartItems['subTotal'];
        $grandtotal = $cartItems['grandTotal'];
        $countryDetails = Country::where('countrycode', $cartItems['countryCode'])->first();

        $punchoutUser = PunchoutSession::find(session('punchout_id'));
        
        $headerXml = new \SimpleXMLElement($punchoutUser->cxml);
        $fromIdentity = (string)$headerXml->Header->From->Credential->Identity ?? '';
        $toIdentity = (string)$headerXml->Header->To->Credential->Identity ?? '';
        $senderIdentity = (string)$headerXml->Header->Sender->Credential->Identity ?? '';
        $sharedSecret   = (string)$headerXml->Header->Sender->SharedSecret ?? '';
        $sharedUserAgent   = (string)$headerXml->Header->Sender->UserAgent ?? '';

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><cXML/>');
         
        $xml->addAttribute('version', "1.2.051");
        $xml->addAttribute('payloadID', uniqid());
        $xml->addAttribute('timestamp', now()->toIso8601String());
        
        $header = $xml->addChild('Header');
        $headerFrom = $header->addChild('From');
        $headerFromCredential = $headerFrom->addChild('Credential');
        $headerFromCredential->addAttribute('domain', "DUNS");
        $headerFromCredential->addChild('Identity', $fromIdentity);
        
        $headerTo = $header->addChild('To');
        $headerToCredential = $headerTo->addChild('Credential');
        $headerToCredential->addAttribute('domain', "NetworkID");
        $headerToCredential->addChild('Identity', $toIdentity);
        
        $headerSender = $header->addChild('Sender');
        $headerSenderCredential = $headerSender->addChild('Credential');
        $headerSenderCredential->addAttribute('domain', "DUNS");
        $headerSenderCredential->addChild('Identity', $senderIdentity);
        $headerSenderCredential->addChild('SharedSecret', $sharedSecret);
        $headerSender->addChild('UserAgent',$sharedUserAgent);

        $response = $xml->addChild('Message');
        $punchOut = $response->addChild('PunchOutOrderMessage');

        $punchOut->addChild('BuyerCookie', $punchoutUser->buyer_cookie);
        $PunchOutOrderMessageHeader = $punchOut->addChild('PunchOutOrderMessageHeader');
        $PunchOutOrderMessageHeader->addAttribute('operationAllowed', "inspect");
        $PunchOutOrderMessageHeaderTotal = $PunchOutOrderMessageHeader->addChild('Total');
        $PunchOutOrderMessageHeaderTotalMoney = $PunchOutOrderMessageHeaderTotal->addChild('Money',$grandtotal);
        $PunchOutOrderMessageHeaderTotalMoney->addAttribute('currency', "SGD");

        $lineNumber = 1;

        foreach ($cartdata as $item) {
            
            $unpscCode = '';
            $productDetails = Product::where('Id',$item['productId'])->first();
            $productCategory = Category::where('TypeId',$productDetails->Types)->first();
            if($productCategory){
                if($productCategory->ParentLevel==0) $unpscCode = $productCategory->unpsc_code;
                if($productCategory->ParentLevel!=0){
                    $productCategoryOne = Category::where('TypeId',$productCategory->ParentLevel)->first();
                    if($productCategoryOne){
                        if($productCategoryOne->ParentLevel==0) $unpscCode = $productCategoryOne->unpsc_code;
                        if($productCategoryOne->ParentLevel!=0){
                            $productCategoryTwo = Category::where('TypeId',$productCategoryOne->ParentLevel)->first();
                            if($productCategoryTwo) $unpscCode = $productCategoryTwo->unpsc_code;
                        }
                    }
                }
            }

            $itemNode = $punchOut->addChild('ItemIn');
            $itemNode->addAttribute('quantity', $item['qty']);
            $itemNode->addAttribute('lineNumber', $lineNumber);

            $itemID = $itemNode->addChild('ItemID');
            $itemID->addChild('SupplierPartID', $item['productId']);

            $itemDetail = $itemNode->addChild('ItemDetail');

            $unitPrice = $itemDetail->addChild('UnitPrice');
            $money = $unitPrice->addChild('Money', number_format($item['price'], 2));
            $money->addAttribute('currency', 'SGD');

            $desc = $itemDetail->addChild('Description', htmlspecialchars(($item['productName'].($item['productoption'] ? ' - '.$item['productoption'] : '')), ENT_XML1, 'UTF-8'));
            $desc->addAttribute('xml:lang', 'en');
            
            $classify = $itemDetail->addChild('Classification', htmlspecialchars($unpscCode, ENT_XML1, 'UTF-8'));
            $classify->addAttribute('domain', 'UNSPSC');
            
            $manufID = $itemDetail->addChild('ManufacturerPartID', "");
            $manufName = $itemDetail->addChild('ManufacturerName', "");
            $leadTime = $itemDetail->addChild('LeadTime', "");

            $itemDetail->addChild('UnitOfMeasure', 'EA');

            $lineNumber++;
        }

        $browserFormPost = $punchoutUser->browser_form_post_url;
        
        // $encodedXml = urlencode($xml->asXML());
        $encodedXml = $xml->asXML();
        session()->forget(['punchout', 'punchout_id', 'buyer_cookie','cartdata', 'discount','discounttext','couponcode', 'discounttype','old_order_id']);
        DB::table('cart_details')->where('user_token',Session::get('_token'))->delete();
        
        $punchoutUser->status = 0;
        $punchoutUser->save();
        
        $html = '
            <form id="punchoutForm" method="POST" action="'.$browserFormPost.'">
                <input type="hidden" name="cXML-urlencoded" value="'.htmlspecialchars($encodedXml, ENT_QUOTES, 'UTF-8').'" />
            </form>
            <script>
                document.getElementById("punchoutForm").submit();
            </script>
            ';

        return response($html);
    }
    
    public function checkoutTest(Request $request)
    {
        if (!session()->has('punchout')) {
            DB::table('cart_details')->where('user_token',session()->getId())->delete();
            abort(401, 'Unauthorized punchout checkout');
        }

        $cartItems = $this->cartServices->cartItems($billinginfo['ship_country'] ?? null);
        $cartdata = $cartItems['cartItems'];
        $subtotal = $cartItems['subTotal'];
        $grandtotal = $cartItems['grandTotal'];
        $countryDetails = Country::where('countrycode', $cartItems['countryCode'])->first();

        $punchoutUser = PunchoutSession::find(session('punchout_id'));
        
        $headerXml = new \SimpleXMLElement($punchoutUser->cxml);
        $fromIdentity = (string)$headerXml->Header->From->Credential->Identity ?? '';
        $toIdentity = (string)$headerXml->Header->To->Credential->Identity ?? '';
        $senderIdentity = (string)$headerXml->Header->Sender->Credential->Identity ?? '';
        $sharedSecret   = (string)$headerXml->Header->Sender->SharedSecret ?? '';
        $sharedUserAgent   = (string)$headerXml->Header->Sender->UserAgent ?? '';

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><cXML/>');
         
        $xml->addAttribute('version', "1.2.051");
        $xml->addAttribute('payloadID', uniqid());
        $xml->addAttribute('timestamp', now()->toIso8601String());
        
        $header = $xml->addChild('Header');
        $headerFrom = $header->addChild('From');
        $headerFromCredential = $headerFrom->addChild('Credential');
        $headerFromCredential->addAttribute('domain', "DUNS");
        $headerFromCredential->addChild('Identity', $fromIdentity);
        
        $headerTo = $header->addChild('To');
        $headerToCredential = $headerTo->addChild('Credential');
        $headerToCredential->addAttribute('domain', "NetworkID");
        $headerToCredential->addChild('Identity', $toIdentity);
        
        $headerSender = $header->addChild('Sender');
        $headerSenderCredential = $headerSender->addChild('Credential');
        $headerSenderCredential->addAttribute('domain', "DUNS");
        $headerSenderCredential->addChild('Identity', $senderIdentity);
        $headerSenderCredential->addChild('SharedSecret', $sharedSecret);
        $headerSender->addChild('UserAgent',$sharedUserAgent);

        $response = $xml->addChild('Message');
        $punchOut = $response->addChild('PunchOutOrderMessage');

        $punchOut->addChild('BuyerCookie', $punchoutUser->buyer_cookie);
        $PunchOutOrderMessageHeader = $punchOut->addChild('PunchOutOrderMessageHeader');
        $PunchOutOrderMessageHeader->addAttribute('operationAllowed', "inspect");
        $PunchOutOrderMessageHeaderTotal = $PunchOutOrderMessageHeader->addChild('Total');
        $PunchOutOrderMessageHeaderTotalMoney = $PunchOutOrderMessageHeaderTotal->addChild('Money',$grandtotal);
        $PunchOutOrderMessageHeaderTotalMoney->addAttribute('currency', "SGD");

        $lineNumber = 1;

        foreach ($cartdata as $item) {
            
            $unpscCode = '';
            $productDetails = Product::where('Id',$item['productId'])->first();
            $productCategory = Category::where('TypeId',$productDetails->Types)->first();
            if($productCategory){
                if($productCategory->ParentLevel==0) $unpscCode = $productCategory->unpsc_code;
                if($productCategory->ParentLevel!=0){
                    $productCategoryOne = Category::where('TypeId',$productCategory->ParentLevel)->first();
                    if($productCategoryOne){
                        if($productCategoryOne->ParentLevel==0) $unpscCode = $productCategoryOne->unpsc_code;
                        if($productCategoryOne->ParentLevel!=0){
                            $productCategoryTwo = Category::where('TypeId',$productCategoryOne->ParentLevel)->first();
                            if($productCategoryTwo) $unpscCode = $productCategoryTwo->unpsc_code;
                        }
                    }
                }
            }

            $itemNode = $punchOut->addChild('ItemIn');
            $itemNode->addAttribute('quantity', $item['qty']);
            $itemNode->addAttribute('lineNumber', $lineNumber);

            $itemID = $itemNode->addChild('ItemID');
            $itemID->addChild('SupplierPartID', $item['productId']);

            $itemDetail = $itemNode->addChild('ItemDetail');

            $unitPrice = $itemDetail->addChild('UnitPrice');
            $money = $unitPrice->addChild('Money', number_format(CartServices::excludeItemPriceGst($item['price'],($countryDetails->taxpercentage ?? 0)), 2));
            $money->addAttribute('currency', 'SGD');

            $desc = $itemDetail->addChild('Description', htmlspecialchars(($item['productName'].($item['productoption'] ? ' - '.$item['productoption'] : '')), ENT_XML1, 'UTF-8'));
            $desc->addAttribute('xml:lang', 'en');
            
            $classify = $itemDetail->addChild('Classification', htmlspecialchars($unpscCode, ENT_XML1, 'UTF-8'));
            $classify->addAttribute('domain', 'UNSPSC');
            
            $manufID = $itemDetail->addChild('ManufacturerPartID', "");
            $manufName = $itemDetail->addChild('ManufacturerName', "");
            $leadTime = $itemDetail->addChild('LeadTime', "");

            $itemDetail->addChild('UnitOfMeasure', 'EA');

            $lineNumber++;
        }

        $browserFormPost = $punchoutUser->browser_form_post_url;
        
        /*
        // When returning, prepend declaration + DOCTYPE
       # $output = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        #$output .= '<!DOCTYPE cXML SYSTEM "http://xml.cxml.org/schemas/cXML/1.2.051/cXML.dtd">' . PHP_EOL;
        #$output .= $xml->asXML();
        */
        $encodedXml = urlencode($xml->asXML());
        // $encodedXml = urlencode($output);

        // session()->forget(['punchout', 'punchout_id', 'buyer_cookie','cartdata', 'discount','discounttext','couponcode', 'discounttype','old_order_id']);
        // DB::table('cart_details')->where('user_token',Session::get('_token'))->delete();
        
        // $punchoutUser->status = 0;
        // $punchoutUser->save();
        
        return response($xml->asXML(), 200)
            ->header('Content-Type', 'text/xml');

        $html = '
            <form id="punchoutForm" method="POST" action="'.$browserFormPost.'">
                <input type="hidden" name="cXML-urlencoded" value="'.$encodedXml.'" />
            </form>
            <script>
                document.getElementById("punchoutForm").submit();
            </script>
            ';

        // return response($html);
    }
}
