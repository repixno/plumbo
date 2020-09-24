<?PHP
   chdir( dirname( __FILE__ ) );
   include '../../bootstrap.php';

   config( 'website.config' );
   config( 'website.countries' );
   config( 'production.settings');
   import( 'system.cli' );
   import( 'storage.util' );




   class OrderImportScript extends Script {

      private $imagepath = '/mnt/storage/export/images/upload/';
      
      private $rfuser = 842373;
      
      
      Public function Main(){
         die();
         Login::byUserId( $this->rfuser );
   
         Session::id( md5( rand( 0, 999999999 ) ) );
         foreach ( glob( $this->imagepath . '*') as $folder ){
            
            
            if( !file_exists( $folder . '/done.txt' ) ){
               
               //create new album
               $album = new Album();
               $album->uid = $this->rfuser;
               $album->namn = basename( $folder );
               $album->access = 2;
               $album->for_sale = true;
               $album->for_download = false;   
               $album->save();
            
               util::Debug( $album );
            
               //PermissionManager::current()->grantAccessTo( $album->aid, 'album', PERMISSION_OWNER );
               
               util::Debug( "finns");
                           
               $folder = $folder . '/';
            
               foreach ( glob( $folder . '*.JPG') as $image ){
                  
                  try{
                     $filename = basename( $image );
                     util::Debug( $image );
                     
                     StorageUtil::uploadImage(
                        $this->rfuser,
                        $album->aid,
                        $image, 
                        'jpeg', 
                        $filename
                     );
                  }catch ( Exception $e){
                     util::Debug( "FEIL MED OPPLASTING BILDE " . $image  );
                     util::Debug( $e->getMessage() );
                  }
                 
               }
               
               file_put_contents( $folder . '/done.txt', date( 'Y-m-d H:i:s') );
            }
            
            
         }
         
         
         
         
      }
      

  
   }
   
   CLI::Execute();

?>