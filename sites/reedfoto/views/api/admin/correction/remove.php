<?php

   /**
    * Class to remove correction.
    *
    * @author Svein Arild Bergset <sab@interweb.no>
    *
    */

   import( 'reedfoto.pages.json' );
   import( 'reedfoto.correction' );
   
   class ReedFotoApiAdminCorrectionRemove extends JSONPage implements IValidatedView {

      public function Validate() {

         return array(
            'execute' => array(
               'post' => array(
                  'id' => VALIDATE_INTEGER
               )
            )
         );
      }

      /**
       * Remove a correction
       *
       * @api-name admin.correction.remove
       * @api-javascript yes
       * @api-post id Integer Id of correction to be removed
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      public function Execute() {

         $id = $_POST[ 'id' ];

         $correction = new RFCorrection( $id );
         if ( $correction->delete() ) {

            $this->result = true;
            $this->message = 'OK';

         } else {

            $this->result = false;
            $this->message = 'An error occured while removing correction';

         }

      }

   }

?>
