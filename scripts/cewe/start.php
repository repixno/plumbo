<?PHP
   chdir( dirname( __FILE__ ) );
   include '../../bootstrap.php';

   config( 'website.config' );
   config( 'website.countries' );
   import( 'system.cli' );
   model( 'user.cewe_transfers' );
   require_once __DIR__ . '/vendor/autoload.php';
   use PhpAmqpLib\Connection\AMQPStreamConnection;
   use PhpAmqpLib\Message\AMQPMessage;
   
   class StartExportToCewe extends Script {
      
         Public function Main(){
            
            $rabbit_queue = "startcewemove";
            
            //sjekk om det finns pågande opplastinger, start kun 1 om gangen
            //if( DB::query( "SELECT count(*) FROM cewe_transfers WHERE started is not null AND finished is null" )->fetchSingle() ){
            //   exit;
            //}
            
            $readytoexport = DB::query( "SELECT * FROM cewe_transfers WHERE started is null order by id" )->fetchAll( DB::FETCH_ASSOC );
            
                
            $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
            $channel = $connection->channel();
            $channel->queue_declare($rabbit_queue, false, true, false, false);

            //$customers = 0;

            foreach( $readytoexport as $export ){
                
                $data = serialize( $export );
                
                if(empty($data)) $data = "Something went wrong!";
                
                $msg = new AMQPMessage($data,
                                        array('delivery_mode' => 2) // make message persistent
                                      );
                
                $channel->basic_publish( $msg, '', $rabbit_queue );
                
                Util::Debug($export);
                
                $newtransfer = new DBceweTransfer( $export['id'] );
                $newtransfer->started = date('Y-m-d H:i:s');
                $newtransfer->save();
            }
            
            $channel->close();
            $connection->close();
          
            Util::Debug("DONE");
        }
    }
    
    CLI::Execute();
