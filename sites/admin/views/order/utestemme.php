<?PHP
   
    import( 'pages.admin' );
    import( 'website.product' );
    import( 'website.article' );
    model( 'production.utestemme' );
   
    class AdminProdctionUtestemme extends AdminPage implements IView {
      
        protected $template = 'order.utestemme';
        
        public function Execute(){
        
            $ready_orders = DB::query( 'SELECT * FROM production_utestemme WHERE sent is null' )->fetchAll( DB::FETCH_ASSOC );
            
            
            $this->orders = $ready_orders;          
               
        }
		
		public function find(){
			$this->template = 'order.utestemmesearch';
			
			$utestemmeid= $_POST['utestemmeid'];
			
			if( $utestemmeid ){
				
				$this->utestemmeid = $utestemmeid;
				$this->eforderid = DB::query( 'SELECT eforderid FROM production_utestemme WHERE utestemmeid = ?', $utestemmeid  )->fetchSingle();
			}
			
			
			
			
		}
        
        
        public function Send( $id ){
            
            $this->template = null;
            
            if( $id > 0 ){
                
                $order = new DBUtestemme( $id );
                
               // $url = sprintf( 'http://www.utestemme.no/api/v1/orders/%s/close', $order->utestemmeid );
               // $ch = curl_init( );
               // curl_setopt($ch, CURLOPT_URL, $url);
               // curl_setopt($ch, CURLOPT_POST, 1);
               // curl_setopt($ch, CURLOPT_POSTFIELDS, "api_token=faf709a5-1caf-4233-af34-f96fbd175393");
               // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
               //// $output = curl_exec($ch);
                
                //curl_close($ch);
               
	//	Util::Debug(   sprintf( 'http://www.utestemme.com/api/v1/orders/%s/close', $order->utestemmeid )  );
	//	Util::Debug( $output );
	//	die();
		
		$output = exec( sprintf( 'curl http://www.utestemme.no/api/v1/orders/%s/close --data api_token=faf709a5-1caf-4233-af34-f96fbd175393', $order->utestemmeid  )  );

		if ( $output == 'OK' ){
                	$order->sent = date( 'Y-m-d H:i:s' );
                	$order->save();
                	relocate( '/order/utestemme' );
		} 
               /*
                if( $output == 200 ){
                    $order->sent = date( 'Y-m-d H:i:s' );
                    $order->save();
                    relocate( '/order/utestemme' );
                }else{
                    Util::Debug( "Det oppstod en feil prøv igjen" );
                }*/

                
            }
            
            
            
            
        }
      
    }
   
?>
