<?
   library( 'openinviter.openinviter' );

   class FriendImport {
      
      var $import = null;
      var $error = '';
      var $portal = '';
      var $rawdata = null;
      
      var $authenticated = false;
      
      /**
       * Auth to portal
       *
       * @param String $login
       * @param String $user
       * @param String $portal
       * 
       * @return Boolean Success, true or false
       */
      
      function __construct( $user = '', $password = '', $portal = '' ) {
         
         $this->portal = $portal;
         
         $this->import = new OpenInviter( );
         
         if ( $this->import->startPlugin( $portal ) ) {
   
            if ( $this->import->login( $user, $password ) ) {
               
               $this->authenticated = true;
               
               return true;
            
            } else {
               
               $this->error = $this->import->getInternalError();
               
               throw new Exception ( 'Wrong user or password' );
               
            }
         } else {
            
            $this->error = $this->import->getInternalError();
            
            throw new Exception ( $this->error );
            
         }
         
      }
      
      /**
       * Get contacts
       *
       * @param Array $filter
       * 
       * @return Array of fields (id, name, email, cellphone)
       */
      
      function getContacts( $filter = array() ) {

         $contacts = array();
            
         $hits = $this->import->getMyContacts();
         
         $this->rawdata = $hits;
         
         if ( count($hits) > 0 ){
                           
            foreach ( $hits as $key => $hit ) {
               
               $contact = array();
            
               $contact['id'] = $key ? $key : md5( $hit );
                     
               switch ( $this->portal ) {
                  
                  case 'facebook':
                     
                     $contact['name'] = utf8_decode( $hit );
                     
                     break;
                  
                  case 'hotmail':
                     
                     $contact['name'] = utf8_decode( $hit );
                     $contact['email'] = utf8_decode( $key );
                     
                     break;
               
                  case 'yahoo':
                     
                     $contact['name'] = utf8_decode( $hit );
                     $contact['email'] = utf8_decode( $key );
                     
                     break;
                  
                  case 'gmail':
                     
                     $contact['name'] = utf8_decode( $hit );
                     $contact['email'] = utf8_decode( $key );

                  case 'msn':
                     
                     $contact['name'] = utf8_decode( $hit );
                     $contact['email'] = utf8_decode( $key );
                     
                     break;
                          
                  default:
                     break;
                     
               }
               
               $contacts[] = $contact;
               
            }
                        
         } else {
            
            $this->error = $import->getInternalError();
         
            return array();
         
         }
            
         return $contacts;

      }
      
   }

?>