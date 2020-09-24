<?PHP

   /******************************************
    * Script for handling EDI Files
    ***************************************/
   
  //sudo mount -t cifs //10.64.1.176/edi /mnt/edi -o username=produksjon,password=produksjon,gid=1001,uid=1001

   chdir( dirname( __FILE__ ) );
   include '../../bootstrap.php';

   config( 'website.config' );
   import( 'system.cli' );

   class EdiImportScript extends Script {

      
      
      
      Public function Main(){

            
            
            $file = '/home/produksjon/reedfoto/homefolder/bildeopplasting/Halbrend Skule 2015/Liste/Halbrend B-U_liten_jpg.csv';
            
        
            
            $lines = explode( PHP_EOL, file_get_contents( $file ) );
            
            
            

            foreach( $lines as $line ){
                
                
                $exploded = explode(  ';', $line );
                
                //Util::Debug($exploded);
                
                $kake = DB::query( "SELECT id FROM reedfoto_album WHERE identifier = ?", $exploded[9]  )->fetchSingle();
            
            
                if( $kake ){
                    DB::query( "update  reedfoto_album set identifier = ? WHERE id = ?", utf8_decode( $exploded[7] ), $kake   )->fetchSingle();
                }
            
                //Util::Debug($kake);
                
                //$arr[$exploded[9]] = $exploded[7];
                
                
            }
            
            
 
            
            
            
      }
      

   
   }
   

   CLI::Execute();

?>
