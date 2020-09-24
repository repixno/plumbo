<?php

   import( 'pages.xml' );
   import( 'math.uuid' );
   import( 'website.user' );
   import( 'website.login' );
   
   define( 'LOGINERROR', 11 );
   define( 'LOGINERRORMESSAGE', 'Invalid login' );
   
   define( 'EXCEPTIONERROR', 20 );
   
   /**
    * SSO validate Login
    *
    * @author Eivind Moland <eivind@eivind.biz>
    */
   
   class SingleSignOnValidate extends XMLPage implements IView {
      
      public function Execute() {

         try {
            
            $this->setRootNode( 'validate' );
            
            $this->resetFields();
            
            $xmlstring = file_get_contents('php://input');            
            $xml = simplexml_load_string( $xmlstring );
            
            $user = (string)$xml->login;
            $password = (string)$xml->pwd;
            $sessionid = (string)$xml->ocsid;
            
            if ( $this->login( $user, $password, $sessionid ) )  {
            
               $this->head = array(
                  'errorcode' => 0,
                  'errortext' => ''
               );
               
            } else {
               
               $this->head = array(
                  'errorcode' => LOGINERROR,
                  'errortext' => LOGINERRORMESSAGE
               );
               
            }
            
         } catch ( Exception $e ) {
            
            $this->head = array(
               'errorcode' => EXCEPTIONERROR,
               'errortext' => $e->getMessage()
            );
            
         }
         
      }
      
      private function login( $user, $password, $sessionid ) {
         
         $sessiondata = unserialize( sess_read( $sessionid ) );
         
         $keyaccountid = $sessiondata[ 'keyaccountid' ];
            
         if ( !isset( $keyaccountid ) || empty( $keyaccountid ) ) return false;
         
         try {
            
            // get portal by keyaccountid ?
            
            $portal = '';
            
            if ( Login::byPortalUsernameAndPassword( $portal, $user, $password ) ) {
               
               return true;
               
            } else {
            
               return false;
               
            }
            
         } catch ( Exception $e ) {
            
            return false;
            
         }
         
      }

   }
   
?>
