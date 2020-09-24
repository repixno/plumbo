<?php

   /**
    * Gets the corresponding error from BBS
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    *
    */

   class TestEmail extends WebPage implements IView {
      
      protected $template = 'emails.order.confirm';
      
      public function Execute(  ) {
        
            $orderrow = array(
                0 => array(
                    'title' => "test",
                    'unitprice' => 123123,
                    'quantity' => 1,
                    'price' => 12231
                    )  
            );
            
            $deliveryinfo = array(
                'name' => "test",
                'address' => "test",
                'zipcode' => "sdasdas",
                'city' => "ASDASLDAS"
                
            );
        
        
            $this->orderrow     = $orderrow;
            $this->totalprice  = 123123;
            $this->deliveryinfo = $deliveryinfo;
            $this->comment     = "kAKSKASKSA";
            $this->textrow     = $textrow;
            $this->orderid     =  123123123;
        
      }
    }