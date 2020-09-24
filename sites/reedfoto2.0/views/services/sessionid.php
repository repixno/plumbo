<?php
   class SessionIDService extends WebPage implements IView {
      protected $template = false;
      public function Execute() {
         echo Session::ID();
      }
   }
?>
