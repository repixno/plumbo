<?php

   library( 'jcryption.jcryption' );
   
   import( 'pages.json' );
   import( 'website.friendimport' );
   
   class ApiFriendsImport extends JSONPage implements IValidatedView {
      
      /**
       * Validator
       *
       * @return Array
       */

      public function Validate() {

         return array(
            'execute' => array(
               'post' => array(
                  'portal' => VALIDATE_STRING,
                  'user' => VALIDATE_STRING,
                  'password' => VALIDATE_STRING,
                  'encryptedform' => VALIDATE_STRING
               )
            )
         );
      }
      
      /**
       * Execute
       *
       * @api-name user.friends.import
       * @api-auth required
       * @api-post-optional portal String Portal
       * @api-post-optional user String User
       * @api-post-optional password String password
       * @api-post-optional encryptedform jCryption encrypted string containing an array with portal, user and password
       * @api-result contacts Array list of contacts (id, name, cellphone, email)
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      
      public function Execute( ) {
         
         try {
            
            $encryptedform = $_POST['encryptedform'];
            
            if ( !empty( $encryptedform ) ) { 
            
               $jcryption = new jCryption();
               
               $decryptedstring = $jcryption->decrypt( $encryptedform, $_SESSION['key_d']['int'], $_SESSION['key_n']['int']);
               
               parse_str( $decryptedstring, $fields );

               $user = $fields['user'];
               $password = $fields['password'];
               $portal = $fields['portal'];
                           
            	unset( $_SESSION['key_e'] );
            	unset( $_SESSION['key_d'] );
            	unset( $_SESSION['key_n'] );

            } else {
               
               $user = $_POST['user'];
               $password = $_POST['password'];
               $portal = $_POST['portal'];
               
            }
            
            $import = new FriendImport( $user, $password, $portal );
            
            if ( $import->authenticated ) {
               
               $this->contacts = $import->getContacts();
               
               $this->message = 'OK';
               
               $this->result = true;
               
            } else {
               
               $this->message = 'Login failed';
               
               $this->result = true;
            
            }            
         
         } catch ( Exception $e ) {
            
            $this->message = $e->getMessage();
            
            $this->result = false;
         }

      }
      
   }
   
?>