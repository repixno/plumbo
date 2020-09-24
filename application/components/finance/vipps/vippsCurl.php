<?php

model( 'finance.vipps' );

class VippsCurl{
    
    /*TEST
    private $url = "https://apitest.vipps.no/";
    private $client_id = "c27eb103-4c49-4422-b7d3-4e8254c47c48";
    private $client_secret = "WXRVNEY0MlgzTklaTFREaUlBaHI=";
    private $Ocp = "ff02ae6775f443acbfe7a2bcec5ba8ad";
    private  $serialnumber = 211851;
    */
    
    private $url = "https://api.vipps.no/";
    private $client_id = "963c5585-cd8d-479f-a5a2-e931d055ff5f";
    private $client_secret = "MllnQk9pOXNrRjhzYmJiMFRzSGs=";
    private $Ocp = "69094916dfa54b24ba9c29e692b22430";
    private  $serialnumber = 543361;
    
    private $token;
    private $server = '';
    
    public function __construct() {
        
        $this->server = $parameters['server'];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,  $this->url . "accessToken/get");
        curl_setopt($ch, CURLOPT_POST, 1);// set post data to true
        curl_setopt($ch, CURLOPT_POSTFIELDS,"");   // post data
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $headers = [
            'client_id:' . $this->client_id,
            'client_secret:' . $this->client_secret,
            'Ocp-Apim-Subscription-Key:' .$this->Ocp
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $json = curl_exec($ch);
        $jsonarray = json_decode( $json );
        $this->token = $jsonarray->access_token;
        curl_close ($ch);
    }
    

    public function payments( $cart ){
        
        
        
        //lage databasetabell for å logge vipps
        //$orderid = 'merk' . rand(0, 99999);
        
        $uniqid  = md5( uniqid() );
        
        $vipps = new DBVippsTransaction();
        $vipps->vippsid = $uniqid;
        $vipps->amount = $cart['totalprice'] * 100;
        $vipps->merchantid = $this->serialnumber;
        $vipps->status = "new"; 
        $vipps->save();
        
        $vippsid = DB::query( "SELECT max(id) FROM vipps_transaction"  )->fetchSingle();
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,  $this->url . "/ecomm/v2/payments/");
        curl_setopt($ch, CURLOPT_POST, 1);// set post data to true
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $headers = [
            'Authorization: Bearer ' . $this->token, 
            'Content-Type:application/json',
            'Ocp-Apim-Subscription-Key:' .$this->Ocp,
        ];
        
        //TESTBELØP
        //$cart['totalprice'] = 2;
        
        $request = array(
            "customerInfo" => array( "mobileNumber" =>  null ),
            "merchantInfo" =>  array( 
                "authToken"=> $uniqid,
                "callbackPrefix"=> "https://dinmerkelapp.no/kasse/vippscallback",
                "consentRemovalPrefix"=> "https://dinmerkelapp.no/vipps",
                "fallBack"=> "https://dinmerkelapp.no/kasse/fallback-order-result-page/" . $vippsid,
                "isApp"=> false,
                "merchantSerialNumber"=> $this->serialnumber,
                "shippingDetailsPrefix" => true,
                "paymentType" => "eComm Express Payment",
                "shippingDetailsPrefix"=>  "https://dinmerkelapp.no/kasse/vippsshipping",
                "staticShippingDetails" => array(
                    array( 
                        "isDefault"=> "Y",
                        "priority"=> 0,
                        "shippingCost"=> 0,
                        "shippingMethod"=> "A-Post",
                        "shippingMethodId"=> "a-post"
                    )
                  
                )
              
            ),
            "transaction"=> array(
                "amount"=> $cart['totalprice'] * 100,
                "orderId"=> $vippsid,
                "skipLandingPage" =>false,
                "transactionText"=> "Merkelappordre"
            )
        );
         
        $payload = json_encode( $request );
  
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $json = curl_exec($ch);
        $jsonarray = json_decode( $json );
        curl_close ($ch);
        
        return $jsonarray->url;
        
    }
    
    
    public function details( $orderid ){
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,  $this->url . "/ecomm/v2/payments/$orderid/details");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $headers = [
            'Authorization: Bearer ' . $this->token, 
            'Content-Type:application/json',
            'Ocp-Apim-Subscription-Key:' .$this->Ocp,
        ];
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $json = curl_exec($ch);
        $jsonarray = json_decode( $json );
        curl_close ($ch);
        
        return $jsonarray;
    }
    
    
}
