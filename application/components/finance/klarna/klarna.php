<?PHP
library( 'Klarna.Checkout' );
config( 'finance.klarna' );

Class KlarnaEF{
    
    
    private $connector;
    public $order;
    public $snippet;
    private $eid = '';
    private $sharedSecret = '';
    private $server = '';
    
    public function __construct() {
        
        //$parameters = Settings::Get( 'finance', 'testklarna' );
        $parameters = Settings::Get( 'finance', 'klarna' );
        
        //if( $_GET['live'] ){
        $this->eid = $parameters['eid'];
        $this->sharedSecret = $parameters['sharedSecret'];
        $this->server = $parameters['server'];
        //}
        if( $_GET['flush'] !== null ){
            unset($_SESSION['klarna_checkout']);
        }
        Klarna_Checkout_Order::$baseUri = 'https://' . $this->server . '/checkout/orders';
        Klarna_Checkout_Order::$contentType = "application/vnd.klarna.checkout.aggregated-order-v2+json";
               
        $this->connector = Klarna_Checkout_Connector::create( $this->sharedSecret );
        
    }
    
    public function register( $cart, $delivery = null ){

        $cartArray = $cart->asArray();
        $ksarray = explode( '/', $_SESSION['klarna_checkout'] );    
        //unset($_SESSION['klarna_checkout']);
        
        if( end( $ksarray  ) != $cartArray['klarnaid'] ){
            unset($_SESSION['klarna_checkout']);
        }
        
        if( $_SESSION['klarna_checkout'] ){
            $this->order = new Klarna_Checkout_Order( $this->connector, $_SESSION['klarna_checkout'] );
            $this->update($cart);
        }else{
            $this->order = new Klarna_Checkout_Order( $this->connector );
            $user = new User( Login::userid() );
            $userArray =  $user->asArray();
            $klarnacart = array();
            $klarnacart = $this->Additems($cartArray);
            
            $deliveryprice = (int)str_replace('.', '', $cartArray['deliverytype']['price'] );
            
            if( !$deliveryprice ){
                $deliveryprice = $delivery['price'];
                $deliverytitle = $delivery['title'];
                
                if( $deliveryprice == 0 ){
                    $deliveryprice = 0000;
                }
                
                $klarnacart[] = array(
                    'type' => 'shipping_fee',
                    'reference' => 'SHIPPING',
                    'name' => $deliverytitle,
                    'quantity' => 1,
                    'unit_price' => $deliveryprice,
                    'tax_rate' => 2500
                );
                
            }else{
                
                if( $deliveryprice == 0 ){
                    $deliveryprice = 0000;
                }
                
                $deliverytitle = $cartArray['deliverytype']['title'];
                $klarnacart[] = array(
                    'type' => 'shipping_fee',
                    'reference' => 'SHIPPING',
                    'name' => $deliverytitle,
                    'quantity' => 1,
                    'unit_price' => $deliveryprice,
                    'tax_rate' => 2500
                );
                
            }
            
            
            
            $site = 'http://' . $_SERVER[HTTP_HOST];
            
            
            $sessionid = $user;
            
            $create['purchase_country'] = 'NO';
            $create['purchase_currency'] = 'NOK';
            $create['locale'] = 'nb-no';
            $create['merchant']['id'] = $this->eid;
            $create['merchant']['terms_uri'] = $site . '/hjelp/sluttbrukervilkar/';
            $create['merchant']['checkout_uri'] = $site . '/kasse/';
            $create['merchant']['confirmation_uri'] = $site . '/kasse/execute';
            $create['merchant']['push_uri'] = $site .'/kasse/push?klarna_order={checkout.order.uri}';
            
            if($userArray['zipcode'] ){
                $create['shipping_address']['postal_code'] = $userArray['zipcode'] ;
            }
            if($userArray['firstname'] ){
                $create['shipping_address']['given_name'] = $userArray['firstname'] ;
            }
            if($userArray['lastname'] ){
                $create['shipping_address']['family_name'] = $userArray['lastname'] ;
            }
            if($userArray['address'] ){
                $create['shipping_address']['street_address'] = $userArray['address'] ;
            }
            
            $create['shipping_address']['email'] = $userArray['email'];
            foreach ($klarnacart as $item) {
                $create['cart']['items'][] = $item;
            }
            
            $create['options']['allow_separate_shipping_address'] = true;
            //$create['options']['shipping_details'] = "true";
            
            $this->order->create($create);
            $this->order->fetch();
            
            $cart->setKlarnaid( $this->order['id'] );
            $cart->save();
            
           }
           
           
        $_SESSION['klarna_checkout'] = $sessionId = $this->order->getLocation();
        $this->snippet = $this->order['gui']['snippet'];
        
    }
    
    
    public function fetch( $checkoutId = null ){
        
        $checkoutId = $checkoutId ? $checkoutId : $_SESSION['klarna_checkout'];
        
        $this->order = new Klarna_Checkout_Order( $this->connector, $checkoutId);
        $this->order->fetch();
        
        $this->snippet = $this->order['gui']['snippet'];
    }
    
    public function update( $cart ){
        
        $checkoutId = $_SESSION['klarna_checkout'];
        $this->order = new Klarna_Checkout_Order($this->connector, $checkoutId);
        $this->order->fetch();
        
        $cartArray = $cart->asArray();
        $klarnacart = array();
        
        $klarnacart = $this->Additems($cartArray );
        
        $deliveryprice = (int)str_replace('.', '', $cartArray['deliverytype']['price'] );
        
        $klarnacart[] = array(
                'type' => 'shipping_fee',
                'reference' => 'SHIPPING',
                'name' => $cartArray['deliverytype']['title'],
                'quantity' => 1,
                'unit_price' => $deliveryprice,
                'tax_rate' => 2500
            );
        // Reset cart
        $update = array();
        $update['cart']['items'] = array();
        
        foreach ($klarnacart as $item) {
            $update['cart']['items'][] = $item;
        }
        
        $this->order->update($update);
        
    }
    
    
    public function setOrderid( $orderid ){   
        $update['cart']['merchant_reference']['orderid1'] = $orderid;
        $this->order->update( $update );
    }
    
    
    private function Additems($cartArray){
        
        foreach( $cartArray['items'] as $type=>$item ){
           foreach( $item as $product ){
            
            if( is_array($cartArray['credits'] ) ){
                foreach( $cartArray['credits'] as $credit ){
		    if( !isset(  $product['refid'] ) ) continue;
                    if(  $credit['refid'] == $product['refid'] ){
                        
                        if( $credit['quantity'] >= $product['quantity'] ){
                            $quantity = (int)$product['quantity'];
                        }else{
                            $quantity = (int)$credit['quantity'];
                        }
                        
                        $credit['usertitle'] = $credit['usertitle']?$credit['usertitle']:"Credit";
                        
                        $klarnacart[] = array(
                            'reference' => $product['prodno'],
                            'name' => $credit['usertitle'],
                            'quantity' => $quantity,
                            'unit_price' => -( $product['unitprice'] * 100 ),
                            'discount_rate' => 0,
                            'tax_rate' => 2500
                        );
                    }    
                }
            }
            switch ($type) {
              case 'goods':
              case 'prints':
              case 'enlargements':
              case 'subscription':
                  $klarnacart[] = array(
                      'reference' => $product['prodno'],
                      'name' => $product['product']['title'],
                      'quantity' => (int)$product['quantity'],
                      'unit_price' => $product['unitprice'] * 100,
                      'discount_rate' => 0,
                      'tax_rate' => 2500
                  );
                  if( $product['license'] ){
                        foreach( $product['license']  as $license ){
                            $klarnacart[] = array(
                                  'reference' => 'license_' . $license['product']['id'] ,
                                  'name' => $license['product']['title'],
                                  'quantity' => (int)$license['quantity'],
                                  'unit_price' => $license['unitfee'] * 100,
                                  'tax_rate' => 2500
                            );
                        }
                    } 
                  break;
              case 'gifts':
              case 'mediaclip':
              case 'textgift':
              case 'module':
              case 'merkelapp':
              case 'stempel':
              case 'digital':
              case 'ukeplan':
                  foreach( $product as $ret ){
                      $klarnacart[] = array(
                              'reference' => $ret['prodno'],
                              'name' => $ret['product']['title'],
                              'quantity' => (int)$ret['quantity'],
                              'unit_price' => $ret['unitprice'] * 100,
                              'tax_rate' => 2500
                        );
                      
                      
                    if( $ret['redeyeremoval'] ){
                        $klarnacart[] = array(
                              'reference' => $ret['redeyeremoval']['prodno'],
                              'name' => $ret['redeyeremoval']['product']['title'],
                              'quantity' => (int)$ret['redeyeremoval']['quantity'],
                              'unit_price' => $ret['redeyeremoval']['price'] * 100,
                              'tax_rate' => 2500
                        ); 
                    }
                    if( $ret['upgrade'] ){
                        $klarnacart[] = array(
                              'reference' => $ret['upgrade']['prodno'],
                              'name' => $ret['upgrade']['product']['title'],
                              'quantity' => (int)$ret['upgrade']['quantity'],
                              'unit_price' => $ret['upgrade']['unitprice'] * 100,
                              'tax_rate' => 2500
                        ); 
                    }
                    if( $ret['varnish'] ){
                        $klarnacart[] = array(
                              'reference' => $ret['varnish']['prodno'],
                              'name' => $ret['varnish']['product']['title'],
                              'quantity' => (int)$ret['varnish']['quantity'],
                              'unit_price' => $ret['varnish']['unitprice'] * 100,
                              'tax_rate' => 2500
                        ); 
                    }
                    if( $ret['extrapages'] ){
                        $klarnacart[] = array(
                              'reference' => $ret['extrapages']['prodno'],
                              'name' => $ret['extrapages']['product']['title'],
                              'quantity' => (int)$ret['extrapages']['quantity'],
                              'unit_price' => $ret['extrapages']['unitprice'] * 100,
                              'tax_rate' => 2500
                        ); 
                    }
                    if( $ret['license'] ){
                        foreach( $ret['license']  as $license ){
                            $klarnacart[] = array(
                                  'reference' => 'license_' . $license['product']['id'] ,
                                  'name' => $license['product']['title'],
                                  'quantity' => (int)$license['quantity'],
                                  'unit_price' => $license['unitfee'] * 100,
                                  'tax_rate' => 2500
                            );
                        }
                    } 
                  }
            }

           }
           if( $type == 'paperquality' || $type == 'correctionmethod' || $type == 'productionmethod' ){
            
                if( $item['unitprice'] > 0 ){
                    $klarnacart[] = array(
                            'reference' => $item['prodno'],
                            'name' => $item['product']['title'],
                            'quantity' => (int)$item['quantity'],
                            'unit_price' => $item['unitprice'] * 100,
                            'discount_rate' => 0,
                            'tax_rate' => 2500
                        );
                }
           }
           
           
        }

        
        
        
        
        if( count( $cartArray['discount']['final']  ) > 0  ){
            
            
            foreach( $cartArray['discount']['final'] as $final ){
                
                $klarnacart[] = array(
                    'reference' => (string)$cartArray['discount']['info']['id'],
                    'name' =>$cartArray['discount']['info']['name'],
                    'quantity' => (int)$final['quantity'],
                    'unit_price' => -$final['unitdiscount'] * 100,
                    'discount_rate' => 0,
                    'tax_rate' => 2500
                );
            }
            
            
        }

        return $klarnacart;
        
    }
}












?>
