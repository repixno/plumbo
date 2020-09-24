<?PHP
   chdir( dirname( __FILE__ ) );
   include '../../bootstrap.php';

   config( 'website.config' );
   config( 'website.countries' );
   import( 'system.cli' );
   import('cewe.default');
   import( 'cewe.cewemyphotos'  );
   import( 'cewe.uploadcewe'  );
   model( 'user.ceweuploads' );
   require_once __DIR__ . '/vendor/autoload.php';
   use PhpAmqpLib\Connection\AMQPStreamConnection;
   use PhpAmqpLib\Message\AMQPMessage;

   class imageExportToCewe extends Script {

      Public function Main(){

         try{
            $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
            $channel = $connection->channel();
            $channel->queue_declare('startcewemove', false, true, false, false);

            $callback = function( $msg ){
                $data =  unserialize(  $msg->body );
                $this->Export( $data );
                $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
                echo "Wait.........\n";
            };

            //echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

            $channel->basic_qos(null, 1, null);
            $channel->basic_consume('startcewemove', '', false, false, false, false, $callback);

            while(count($channel->callbacks)) {
                $channel->wait();
            }

            $channel->close();
            $connection->close();
         } catch (exception $e) {
            Util::Debug( $e->getMessage() );
            mail( "adele@eurofoto.no", "CEWE EXPORT BUG" , $e->getMessage() );
            mail( "claus@eurofoto.no", "CEWE EXPORT BUG" , $e->getMessage() );
         }
      }

      Public function Export( $data ){

         //session_start();

         $api = new ceweApi();
         //$login = $api->getApi( '/account'  );
         Util::Debug($data);
         //Util::Debug( $login );

         //exit;

         $userid = $data['userid'];
         $c_uid = $data['ceweuserid'];

         //if( $login->session->credentialLoggedIn != 1 ){

            Util::Debug("userid: $userid");

            $tasks = $api->getTask( $data['ceweuserid'], "UploadImages" );
            Util::Debug($tasks);

            if ($tasks == 400 || $tasks == 403 || $tasks == 404 || $tasks == 500) {
                Util::Debug("http error status: $tasks");
                $this->result = false;
                $this->message = "UNABLE TO CREATE TASK";
                exit;
            }

            foreach( $tasks as $task ){

               if( $task->title == 'UploadImages' ){
                  break;
               }else{
                   $task = null;
               }

            }

            $timestart = time();
            $size = 0;

            if( $task->login  && $task->title == "UploadImages" && $task->id && $userid ){

                Util::Debug("task: ", $task );

                $albums = array();
                $albums = DB::query("select * from bildealbum where uid = ?", $userid);
                $albumlist = $albums->fetchAll(DB::FETCH_ASSOC);
                Util::Debug("ALBUMLIST: " . $albumlist);

                foreach ($albumlist as $albs) {
                    $albumid = $albs['aid'];
                    Util::Debug("ALBUMs uid-item-in-array: " . $albs['uid']);

                    Util::Debug("ALBUMID: " . $albs['aid']);
                    //$albums = new Album($albs['id']);
                    $timing = microtime();
                    //$images = $albums->getImages();
                    $images = DB::query("select * from bildeinfo where owner_uid = ? and aid = ?", $userid, $albumid)->fetchAll(DB::FETCH_ASSOC);
                    //Util::Debug($images);
                  
                    $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
                    $channel = $connection->channel();

                    foreach($images as $image) {
                     
                        $imageid = DB::query( "SELECT bid FROM cewe_uploads where bid = ? ", $image['bid'] )->fetchSingle();
                        Util::Debug("bid: " . $image['bid']);
                     
                        if ($imageid) {
                            Util::Debug( "IMAGE ID FINNS" );
                        } else {

                            $t_bid = $image['bid'];
                            $t_aid = $image['aid'];
                            $t_uid = $image['owner_uid'];
                            $hcode = $image['hashcode'];
                            $this_date = date('Y-m-d H:i:s');
                            $sql = "insert into cewe_uploads (bid, aid, uid, ceweimageid, ceweuserid, created) values ($t_bid, $t_aid, $t_uid, '$hcode', '$c_uid', '$this_date')";

                            DB::query($sql);

                            $file =  $image['filnamn'];

                            $imageupload = array(
                                'bid' => $image['bid'],
                                'ceweuserid' => $task->login,
                                'taskid' => $task->id,
                                'file' => $file,
                                'title' => $image['tittel'],
                                'albumtitle' => $albs['namn'],
                                'hashcode' => $image['hashcode']
                            );

                            //$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
                            //$channel = $connection->channel();

                            $data = serialize( $imageupload );

                            if(empty($data)) $data = "Something went wrong!";

                            $msg = new AMQPMessage($data,
                                                array('delivery_mode' => 2) # make message persistent
                                            );

                            $channel->basic_publish( $msg, '', 'uploadcewe' );

                            echo  $image['bid'] . " Sent\n";

                            //$channel->close();
                            //$connection->close();
                        }
                    }
                    $channel->close();
                    $connection->close();
                }

                // If we are dealing with the inbox (albumid equal to null) id is set to zero (0).
                $innboksbilder = array();
                $innboks = DB::query("SELECT * FROM bildeinfo WHERE owner_uid = ? AND aid IS NULL", $userid);
                $bilder  = $innboks->fetchAll(DB::FETCH_ASSOC);

                $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
                $channel = $connection->channel();

                foreach ($bilder as $bilde) {
                    Util::Debug("innboks-bilde-bid: " . $bilde['bid']);
                    //Util::Debug("innboks-bilde-owner_uid: " . $bilde['owner_uid']);

                    $imageid = DB::query( "SELECT bid FROM cewe_uploads where bid = ? ", $bilde['bid'] )->fetchSingle();
                     
                    if ($imageid) {
                        Util::Debug( "IMAGE ID FINNS" );
                    } else {

                        $t_bid = $bilde['bid'];
                        $t_aid = 0;
                        $t_uid = $bilde['owner_uid'];
                        $hcode = $image['hashcode'];
                        $this_date = date('Y-m-d H:i:s');
                        $sql = "insert into cewe_uploads (bid, aid, uid, ceweimageid, ceweuserid, created) values ($t_bid, $t_aid, $t_uid, '$hcode', '$c_uid', '$this_date')";

                        DB::query($sql);

                        //$file = basename($bilde['filnamn']);

                        $file = $bilde['filnamn'];
                    
                        $imageupload = array(
                            'bid' => $bilde['bid'],
                            'ceweuserid' => $task->login,
                            'taskid' => $task->id,
                            'file' => $file,
                            'title' => $bilde['tittel'],
                            'albumtitle' => 'Innboks',
                            'hashcode' => $bilde['hashcode']
                        );
                    
                        //$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
                        //$channel = $connection->channel();

                        $data = serialize( $imageupload );

                        if(empty($data)) $data = "Something went wrong!";

                        $msg = new AMQPMessage($data,
                                             array('delivery_mode' => 2) # make message persistent
                                           );

                        $channel->basic_publish( $msg, '', 'uploadcewe' );

                        //$channel->close();
                        //$connection->close();
                    }
                }
                $channel->close();
                $connection->close();

            //}

            $timeend = time();

         }
         Util::Debug("DONE");
      }
    }


    CLI::Execute();
