<?php

   import( 'website.subscription' );
   import( 'website.album' );
   
   import( 'pages.protected' );
   
   class AccountProjects extends ProtectedPage implements IView {

      protected $template = 'account.projects.index';
      
      /**
       * Execute
       *
       * adds projects to view
       * 
       */

		public function Execute( ) {

		   // fetch projects
		   $user = new User( Login::userid() );
		   
		   $projects = array();
		   
		   // loop projects into array
		   foreach ( $user->listProjects() as $project ) {
		      
		      $projects[] = $project->asArray();
		      
		   }
         
		   // add projects to view
		   $this->projects = $projects;

		}

   }
      
?>