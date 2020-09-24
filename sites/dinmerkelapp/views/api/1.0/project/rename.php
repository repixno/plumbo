<?php

   /**
    * Delete project.
    *
    * @author Svein Arild Bergset <sab@interweb.no>
    *
    */

   import( 'pages.json' );
   import( 'website.project' );

   class APIProjectRename extends JSONPage implements NoAuthRequired, IValidatedView {

      public function Validate() {

         return array(
            'execute' => array(
               'fields' => array(
                  VALIDATE_INTEGER,
                  VALIDATE_STRING
               ),
               'post' => array(
                  'projectid' => VALIDATE_INTEGER,
                  'title' => VALIDATE_STRING
               )
            )
         );

      }

      /**
       * Rename project
       *
       * @api-name project.rename
       * @api-auth required
       * @api-param projectid Integer The id of the project to rename
       * @api-param title String The title of the renamed project
       * @api-post-optional projectid Integer ID of the project to rename
       * @api-post-optional title String The title of the renamed project
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      public function Execute( $id = null, $title = null ) {

         if ( !isset( $id ) ) $id = $_POST['projectid'];
         if ( !isset( $title ) ) $title = $_POST['title'];
         
         $project = new Project( $id );
         if( $project->isLoaded() ) {
            
            if( $project->userid && $project->userid != Login::userid() ) {
               
               $this->result = false;
               $this->message = 'You have no access to this project';
               
            } else {
               
               $project->title = $title;
   
               if ( $project->save() ) {
   
                  $this->result = true;
                  $this->message = 'OK';
   
               } else {
   
                  $this->result = false;
                  $this->message = 'Error while saving project title';
   
               }
               
            }
            
         } else {

            $this->result = false;
            $this->message = 'Error while loading project';

         }

      }

   }

?>
