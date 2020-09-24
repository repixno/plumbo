<?php
   
   Dispatcher::extendView( 'website|create.gift' );
   
   class StampCreator extends GiftCreator implements IView {
      
      protected $template = 'create.stamp';
      
   }
   
?>