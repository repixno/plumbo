<?php

   /**
    * Delete project.
    *
    * @author Svein Arild Bergset <sab@interweb.no>
    *
    */

   import( 'pages.json' );
   import( 'website.project' );

   class APIProjectDelete extends JSONPage implements NoAuthRequired, IValidatedView {

      public function Validate() {

         return array(
            'execute' => array(
               'fields' => array(
                  VALIDATE_INTEGER
               ),
               'post' => array(
                  'projectid' => VALIDATE_INTEGER,
               ),
            ),
         );

      }

      /**
       * Delete project
       *
       * @api-name project.delete
       * @api-auth required
       * @api-param-optional projectid Integer ID of the project to delete
       * @api-post-optional projectid Integer ID of the project to delete
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      public function Execute( $id = null ) {

         if( is_null( $id ) ) $id = $_POST['projectid'];
         
         $project = new Project( $id );
         if( $project->isLoaded() ) {
            
            if( $project->userid && $project->userid != Login::userid() ) {
               
               $this->result = false;
               $this->message = 'You have no access to this project';
               
            } else {
               
               $this->result = true;
               $this->message = 'OK';
               
               if ( !$project->delete() ) {
      
                  $this->result = false;
                  $this->message = 'Project deletetion failed!';
                  
               }
               
            }
            
         } else {
            
            $this->result = false;
            $this->message = 'Error locating project. Already deleted?';
            
         }
         
         // Return values.
         return true;
         
      }
      
   }
   
?>