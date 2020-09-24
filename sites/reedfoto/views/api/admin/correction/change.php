<?php

   /**
    * Change a correction.
    *
    * @author Svein Arild Bergset <sab@interweb.no>
    *
    */

   import( 'reedfoto.pages.json' );
   import( 'reedfoto.correction' );

   class ReedFotoApiAdminCorrectionChange extends JSONPage implements IValidatedView {

      public function Validate() {

         return array(
            'execute' => array(
               'post' => array(
                  'id' => VALIDATE_INTEGER,
                  'title' => VALIDATE_STRING,
                  'comment' => VALIDATE_STRING
               )
            )
         );
      }

      /**
       * Change a correction
       *
       * @api-name admin.correction.change
       * @api-javascript yes
       * @api-post id Integer Id of correction
       * @api-post title String Title of correction
       * @api-post comment String Comment for correction
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       * @api-result changedcorrection Array Properties of changed correction
       */
      public function Execute() {

         $id = $_POST[ 'id' ];
         $title = $_POST[ 'title' ];
         $comment = $_POST[ 'comment' ];

         $correction = new RFCorrection( $id );
         $correction->title = $title;
         $correction->comment = $comment;
         $correction->save();

         $this->result = true;
         $this->message = 'OK';
         $this->changedcorrection = $correction->asArray();

      }

   }

?>
