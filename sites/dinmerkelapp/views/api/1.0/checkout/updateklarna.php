<?PHP
    import( 'pages.json' );
    import( 'finance.klarna.klarna' );
   
    class updateKlarna extends JSONPage implements NoAuthRequired, IView {

          
        public function Execute() {
            
            $deliverytype = $_POST['delivery-method'];
           
            $cart = new Cart();
            $cart->setDeliveryType($deliverytype);
            $cart->save();

            $klarna = new KlarnaEF();
            $klarna->update($cart);
            
            $this->result = true;
            $this->message = 'OK';
            return true;
              
        }      
    }



?>