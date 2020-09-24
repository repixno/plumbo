<?PHP

/***********************************
 *
 * Srcript for å laste opp cdupload
 * blir kjørt som www-data på susanne
 *
 *
 *
 *
 *
 ***********************************************/

   chdir( dirname( __FILE__ ) );
   include '../../bootstrap.php';

   config( 'website.config' );
   config( 'website.countries' );
   config( 'production.settings');
   import( 'system.cli' );
   import( 'storage.util' );


   class cdUploadScript extends Script {

   
        private $imagepath = '/home/produksjon/cdupload/'; 
      
            public function Main(){
                
                $i = 0;
                
                foreach ( glob( $this->imagepath . "*") as $userfolder ){
                    
                    if( file_exists( $userfolder . "/finished" ) ) continue;
                    if( file_exists( $userfolder . "/user_not_found" ) ) continue;
                    if( file_exists( $userfolder . "/working" ) ) continue;
                    
                   
                    file_put_contents( $userfolder . "/working", date( 'Y-m-d H:i:s ') );
                    
                    Util::Debug( $userfolder );
                    
                    if( is_dir( $userfolder ) ){
                        
                        Session::id( md5( rand( 0, 999999999 ) ) );
                        $i++;
                        
                        $username = basename( $userfolder );
                        
                        Util::Debug( $username );
                        
                        $user = User::fromUsernameAndPortal( $username );
                        
                        Util::Debug( $user );
                        
                        if( !$user->uid ){
                            file_put_contents( $userfolder . "/user_not_found", date( 'Y-m-d H:i:s ') );
                            continue;
                        }
                        
                        Login::byUserId( $user->uid );
                        
                        foreach( glob( $userfolder . "/*" ) as $imagefolder ){
                            
                            if( file_exists( $imagefolder . "/finished" ) ) continue;
                            
                            if( is_dir( $imagefolder  ) ){
                                
                                $albumname = basename( $imagefolder );
                                
                                $album = new Album();
                                $album->uid = $user->uid ;
                                $album->namn = $albumname;
                                $album->save();
                                
                                Util::Debug( $album );
                                
                                if(  count(  glob( $imagefolder . "/*" ) > 0 ) ){
                                
                                    foreach( glob( $imagefolder . "/*")  as $image ) {
                                        if(exif_imagetype($image) === IMAGETYPE_JPEG){
                                            
                                            try{
                                                $filename = basename( $image );
                                                util::Debug( "uploading " . trim( $image ) );      
                                                
                                                $imageid = StorageUtil::uploadImage(
                                                    $user->uid ,
                                                    $album->aid,
                                                    trim( $image ), 
                                                    'jpeg', 
                                                    $filename
                                                );
                                                 
                                                util::Debug( $imageid );
                                            
                                             }catch ( Exception $e){
                                                 util::Debug( "FEIL MED OPPLASTING BILDE " . $image  );
                                                 util::Debug( $e->getMessage() );
                                             }
                                        }
                                    }
                                }
                                
                                file_put_contents( $imagefolder . "/finished", date( 'Y-m-d H:i:s ') );
                                    
                            }
                            
                            
                            
                        }
                        
                        file_put_contents( $userfolder . "/finished", date( 'Y-m-d H:i:s ') );
                        unlink( $userfolder . "/working" );
                        //Login::logout();
                    }
                 
                      
                }
         
         die( "cdupload ");
        }
      

  
   }
   
   CLI::Execute();

?>