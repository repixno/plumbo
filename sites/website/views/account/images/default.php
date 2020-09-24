<?php

   import( 'website.subscription' );
   import( 'website.album' );
   
   import( 'pages.protected' );
   
   class AccountImages extends ProtectedPage implements IView {

      protected $template = 'account.images.index';
      
      function Execute() {
         
      }
      
   }
      
?>