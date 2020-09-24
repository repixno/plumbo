<?php

   /**
    * @author André Nordstrand <andre@iw.no>
    */

   import( 'website.image' );
   import( 'website.mmsalbum' );
	import( 'core.util' );

	class MyAccountAlbums extends UserPage implements IView {
      
		protected $template = 'myaccount.mobilephotos';
		
		public function Execute( ) {
	      
		   $images = new MMSAlbum();
		   foreach( $images->collection( array( 'mmsid' ), array( 'uid' => Login::userid() ) )->fetchAllAs( 'MMSAlbum' ) as $mms ) {
		      if( $mss->deleted == false ) $mmses[] = $mms->asArray();
         }
         
         if( !count( $mmses ) ) {
            $mmses = array();
         }
         
         $this->mmses = $mmses;
         
         return $mmses;
         
		}
		
	}

?>