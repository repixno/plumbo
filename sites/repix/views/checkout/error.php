<?php

   /**
    * Gets the corresponding error from BBS
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    *
    */

   class CheckoutError extends WebPage implements IView {
      
      protected $template = 'checkout.error';
      
      public function Execute( $error = 0 ) {
         
         if( isset( $_GET['error'] ) ) {
            $error = $_GET['error'];
         }
         
         if( Dispatcher::getPortal() == 'UP-DK' ){
            $error2 = "Du valgte at afbryte købsprocessen";
         }else{
            $error2 = "Du valgte å avbryte kjøpsprosessen";
         }
         
         
         switch( $error ) {
            
            case 1:
            $this->error = "Betaling feilet, vennligst kontakt din kortutsteder.";
            break;
         case 2:
            $this->error = $error2;
            break;
         case 3:
            $this->error = "Betaling feilet, vennligst kontakt din kortutsteder.";
            break;
         default:
            $this->error = "UNKNOWN ERROR!";
            break;   
            
         }
         
      }
      
   }


?>