<?php

   /**

    * 
    */
   
   class GalleryUkeplan extends WebPage implements IView {
      
      protected $template = 'gallery.album.showalbum';
		
      public function Execute(){
         
            $album = new Album(3288550);
            $this->album = $album->asArray();
            $this->images = $album->getImages();
            
      }
      
      public function  Presseklipp(){
            $album = new Album(3288684);
            $this->album = $album->asArray();
            $this->images = $album->getImages();
         
      }
      
      public function  Bedriftsplan(){
            $album = new Album(3313250);            
            $this->album = $album->asArray();
            $this->images = $album->getImages();
         
      }
      
      public function  Barnehageplan(){
            $album = new Album(3288917);            
            
            $this->album = $album->asArray();
            $this->images = $album->getImages();
         
      }
      
      
   }


?>