<?PHP
   chdir( dirname( __FILE__ ) );
   include '../../bootstrap.php';

   config( 'website.config' );
   config( 'website.countries' );
   config( 'production.settings');
   import( 'system.cli' );
   import( 'storage.util' );




   class OrderImportScript extends Script {

      private $imagepath = '/mnt/storage/export/norwaycup_folk_til_japanphoto/';
      
      private $rfuser =  851094;
      
      
      Public function Main(){
die();
         $textfile = $this->imagepath . "kobling_epost_bilder_norwaycup_til_japanphoto.csv";
         Login::byUserId( $this->rfuser );
         
         Session::id( md5( rand( 0, 999999999 ) ) );
         $i= 0;
         foreach ( file( $textfile ) as $kunde  ){
            
            $info = explode(';' , $kunde );
   
            //$exists_check = DB::query( 'SELECT uid FROM brukar WHERE brukarnamn = ?', $info[0] )->fetchSingle();

            
            $album_exist = DB::query( "SELECT aid FROM bildealbum WHERE namn ilike ? AND uid = ?", $info[0], $this->rfuser )->fetchSingle();
            
            if( !empty( $album_exist ) ){
               
               $album = new Album( $album_exist );
               
               //util::Debug( $album );
               
               
            }else{
               
               $album = new Album();
               $album->uid = $this->rfuser ;
               $album->namn = $info[0];
               $album->access = 0;
               $album->for_sale = true;
               $album->for_download = false;
               $album->save();
               //util::Debug( $album );
            }
            
                     
            try{
               $filename = $info[1];
               $image  = $this->imagepath . $info[1];
               
               util::Debug( "uploading " . $image );      
               
               $imageid = StorageUtil::uploadImage(
                  $this->rfuser,
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
         
         echo $i;
         
         //util::Debug( readfile( $this->imagepath . "kobling_epost_bilder_norwaycup_til_japanphoto.csv") );
         
         //Login::byUserId( $this->rfuser );
   
         //Session::id( md5( rand( 0, 999999999 ) ) );

         
         
         
         
      }
      

  
   }
   
   CLI::Execute();

?>