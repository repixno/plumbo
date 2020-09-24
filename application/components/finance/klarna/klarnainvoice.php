<?PHP
library( 'Klarna.Invoice.Klarna' );
config( 'finance.klarna' );
config( 'finance.klarnaerrors');

Class KlarnaInvoiceEF{
    
    private $klarna;
    public $order;
    public $snippet;
    public $eid = '';
    private $sharedSecret = '';
    private $server = '';
    public $vat = 25;
    
    public function __construct() {
        
        // Dependencies from http://phpxmlrpc.sourceforge.net/
        require_once '/var/www/repix/application/library/Klarna/Invoice/transport/xmlrpc-3.0.0.beta/lib/xmlrpc.inc';
        require_once '/var/www/repix/application/library/Klarna/Invoice/transport/xmlrpc-3.0.0.beta/lib/xmlrpc_wrappers.inc';
        
        //$parameters = Settings::Get( 'finance', 'testklarnainvoice' );
        $parameters = Settings::Get( 'finance', 'klarnainvoice' );
        
        //if( $_GET['live'] ){
        $this->eid = $parameters['eid'];
        $this->sharedSecret = $parameters['sharedSecret'];
        //}
        
        $this->klarna = new Klarna();
        
        $this->klarna->config(
            $parameters['eid'],             // Merchant ID
            $parameters['sharedSecret'],    // Shared secret
            KlarnaCountry::NO,              // Purchase country
            KlarnaLanguage::NB,             // Purchase language
            KlarnaCurrency::NOK,            // Purchase currency
            $parameters['server'],                   // Server
            'json',                         // PClass storage
            './pclasses.json'               // PClass storage URI path
        );
        
    }
    
    
    public function register( $cart, $post = ''  ){
        
        $pno = $post['pno'];
        $mobile = $post['mobile'];
        $firstname = utf8_decode( $post['cname'] );
        $lastname = utf8_decode(  $post['contactperson'] );
        $address = utf8_decode(  $post['address'] );
        $zipcode = $post['zipcode'];
        $city    = utf8_decode(  $post['city'] );
        
        $cartArray = $cart->asArray();
        
        $user = new User( Login::userid() );
        $userArray =  $user->asArray();
        $klarnacart = array();
        
        $klarnacart = $this->Additems($cartArray);
        foreach( $klarnacart as $item ){    
            list( $quantity, $articlenr, $title, $price, $vat, $discount, $flags ) = $item;
            $this->klarna->addArticle( $quantity, $articlenr, $title, $price, $vat, $discount, KlarnaFlags::INC_VAT  );
        }
        
        $deliveryprice = $cartArray['deliverytype']['price'];
        
        if( !$deliveryprice ){
            $deliveryprice = 49.00;
            $deliverytitle = "delivery";
        }else{
            $deliverytitle = $cartArray['deliverytype']['title'];
        }
        
        $this->klarna->addArticle( 1, "", $deliverytitle, $deliveryprice, 25, 0, KlarnaFlags::INC_VAT | KlarnaFlags::IS_SHIPMENT );
        $site = 'http://' . $_SERVER[HTTP_HOST];
        
        $addr = new KlarnaAddr(
            $userArray['email'], // Email address
            '',                  // Telephone number, only one phone number is needed
            $mobile,              // Cell phone number
            '',      // First name (given name)
            '',       // Last name (family name)
            '',                           // No care of, C/O
            $address,        // Street address
            $zipcode,        // Zip code
            $city,           // City
            KlarnaCountry::NO,            // Country
            null,                         // House number (AT/DE/NL only)
            null                          // House extension (NL only)
        );
        
        
        $addr->setCompanyName( $firstname );
        
        $this->klarna->setAddress(KlarnaFlags::IS_BILLING, $addr);
        $this->klarna->setAddress(KlarnaFlags::IS_SHIPPING, $addr);
        
        $this->klarna->setReference( $lastname, '');

        try {
            $result = $this->klarna->reserveAmount(
                $pno, // PNO (Date of birth for AT/DE/NL)
                null, // KlarnaFlags::MALE, KlarnaFlags::FEMALE (AT/DE/NL only)
                -1,   // Automatically calculate and reserve the cart total amount
                KlarnaFlags::NO_FLAG,
                KlarnaPClass::INVOICE
            );
            
            $rno = $result[0];
            $status = $result[1];
            
            $_SESSION['klarna_invoice'] = $rno;
            // $status is KlarnaFlags::PENDING or KlarnaFlags::ACCEPTED.
             return array( 'status' => $status, 'rno' => $rno);
        } catch(Exception $e) {
            $errors = Settings::Get( 'finance', 'klarnaerrors' );
            $message = $errors[$e->getCode()];
            return array( 'status' => $e->getCode(), 'message' => $message['message'] );
        }
    }
    
    
    
    public function updateOrder( $orderid = nullm, $reference ){
        
        $this->klarna->setEstoreInfo(
            $orderid,       // Order ID 1
            '',             // Order ID 2
            ''              // Optional username, email or identifier
        );
        
        $this->klarna->update( $reference );
    }
    
    
    public function test(){
        exit;
        //$result = $this->klarna->hasAccount('4103219202');
            $response = $this->klarna->checkoutService(
                100050, // Total price of the checkout including VAT
                'NOK', // Currency used by the checkout
                'nb_NO' // Locale used by the checkout
            );
        
        
        Util::Debug( $response );
        exit;
        
        
        return true;
        
        //Util::Debug($this->klarna);
        //exit;
        
        
    }
    
    
    private function Additems($cartArray){
        
        foreach( $cartArray['items'] as $type=>$item ){
           foreach( $item as $product ){
            
            if( is_array($cartArray['credits'] ) ){
                foreach( $cartArray['credits'] as $credit ){
                    if(  $credit['refid'] == $product['refid'] ){
                        if( $credit['quantity'] >= $product['quantity'] ){
                            $quantity = (int)$product['quantity'];
                        }else{
                            $quantity = (int)$credit['quantity'];
                        }
                        $klarnacart[] = array( $quantity, $product['prodno'], $credit['usertitle'], $product['unitprice'], $this->vat, 0 , KlarnaFlags::INC_VAT  );
                    }    
                }
            }
            switch ($type) {
              case 'goods':
              case 'prints':
              case 'enlargements':
                $klarnacart[] = array( (int)$product['quantity'], $product['prodno'], $product['product']['title'], $product['unitprice'], $this->vat, 0 , KlarnaFlags::INC_VAT  );
                break;
              case 'gifts':
              case 'mediaclip':
              case 'textgift':
              case 'merkelapp':
              case 'stempel':
                  foreach( $product as $ret ){
                    $klarnacart[] = array( (int)$ret['quantity'], $ret['prodno'], $ret['product']['title'], $ret['unitprice'], $this->vat, 0 , KlarnaFlags::INC_VAT  );
                    if( $ret['redeyeremoval'] ){
                        $klarnacart[] = array( (int)$ret['redeyeremoval']['quantity'],$ret['redeyeremoval']['prodno'], $ret['redeyeremoval']['product']['title'], $ret['redeyeremoval']['price'], $this->vat, 0 , KlarnaFlags::INC_VAT  );
                    }
                    if( $ret['upgrade'] ){
                        $klarnacart[] = array( (int)$ret['upgrade']['quantity'],$ret['upgrade']['prodno'], $ret['upgrade']['product']['title'], $ret['upgrade']['unitprice'], $this->vat, 0 , KlarnaFlags::INC_VAT  );
                    }
                    if( $ret['varnish'] ){
                        $klarnacart[] = array( (int)$ret['varnish']['quantity'],$ret['varnish']['prodno'], $ret['varnish']['product']['title'], $ret['varnish']['unitprice'], $this->vat, 0 , KlarnaFlags::INC_VAT  );
                    }
                    if( $ret['extrapages'] ){
                        $klarnacart[] = array( (int)$ret['extrapages']['quantity'],$ret['extrapages']['prodno'], $ret['extrapages']['product']['title'], $ret['extrapages']['unitprice'], $this->vat, 0 , KlarnaFlags::INC_VAT  );
                    }
                  }
            }  
           }
           if( $type == 'paperquality' || $type == 'correctionmethod' ){
                $klarnacart[] = array( (int)$item['quantity'], $item['prodno'], $item['product']['title'], $item['unitprice'], $this->vat, 0 , KlarnaFlags::INC_VAT  );
           }
             
        }
        
        if( count( $cartArray['discount']['final']  ) > 0  ){
            foreach( $cartArray['discount']['final'] as $final ){
                $klarnacart[] = array( (int)$final['quantity'], (string)$cartArray['discount']['info']['id'], $cartArray['discount']['info']['name'], -$final['unitdiscount'] , $this->vat, 0 , KlarnaFlags::INC_VAT  );
            }
        }

        return $klarnacart;
        
    }
    
    
    
    
    
}