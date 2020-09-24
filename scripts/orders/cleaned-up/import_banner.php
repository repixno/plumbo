<?PHP
   chdir( dirname( __FILE__ ) );
   include '../../bootstrap.php';

   config( 'website.config' );
   config( 'website.countries' );
   config( 'production.settings');
   import( 'system.cli' );
   import( 'website.order' );
   import( 'production.container');
   import( 'production.conversions' );
   import( 'website.order.template' );
   import( 'website.giftordertemplate' );
   import( 'website.giftpagetemplate' );
   import( 'website.giftordertext' );
   import( 'website.giftorderclipart' );

   class OrderImportScript extends Script {
      
      public $tidspunkt = '';
      
      private $imageserver = "http://therese.eurofoto.no/production/index.php";
      private $securecode = "p-dLuxTl30qH-AhKICmehN2DalJsJNGjjAGrzom1DNQ";   
      
      private $clipartpath = '/data/global/clipart';
      private $fontpath = '/home/httpd/www.eurofoto.no/webside/font' ;
      private $originaltemplatespath = '/data/global/maler/orginal';
      
      private $imgserver = "10.64.1.134"; //eva
      
      Public function Main(){
         
         $orders = array();
         $oldest = '2015-06-20';
         
         $orderDownloadQuery = sprintf( "SELECT ordrenr, tidspunkt, source FROM historie_ordre WHERE 
                                          tidspunkt > '%s' AND 
                                          to_production > '%s' AND
                                          download_began_at IS NULL 
                                          ORDER BY ordrenr LIMIT 1", $oldest, $oldest );

                                          //denne her kan du velge å importere spesifikk ordre
       //  $orderDownloadQuery = sprintf( "SELECT ordrenr, tidspunkt, source FROM historie_ordre WHERE ordrenr in ( 2206581,2206578)" );
                                          
                                          
         //while ( $kake =  DB::query( $orderDownloadQuery )->fetchAll( DB::FETCH_ASSOC )  ) {
         foreach( DB::query( $orderDownloadQuery )->fetchAll( DB::FETCH_ASSOC ) as $item){
               //$item = $kake;
               $id = DB::query( "SELECT id FROM historie_ordre WHERE ordrenr = ?", $item['ordrenr'] )->fetchSingle();
               $order = new Order( $id );
               $order->download_began_at =  date( 'Y-m-d H:i:s' );
               $order->save();  
               util::Debug( $item);
            //foreach ( DB::query( "SELECT ordrenr, tidspunkt FROM historie_ordre where ordrenr = 1478854" )->fetchAll( DB::FETCH_ASSOC ) as $item ) {
               $hb_id = null;
               $hm_id = null;
            
               $hb_id = DB::query( "SELECT ordrenr FROM historie_bilde WHERE ordrenr = ?", $item['ordrenr'] )->fetchSingle();
               $hm_id = DB::query( "SELECT ordrenr FROM historie_mal WHERE ordrenr = ?", $item['ordrenr'] )->fetchSingle();               
               
               if( $hb_id || $hm_id ){
                  $this->tidspunkt = $item['tidspunkt'];
                  if( $item['source'] == 'MANUAL' ){
                     $this->fetchManualFiles( $item['ordrenr'], $item['tidspunkt'] );
                  }else{
                     $this->fetchFiles( $item['ordrenr'], $item['tidspunkt'] );
                  }
                  $this->PrintOrder( $item['ordrenr'] );
                  $this->MalOrder( $item['ordrenr'], $item['source'] );
               }
               
               $rfmodules = DB::query( "SELECT * FROM historie_ordrelinje WHERE artikkelnr IN ( SELECT artnr from article where grouptype  = 70 ) AND ordrenr = ?", $item['ordrenr'] )->fetchAll(DB::FETCH_ASSOC);
                  
               if( count($rfmodules)  > 0 ){
                  
                  $this->reedfotoModules($rfmodules);
                  
               }
               
               
               $order->download_ended_at =  date( 'Y-m-d H:i:s' );
               $order->save();
               $orderDownloadQuery = sprintf( "SELECT ordrenr, tidspunkt, source FROM historie_ordre WHERE 
                                       tidspunkt > '%s' AND 
                                       to_production > '%s' AND
                                       download_began_at IS NULL 
                                       ORDER BY ordrenr LIMIT 1", $oldest, $oldest );
               
         }
      }
      
      
      
      public function reedFotoModules( $rfmodules ){
         import( 'production.reedfotomodules' );
         
         foreach($rfmodules as $rfmodule ){
            
            $attributes = unserialize( $rfmodule['attributes']);
            
            $orderid = $rfmodule['ordrenr'];
            $id = $rfmodule['id'];
            $artikkelnr = $rfmodule['artikkelnr'];
            
            if( $artikkelnr != 7291 ){
               if( $attributes['bid'] ){
                  $imagefile = $this->fetchReedfotoImage( $attributes['bid'], $orderid, $id, $artikkelnr  );
               }
               if( $attributes['gruppebid'] ){
                  $gruppeimage = $this->fetchReedfotoImage( $attributes['gruppebid'], $orderid, $id, $artikkelnr  );
               }
               $convert = new reedFotoModuleConvert();
               
               $convert->convertimage( $imagefile, $gruppeimage, $rfmodule, $attributes['bw'] );
            }
            //Util::Debug($rfmodule['id']);          
            //Util::Debug($rfmodule['artikkelnr']);
            //Util::Debug( $attributes['bid']);
            
         }
      }
      
      
      
      public function fetchReedfotoImage( $imageid, $orderid, $id, $artikkelnr  ){
         
         $orderdate = DB::query( "SELECT tidspunkt FROM historie_ordre WHERE ordrenr = ?", $orderid )->fetchSingle();
         
         $date = date( 'Y-m-d' , strtotime( $orderdate ) );
         if( !file_exists( sprintf( '/home/produksjon/webspool/reedfotokundar/%s', $orderid ) ) ){
            mkdir( sprintf( '/home/produksjon/webspool/reedfotokundar/%s', $orderid ), 0755 , true );
         }
         
         try{
            $imagepath = DB::query( "SELECT filnamn FROM bildeinfo WHERE bid = ?", $imageid)->fetchSingle();
 
            
            //$url = $this->imageserver .  "?path=" . base64_encode($imagepath) . "&secure=" . $this->securecode;
            
            $savepath = sprintf( '/home/produksjon/webspool/reedfotokundar/%s/%s/autoedit', $orderid, $artikkelnr );
            
            
            Util::debug($savepath);
            
            if( !file_exists( $savepath ) ){
               mkdir( $savepath , 0755 , true );
            }
            
            $img = sprintf( '%s/org_%s_%s.jpg', $savepath , $id, $imageid  );
            
            $hashcode = DB::query( "SELECT hashcode FROM bildeinfo WHERE bid = ?", $image['bid'] )->fetchSingle();
            $checksum = null;
            $count = 0;
            //Util::Debug("hash" . $hashcode );
            //while( $checksum != $hashcode && $count < 100 ){
               $count ++;
               if( file_exists( $img )  ){
                  $checksum = md5_file($img);
                  if( $checksum ==  $hashcode ){
                     continue;
                  }else{
                     unlink( $img );
                  }
               }
               
               Util::Debug('/data/bildearkiv/' . $imagepath);
               
               $connection = ssh2_connect('eva-local.eurofoto.no', 22);
               ssh2_auth_password($connection, 'www', 'Kefir4ever!');
               ssh2_scp_recv( $connection, '/data/bildearkiv/' . $imagepath, $img );
               
               //file_put_contents( $img, file_get_contents($url) );
               $checksum = md5_file($img);
               
               if( $count > 1 ){
                  util::Debug( $count . "#BUUUUUUUUUUUUUUUUUUUUUUUUUGS " );
               }
               //Util::Debug( "Checksumm = " . $checksum );
            //}
            
            $pathinfo = pathinfo( $img );

            Util::Debug($pathinfo['extension']);
            
            if( $pathinfo['extension'] == 'png' ){
               
               
               $image['filename'] = str_replace( '.png', '.jpeg', $image['filename'] );
               DB::query( "UPDATE historie_bilde SET filename = ? WHERE id = ?", $image['filename'], $image['id'] );
               
               $img2jpg = sprintf( '%s/%s', $savepath , $image['filename']  );  
               
               $imagick = new Imagick( $img );
               $imagick->setImageFormat('jpeg');
               $imagick->writeImage( $img2jpg );
               
               
               unlink($img);
               
            }


            
         }catch( Exception $e){
            Util::Debug( $e->getMessage() );
         }
         
         return $img;
      }
      
      
      
      //place printorders at the right location
      public function PrintOrder( $orderid = 0 ){
         $printcontainer = Settings::Get( 'production' , 'printsize' );
         //$malcontainer = Settings::Get( 'production' , 'malprintsize' );
         $articles = array();
         $container = array();
         foreach ( DB::query( "SELECT *  FROM historie_ordrelinje WHERE ordrenr=?", $orderid )->fetchAll( DB::FETCH_ASSOC ) as $item ) {
            foreach ( $printcontainer as $size=>$articlelist ){
               if( in_array( $item['artikkelnr'] , $articlelist ) ){
                  $container[$size][] = $item['artikkelnr'];
               }
            }
         }

         Util::Debug($container);
         
         if( count( $container ) ){   
            $containerPrints = new Container();
            $containerPrints->C8Print( $orderid,  $container );
         }
      }
      
      //place malorders at the right location
      public function MalOrder( $orderid = 0, $source = null ){
         $malcontainer = Settings::Get( 'production' , 'malprintsize' );
         $articles = array();
         $container = array();
         
         if( $soruce != 'MANUAL '){
         
            foreach ( DB::query( "SELECT distinct( artikkelnr)   FROM historie_mal WHERE ordrenr=?", $orderid )->fetchAll( DB::FETCH_ASSOC ) as $item ) {
               
               $conversion = new Conversions();
               $conversion->order_date = date('Y-m-d', strtotime(  $this->tidspunkt ));
               $conversion->order_id = $orderid;
               $conversion->article_id = $item['artikkelnr'];
               $conversion->added_at = date( 'Y-m-d H:i:s' );
               $conversion->hostname = exec( 'hostname');
               $conversion->status  = 'ready';
               $conversion->save();
   
               $container[] = $item['artikkelnr'] ;
   
            }
         }
          
         if( count( $container ) ){
            $testcontainer = new Container();
            $testcontainer->C8Mal( $orderid,  $container, $this->tidspunkt );
         }
         
      }
      
      //download orders from file server.
      public function FetchFiles( $orderid, $orderdate ){
         
         $fileserver = Settings::Get( 'production' , 'fileserver' );
         
         $id = DB::query( "SELECT id FROM historie_ordre WHERE ordrenr = ?", $orderid )->fetchSingle();
         
         $order = new Order( $id );
         $order->download_began_at =  date( 'Y-m-d H:i:s' );
         $order->save();
         $date = date( 'Y-m-d' , strtotime( $orderdate ) );
         if( !file_exists( sprintf( '/home/produksjon/webspool/%s', $date ) ) ){
            mkdir( sprintf( '/home/produksjon/webspool/%s', $date ), 0755 , true );
         }
         
         
         
         $images = DB::query( "SELECT * FROM historie_bilde WHERE ordrenr = ?", $orderid )->fetchAll( DB::FETCH_ASSOC );
         
         if( is_array( $images ) ){
            foreach( $images as $image ){
               
               try{
                  $imagepath = DB::query( "SELECT filnamn FROM bildeinfo WHERE bid = ?", $image['bid'] )->fetchSingle();
       
                  
                  //$url = $this->imageserver .  "?path=" . base64_encode($imagepath) . "&secure=" . $this->securecode;
                  
                  $remotefile = '/data/bildearkiv/' . $imagepath;
                  
                  util::Debug( "remotefile-" . $remotefile );
                  
                  $savepath = sprintf( '/home/produksjon/webspool/%s/%s/%s', $date, $orderid, $image['artikkelnr'] );
                  
                  if( !file_exists( $savepath ) ){
                     mkdir( $savepath , 0755 , true );
                  }
                  
                  if( strpos( $image['filename'] , '.pjpeg')){
                     $image['filename'] = str_replace( '.pjpeg', '.jpeg', $image['filename'] );
                     DB::query( "UPDATE historie_bilde SET filename = ? WHERE id = ?", $image['filename'], $image['id'] );
                  }
                  if( strpos( $image['filename'] , '.x-png')){
                     $image['filename'] = str_replace( '.x-png', '.png', $image['filename'] );
                      DB::query( "UPDATE historie_bilde SET filename = ? WHERE id = ?", $image['filename'], $image['id'] );
                  }
                  if( strpos( $image['filename'] , '.x-citrix-pjpeg')){
                     $image['filename'] = str_replace( '.x-citrix-pjpeg', '.jpeg', $image['filename'] );
                      DB::query( "UPDATE historie_bilde SET filename = ? WHERE id = ?", $image['filename'], $image['id'] );
                  }
                  
                  $img = sprintf( '%s/%s', $savepath , $image['filename']  );            
                  $hashcode = DB::query( "SELECT hashcode FROM bildeinfo WHERE bid = ?", $image['bid'] )->fetchSingle();
                  $checksum = null;
                  $count = 0;
                  //Util::Debug("hash" . $hashcode );
                  while( $checksum != $hashcode && $count < 100 ){
                     $count ++;
                     if( file_exists( $img )  ){
                        $checksum = md5_file($img);
                        if( $checksum ==  $hashcode ){
                           continue;
                        }else{
                           unlink( $img );
                        }
                     }
                     
                     //$connection = ssh2_connect('therese.eurofoto.no', 22);
                     $connection = ssh2_connect($this->imgserver, 22);
                     //ssh2_auth_password($connection, 'www-data', 'Kefir4ever!');
                     ssh2_auth_password($connection, 'www', 'Kefir4ever!');
                     
                     ssh2_scp_recv( $connection, '/data/bildearkiv/' . $imagepath, $img);
                     
                     //file_put_contents( $img, file_get_contents($url) );
                     $checksum = md5_file($img);
                     
                     if( $count > 1 ){
                        util::Debug( $count . "#BUUUUUUUUUUUUUUUUUUUUUUUUUGS " );
                     }
                     //Util::Debug( "Checksumm = " . $checksum );
                  }
                  
                  
                  
                  $pathinfo = pathinfo( $img );

                  Util::Debug($pathinfo['extension']);
                  
                  if( $pathinfo['extension'] == 'png' ){
                     
                     
                     $image['filename'] = str_replace( '.png', '.jpeg', $image['filename'] );
                     DB::query( "UPDATE historie_bilde SET filename = ? WHERE id = ?", $image['filename'], $image['id'] );
                     
                     $img2jpg = sprintf( '%s/%s', $savepath , $image['filename']  );  
                     
                     $imagick = new Imagick( $img );
                     $imagick->setImageFormat('jpeg');
                     $imagick->writeImage( $img2jpg );
                     
                     
                     unlink($img);
                     
                  }
                  
               }catch( Exception $e){
                  Util::Debug( $e->getMessage() );
               }
               
            }
         }
         
         $mals = DB::query( "SELECT * FROM historie_mal WHERE ordrenr = ?", $orderid )->fetchAll( DB::FETCH_ASSOC );
         
         
         Util::Debug($orderid);
         $fileserver = "remus.eurofoto.no"; 
         //$fileserver = "monica.eurofoto.no"; 
         if( is_array( $mals ) ){
            foreach( $mals as $mal ){
               
               try{
                  if( DB::query( "SELECT orderid from ukeplan_orders where orderid = ?", $orderid  )->fetchSingle()  > 0  ){
                     
                     util::Debug("downloading mals");
                     util::Debug($mals);
                     exec ("/usr/local/bin/rsync --ignore-missing-args -a $fileserver::ordrer/\*/print_download/$date/$orderid /home/produksjon/webspool/$date");
                  }else{
                     $this->produceTemplate( $mal['lopenummer'] );
                  }
               }catch( Exception $e ){
                  $e->getMessage();
               }
               
            }
             
            
         }

         
         
         //exec ("rsync -a $fileserver::ordrer/\*/print_download/$date/$orderid /home/produksjon/webspool/$date");

         $order->download_ended_at =  date( 'Y-m-d H:i:s' );
         $order->save();

      }
      
      public function fetchManualFiles( $orderid, $orderdate ){
         
         util::debug( "manual order ");
         
         $id = DB::query( "SELECT id FROM historie_ordre WHERE ordrenr = ?", $orderid )->fetchSingle();
         $userid = DB::query( "SELECT uid FROM historie_ordre WHERE ordrenr = ?", $orderid )->fetchSingle();
         $kampanje_kode = DB::query( "SELECT kampanje_kode FROM historie_ordre WHERE ordrenr = ?", $orderid )->fetchSingle();
         
         $order = new Order( $id );
         $order->download_began_at =  date( 'Y-m-d H:i:s' );
         $order->save();
         $date = date( 'Y-m-d' , strtotime( $orderdate ) );
         if( !file_exists( sprintf( '/home/produksjon/webspool/%s', $date ) ) ){
            mkdir( sprintf( '/home/produksjon/webspool/%s', $date ), 0755 , true );
         }
         
         foreach ( DB::query( "SELECT artikkelnr, manualpath, filename, text FROM historie_bilde WHERE ordrenr = ?", $orderid )->fetchAll() as $res){
            list( $artnr, $path, $filename, $text  ) = $res;
            
            
            if( $kampanje_kode == '1030157 '){
               if( !file_exists( sprintf( '/home/produksjon/webspool/Smil_preview/%s/%s_%s', $date, $orderid, $artnr ) ) ){
                  mkdir( sprintf( '/home/produksjon/webspool/Smil_preview/%s/%s_%s', $date, $orderid, $artnr ), 0755 , true );
               }
               $destination = sprintf( '/home/produksjon/webspool/Smil_preview/%s/%s_%s/%s', $date, $orderid , $artnr ,$filename ); 
            
            }
            
            
         
              if( $kampanje_kode == '1030136' && '1030138' && '1030146' && '1030148' && '1030149' &&'1030150' && '1030151' && '1030152' && '1030154' && '1030155' && '1030156'  && '1030159' && '1030160' && '1033715' && '1033716' && '1033724' &&  '1105325' && '1114803' && '1120915' && '1131937' && '1132808' )
                    
             
             {
               if( !file_exists( sprintf( '/home/produksjon/webspool/Netlife_preview/%s/%s_%s', $date, $orderid, $artnr ) ) ){
                  mkdir( sprintf( '/home/produksjon/webspool/Netlife_preview/%s/%s_%s', $date, $orderid, $artnr ), 0755 , true );
               }
               $destination = sprintf( '/home/produksjon/webspool/Netlife_preview/%s/%s_%s/%s', $date, $orderid , $artnr ,$filename ); 
            
            }
            
            
            else{
               if( !file_exists( sprintf( '/home/produksjon/webspool/%s/%s/%s', $date, $orderid, $artnr ) ) ){
                  mkdir( sprintf( '/home/produksjon/webspool/%s/%s/%s', $date, $orderid, $artnr ), 0755 , true );
               }
              $destination = sprintf( '/home/produksjon/webspool/%s/%s/%s/%s', $date, $orderid , $artnr ,$filename ); 
            }
            
            //system( 'ln -s "' . $path . '" ' . $destination );
            
            $exploded = explode( ',',  $text );
            
            if( $userid == 983136 ){
               import( 'production.foveamodules' );
               import( 'production.foveatemplate' );
               config( 'production.fovea' );
               
               $templatename = trim( $exploded[1] );
               $modulname =(int)$exploded[1];
               
               
               $template = Settings::Get( 'fovea' , 'template' );
               $module = Settings::Get( 'fovea' , 'modules' );
               
               $prevdestination = sprintf( '/home/produksjon/fovea_preview/%s/%s_%s/%s', $date, $orderid , $artnr ,$filename );
               
               if( $template[$templatename] ){
                  $templated = new foveaTemplate();
                  $templated->convertimage( $path, $prevdestination, $template[$templatename] );
                  $container[] = $artnr;
                  $testcontainer = new Container();
                  $testcontainer->C8Mal( $orderid,  $container, $date );
               }
               else if($module[$modulname]){
                  $modul = new foveamodules();
                  $modul->convertimage( $path, $destination, $module[$modulname], $prevdestination );
                  $container[] = $artnr;
                  $testcontainer = new Container();
                  $testcontainer->C8Mal( $orderid,  $container, $date );
               }
               else{
                  if( !file_exists( dirname( $prevdestination ) ) ){
                     mkdir( dirname( $prevdestination ), 0755, true );
                  }
                  
                  if( file_exists( $prevdestination ) ){
                      unlink( $prevdestination );
                  }
                  
                  copy( $path , $prevdestination );
                  $container[] = $artnr;
                  $testcontainer = new Container();
                  $testcontainer->C8Mal( $orderid,  $container, $date );
                  
                  if( file_exists( $destination ) ){
                      unlink( $destination );
                  }
                  copy( $path , $destination );
           
               }   
            }
            else{
               
               try{
                  copy( $path , $destination );
               }catch( Exception $e ){
                  Util::Debug(  $e->getMessage() );
               }
             
            }
             
            
         }
         
         
         $mals = DB::query( "SELECT * FROM historie_mal WHERE ordrenr = ?", $orderid )->fetchAll( DB::FETCH_ASSOC );
         
         if( is_array($mals) ){
            foreach( $mals as $mal ){
               
               $artnr = $mal['artikkelnr'];
               $filename = $mal['filnamn'];
               $path = $mal['text'];
               
               if( file_exists( $path )){
                  
                  
                  if( is_dir( $path )  ){
                     $path =  glob( $path . '/*' );                     
                  }
                  
                  
                  if( $kampanje_kode == '1030157'){
                     if( !file_exists( sprintf( '/home/produksjon/webspool/Smil_preview/%s/%s_%s', $date, $orderid, $artnr ) ) ){
                        mkdir( sprintf( '/home/produksjon/webspool/Smil_preview/%s/%s_%s', $date, $orderid, $artnr ), 0755 , true );
                     }
                     $destination = sprintf( '/home/produksjon/webspool/Smil_preview/%s/%s_%s/%s', $date, $orderid , $artnr ,$filename ); 
                  }
                  
                  
               
                    
                    
                    if ($kampanje_kode ==  '1030136' && '1030138' && '1030146' && '1030148' && '1030149' &&'1030150' && '1030151' && '1030152' && '1030154' && '1030155' && '1030156' && '1030159' && '1030160' && '1033715' && '1033716' && '1033724'  &&  '1039217' &&  '1034717' &&  '1034721' &&  '1034722' && '1034920' && '1034922' && '1034957' && '1043366' && '1035956' && '1034929' && '1034743' && '1043368' && '1034911' && '1034952' &&'1041409' && '1034917' && '1037991' && '1037998' && '1090600' &&'1105325' && '1114803' && '1120915' && '1131937' && '1132808')
                   {
                     if( !file_exists( sprintf( '/home/produksjon/webspool/Netlife_preview/%s/%s_%s', $date, $orderid, $artnr ) ) ){
                        mkdir( sprintf( '/home/produksjon/webspool/Netlife_preview/%s/%s_%s', $date, $orderid, $artnr ), 0755 , true );
                     }
                     $destination = sprintf( '/home/produksjon/webspool/Netlife_preview/%s/%s_%s/%s', $date, $orderid , $artnr ,$filename ); 
                  }
                  
                  
                  
                  else{
                     if( !file_exists( sprintf( '/home/produksjon/webspool/%s/%s/%s', $date, $orderid, $artnr ) ) ){
                        mkdir( sprintf( '/home/produksjon/webspool/%s/%s/%s', $date, $orderid, $artnr ), 0755 , true );
                     }
                     $destination = sprintf( '/home/produksjon/webspool/%s/%s/%s/%s', $date, $orderid , $artnr ,$filename );
                  }
                  Util::Debug( $orderid );
                  Util::Debug( $artnr );
                  Util::Debug( $date );
                  Util::Debug( $filename );
                  Util::Debug($destination);
                  
                   Util::Debug( $path);
                   
                   if( is_array( $path ) ){
                     
                     foreach( $path as $pa ){
                        copy( $pa , $destination );
                     }
                     
                     
                   }else{
                     copy( $path , $destination );  
                   }
                   
                  
                  
               }
            }
         }
         
         $order->download_ended_at =  date( 'Y-m-d H:i:s' );
         $order->save();
         
      }
      
         
      /**
       * Produce compilation scripts for making template order
       * and put them in the necessary directories.
       *
       * @param DBOrderTemplate $ordertemplate
       * @return boolean
       * 
       * @todo move sRGB.icm to ef3 folder
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      private function produceTemplate( $lopenummer ) {
         
         Util::Debug( $lopenummer );
         
         //$ordertemplate = DBOrderTemplate::fromFieldValue( array( 'lopenummer' =>  $lopenummer ), 'DBOrderTemplate', false  );
         //$ordertemplate = new DBOrderTemplate::fromFieldValue( array( 'lot' =>  1054618, 'orderid' => 1478720 ), 'DBOrderTemplate'  );
         
         $ordertemplate = DB::query( "SELECT * FROM historie_mal WHERE lopenummer = ?" , $lopenummer )->fetchAll( DB::FETCH_ASSOC );
         
         //Util::Debug( $ordertemplate );
         
         $ordertemplate = end( $ordertemplate );
         
         //Util::Debug( $ordertemplate );
         
         //if( $ordertemplate instanceof DBOrderTemplate && $ordertemplate->isLoaded() ) {
            
            //$image               = new Image( $ordertemplate->imageid );
            $image               = new DBObject( $ordertemplate[bid] );
            
            
            Util::Debug( $image );
            
            $filename            = $image->filename;
            $filetype            = $image->filetype;
            $title               = $image->title;
            $lot                 = $ordertemplate->lot;
            $unique              = sprintf( "%03d", $ordertemplate[lopenummer]  );
            $targetfilename      = $ordertemplate[filnamn];
            $originalfilename    = "org_$unique.$filetype";
            $templateorderfiles  = '';
            $oldroot = '/home/httpd/www.eurofoto.no/';
            
            try {
               
               $torder                 = new GiftOrderTemplate( $ordertemplate[lopenummer] );
               $partition              = explode( '/', $image->filename );
               $partition              = reset( $partition );
               $orderdirectory         = "/home/produksjon/webspool";
               
               
               // Create production directories to put files in
               if( !is_dir( $orderdirectory ) ) {
                  mkdir( $orderdirectory, 0750 );
               }
               $orderdirectory.= "/" . date( 'Y-m-d', strtotime( $this->tidspunkt ) );
               if( !is_dir( $orderdirectory ) ) {
                  mkdir( $orderdirectory, 0755 );
               }
               $orderdirectory.= "/". $ordertemplate[ordrenr];
               if( !is_dir( $orderdirectory ) ) {
                  mkdir( $orderdirectory, 0755 );
               }
               $orderdirectory.= "/" . $ordertemplate[artikkelnr];
               if( !is_dir( $orderdirectory ) ) {
                  mkdir( $orderdirectory, 0755 );
               }
               if( !is_dir( $orderdirectory."/autoedit" ) ) {
                  mkdir( $orderdirectory."/autoedit", 0755 );
               }
               
               Util::Debug( $orderdirectory );
               
               if( file_exists( $orderdirectory."/autoedit/".$originalfilename ) ) {
              	 unlink( $orderdirectory."/autoedit/".$originalfilename  );
		
		}    
                  //$url = $this->imageserver .  "?path=" . base64_encode($filename) . "&secure=" . $this->securecode;
                
                
               
                  
                $img =  $orderdirectory."/autoedit/".$originalfilename;
                $connection = ssh2_connect($this->imgserver, 22);
            ssh2_auth_password($connection, 'www', 'Kefir4ever!');
            
         //  ssh2_scp_recv( $connection, '/data/bildearkiv/' . $filename , $img);
            
            

                 
                 
                  //file_put_contents( $img, file_get_contents($url) );
                  
                  //link( $this->imagepath."/".$filename, $orderdirectory."/autoedit/".$originalfilename );
               
               
               //chmod( $orderdirectory."/autoedit/".$originalfilename, 0664 );
               
               
               if($torder->malid == 0){
                  
                  $commands = array();
                  
                  $editor_x = $torder->editor_x;
                  $editor_y = $torder->editor_y;
                  
                  $fullsize_x = $image->x;
                  $fullsize_y = $image->y;
                  
                  $editRatio_x = $fullsize_x / $editor_x;
                  $editRatio_y = $fullsize_y / $editor_y;
                  
                  $command_x = round( $torder->x * $editRatio_x);
                  $command_y = round( $torder->y * $editRatio_y );
                  $command_dx = round( $torder->dx * $editRatio_x);
                  $command_dy = round( $torder->dy * $editRatio_y);
                  
                  $cropRatio = $command_dx / $command_dy;
                  
                  $printsize_x = round($torder->printsize_x * 94.5);
                  $printsize_y = round($torder->printsize_y * 94.5);
                  
                  
                  $commands[] = sprintf("convert -size %sx%s -units PixelsPerInch -density 240x240 xc:white org.png", $command_dx, $command_dy);
                  $commands[] = sprintf("composite -geometry -%s-%s %s org.png -profile /home/httpd/www.eurofoto.no/webside/grafikk/sRGB.icm org.png", $command_x, $command_y, $originalfilename);
                  
                 $printtype = $torder->printtype;
                 
                 $gallery = explode("x", $printtype);
                  
                  if(count($gallery) == 2){       
                     $gallery_x = $gallery[1];
                     $gallery_y = $gallery[0];
                                     
                     $fullimage = 'org.jpg';
                     
                     $framewidth = round(94.5  * 4);
                     
                     $each_printsize_x = (($printsize_x - (($gallery_x+1) * $framewidth)) / $gallery_x) + ($framewidth * 2);
                     $each_printsize_y = (($printsize_y - (($gallery_y+1) * $framewidth)) / $gallery_y) + ($framewidth * 2);

                     $commands[] = sprintf("convert org.png -resize %sx%s -quality 100 %s", $printsize_x, $printsize_y, $fullimage );
                     $commands[] = sprintf("convert -size %sx%s -units PixelsPerInch -density 240x240 xc:white -quality 100 orgeach.jpg", $each_printsize_x, $each_printsize_y);
                     $commands[] = sprintf("convert -size %sx%s -units PixelsPerInch -density 240x240 -background white -bordercolor black -border 1 -pointsize 16 -gravity South caption:'%s' canvas.tif",$each_printsize_x + 280, $each_printsize_y + 280, $this->orderid );
                     
                     for($x1 = 0; $x1 < $gallery_x; $x1++){
                        
                        $crop_x =  ($each_printsize_x - $framewidth)  *  $x1;  
                        for($y = 0; $y < $gallery_y; $y++ ){
                           $crop_y = ($each_printsize_y - $framewidth)  *  $y;
                           $commands[] = sprintf("composite -geometry -%s-%s %s orgeach.jpg org%s.tif", $crop_x, $crop_y, $fullimage, $x1.$y);
                           $commands[] = sprintf("convert org%s.tif -bordercolor black -border 1 -quality 97 org%s.jpg", $x1.$y, $x1.$y);
                           $commands[] = sprintf("composite -geometry +141+141 org%s.jpg canvas.tif -profile /home/httpd/www.eurofoto.no/webside/grafikk/sRGB.icm -units PixelsPerInch -density 240x240 -quality 97 ../%s_%s", $x1.$y, $x1.$y , $targetfilename);
                           
                        }
                     }   
                  }
                  else{
                     $commands[] = sprintf("convert org.png -resize %sx%s -bordercolor black -border 1 -quality 100 org.jpg", $printsize_x, $printsize_y);
                     $commands[] = sprintf("convert -size %sx%s -units PixelsPerInch -density 240x240 -background white -bordercolor black -border 1 -pointsize 16 -gravity South caption:'%s' canvas.tif",$printsize_x + 280, $printsize_y + 280, $this->orderid  );
                     $commands[] = sprintf("composite -geometry +141+141 org.jpg canvas.tif -profile /home/httpd/www.eurofoto.no/webside/grafikk/sRGB.icm -units PixelsPerInch -density 240x240 -quality 97 ../%s", $targetfilename);
                     $commands[] = "rm canvas.tif";
                  }
                  file_put_contents( "$orderdirectory/autoedit/$unique"."_command_list.sh", implode( "\n", $commands ) );                  
                  
               }
               else{
                  
                  $artnr = $ordertemplate['artikkelnr'] ;
                  
                  if( $artnr == 7303 ){
                     $torder->malid = 2694;
                  }
               
                  $tpagedata              = GiftPageTemplate::fromTemplateIdAndPageId( $torder->malid, $torder->page );
                  
                  $templatefile           = "$this->originaltemplatespath/$tpagedata->fullsize_src";
                  $templatetarget         = $tpagedata->fullsize_src;
   
                  
                  $templateorderfiles .= $ordertemplate[artikkelnr]  ."/autoedit/$orginalfilename ";
                  if( !file_exists( $orderdirectory."/autoedit/$templatetarget" ) ) {
                     copy( $templatefile, $orderdirectory."/autoedit/".$templatetarget );
                     chmod( $orderdirectory."/autoedit/".$templatetarget, 0664 );
                     $templateorderfiles .= "$artnr/autoedit/$templatetarget ";
                  }
                  
                  
                  // Handle uploaded files here? Original code
                  /*$client_info["uploaded_images"] = convertToUploadedImages( $client_info );
                  if($fildata[0]["owner_uid"] == 61224 && $client_info["uploaded_images"][$bid] == 1){
                     //Ok situation
                  }
                  else if($fildata[0]["owner_uid"]){
                     //Ok situation, but want to move the image please
                     sql_simpleExec("UPDATE bildeinfo SET owner_uid=$shopuid WHERE bid=$bid;");
                  }
                  else{
                     must_pictureaccess($bid);
                  }*/
                  
                  
                  
                  // Ok let's start calculating stuff
                  
                  // Rotate image
                  if( $torder->rotate == 90 || $torder->rotate == 270 ) {
                     $tmp = $image->x;
                     $image->x = $image->y;
                     $image->y = $tmp;
                  }
                  
                  $ratio_image = $image->x / $torder->dx;
                  
                  $ratio = $tpagedata->fullsize_x / $torder->editor_x;
                  
                  $clip_dx = ceil( $tpagedata->fullsize_pos_dx * $ratio_image );
                  $clip_dy = ceil( $tpagedata->fullsize_pos_dy * $ratio_image );
                  
                  if( $clip_dx > $clip_dy ) {
                     $clip_dy = $clip_dx;
                  } else{
                     $clip_dx = $clip_dy;
                  }
                  
                  
                  // Calculate offsets
                  $offset_clip_x = ( $torder->x - $tpagedata->fullsize_pos_x  ) * $ratio_image;
                  $offset_clip_x = ceil( $offset_clip_x );
                  if( $offset_clip_x >= 0 ) {
                     $offset_clip_x = "+$offset_clip_x";
                  }
                  $offset_clip_y = ( $torder->y - $tpagedata->fullsize_pos_y ) * $ratio_image;
                  $offset_clip_y = ceil( $offset_clip_y );
                  if( $offset_clip_y >= 0 ) {
                     $offset_clip_y = "+$offset_clip_y";
                  }
                  
                  
                  $offset_x = $tpagedata->fullsize_pos_x;
                  $offset_x = ceil( $offset_x );
                  if( $offset_x >= 0 ) {
                     $offset_x = "+$offset_x";
                  }
                  $offset_y = $tpagedata->fullsize_pos_y;
                  $offset_y = ceil( $offset_y );
                  if( $offset_y >= 0 ) {
                     $offset_y = "+$offset_y";
                  }
                  $fullclip_x = $tpagedata->fullsize_pos_dx;
                  $fullclip_y = $tpagedata->fullsize_pos_dy;
                  if( $fullclip_x > $fullclip_y ) {
                     $fullclip_y = $fullclip_x;
                  } else{
                     $fullclip_x = $fullclip_y;
                  }
                  
                  
                  //Util::Debug( $this->imagepath."/".$filename );
                  
                  // Start creating the compilation script
                  //if( file_exists( $this->imagepath."/".$filename ) ) {
                     
                     $textcommands = array();
                     $tempfiles = array();
                     $private_kode = $unique;
                     $final = $private_kode.".tif";
                     if( $clip_dx > 0 &&  $clip_dy ){
                        $textcommands[] = "convert -size $clip_dx" . "x" . "$clip_dy xc:white $private_kode.jpg";
                        $textcommands[] = "composite -geometry $offset_clip_x$offset_clip_y  $originalfilename $private_kode.jpg -profile $oldroot". "webside/grafikk/sRGB.icm $final";
                     
                        $textcommands[] = "convert -size " . $tpagedata->fullsize_pos_dx . "x" . $tpagedata->fullsize_pos_dy . " xc:white background1.jpg";
                        $textcommands[] = "convert background1.jpg $final -geometry $fullclip_x" . "x" . "$fullclip_y -composite $final";
      
                        $textcommands[] = "convert -size " . $tpagedata->fullsize_x . "x" . $tpagedata->fullsize_y . " xc:white background2.jpg";
                        $convertcommand = "convert background2.jpg $final -geometry $offset_x$offset_y -composite -quality 100";
                        
                     }else{
                        $textcommands[] = "convert -size " . $tpagedata->fullsize_x . "x" . $tpagedata->fullsize_y . " xc:white background2.jpg";
                        
                        $convertcommand = "convert background2.jpg -quality 100";
                        
                     }
                     
                     
                     if( $tpagedata->fullsize_src ) {
                        $convertcommand .= " " . $tpagedata->fullsize_src . " -geometry +0+0 -composite";
                     }
   
                     $final = $private_kode.".jpg";
                     $convertcommand .= " $final";
                     
                     $textcommands []= $convertcommand;
                     $textcommands []= "convert $final -quality 97 $private_kode" . "_image_and_template.jpg";
   
                     $texts     = GiftOrderText::enumTextsFromTemplateIdAndPageId( $torder->malorderid, $torder->page );
                     $clipart   = GiftOrderClipart::enumClipartFromTemplateIdAndPageId( $torder->malorderid, $torder->page );
                     
                     // Cliparts
                     $n = count( $clipart );
                     $prevfile = $tmp7;
                     $clipcommands = array();
                     $counter = 0;
                     
                     for( $i=0; $i<$n; $i++ ) {
                        try{
                           $fullx   = $clipart[$i]['x'];
                           $fully   = $clipart[$i]['y'];
                           $fulldx  = $clipart[$i]['dx'];
                           $fulldy  = $clipart[$i]['dy'];
                           if( $fulldx < 0 || $fulldy < 0 ) {
                              continue;
                           }
                           
                           $clipid = $clipart[$i]['clipid'];
                           $outfile = $private_kode . "t$i" . ".png";
                           $tempfiles []= $outfile;
                           
                           if( $fullx < 0 ) {
                              $fullx = $fullx;
                           }else{
                              $fullx = "+" . $fullx;
                           }
                           if( $fully < 0 ) {
                              $fully = $fully;
                           }else{
                              $fully = "+" . $fully;
                           }
                           
                           $textcommands []= "composite -geometry !" . $fulldx . "x!" . $fulldy . $fullx . "$fully " . $clipid ."_" . $clipart[$i]['clipnr'] . ".png $final $final";
                           $clipsrc = $this->clipartpath . "/$clipid".".png";
                           $clipdest = $orderdirectory."/autoedit/$clipid"."_".$clipart[$i]["clipnr"].".png";
                           copy( $clipsrc, $clipdest );
                           $prevfile = $outfile;
                           $counter++;
                        }
                        catch( Exception $e ){
                           Util::Debug($e->getMessage());
                        } 
                     }
                     
                     // Texts
                     $n = count( $texts );
                     $counter = 0;
                     for( $i=0;$i<$n;$i++ ){
                        
                        $fullx   = $texts[$i]["x"];
                        $fully   = $texts[$i]["y"];
                        $fulldx  = $texts[$i]["dx"];
                        $fulldy  = $texts[$i]["dy"];
                        if( $fulldx < 0 || $fulldy < 0 ) {
                           continue;
                        }
                        
                        
                        Util::Debug($texts[$i]);
                        
                        $text    = $texts[$i]["text"];
                        $color   = $texts[$i]["color"];
                        $font    = $texts[$i]["font"];
                        $gravity = $texts[$i]["gravity"];
                        
                        if( !empty( $gravity )  ){
                           $shadow = true;
                        }else{
                           $shadow = false;
                        }
                        
                        switch( $gravity ) {
                           case "West":
                              break;
                           case "Center":
                              break;
                           case "East":
                              break;
                           default:
                              $gravity = "West";
                        }
   
                        if( $text == ""  || $text == 'Din text här') {
                           continue;
                        }
                        
                        $text = str_replace( "XXNYLINJEXX", "\\n", $text );
                        $text = str_replace( "XXOGXX", "&", $text );
                        $text = str_replace( "\r", "", $text );
                        
                        
                        
                        
                        if( $ordertemplate['artikkelnr'] == 7454 || $ordertemplate['artikkelnr'] == 7474 ){
                           
                           if( $shadow ){
                              if( $fullx < 0 ) {
                                 $fullxshadow = $fullx+20;
                              }else{
                                 $fullxshadow = "+" . ( $fullx + 20 ) ;
                              }
                              
                              if( $fully < 0 ) {
                                 $fullyshadow = $fully + 20;
                              }else{
                                 $fullyshadow = "+" . ( $fully + 20 );
                              }
                              
                              if( $ordertemplate['artikkelnr'] == 7454 || $ordertemplate['artikkelnr'] == 7474 ){
                                 
                                 $text = " " . $text . " ";
                                 
                              }
                              
                              $textcommands []= "convert -background transparent -pointsize 300 -fill \"#333\" -font $this->fontpath/$font.ttf -gravity $gravity label:\"$text\" -repage +0+0 -resize !$fulldx" . "x!$fulldy text$unique.$counter.png";
                              $textcommands[] = "convert  text$unique.$counter.png -channel RGBA -blur 0x18 text$unique.$counter.png";
                              $textcommands []= "composite -compose over -geometry $fullxshadow"."$fullyshadow text$unique.$counter".".png $final $final";
                           }
                              
                           $textcommands []= "convert -background transparent -pointsize 300 -fill \"#$color\" -font $this->fontpath/$font.ttf -gravity $gravity label:\"$text\" -repage +0+0 -resize !$fulldx" . "x!$fulldy text$unique.$counter.png";
                                                      
                        }else{
                           $textcommands []= "convert -background transparent -pointsize 300 -fill \"#$color\" -font $this->fontpath/$font.ttf -gravity $gravity label:\"$text\" -trim -repage +0+0 -resize !$fulldx" . "x!$fulldy text$unique.$counter.png";
                        } 
                        
                        if( $fullx < 0 ) {
                           $fullx = $fullx;
                        }else{
						   $fullx = "+" . $fullx;
						}
                        if( $fully < 0 ) {
						   $fully = $fully;
                        }else{
						   $fully = "+" . $fully;
						}
   
                        
                        $textcommands []= "composite -compose over -geometry $fullx"."$fully text$unique.$counter".".png $final $final";
                        
                        $counter++;
                        
                     }
                     
                     if( $this->artnr == 7196 ){
                        $textcommands [] = "composite -pointsize 18 label:$this->orderid  $final $final";
                     }
                     
                      if( $this->artnr == 7756 ){
                        $textcommands [] = "composite -pointsize 18 label:$this->orderid  $final $final";
                     }
                     
                     
                     if( $ordertemplate['artikkelnr']  == 7475 ){
                        
                        $attr = DB::query( "SELECT attributes FROM historie_ordrelinje WHERE ordrenr = ? AND artikkelnr = ? AND malid = ?", $ordertemplate['ordrenr'] , $ordertemplate['artikkelnr'], $lopenummer  )->fetchSingle();
                        
                        $attr = unserialize( $attr );
                        
                        Util::Debug($attr);
                        
                        $color = $attr['fontcolor'];
                        $r = 0;
                        
                        $textcommands[] = "convert background2.jpg " . $tpagedata->fullsize_src . " -layers flatten -quality 100  letterbackground.jpg";
                        
                        foreach( str_split($attr['name']) as $letter ){
                           $letter = strtoupper( $letter );
                           
                           $fontfile = $this->fontpath . '/source-sans-pro.regular.ttf';
                           
                           $textcommands[] = "convert letterbackground.jpg  -font '$fontfile' -gravity North -pointsize 1400 -fill '$color' -annotate +0+0 '$letter'  -quality 100 ../letter$lopenummer-$r.jpg";
                           
                           
                           $r++;   
                        }
                        
                        
                     }

                     $textcommands []= "convert $final -profile $oldroot" . "webside/grafikk/sRGB.icm -units PixelsPerInch -density " . $tpagedata->fullsize_dpi . "x" . $tpagedata->fullsize_dpi. " -quality 99 ../$targetfilename";
                     $textcommands []= "rm -f " . "$private_kode.tif $private_kode.jpg";
                     $n = count( $tempfiles );
                     
                     util::Debug( $textcommands );
                     //util::Debug( "$orderdirectory/autoedit/$unique"."_command_list.sh" );
                     
                     file_put_contents( "$orderdirectory/autoedit/$unique"."_command_list.sh", implode( "\n", $textcommands ) );
                  }
                  
                  // Save the production script to disc
                  
                  
                  #util::debug( "$orderdirectory/autoedit" );
                  #die();
               //}
            } catch( Exception $e ) {
               
               // Do some error handling here
               util::debug( $e->getMessage() );
               
            }
           
            
         //}
         
         return true;
         
      }
  
   }
   
   CLI::Execute();

?>
