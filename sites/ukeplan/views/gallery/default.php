<?php
   /**

    * 
    */
   
   class GalleryUkeplan extends WebPage implements IView {
      
      protected $template = 'gallery.album.showalbum';
		
      public function Execute(){
         // default gammel 3288550
            $album = new Album(4507431);
            $this->album = $album->asArray();
            $this->images = $album->getImages();
            
      }
      
	  public function  Manedsplan(){
		// gammel 3710102
            $album = new Album(4507432);
            $this->album = $album->asArray();
            $this->images = $album->getImages();
         
      }
	  
      public function  Presseklipp(){
			// gammel 3288684
            $album = new Album(4507433);
            $this->album = $album->asArray();
            $this->images = $album->getImages();
         
      }
      
      public function  Bedriftsplan(){
			
			// gammel 3313250
            $album = new Album(4507436);            
            $this->album = $album->asArray();
            $this->images = $album->getImages();
         
      }
	  
      public function  Barnehageplan(){
			// gammel 3313250
            $album = new Album(4507435);
            $this->album = $album->asArray();
            $this->images = $album->getImages();  
      }
	  
      public function  Dorskilt(){
            $album = new Album(4507400);            
            // 4415704
            $this->album = $album->asArray();
            $this->images = $album->getImages();
         
      }
	  public function  Dagplan(){
            $album = new Album(4507434);            
            $this->album = $album->asArray();
            $this->images = $album->getImages();
      }
	  
      public function  Magnetplan(){
            $album = new Album(4507429);            
            
            $this->album = $album->asArray();
            $this->images = $album->getImages();
         
      }
	  
	    public function  Menyplan(){
            $album = new Album(4415700);            
            
            $this->album = $album->asArray();
            $this->images = $album->getImages();
         
      }
	  
	  
	  
	  
	  
	  
   }


?>