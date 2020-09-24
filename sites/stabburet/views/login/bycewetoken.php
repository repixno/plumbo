<?PHP
   
   import( 'xtci.config');
   import( 'xtci.xtci');
   import( 'xtci.xtci_sso');
   
   class LoginByCEWEToken extends WebPage implements IView {
      
      protected $template = false;
      private $key = "933d5cda1015fb4baff36bfcbd873d53";
      private $iv = "00050002000000010101000000000003";
      
      public function Execute() {
         
        $code = isset( $_GET['ctoken'] ) ? $_GET['ctoken'] : $password;
        if( $code ){
            $decoded = $this->aes128decrypt( $code );
        }
        try {
            $XTCiForSSO = new XTCiForSSO();
            $response = $XTCiForSSO->set_xtci_session( $decoded );
            $userinfo =   $XTCiForSSO->xtci_userinfo();
            $logingroup = Dispatcher::getLoginGroup();
            $identifier = (string) $userinfo[0]['id'];
            $uid = DB::query( "SELECT b.uid FROM kunde k, brukar b WHERE b.uid = k.uid AND contactemail = ?  AND b.logingroup = ? AND  b.deleted is null ORDER BY uid desc LIMIT 1", $userinfo[0]['login'], $logingroup  )->fetchSingle();
            if( !empty( $uid ) ){
                $user = new DBUser( $uid );
            }
            if( !$user instanceof DBUser || !$user->isLoaded() ) {
                                          
               //if( preg_match("/[a-zA-Z0-9_-.+]+@[a-zA-Z0-9-]+.[a-zA-Z]+/", $identifier ) > 0 ){
               if( strlen ( $identifier ) > 3 ) {
                  
                  $user = new User();
                  $user->portal = Dispatcher::getPortal();
                  $user->logingroup = Dispatcher::getLoginGroup();
                  $user->username = $userinfo[0]['login'];
                  $user->fullname   = trim( trim( $userinfo[0]['firstname']  ).' '.$userinfo[0]['lastname'] );
                  $user->firstname  = $userinfo[0]['firstname'] ;
                  $user->lastname   = $userinfo[0]['lastname'];
                  $user->streetaddress = $userinfo[0]['street'];
                  $user->streetaddress2 = '';
                  $user->zipcode    = $userinfo[0]['zip'];
                  $user->city       = $userinfo[0]['city'];
                  $user->contactemail = $userinfo[0]['login'];
                  $user->cellphone  = $userinfo[0]['phone'];
                  $user->country = 160; // default to Norway 
                  // save the user
                  $user->save();
                  
               }
               else{
                  throw new Exception( 'Login failed. Invalid user!' );
               }
               
            }
            
            
            $user = new User( $user->uid );
            if( !$user instanceof User || !$user->isLoaded() ) {
               throw new Exception( 'Login failed. Local user not valid!' );
            }                  
            if( !Login::byUserObject( $user ) ) {
               throw new Exception( 'Login failed. Local user not valid!' );
            }
            
            $redirecturl  = Session::pipe( 'loginredirecturl' );
            
            if( !empty($redirecturl) ){
               relocate($redirecturl);
            }else{
               relocate( '/' );
            }
            
            
         
        }catch( Exception $e ){
            util::Debug( $e );
        }
        
      }
      
      private function aes128decrypt( $code ){
         
         //$key = hex2bin( $this->key );
         //$iv = hex2bin( $this->iv );
         
         $key = $this->hextobin( $this->key );
         $iv = $this->hextobin( $this->iv );
         
         $code = str_replace( '_', '/', $code );
         $code = str_replace( '-', '+', $code );
         $data = base64_decode( $code );
         $decoded = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key,  $data, MCRYPT_MODE_CBC , $iv);
         
         $padding = ord($decoded[strlen($decoded) - 1]); 
         $decoded =  substr($decoded, 0, -$padding);
         
         return $decoded;
      }
   
   
      private function hextobin($hexstr){
           $n = strlen($hexstr);
           $sbin="";  
           $i=0;
           while($i<$n)
           {      
               $a =substr($hexstr,$i,2);          
               $c = pack("H*",$a);
               if ($i==0){$sbin=$c;}
               else {$sbin.=$c;}
               $i+=2;
           }
           return $sbin;
       }
      
   }
   
?>