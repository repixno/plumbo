<?php

   /**
    * Class to fetch a user.
    *
    *
    */

   import( 'reedfoto.pages.json' );
   import( 'reedfoto.user' );

   class ReedFotoApiAdminUserFetch extends JSONPage implements IValidatedView {

      public function Validate() {

         return array(
            'execute' => array(
               'post' => array(
                  'userid' => VALIDATE_INTEGER,
               ),
               'fields' => array(
                  'userid' => VALIDATE_INTEGER,
               )
            )
         );
      }

      /**
       * Fetch user
       *
       * @api-name admin.correction.fetch
       * @api-javascript yes
       * @api-post-optional userid Integer User ID
       * @api-param-optional userid Integer User ID
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       * @api-result user Array user array
       */
      public function Execute( $userid = 0 ) {
         
         $userid = $_POST[ 'userid' ] ? $_POST[ 'userid' ] : $userid;
         
         $user = new RFUser( $userid );

         $this->user = $user->asArray();

         $this->result = true;
         $this->message = 'OK';
         
      }

   }
   
?>