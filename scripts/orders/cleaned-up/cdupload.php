<?PHP

   /******************************************
    * Script for handling CD/DVD archiveorders.
    * runst the converts script and moves
    * orders to correct location
    * 
    ***************************************/
   chdir( dirname( __FILE__ ) );
   include '../../bootstrap.php';

   config( 'website.config' );
   config( 'website.countries' );
   import( 'system.cli' );
   model( 'order.cduploads');

   class ArchiveImportScript extends Script {
      
      public $cduploadFolder = '/home/produksjon/cdupload/';

      Public function Main(){
         $allowed_array = array( 'jpg', 'jpeg' );
         $archivelist =  $this->cdUploadlist();
         
         if( is_array( $archivelist ) ){
            
            try{
               foreach ( $archivelist as $archive ){
                  
                  $targetfolder =  $this->cduploadFolder . $archive['email'];
                  
                  if( !file_exists( $targetfolder ) ){
                     mkdir( $targetfolder );
                  }
                  
                  $destination =  $targetfolder . '/' . basename( $archive['location'] );
                  
                  //util::Debug( $archive['location'] );
                  
                  //util::Debug( glob(  $archive['location'] . '/*')); 
                  
                  if( !file_exists( $destination ) ){
                     
                     mkdir( $destination );
                     foreach ( glob(  $archive['location'] . '/*') as $image){
                        
                        $pathinfo = pathinfo( $image );
                        
                        $extension =  strtolower( $pathinfo['extension'] );

                        if( in_array( $extension , $allowed_array ) ){
                           
                           copy( $image,  $destination . '/' .basename( $image ) );
                        
                        }
                        
                     }
                     
                     //exec( sprintf( "cp -r %s %s", $archive['location'] , $destination ));
                     //$this->RecursiveCopy( $archive['location'] , $destination );
                  }
                  
                  $cdupload = new DBCdUploads( $archive['id'] );
                  $cdupload->done = date( 'Y-m-d H:m:s' );
                  $cdupload->save();
                  
               }
            }catch ( Exception  $e ){ mail( 'tor.inge@eurofoto.no' , "cdorder fail" , "test "); } 
         }else util::Debug( 'no orders' );
            
      }
      
      private function cdUploadlist(){

         foreach(  DB::query( 'SELECT * FROM cduploads WHERE done IS NULL' )->fetchAll( DB::FETCH_ASSOC ) as $collections ) {
            $ret[] = $collections;
         }
         return $ret;

      
      }
   
   
   }
   

   CLI::Execute();

?>