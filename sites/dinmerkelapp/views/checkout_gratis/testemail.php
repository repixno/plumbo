<?php

   /**
    * Gets the corresponding error from BBS
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    *
    */

   import( 'mail.send');
    
   class TestEmail extends WebPage implements IView {
      
      protected $template = 'emails.order.merkelapp';
      
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
            
              $fields =  array(
                  'orderrow'     => $orderrow ,
                  'totalprice'   => 2000,
                  'deliveryinfo' => $deliveryinfo,
                  'comment'      => $this->comment,
                  'textrow'      => '',
                  'orderrow'     => $orderrow,
                  'orderid'      =>  '1111111',
                  'imagecodes'    => $imagecodes
               );
         
            
            MailSend::Simple( 'adele@storys.no', "TESTMAIL", 'order.confirm', $fields, 'phptal' );
        
      }
    }