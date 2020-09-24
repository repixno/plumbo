<?php

   /**
    * API for setting user preferences
    *
    */

   import( 'pages.json' );
   import( 'website.user' );
   import( 'cache.memcache' );

   class APIUserSetPreference extends JSONPage implements IValidatedView {
      
      /**
       * Validator
       *
       * @return Array
       */

      public function Validate() {

         return array(
            'execute' => array(
               'post' => array(
                  'key' => VALIDATE_STRING,
                  'value' => VALIDATE_STRING
               ),
               'get' => array(
                  'key' => VALIDATE_STRING,
                  'value' => VALIDATE_STRING
               )
            )
         );

      }
      
      /**
       * Add key/value to user preference
       * 
       * @api-name user.set.preference
       * @api-auth required
       * @api-post-optional key String Key
       * @api-post-optional value String Value
       * @api-get-optional key String Key
       * @api-get-optional value String Value
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      
      public function Execute() {
         
         $key = $_POST['key'] ? $_POST['key'] : $_GET['key'];
         $value = $_POST['value'] ? $_POST['value'] : $_GET['value'];
         
         try {
            
            $user = new User( Login::userid() );
            $user->setPreference( $key, $value );
            
            $this->result = true;
            $this->message = 'OK';
         
         } catch ( Exception $e ) {
            
            $this->result = false;
            $this->message = 'Failed';
            
         }
        
      }
   }
   
?>