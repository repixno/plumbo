<?php

	import( 'core.util' );
	import( 'website.user' );

	class MyAccountProjects extends UserPage implements IView {

		protected $template = 'myaccount.projects';

		public function Execute( ) {

		   $user = new User( Login::userid() );
		   $projects = array();
		   foreach ( $user->listProjects() as $project ) {
		      
		      $projects[] = $project->asArray();
		      
		   }

		   $this->projects = $projects;

		}

	}

?>