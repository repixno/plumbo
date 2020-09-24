<?php

   /**
    * Class to fetch page
    *
    *
    */

   import( 'reedfoto.pages.json' );
   import( 'reedfoto.pagecomments' );

   class ReedFotoApiUserPageFetch extends JSONPage implements IValidatedView {

      public function Validate() {

         return array(
            'execute' => array(
               'post' => array(
                  'pageid' => VALIDATE_INTEGER,
               )
            )
         );
      }

      /**
       * Fetch page by pageid
       *
       * @api-name page.fetch
       * @api-javascript yes
       * @api-post pageid Integer Id of the page to fetch corrections for
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       * @api-result page Array Page
       */
      public function Execute() {

         $pageid = (int)$_POST[ 'pageid' ];
         
         $page = new RFPage( $pageid );
         
         $this->result = true;
         $this->message = 'OK';
         
         $this->page = $page->asArray();
         

      }

   }

?>