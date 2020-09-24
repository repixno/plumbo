<?php

   import( 'pages.admin' );
   import( 'website.subscription' );
   
   class AdminImportCewe extends AdminPage implements IView {
      
        protected $template = null;
        
        public function Execute() {
            die( 'NO NEW' );
            
            $folder = "/home/produksjon/cewe0514.csv";    
         
            foreach( glob( $folder ) as $liste ){
               $lines = explode( PHP_EOL, file_get_contents( $liste ) );
            }
            
            $start = date( 'Y-m-d' );
            $stop = date( 'Y-m-d', strtotime( '+1 year' ));
            
            
            foreach( $lines as $line ){
                
                
                $info = explode( ';' ,  $line );
                //$info = $line;
                //$info = trim(preg_replace('/\s\s+/', '', $line));
                $info = preg_replace("/[\\n\\r]+/", "", $line);
                
                print_r($info);

                $userid = DB::query( "SELECT uid from brukar WHERE brukarnamn = ?", $info )->fetchSingle();                
                
                Util::Debug( $userid );
                if( $userid ){
                    
                    $subscription = new Subscription();
                    $subscription->uid = $userid;
                    $subscription->type_subscription = 2;
                    $subscription->registered = $start;
                    $subscription->start = $start;
                    $subscription->valid_to = $stop;
                    $subscription->active = 1;
                    $subscription->save();
                    
                    
                    Util::Debug( $subscription);

                }else{
                    //Util::debug( $info[15] );
                }
            }
         
        }
      
    }


?>