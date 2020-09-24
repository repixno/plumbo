<?PHP


   import( 'pages.json' );
   import( 'website.order.merkelapptip' );

   class AddTip extends JSONPage implements NoAuthRequired, IView {
      
      private $campaigncode = "asdasdasdasdasdas";
      

      public function Execute( $email = '' ) {
         
         if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            exit("E-mail is not valid");
         }
         util::Debug( $this->check_email_address( $email ) ) ;
         
         die();
         
         
         if( empty( $email ) ){
            
            $email = $_POST['email'];
            
         }

         $userid = Login::userid();
         
         
         $tip = new UserMerkelappTip();
                  
         try{
            $tip->checkFriend( $email );
         }catch ( Exception $e ){
            util::Debug( $e->getMessage() );
         }
         
         if( $tip->checkFriend( $email ) ){
            $this->result = true;
            $this->friend = $friend;
            $this->message = 'Exists';
            return false;
         }
         else{
            $tip->friends_email = $email;
            $tip->date_tipped = date( 'Y-m-d H:i:s' );
            $tip->campaign_code = $this->campaigncode;
            $tip->save();
         }
 
         $this->result = true;
         $this->projects = $friend;
         $this->message = 'OK';
         
         return true;
         

         }
         
         
         private function check_email_address($email) {
           // First, we check that there's one @ symbol, 
           // and that the lengths are right.
           if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
             // Email invalid because wrong number of characters 
             // in one section or wrong number of @ symbols.
             return false;
           }
           // Split it into sections to make life easier
           $email_array = explode ("@" , $email);
           $local_array = explode( "." , $email_array[0] );
           for ( $i = 0; $i < sizeof($local_array); $i++ ) {
              if( !ereg( "^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$",$local_array[$i] ) ) {
                  return false;
                }
           }
           // Check if domain is IP. If not, 
           // it should be valid domain name
           if ( !ereg("^\[?[0-9\.]+\]?$", $email_array[1]) ) {
             $domain_array = explode( ".", $email_array[1] );
             if ( sizeof($domain_array) < 2 ) {
                 return false; // Not enough parts to domain
             }
             for ( $i = 0; $i < sizeof($domain_array); $i++ ) {
               if( !ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
                 return false;
               }
             }
           }
           return true;
         }


         
   }



?>
