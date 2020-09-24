<?php

   /**
    * API for removing user preferences
    *
    */

   import( 'pages.json' );
   import( 'website.user' );
   import( 'cache.memcache' );

   class APIUserRemovePreference extends JSONPage implements IView {
      
      /**
       * Validator
       *
       * @return Array
       */

      public function Validate() {

         return array(
            'execute' => array(
               'post' => array(
                  'key' => VALIDATE_STRING
               ),
               'get' => array(
                  'key' => VALIDATE_STRING
               )
            )
         );

      }
      
      /**
       * Remove key from user preference
       * 
       * @api-name user.remove.preference
       * @api-auth required
       * @api-post-optional key String Key
       * @api-get-optional key String Key
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      
      public function Execute() {
         
         $key = $_POST['key'] ? $_POST['key'] : $_GET['key'];

         
         try {
            
            $user = new User( Login::userid() );
            $user->removePreference( $key );
            
            $this->result = true;
            $this->message = 'OK';
         
         } catch ( Exception $e ) {
            
            $this->result = false;
            $this->message = 'Failed';
            
         }
        
      }
   }
   
?>