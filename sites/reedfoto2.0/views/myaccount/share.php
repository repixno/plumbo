<?php


   import( 'website.user' );

   class MyAccountShare extends UserPage implements IView {
      
      protected $template = 'myaccount.album.share.index';
      
      public function Execute() {
         
      }
      
      public function link() {
         
         $this->setTemplate( 'myaccount.album.share.link' );
         
      }
      
   }
?>