<?PHP
    import( 'pages.json' );
    import( 'finance.klarna.klarnainvoice' );
   
    class klarnaInvoice extends JSONPage implements NoAuthRequired, IView {
        
        
        public function Execute() {
            
            $pno = $_POST['pno'];
            $klarnainvoice = new KlarnaInvoiceEF();
            $cart = new Cart();
            
            $result = $klarnainvoice->register($cart, $_POST );
            

            if( $result['status'] == 1 ){
                $klarnainfo = array(
                                'reference' => $result['rno'],
                                'reservation' => $result['rno'],
                                'eid' => $klarnainvoice->eid,
                                'paymentmethod' => 'KLARNAINV',
                                'pno' => $pno
                                );
               
                $cart->setKlarnaid( serialize( $klarnainfo ) );
                $cart->save();
                $this->result = $result['status'];
                $this->message = $result['message'];
                return true;
            }else{
                $this->result = $result['status'];
                $this->message = $result['message'];
                return false;
            }
           
        }      
    }



?>