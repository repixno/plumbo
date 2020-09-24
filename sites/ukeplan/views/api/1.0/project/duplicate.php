<?php

   /**
    * Share project.
    *
    * @author Svein Arild Bergset <sab@interweb.no>
    *
    */

   import( 'pages.json' );
   import( 'website.project' );

   class APIProjectDuplicate extends JSONPage implements NoAuthRequired, IValidatedView {

      public function Validate() {

         return array(
            'execute' => array(
               'post' => array(
                  'projectid' => VALIDATE_INTEGER,
                  'title' => VALIDATE_STRING
                  )
               )
            );

      }
      
      /**
       * Duplicate project
       *
       * @api-name project.duplicate
       * @api-auth required
       * @api-post projectid Integer ID of the project to duplicate
       * @api-post title String The title of the duplicated project
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      public function Execute() {

         $id = $_POST[ 'projectid' ];
         $title = $_POST[ 'title' ];

         $project = new Project( $id );
         if( $project->isLoaded() ) {

            if( $project->userid && $project->userid != Login::userid() ) {

               $this->result = false;
               $this->message = 'You have no access to this project';

            } else {

               $clonedProject = clone $project;
               $clonedProject->title = $title;
               $clonedProject->share = false;
               $clonedProject->share_id = '';
               $clonedProject->save();

               $this->result = true;
               $this->message = 'OK';

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