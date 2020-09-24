<?php

   /**
    * Class to remove a user
    *
    *
    */

   import( 'reedfoto.pages.json' );
   import( 'reedfoto.user' );

   class ReedFotoApiAdminUserRemove extends JSONPage implements IValidatedView {

      public function Validate() {

         return array(
            'execute' => array(
               'post' => array(
                  'userid' => VALIDATE_INTEGER
               )
            )
         );
      }

      /**
       * Remove a user
       *
       * @api-name admin.user.remove
       * @api-javascript yes
       * @api-post userid Integer Id of user to be removed
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      public function Execute() {

         $userid = $_POST[ 'userid' ];

         $user = new RFUser( $userid );
         if ( $user->delete() ) {

            $this->result = true;
            $this->message = 'OK';

         } else {

            $this->result = false;
            $this->message = 'An error occured while removing user';

         }

      }

   }

?>
