<?php

   import( 'website.album' );
   
   class CreateSmileontiles extends WebPage implements IView {
      
      protected $template = 'editor.index';
      
      public function execute() {
         
         
         $this->albums = Album::enum();
         $this->sharedAlbums = Album::enumSharedToMe( true );
         
      }
      
   }
   
?>