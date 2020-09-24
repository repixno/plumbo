<?php

   /**
    * Share project.
    *
    * @author Svein Arild Bergset <sab@interweb.no>
    *
    */

   import( 'pages.json' );
   import( 'website.project' );

   class APIProjectShare extends JSONPage implements NoAuthRequired, IValidatedView {

      public function Validate() {

         return array(
            'execute' => array(
               'post' => array(
                  'projectid' => VALIDATE_INTEGER,
                  )
               )
            );

      }

      /**
       * Share project
       *
       * @api-name project.share
       * @api-auth required
       * @api-post projectid Integer ID of the project to share
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      public function Execute() {

         $id = $_POST['projectid'];

         $project = new Project( $id );
         if( $project->isLoaded() ) {

            if( $project->userid && $project->userid != Login::userid() ) {

               $this->result = false;
               $this->message = 'You have no access to this project';

            } else {


               list( $shareUrl ) = $project->share();

               if ( $shareUrl === false ) {

                  $this->result = false;
                  $this->message = 'Can not share project.';

               } else {

                  $this->shareurl = $shareUrl;
                  $this->result = true;
                  $this->message = 'OK';

               }

            }

         } else {

            $this->result = false;
            $this->message = 'Error while loading project';

         }

         // Return values.
         return true;

      }


   }


?>