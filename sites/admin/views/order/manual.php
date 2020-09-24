<?PHP
   
   /***************************************
    *must mount fileserver before use
    *sudo mount -t cifs //zetta.eurofoto.no/produksjon /home/produksjon -o username=produksjon,password=produksjon,rw,iocharset=utf8,file_mode=0755,dir_mode=0755
    ***************************************/
   
   import( 'pages.admin' );
   import( 'website.product' );
   import( 'website.article' );
   import( 'website.order.manual.default' );
   
   class AdminManualOrder extends AdminPage implements IView {
      
      protected $template = 'order.manual';
      
      public function Execute( ) {
        
         //$this->Getfolders(); 
         
         $this->articles = array(
               0 => array(
                  'artnr'  => 'auto',
                  'title'  => 'Automatisk'
               ),
               1 => array( 
                 'artnr'   => 1,
                 'title'   => '10x15' 
               ),
               2 => array(
                  'artnr'  => 419,
                  'title'  => '10x13'                
               ),
               3 => array(
                  'artnr'  => 2,
                  'title'  => '15x20'                
               ),
               4 => array( 
                  'artnr'  => 439,
                  'title'  => '20x30'
               ),
               5 => array(
                  'artnr'  => 3,
                  'title'  => '20x25'
               ),
               6 => array( 
                  'artnr'  => 4,
                  'title'  => '25x30'
               ),
               7 => array( 
                  'artnr'  => 5,
                  'title'  => '30x40'            
               )   
            );
         
         
         if( $_POST['search'] ){

            $search = $_POST['search']; 
            if( is_numeric( $search ) ){
               foreach ( DB::query( "SELECT uid FROM brukar WHERE uid = ? ", $search )->fetchAll() as $res  ){               
                  list( $uid) = $res;
               }
            }
            else{
               
            }
            
            $user = new User( $uid );
            $this->userdata = $user->asArray();
            
            //util::Debug( $this->userdata );
            //die();
  
         }
         
      }
      
      
      public function Confirm(){
         
         $userid = $_POST['userid'];
         //util::debug( $_POST );
         $order = new ManualOrder();
         $orderid = $order->executeManualOrder( $_POST );
         
         relocate( '/order/manual/finished/' . $orderid );

      }
      
      public function Finished( $orderid ){
         
         $this->template = 'order.finished';
         
         $this->orderid = $orderid;
         
         
      }
      
      
      public function Getfolders(){

         $path[] = '/home/produksjon/man_ord/*';
         
         while(count($path) != 0)
         {
             $v = array_shift($path);
             foreach(glob($v) as $item)
             {
                 if (is_dir($item)){
                     $path[] = $item . '/*';
                     util::Debug( $item );
                 }
                 elseif (is_file($item))
                 {
                     util::Debug( $item );
                 }
             }
         }
         die();
         
      }
      
   }
   
?>