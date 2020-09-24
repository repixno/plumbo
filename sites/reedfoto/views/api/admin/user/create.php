<?php

   /**
    * Class to create new user.
    *
    *
    */

   import( 'reedfoto.pages.json' );
   import( 'reedfoto.user' );

   class ReedFotoApiAdminUserCreate extends JSONPage implements IValidatedView {

      public function Validate() {

         return array(
            'execute' => array(
               'post' => array(
                  'type' => VALIDATE_STRING,
                  'username' => VALIDATE_STRING,
                  'password' => VALIDATE_STRING,
                  'fullname' => VALIDATE_STRING
               )
            )
         );
      }

      /**
       * Create a new user
       *
       * @api-name admin.correction.create
       * @api-javascript yes
       * @api-post type String 'admin' or 'user'
       * @api-post username String Username
       * @api-post password String Password
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       * @api-result userid Integer ID of the new user
       */
      public function Execute() {

         $type = $_POST[ 'type' ];
         $username = $_POST[ 'username' ];
         $password = $_POST[ 'password' ];
         $fullname = $_POST[ 'fullname' ];
         
         $user = new RFUser();
         $user->type = $type;
         $user->password = md5( $password );
         $user->username = $username;
         $user->fullname = $fullname;
         $user->save();

         $this->result = true;
         $this->message = 'OK';
         
         $this->userid = $user->id;

      }

   }

?>
