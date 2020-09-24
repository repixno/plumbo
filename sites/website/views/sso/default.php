<?php

   import( 'pages.xml' );
   
   /**
    * SSO Default
    *
    * @author Eivind Moland <eivind@eivind.biz>
    */

   class SingleSignOnDefault extends XmlPage implements IView {
      
      public function Execute() {
         
         try {
            
            $this->resetFields();
         
            $xmlstring = file_get_contents('php://input');
               
            if ( !empty( $xmlstring ) ) {
               
               $xml = simplexml_load_string( $xmlstring );
               
               switch( $xml->getName() ) {
                  
                  case 'session_request':
                     
                     $this->setRootNode( 'session' );
                     
                     $this->quickRoute( '/sso/session', 'SingleSignOnSession' );
                     
                     break;
      
                  case 'login_request':
                     
                     $this->setRootNode( 'login' );
                     
                     $this->quickRoute( '/sso/login', 'SingleSignOnLogin' );
                     
                     break;

                  case 'validate_request':
                     
                     $this->setRootNode( 'validate' );
                     
                     $this->quickRoute( '/sso/validate', 'SingleSignOnValidate' );
                     
                     break;
                     
                  case 'useradd_request':
                     
                     $this->setRootNode( 'useradd' );
                     
                     $this->quickRoute( '/sso/useradd', 'SingleSignOnUserAdd' );
                     
                     break;
                     
                  case 'userchange_request':
                     
                     $this->setRootNode( 'userchange' );
                     
                     $this->quickRoute( '/sso/userchange', 'SingleSignOnUserChange' );
                     
                     break;
                     
                  case 'userinfo_request':
                     
                     $this->setRootNode( 'userinfo' );
                     
                     $this->quickRoute( '/sso/userinfo', 'SingleSignOnUserInfo' );
                     
                     break;
                     
                  default:
                     break;
                  
               }
            } else {
               
               die( 'Missing XML.' );
               
            }
         } catch ( Exception $e ) {
            
            die( 'Error reading XML.'. $e);
            
         }
         
      }
      
   }
   
?>
