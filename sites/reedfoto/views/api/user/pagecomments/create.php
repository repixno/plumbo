<?php

   /**
    * Class to create new pagecomment.
    *
    *
    */

   import( 'reedfoto.pages.json' );
   import( 'reedfoto.pagecomments' );

   class ReedFotoApiUserPageCommentsCreate extends JSONPage implements IValidatedView {

      public function Validate() {

         return array(
            'execute' => array(
               'post' => array(
                  'pageid' => VALIDATE_INTEGER,
                  'type' => VALIDATE_STRING,
                  'comment' => VALIDATE_STRING,
                  'filehash' => VALIDATE_STRING,
                  'filetype' => VALIDATE_STRING,
                  'filesize' => VALIDATE_INTEGER,
               )
            )
         );
      }

      /**
       * Create a new comment for a page
       *
       * @api-name user.pagecomments.create
       * @api-javascript yes
       * @api-post pageid Integer Id of page
       * @api-post type String 'file' or 'comment'
       * @api-post filehash String Hash of the uploaded file
       * @api-post filetype String Type of the uploaded file
       * @api-post filesize Integer Size of the uploaded file
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      public function Execute() {

         $pageid = $_POST[ 'pageid' ];
         $type = $_POST[ 'type' ];
         $comment = $_POST[ 'comment' ];
         
         if ( $type == 'file' ) {
            
            $filehash = $_POST[ 'filehash' ];
            $filetype = $_POST[ 'filetype' ];
            $filesize = $_POST[ 'filesize' ];
         } else {
            $filehash = null;
            $filetype = null;
            $filesize = null;
         }
         
         $pagecomment = new RFPageComment();
         $pagecomment->pageid = $pageid;
         $pagecomment->comment = $comment;
         $pagecomment->filehash = $filehash;
         $pagecomment->filetype = $filetype;
         $pagecomment->filesize = $filesize;
         $pagecomment->created = date( 'Y-m-d' );
         $pagecomment->createdby = Login::userid();
         $pagecomment->save();

         $this->result = true;
         $this->message = 'OK';

      }

   }

?>
