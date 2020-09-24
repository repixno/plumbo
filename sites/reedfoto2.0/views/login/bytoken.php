<?PHP
   
   import( 'website.login.authkey' );
   import( 'website.login.authtoken' );
   
   class LoginByToken extends WebPage implements IView {
      
      protected $template = false;
      
      public function Execute( $authtoken = '', $returnurlbase64 = '' ) {
         
         try {
            
            $token = new AuthToken( $authtoken );
            if( $token instanceof AuthToken && $token->isLoaded() ) {
               
               if( $token->login ) {
                  throw new Exception( 'AuthToken already used', 403 );
               }
               
               $token->sessionid = Session::id();
               $token->login = 'now';
               $token->save();
               
               Login::byUserId( $token->userid );
               
               
               $logindata = Dispatcher::getCustomAttr('login');
               $returnurl = trim( @base64_decode( $returnurlbase64 ) );
               if( $returnurlbase64 && $returnurl ) {
                  
                  relocate( $returnurl );
                  
               } else if( isset( $logindata['bytoken'] ) ) {
                     
                  relocate( $logindata['bytoken'] );
                  
               } else {
                  
                  relocate( WebsiteHelper::rootBaseUrl() );
                  
               }
               
            }
            
         } catch( Exception $e ) {
            
            $returnurl = trim( @base64_decode( $returnurlbase64 ) );
            if( $returnurlbase64 && $returnurl ) {
               
               relocate( $returnurl );
               
            } else {
               
               relocate( WebsiteHelper::rootBaseUrl() );
               
            }
            
         }
         
      }
      
   }
   
?>