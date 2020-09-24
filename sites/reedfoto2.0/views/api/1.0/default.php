<?php

   /**
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'pages.json' );

   class APIFunctionNotFound extends JSONPage implements IView {
      
      protected $template = '';
      
      public function Execute() {
         
         $this->result = false;
         $this->message = "API function not implemented";
         
      }
      
   }


?>