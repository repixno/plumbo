<?php

   /**
    * @author Svein Arild Bergset <sab@interweb.no>
    */

	import( 'core.util' );
	import( 'website.album' );

	class MyAccountFriendsPhotos extends UserPage implements IView {

		protected $template = 'myaccount.friendsphotos';

		public function Execute( ) {

		   $albums = new Album();

		   $this->albums = $albums->listSharedToMe();

		}

	}

?>