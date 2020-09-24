<?php

   /**
    * Get info about a project
    * 
    */

   import( 'pages.json' );
   import( 'website.project' );

   class APIProjectInfo extends JSONPage implements IValidatedView {
      
      public function Validate() {
       
         return array(
            'execute' => array(
               'post' => array(
                  'projectid' => VALIDATE_INTEGER,
               ),
               'fields' => array(
                  VALIDATE_INTEGER,
               )
            )
         );
           
      }
      
      /**
       * Get info about an album.
       * 
       * @api-name project.info
       * @api-post-optional projectid Integer ID of the project
       * @api-param-optional projectid Integer ID of the project
       * @api-result project Array Project info
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */

      public function Execute( $projectid = 0 ) {
         
         $projectid = $_POST['projectid'] ? $_POST['projectid'] : $projectid;
         
          try {
            
            $project = new Project( $projectid );

            $this->project = $project->asArray();

            $this->result = true;
            $this->message = 'OK';

          } catch( Exception $e ) {
            
            $this->result = false;
            $this->message = 'No such project or no access to this project.';
          
            return false;
            
         }
         
      }

   }


?>
