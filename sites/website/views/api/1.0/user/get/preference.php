<?php

   /**
    * API for getting user preferences
    *
    */

   import( 'pages.json' );
   import( 'website.user' );
   import( 'cache.memcache' );

   class APIUserGetPreference extends JSONPage implements IValidatedView {
      
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
       * Add key/value to user preference
       * 
       * @api-name user.get.preference
       * @api-auth required
       * @api-post-optional key String Key
       * @api-get-optional key String Key
       * @api-result key String Key
       * @api-result value String Value
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      
      public function Execute() {
         
         $key = $_POST['key'] ? $_POST['key'] : $_GET['key'];
         
         try {
            
            $user = new User( Login::userid() );

            $this->key = $key;
            $this->value = $user->getPreference( $key );
            
            $this->result = true;
            $this->message = 'OK';
         
         } catch ( Exception $e ) {
            
            $this->result = false;
            $this->message = 'Failed';
            
         }
        
      }
   }
   
?>