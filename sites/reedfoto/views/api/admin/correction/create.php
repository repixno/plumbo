<?php

   /**
    * Class to create new correction.
    *
    * @author Svein Arild Bergset <sab@interweb.no>
    *
    */

   import( 'reedfoto.pages.json' );
   import( 'reedfoto.correction' );

   class ReedFotoApiAdminCorrectionCreate extends JSONPage implements IValidatedView {

      public function Validate() {

         return array(
            'execute' => array(
               'post' => array(
                  'userid' => VALIDATE_INTEGER,
                  'title' => VALIDATE_STRING,
                  'comment' => VALIDATE_STRING
               )
            )
         );
      }

      /**
       * Create a new correction for a user
       *
       * @api-name admin.correction.create
       * @api-javascript yes
       * @api-post userid Integer Id of user
       * @api-post title String Title of new correction
       * @api-post comment String Comment for new correction
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       * @api-result newcorrection Array Properties of result correction
       */
      public function Execute() {

         $userid = $_POST[ 'userid' ];
         $title = $_POST[ 'title' ];
         $comment = $_POST[ 'comment' ];

         $correction = new RFCorrection();
         $correction->userid = $userid;
         $correction->title = $title;
         $correction->comment = $comment;
         $correction->created = date( 'Y-m-d' );
         $correction->createdby = Login::userid();
         $correction->ready = null;
         $correction->opened = null;
         $correction->approved = null;
         $correction->save();

         $this->result = true;
         $this->message = 'OK';
         $this->newcorrection = $correction->asArray();

      }

   }

?>
