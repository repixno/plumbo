<?php


    import('finance.klarna.klarnainvoice');


   class TestKlarnaInvoice extends WebPage implements IView {
      
      protected $template = null;
      
      public function Execute( ) {
        
        
        $kake = new KlarnaInvoiceEF();
        
        
        $cart = new Cart();
        
        $kake->register($cart, "01121579533" );
        
        
      }
      
      
      public function test(){
         $this->template = 'checkout.klarna';
      }
      
   }


?>