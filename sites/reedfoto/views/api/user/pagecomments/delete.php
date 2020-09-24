<?php

   /**
    * Delete a page comment
    *
    *
    */

   import( 'reedfoto.pages.json' );
   import( 'reedfoto.pagecomments' );

   class ReedFotoApiUserPageCommentsDelete extends JSONPage implements IValidatedView {

      public function Validate() {

         return array(
            'execute' => array(
               'post' => array(
                  'pagecommentid' => VALIDATE_INTEGER,
               )
            )
         );
      }

      /**
       * Delete a page comment
       *
       * @api-name user.pagecomments.delete
       * @api-javascript yes
       * @api-post pagecommentid Integer Id of page
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      public function Execute() {

         $pagecommentid = (int)$_POST[ 'pagecommentid' ];
         
         $pagecomment = new RFPageComment( $pagecommentid );
         $pagecomment->deleted = date( 'Y-m-d' );
         $pagecomment->deletedby = Login::userid();
         $pagecomment->save();

         $this->result = true;
         $this->message = 'OK';

      }

   }

?>
