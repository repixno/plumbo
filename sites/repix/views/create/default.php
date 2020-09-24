<?php

   class CreateRedirector extends WebPage implements IView {
      
      public function execute() {
         
         relocate( '/create/photobook' );
         
      }
      
   }
   
?>