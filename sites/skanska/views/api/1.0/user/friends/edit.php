<?php

   /**
    * Class create friend in users friend list
    *
    *
    */

   import( 'pages.json' );
   import( 'website.user' );
   import( 'website.user' );
   import( 'validate.email' );
   
   model( 'user.groups' );

   class APIUserFriendsEdit extends JSONPage implements IValidatedView {

      public function Validate() {

         return array(
            'execute' => array(
               'post' => array(
                  'friendid' => VALIDATE_INTEGER,
                  'email' => VALIDATE_STRING,
                  'name' => VALIDATE_STRING,
                  'cellphone' => VALIDATE_STRING
               )
            )
         );
      }
      /**
       * Create friend
       * 
       * @api-name user.friends.edit
       * @api-auth required
       * @api-post friendid Id of the friend
       * @api-post-optional email String E-mail
       * @api-post-optional name String Name
       * @api-post-optional cellphone String Cellphone
       * @api-result friendid String Provided friend id
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      public function Execute() {
         
         $friendid = $_POST['friendid'];

         $user = new User();
         
         if ( $user->editFriend( $friendid, $_POST ) ) {

            $this->result = true;
            $this->message = "OK";
         
         } else {
            
            $this->result = false;
            $this->message = "Failed";
         
         }

      }

   }


?>