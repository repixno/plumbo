<?PHP
   
    import( 'pages.admin' );
    import( 'website.product' );
    import( 'website.article' );
    model( 'production.fovea' );
   
    class AdminProdctionUtestemme extends AdminPage implements IView {
      
        protected $template = 'order.fovea';
        
        public function Execute(){
        
            $ready_orders = DB::query( 'SELECT * FROM production_fovea WHERE sent is NULL AND deleted is NULL' )->fetchAll( DB::FETCH_ASSOC );
			
			
            $this->orders = $ready_orders;          
               
        }
        
		public function Search(){
			
			$this->template = 'order.foveasearch';
			
			
			if( $_POST['foveaid'] > 100000000 ){
				$foveaid = $_POST['foveaid'] ;
				$this->eforderid = DB::query( 'SELECT eforderid FROM production_fovea WHERE foveaid = ?', $foveaid )->fetchSingle();
				$this->foveaid = $foveaid;
			}
			
		}
        
        public function Send( $id ){
            
            $this->template = null;
            
            if( $id > 0 ){
                
                $order = new DBFovea( $id );
                	
				$order->sent = date( 'Y-m-d H:i:s' );
                $order->save();
                relocate( '/order/fovea' );
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
		
		public function Delete( $id ){
            
            $this->template = null;
            
            if( $id > 0 ){
                
                $order = new DBFovea( $id );
                	
				$order->deleted = date( 'Y-m-d H:i:s' );
                $order->save();
                relocate( '/order/fovea' );
				
            }
        }
      
    }
   
?>
