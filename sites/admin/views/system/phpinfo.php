<?php
   
   import( 'pages.admin' );

   class PHPInfo extends AdminPage implements IView {
      
      public function Execute() {
         
         // create a header
         $this->header = __( 'PHP Info' );
         
         // write the phpinfo() result to a buffer
         ob_start(); phpinfo(); $buffer = ob_get_clean();
         
         // fetch just the <body />-tag and its content
         preg_match( "/<body>(.*)<\/body>/si", $buffer, $matches );
         
         // echo it
         echo end( $matches );
         
      }
      
   }
   
?>