<?php

   /**
    * @author Tor Inge Løvland <tor.inge@eurofoto.no>
    */

	import( 'core.util' );
	import( 'website.album' );

	class MyAccountSharedAlbums extends UserPage implements IView {

		protected $template = 'myaccount.sharedphotos';

		public function Execute( ) {

		   $albums = new Album();

		   $this->albums = $albums->enumSharedByMe();

		}

	}

?>