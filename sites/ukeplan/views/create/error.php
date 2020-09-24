<?php
   
   class Contact extends WebPage implements IView {
      
      protected $template = 'create.error';

      public function Execute($error = "No error", $detailed_error = "") {
         $this->error_message = base64_decode($error);
      }
      
   }
   

?>