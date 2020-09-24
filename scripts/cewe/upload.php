<?PHP
   chdir( dirname( __FILE__ ) );
   include '../../bootstrap.php';

   config( 'website.config' );
   config( 'website.countries' );
   import( 'system.cli' );
   import('cewe.default');
   import( 'cewe.cewemyphotos'  );
   model( 'user.ceweuploads' );
   
   
   require_once __DIR__ . '/vendor/autoload.php';
   use PhpAmqpLib\Connection\AMQPStreamConnection;
   
   
   class imageExportToCewe extends Script {
      
      Public function Main(){
         
            try{
               $starttime = null;  
               
               $connection = new AMQPStreamConnection('nelly.eurofoto.no', 5672, 'guest', 'guest');
               $channel = $connection->channel();
            
               $channel->queue_declare('uploadcewe', false, true, false, false);
                  
               $callback = function($msg){
   
                  if( !$starttime ){
                     $starttime = microtime();  
                  }
   
                  $data =  unserialize(  $msg->body );
                  $ceweupload = new DBceweUploads($data['bid']);
                  
                  $upload = new pixiUpload();
                  
                  $ceweupload->started = date( 'Y-m-d H:i:s' );
                  
                  Util::Debug($data);
                  
                  $uploaded = $upload->UploadApi( $data['file'], $data['title'], $data['albumtitle'], $data['taskid'], $data['login']  );

                  if ($uploaded == 400 || $uploaded == 403 || $uploaded == 404 ) {
                      Util::Debug("http error status: $uploaded");
                      $this->result = false;
                      $this->message = "UNABLE TO UPLOAD FILE";
                      $ceweupload->failed = date( 'Y-m-d H:i:s' );
                  }

                  $ceweupload->ceweimageid = $uploaded->id;
                  $ceweupload->finished = date( 'Y-m-d H:i:s' );
                  $ceweupload->save();
                  
                  Util::Debug( $uploaded );
                  //$start = file('/home/toringe/count.txt')[0];
                  //$time = microtime()  - $start;
   
                  //file_put_contents( '/home/toringe/count.txt', date( 'H:i:s') . "\n" , FILE_APPEND);
                   
                  $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
                   
                   //echo "aslkas";
               };
                 
                 
               //echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";
               
               $channel->basic_qos(null, 1, null);
               $channel->basic_consume('uploadcewe', '', false, false, false, false, $callback);
                 
               while(count($channel->callbacks)) {
                   $channel->wait();
               }
                 
               $channel->close();
               $connection->close();
               
             
            }catch( exception $e ){
              Util::Debug( $e->getMessage() );
              mail( "adele@eurofoto.no", "CEWE UPLOAD BUG" , $e->getMessage() );
              mail( "claus@eurofoto.no", "CEWE UPLOAD BUG" , $e->getMessage() );
           }
            
      }
      
      
      
    }
    
    
    CLI::Execute();
