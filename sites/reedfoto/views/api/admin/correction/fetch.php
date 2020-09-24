<?php

   /**
    * Class to fetch correction data.
    *
    * @author Svein Arild Bergset <sab@interweb.no>
    *
    */

   import( 'reedfoto.pages.json' );
   import( 'reedfoto.correction' );

   class ReedFotoApiAdminCorrectionFetch extends JSONPage implements IValidatedView {

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
       * Fetch correction data
       *
       * @api-name admin.correction.fetch
       * @api-javascript yes
       * @api-post id Integer Id of correction
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       * @api-result correction Array Properties of fetched correction
       */
      public function Execute() {

         $id = $_POST[ 'id' ];

         $correction = new RFCorrection( $id );

         $this->result = true;
         $this->message = 'OK';
         $this->correction = $correction->asArray();

      }

   }

?>
