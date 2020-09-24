<?PHP
   
   class MyAccountAlbumTodaysPhoto extends UserPage implements IView {
      
      protected $template = 'myaccount.album.todays-photo';
      
      public function Execute( $imageid = 0 ) {
         
         if( $imageid > 0 ) {
            
            // load the image and return it
            try {
               $image = new Image( $imageid );
               $this->image = $image->asArray();
            } catch( Exception $e ) {}
            
         }
         
      }
      
      public function confirmed( $imageid = 0 ) {
      	
      		if( $imageid > 0 ) {
            
            // load the image and return it
            try {
               $image = new Image( $imageid );
               $this->image = $image->asArray();
            } catch( Exception $e ) {}
            
         }
      	
      		$this->setTemplate( 'myaccount.album.todays-photo-confirmed' );
		
      }
      
   }
   
?>