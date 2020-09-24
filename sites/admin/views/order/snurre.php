<?PHP


   import( 'pages.admin' );
   model( 'production.snurre' );

   
   class AdminProductionLablink extends AdminPage implements IView {
	
	  protected $template = 'order.snurre';
    
      public function Execute(){
        
		 $ready_orders = DB::query( 'SELECT * FROM production_snurresprett ORDER by downloaded DESC')->fetchAll( DB::FETCH_ASSOC ); 
		 // $ready_orders = DB::query( 'SELECT * FROM production_utestemme WHERE sent is null' )->fetchAll( DB::FETCH_ASSOC );
         $this->orders = $ready_orders;
           
      }
        
	  public function Search(){
			
		 $this->template = 'order.foveasearch';
		 $foveaid = $_POST['foveaid'] ;
		 $this->eforderid = DB::query( "SELECT eforderid FROM production_snurresprett WHERE snurresprettid ilike '%$foveaid%'" )->fetchSingle();
		 $this->foveaid = $foveaid;
		 
	  }
		
		
	  public function Delete( $id ){
		 
		 $this->template = null;
		 $snurreorder = new DBSnurre( $id );
		 $snurreorder->deleted = date( 'Y-m-d H:i:s');
		 $snurreorder->save();		 
		 relocate("/order/snurre");

	  }
    }
   
   
   // select * from historie_ordre where uid 
   
?>

