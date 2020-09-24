<?php
   
   Dispatcher::extendView( 'create.gift' );
   
   class QuickGiftCreator extends GiftCreator implements IView {
      
      protected $template = 'create.quick-gift';
      
      public function setGiftTemplate(){
         
         $detect = new Mobile_Detect;
         
         if( $detect->isMobile() ){
            $this->template = 'create.m-quick-gift';
         }
      }
      
   }
   
?>