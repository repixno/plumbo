<?PHP
   chdir( dirname( __FILE__ ) );
   include '../../bootstrap.php';

   config( 'website.config' );
   config( 'website.countries' );
   config( 'production.settings');
   import( 'system.cli' );
   import( 'website.order' );
   
   model( 'production.mapaid' );
   
   class OrderImportMapaidScript extends Script {
      
      private $username = 'eurofoto';
      private $password = 'gL2ss7m';
      private $url = 'http://www.settfraoven.com/';
      
      public $tidspunkt = '';
      
      public $orderfolder = "/home/produksjon/mapaid/";
      
      
      Public function Main(){
           

         $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->url . "API/getOrders/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, sprintf ( '%s:%s' ,$this->username, $this->password ) );
        $content = curl_exec ($ch);
                 
        $content = json_decode( $content );
         
         foreach ( $content as $order ){
            
            
            $query = sprintf( "SELECT id FROM production_mapaid WHERE mapaidid = %s" ,  $order->id );
            
            if( !DB::query( $query )->fetchSingle( ) ){
               
            $ordertime = date( "Y-m-d" , $order->timestamp );
            
            //util::debug(   $order  );

            $orderinfo = '';
            $orderinfo .= "Ordrenr: " . $order->id . "\n";
            $orderinfo .= "Navn: " . $order->client->name . "\n";
            $orderinfo .= "Firma: " . $order->client->company . "\n";
            $orderinfo .= "Adresse: " . $order->client->address . "\n";
            $orderinfo .= "Land: " . $order->client->country . "\n";
            $orderinfo .= "Telefon: " . $order->client->phone . "\n";
            $orderinfo .= "Epost: " . $order->client->email . "\n";
            
            $orderinfo .= "\nOrdrelinjer:\n";
            
            
            $orderdatefolder = $this->orderfolder . $ordertime;
        
            
            if( !file_exists( $orderdatefolder ) ){
               mkdir( $orderdatefolder );
            }
            $orderdateidfolder = $orderdatefolder . "/" . $order->id;
            if( !file_exists( $orderdateidfolder ) ){
               mkdir( $orderdateidfolder );
            }
            
            foreach ( $order->pictures as $pictures ){
               
               $orderinfo .= sprintf( "Produkt: %s,  Antall: %s,  Størrelse: %s, Bildeid: %s  \n" , $pictures->type , $pictures->quantity, $pictures->size,  $pictures->code );
               
               
               $productfolder = $orderdateidfolder . "/" . $pictures->type;
               if( !file_exists( $productfolder ) ){
                  mkdir( $productfolder );
               }
               $sizefolder = $productfolder . "/" . $pictures->size;
               if( !file_exists( $sizefolder ) ){
                  mkdir( $sizefolder );
               }

                $imagefile = $sizefolder . "/" . $pictures->code . ".jpg";
                           
                curl_setopt($ch, CURLOPT_URL, $this->url . 'API/getImageUrl/' . $pictures->code );
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $rawdata=curl_exec($ch);
                file_put_contents($imagefile, $rawdata);
 
            }
            
            file_put_contents( $orderdateidfolder . "/ordertext.txt" , $orderinfo );
            
            
            
            $orderdata = new DBMapaid();
            
            $orderdata->mapaidid = $order->id;
            $orderdata->downloaded = date( 'Y-m-d H:i:s' );
            $orderdata->save();
            
            
            util::Debug( $orderdata );
            
            //util::Debug( $order );
 
         }
         
         
         }
         
         curl_close ($ch); 
         
         


      }
      
      
      
      
      

   }
   
   CLI::Execute();

?>