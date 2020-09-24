<?PHP
   
    import( 'pages.protected' );
    import( 'website.order' );
    import( 'website.reedfoto.reedfotoalbum');
   
    class ReedfotoAdmin extends ProtectedPage implements IView {
      
        protected $template = null;
      
        public function Execute($year = null ) {
            
            $this->template = 'partner.reedfoto.orders.schoollist';
            
            if( $year ){
                $albums = DB::query( "SELECT school from reedfoto_project  where date_trunc('year', imported ) = '".$year."-01-01 00:00:00' group by school order by school" )->fetchAll(DB::FETCH_ASSOC );

            }else{
                $albums = DB::query( "SELECT school, count(*) from reedfoto_album group by school order by school" )->fetchAll(DB::FETCH_ASSOC );
                
            }
            
            
            
            
            foreach( $albums as $album ){
                $aktivert = DB::query("SELECT count(*) from tilgangtilalbum_dedikert where aid in ( Select aid from reedfoto_album  where school = ?)", $album['school'] )->fetchSingle();
                
                $antallbilder = DB::query("SELECT count(*) from bildeinfo where aid in ( Select aid from reedfoto_album  where school = ?)", $album['school'] )->fetchSingle();
                
                $elevercount = DB::query( "SELECT count(distinct(identifier))from reedfoto_album where school = ?", $album['school'] )->fetchSingle();
            
                if( $album['school'] ){
                    $albumlist[] = array(
                                        'school' =>  $album['school'],
                                        'count' =>  $elevercount,
                                        'aktivert' =>  $aktivert,
                                        'antallbilder' => $antallbilder,
                                         
                                         ); 
                }
                
            
            }
            
            
            $this->albums = $albumlist;
         
        }
        
        
        
        public function albumlist($school, $pagination = 0){
            
            $prpage = 100;
            
            $this->template = 'partner.reedfoto.orders.albumslist';
            if( $_GET['grade'] ){
                $albums = DB::query( "SELECT * from reedfoto_album WHERE school = ? AND grade = ?", $school,  $_GET['grade'] )->fetchAll(DB::FETCH_ASSOC );
            }
            else if( $_GET['identifier'] ){
                $albums = DB::query( "SELECT * from reedfoto_album WHERE school = ? AND identifier = ?", $school,  $_GET['identifier'] )->fetchAll(DB::FETCH_ASSOC );
            }
            else if( $_GET['ename'] ){
                if( $_GET['thisproject'] ){
                    $albums = DB::query( "SELECT * from reedfoto_album WHERE school = ? AND ename ilike '%" . $_GET['ename'] . "%'", $school )->fetchAll(DB::FETCH_ASSOC );
                }else{
                    $albums = DB::query( "SELECT * from reedfoto_album WHERE ename ilike '%" . $_GET['ename'] . "%'" )->fetchAll(DB::FETCH_ASSOC );
                }
            }
            else{
                $albumcount = DB::query("SELECT count(*) from reedfoto_album WHERE school = ?", $school )->fetchSingle();
                $sides = ceil( $albumcount / $prpage );
                $offsetarray = array();
                $i = 0;
                while( $sides >= $i ){
                    
                    $iprpage = $i * $prpage;
                    if( $iprpage < $albumcount ){
                        $offsetarray[] = $iprpage;
                    }
                    $i++;
                }
                if( $pagination > 0 ){
                    $albums = DB::query( "SELECT * from reedfoto_album WHERE school = ? order by id limit 100 offset ?", $school, $pagination )->fetchAll(DB::FETCH_ASSOC );
                }else{
                    $albums = DB::query( "SELECT * from reedfoto_album WHERE school = ? order by id limit 100", $school )->fetchAll(DB::FETCH_ASSOC );
                }
            }
            $elever = array();
            
            foreach( $albums as $album ){
                $bilder = DB::query( "SELECT * FROM bildeinfo WHERE aid = ? AND deleted_at is null ORDER by bid desc", $album['aid'] )->fetchAll(DB::FETCH_ASSOC);
                $album['bilder'] = $bilder;
                $elever[] = $album;   
            }
            
            if( $albumcount  > 100 ){
                $lastelev = end( $elever );
                if( $pagination > 0 ){
                    //$this->prev =  sprintf(  '/partner/reedfoto/albums/albumlist/%s/%s' , $school, $elever[0]['id'] - 101  );
                    $this->prev = $pagination - 100;
                }
                //$this->last =  sprintf(  '/partner/reedfoto/albums/albumlist/%s/%s' , $school, $lastelev['id'] );
                if( count($elever)  == 100 ){
                  $this->last = $pagination + 100;  
                }
                
            }
            
            
            $grades = array();
            
            foreach( DB::query( "SELECT distinct( grade )  from reedfoto_album WHERE school = ?", $school )->fetchAll(DB::FETCH_ASSOC ) as $grade ){
                $grades[] = $grade['grade'];
            }
            
            asort($grades);
            
            $this->grades = $grades;

            $this->current = $pagination;
            $this->pagination = $offsetarray;
            $this->school = $school;
            $this->elever = $elever;
              
        }
        
        public function elevlist($school=null){
            
            $this->template = null;
            
            
            $school = $_POST['school'] ? urldecode( $_POST['school'] ): $school;
            
            //Util::Debug(  urldecode( $school ) );
            
            $albums = DB::query( "SELECT * from reedfoto_album WHERE school = ? order by id", $school )->fetchAll(DB::FETCH_ASSOC );
             
             
             
             
             
             echo json_encode( $albums );
             exit;
             
             
            
            
            
        }
        
        
        
        public function newstudent(){
            
            $this->template = null;
            
            
            Util::Debug( $_POST );
            
            
            $newalbum = new Reedfotoalbum();
            
            $newalbum->identifier = $_POST['identifier'];
            $newalbum->fname = $_POST['fname'];
            $newalbum->ename = $_POST['ename'];
            $newalbum->address = $_POST['address'];
            $newalbum->zip = $_POST['zip'];
            $newalbum->city = $_POST['city'];
            $newalbum->school = $_POST['school'];
            $newalbum->grade = $_POST['grade'];
            
            
            
            $album = new Album();
            $album->uid = 943910;
            $album->namn =  $_POST['fname'] . " " . $_POST['ename'] . " " . $_POST['grade'];
            $album->for_sale = 't';
            $album->for_download = 'f';
            
            $album->save();
            $newalbum->aid = $album->aid;
            $newalbum->save();
            
            
            //Util::Debug($album);
            
            //Util::Debug( $newalbum );

            
            relocate('/partner/reedfoto/albums/albumlist/'.$_POST['school'].'?identifier=' . $_POST['identifier'] );
            
            exit;
            
        }
        
        
        
        public function upload(){
            
            import( 'storage.util' );
            $this->template = null;

            $albumid = $_POST['aid'];
            
            
            $imagearray = array();
            
            $gruppe = $_POST['gruppe'] ? $_POST['gruppe'] : '';
            
            PermissionManager::current()->grantAccessTo( $albumid, 'album', PERMISSION_OWNER );
            
            
            
            
            // load the album from disk
            //$album = new Album( $albumid );
            
            
            foreach( $_FILES['images']['name'] as $key=>$imagefile ){
                
                //Util::Debug($_FILES['images']['name'][$key]);
                //Util::Debug($_FILES['images']['tmp_name'][$key]);
                //Util::Debug($_FILES['images']['type'][$key]);
                
                // store the uploaded image
                $imageid = StorageUtil::uploadImage(
                   943910,
                   $albumid,
                   $_FILES['images']['tmp_name'][$key],
                   $_FILES['images']['type'][$key],
                   $_FILES['images']['name'][$key]
                );
                
                
                $image = new Image($imageid);
                $image->identifier = $gruppe;
                $image->save();
                
                DB::query( "UPDATE bildeinfo SET aid = ?, identifier = ? WHERE bid = ? AND owner_uid = 943910",(int)$albumid , $gruppe, (int)$imageid )->fetchSingle();
                
                
                $imagearray[] = array( 'imageid' => $imageid , 'aid' => $albumid );
                
            }

            $ret =  json_encode( $imagearray );
            echo $ret;
            
            exit;
            
            
            
        }
        
        public function thumbnail( $bid ){
            header('Content-Type: image/jpeg');
            $storage = "/data/bildearkiv/";
            
            $image = DB::query("SELECT filnamn FROM bildeinfo WHERE bid = ?" , $bid )->fetchSingle();
            
            $thumb =  file_get_contents(  $storage . $image . ".preview.jpg" );
            
            echo $thumb;
            
        }
        
        public function image( $bid ){
            header('Content-Type: image/jpeg');
            $storage = "/data/bildearkiv/";
            
            $image = DB::query("SELECT filnamn FROM bildeinfo WHERE bid = ?" , $bid )->fetchSingle();
            
            $thumb = $storage . $image ;
            
            $imagick = new Imagick($thumb);
            $imagick->thumbnailImage(400, 400, true);
            header("Content-Type: image/jpg");
            echo $imagick->getImageBlob();
            
        }
        
        
        public function moveimages($school){
            
            
            $images = $_POST['image'];
            $aid = $_POST['aid'];
            $type = $_POST['type'];
            
            
            if( is_array($images) && $type  == 'delete' ){
                
                foreach( $images as $image ){
                    
                    if( $image > 100 ){
                    
                       DB::query( "UPDATE bildeinfo set deleted_at = now() WHERE bid = ?" , $image )->fetchSingle();
                        
                    }
                }            
            }
            else if( is_array($images) &&  $aid > 1 && $type == 'move' ){
                
                foreach( $images as $image ){
                    
                    if( $image > 100 ){
                    
                       DB::query( "UPDATE bildeinfo set aid = ? WHERE bid = ?" , $aid, $image )->fetchSingle();
                        
                    }
                }            
            }
            
            echo json_encode($_POST);
            
            //relocate( "/partner/reedfoto/albums/albumlist/" . $school );
            
        }
        
        public function oppdaterelev(){
            $this->template = null;
            
            $id = (int)$_POST['elevid'];
            
            $rfalbum = new DBReedfotoAlbum( $id );
            $rfalbum->identifier = $_POST['identifier'];
            $rfalbum->fname = $_POST['fname'];
            $rfalbum->grade = $_POST['grade'];
            $rfalbum->ename = $_POST['ename'];
            $rfalbum->address = $_POST['address'];
            $rfalbum->zip = $_POST['zip'];
            $rfalbum->city = $_POST['city'];
            
            $rfalbum->save();
            
            
            relocate( "/partner/reedfoto/albums/albumlist/" . $_POST['school'] );
            
            
            
            
            
            
            
        }
        
    }        
        
        
?>