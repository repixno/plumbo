<?php

   /**
    * Class to fetch pagecomments.
    *
    *
    */

   import( 'reedfoto.pages.json' );
   import( 'reedfoto.pagecomments' );

   class ReedFotoApiUserPageCommentsFetch extends JSONPage implements IValidatedView {

      public function Validate() {

         return array(
            'execute' => array(
               'post' => array(
                  'pageid' => VALIDATE_INTEGER,
                  'pagecommentid' => VALIDATE_INTEGER
               )
            )
         );
      }

      /**
       * Fetch page comments by pageid
       *
       * @api-name pagecomments.fetch
       * @api-javascript yes
       * @api-post pageid Integer Id of the page to fetch corrections for
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       * @api-result comments Array Page comments
       */
      public function Execute() {

         $pageid = (int)$_POST[ 'pageid' ];
         
         $pagecommentid = (int)$_POST[ 'pagecommentid' ];
         
         if ( $pagecommentid <= 0 ) {
            
            $comments = array();
   
            foreach ( RFPageComment::enum( $pageid ) as $comment ) {
               $comments[] = $comment->asArray();
            }

         } else {
            
            $comment = new RFPageComment($pagecommentid);
            
            $comments[] = $comment->asArray();
            
         }
         
         $this->result = true;
         $this->message = 'OK';
         
         $this->comments = $comments;
         

      }

   }

?>