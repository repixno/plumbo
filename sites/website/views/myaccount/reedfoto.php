<?php

   /**

    */

	import( 'core.util' );
	import( 'website.album' );

	class MyAccountFriendsPhotos extends UserPage implements IView {

		protected $template = 'myaccount.reedfoto';

		public function Execute( ) {

		   $albums = new Album();

		   $this->albums = $albums->listReedfoto();

		}

	}

?>