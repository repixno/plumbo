<?PHP

   /******************************************
    * Script for handling CD/DVD archiveorders.
    * runst the converts script and moves
    * orders to correct location
    * 
    ***************************************/
   chdir( dirname( __FILE__ ) );
   include '../../bootstrap.php';

   config( 'website.config' );
   config( 'website.countries' );
   config( 'production.settings');
   import( 'system.cli' );
   import( 'website.order' );
   model( 'order.archive');

   class ArchiveImportScript extends Script {
      
      public $webspoolFolder = '/home/produksjon/webspool/';
      
      Public function Main(){
         
         $archivelist =  $this->Archivelist();
         
         if( is_array( $archivelist ) ){
            foreach ( $archivelist as $archive ){
               
               $this->fetch( $archive['ordrenr'] );
                 
            }
         }else util::Debug( 'no orders' );
            
      }
      
      private function Archivelist(){

         foreach(  DB::query( 'SELECT * FROM arkivcd WHERE download_began_at IS NULL AND exported IS NOT NULL' )->fetchAll( DB::FETCH_ASSOC ) as $collections ) {
            $ret[] = $collections;
         }
         return $ret;

      
      }
      
      private function fetch( $orderid ){
         
         $fileserver = Settings::Get( 'production' , 'fileserver' );
         
         $order = new DBArchive ( $orderid );
         $order->download_began_at =  date( 'Y-m-d H:i:s' );
         $order->save();
         $date = date( 'Y-m-d' , strtotime( $order->tidspunkt ) );
         if( !file_exists( sprintf( '/home/produksjon/webspool/%s', $date ) ) ){
            mkdir( sprintf( '/home/produksjon/webspool/%s', $date ), 0755 , true );
         }
         
         exec ("rsync -a $fileserver::ordrer/\*/print_download/$date/$orderid /home/produksjon/webspool/$date");

         $order->download_ended_at =  date( 'Y-m-d H:i:s' );
         $order->save();
         
         util::Debug( $order );
      }
   
   
   }
   

   CLI::Execute();

?>