<?PHP
   chdir( dirname( __FILE__ ) );
   include '../../bootstrap.php';

   config( 'website.config' );
   config( 'website.countries' );
   config( 'production.settings');
   import( 'system.cli' );
   import( 'storage.util' );




   class OrderImportScript extends Script {

      //private $imagepath = '/mnt/storage/export/norwaycup_folk_til_japanphoto/';
      private $imagepath = '/mnt/storage/export/Dyrebar/srgb/';
      
      //private $rfuser =  851094; //noreaycuo
      private $rfuser = 926508; //dyrefoto;
      
      
      
      public function Main(){
         die( "dyrebar ");
         $i = 0;
         
         Login::byUserId( $this->rfuser );
         
         Session::id( md5( rand( 0, 999999999 ) ) );
         $i= 0;
         
         foreach ( glob( $this->imagepath . "*.JPG") as $imagefile ){
            $i++;
            
            //$image = new Imagick($imagefile);
            
            //$profiles = $image->getImageProfiles ( "*" , false );
            
            $exif = $this->output_iptc_data( $imagefile );
            util::Debug( $exif );
            $album_exist = DB::query( "SELECT aid FROM bildealbum WHERE namn ilike ? AND uid = ?", $exif, $this->rfuser )->fetchSingle();
            
            if( !empty( $album_exist ) ){
               $album = new Album( $album_exist );
               //util::Debug( $album );
            }else{
               
               $album = new Album();
               $album->uid = $this->rfuser ;
               $album->namn = $exif;
               $album->access = 0;
               $album->for_sale = true;
               $album->for_download = false;
               $album->save();
               //util::Debug( $album );
            }

            
            try{
               $filename = basename( $imagefile );
               $image  = $imagefile;
               
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
            
            
            //if( $i > 200) die();
         }
         
         
         
      }
      
      
      Public function Main_norwaycup(){
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
      
      private function output_iptc_data( $image_path ) {
         
          $size = getimagesize ( $image_path, $info);
         
          
          $kake = iptcparse($info["APP13"]);
          
          return $kake["2#005"][0];
          
          util::Debug( $kake["2#005"][0] );
          
           
          $ret = array();
          
          if(is_array($info)){
              $iptc = iptcparse($info["APP13"]);
              foreach (array_keys($iptc) as $s) {
                  $c = count ($iptc[$s]);
                  for ($i=0; $i <$c; $i++)
                  {
                     $ret[] =  $iptc[$s][$i];
                  }
              }
          }
          
          return $ret[1];
      }
      

  
   }
   
   CLI::Execute();

?>