<?php

   /**
    * Class to remove page from correction.
    *
    * @author Svein Arild Bergset <sab@interweb.no>
    *
    */

   import( 'reedfoto.pages.json' );
   import( 'reedfoto.page' );

   class ReedFotoApiAdminPageRemove extends JSONPage implements IValidatedView {

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
       * Remove a page from a correction
       *
       * @api-name admin.page.remove
       * @api-javascript yes
       * @api-post id Integer Id of page to be removed
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      public function Execute() {

         $id = $_POST[ 'id' ];

         $page = new RFPage( $id );
         if ( $page->delete() ) {

            $this->result = true;
            $this->message = 'Page removed';

         } else {

            $this->result = false;
            $this->message = 'An error occured while removing page';

         }

      }

   }

?>
