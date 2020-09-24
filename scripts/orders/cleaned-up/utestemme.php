<?PHP
   chdir( dirname( __FILE__ ) );
   include '../../bootstrap.php';

   config( 'website.config' );
   config( 'website.countries' );
   config( 'production.settings');
   import( 'system.cli' );
   import( 'website.order' );
   
   model( 'production.utestemme' );
   
   class OrderImportUtestemmeScript extends Script {
      
      //private $api_token = 'faf709a5-1caf-4233-af34-f96fbd175393';
      private $api_token = 'faf709a5-1caf-4233-af34-f96fbd175393';
      //private $url = 'https://utestemme-staging.herokuapp.com/api/v1/orders/open?api_token=faf709a5-1caf-4233-af34-f96fbd175393';
      private $url = 'http://www.utestemme.no/api/v1/orders/open?api_token=faf709a5-1caf-4233-af34-f96fbd175393';
      public $tidspunkt = '';
      public $orderfolder = "/home/produksjon/utestemme/";
     //public $orderfolder = "/home/toringe/utestemme/";
      
      Public function Main(){
           
            $content = file_get_contents(  $this->url );
            //$content = json_decode( utf8_encode(  $content )  );
            $content = json_decode( $content  );
            mail( 'til@gloppen.net', "test mail zetta", "test mail zettz!" );
            
            foreach ( $content as $order ){
               
               $query = sprintf( "SELECT id FROM production_utestemme WHERE utestemmeid = %s" ,  $order->id );
            
		Util::debug( utf8_decode( $order->shipping_address->street ) );	               
		Util::debug( utf8_decode( $order->id ) );	               

		if( !DB::query( $query )->fetchSingle( ) ){
                              
                  $orderinfo = '';
                  $orderinfo .= "Ordrenr: " . $order->id . "\n";
                  $orderinfo .= "Navn: " . $order->shipping_address->name . "\n";
                  $orderinfo .= "Adresse: " . $order->shipping_address->street . "\n";
                  $orderinfo .= "Postnummmer: " . $order->shipping_address->zip . "\n";
                  $orderinfo .= "Stad: " . $order->shipping_address->city . "\n";
                  
                  $orderinfo .= "\nOrdrelinjer:\n\n";
                  
                  mail( 'stian@eurofoto.no' , "ny utestemmeordre", "Ordrenr: " . $order->id . "\n" );
               
                  $ordertime = date( "Y-m-d" ,  strtotime( $order->date ) );
               
                  $orderdatefolder = $this->orderfolder . $ordertime;
        
                  if( !file_exists( $orderdatefolder ) ){
                     mkdir( $orderdatefolder );
                  }
                  $orderdateidfolder = $orderdatefolder . "/" . $order->id;
                  if( !file_exists( $orderdateidfolder ) ){
                     mkdir( $orderdateidfolder );
                  }
               
                  foreach( $order->pictures  as $pictures ){
                     
                     $pathinfo = pathinfo ( $pictures->url ); 
                     
                     $orderinfo .= sprintf( "Produkt: %s,  Antall: %s,  Størrelse: %s, Bildeid: %s  \n\n" , $pictures->product , $pictures->quantity, $pictures->size ,$pathinfo['filename'] );
                     $productfolder = $orderdateidfolder . "/" . $pictures->type;
                     if( !file_exists( $productfolder ) ){
                        mkdir( $productfolder );
                     }
                     $sizefolder = $productfolder . "/" . $pictures->size;
                     if( !file_exists( $sizefolder ) ){
                        mkdir( $sizefolder );
                     }
                     $img =  $sizefolder . '/' . $pathinfo['filename'] . '.jpg';
                     file_put_contents($img, file_get_contents( $pictures->url ) );
                   
                  }
                  
                  file_put_contents( $orderdateidfolder . "/ordertext.txt" , $orderinfo );
                  
                  $orderdata = new DBUtestemme();
                  $orderdata->utestemmeid = $order->id;
                  $orderdata->downloaded = date( 'Y-m-d H:i:s' );
                  $orderdata->save();
               }
            }
         }
   }
   
   CLI::Execute();

?>
