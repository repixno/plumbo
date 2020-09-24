<?php

   import( 'pages.xml' );
   import( 'math.uuid' );

   define( 'SESSIONERROR', 1 );
   define( 'SESSIONERRORMESSAGE', 'Session could not be created' );
   
   define( 'EXCEPTIONERROR', 20 );
   
   /**
    * SSO Session
    *
    * @author Eivind Moland <eivind@eivind.biz>
    */
   
   class SingleSignOnSession extends XMLPage implements IView {
      
      private $validto = '';
      
      public function Execute() {
         
         $this->setRootNode( 'session' );
         
         $this->resetFields();

         // mandatory blank fields if anything failes
         
         $this->content = array(
            'ocsid' => '',
            'valid_until' => '',
            'server_protocol' => '',
            'server_version' => ''
         );
         
         try {

            $xmlstring = file_get_contents('php://input');
            
            $xml = simplexml_load_string( $xmlstring );
            
            $keyaccountid = (int)$xml->keyacc_id;
            
            // unused variables
            $clientname = (string)$xml->client_name;
            $clientversion = (string)$xml->client_version;
            $serverprotocol = (string)$xml->server_protocol;
            
            $sessionid = $this->generateSession( $keyaccountid );
            
            if ( !empty( $sessionid ) && ( !empty( $keyaccountid ) ) ) {
         
               $this->content = array(
                  'ocsid' => $sessionid,
                  'valid_until' => $this->validto,
                  'server_protocol' => '',
                  'server_version' => ''
               );
               
               $this->head = array(
                  'errorcode' => 0,
                  'errortext' => ''
               );
               
            } else {
               
               $this->head = array(
                  'errorcode' => SESSIONERROR,
                  'errortext' => SESSIONERRORMESSAGE
               );
               
            }
            
         } catch ( Exception $e ) {
            
            $this->head = array(
               'errorcode' => EXCEPTIONERROR,
               'errortext' => $e->getMessage()
            );
            
         }
         
      }
      
      public function generateSession( $keyaccountid ) {
         
         $uuid = uuid::create();
         
         $sessiondata = array( 'keyaccountid' => $keyaccountid );
         sess_write( $uuid, serialize( $sessiondata ) );
         
         $validto = $GLOBALS[ 'sessionlifetime' ] + time() ;
         $this->validto = date( 'YmdHis', $validto ) ;
         
         return $uuid;
         
      }
        
   }
   
?>
