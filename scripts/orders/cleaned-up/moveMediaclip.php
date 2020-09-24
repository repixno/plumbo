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


    class moveMcehance extends Script {
        
        public $infolder = '/mnt/perfectlyclear/Mediaclip_input/';
        public $output = '/mnt/perfectlyclear/Mediaclip_output/';
        
       // public $source = '/mnt/mediaclip/enhance/';
        //public $dest = '/mnt/mediaclip/ready/';
         public $source = ' /mnt/clipproducer2/mediaclip/enhance/';
        public $dest = ' /mnt/clipproducer2/mediaclip/ready/';
        
        
       
        Public function Main(){
            
            // Grab all files from the desired folder
            $files = glob( $this->source . '*' );
            
            // Sort files by modified time, latest to earliest
            // Use SORT_ASC in place of SORT_DESC for earliest to latest
            array_multisort(
                array_map( 'filemtime', $files ),
                SORT_NUMERIC,
                SORT_ASC,
                $files
            );
            
            
            foreach( $files as $file ){
                
                
                
                if( is_dir($file )  ){
                    
                    Util::Debug( $file );

                    if( count(glob($this->infolder . "*")) === 0 && file_exists($file. "/proceed.txt" )){
                         $innfolder =  $this->infolder . pathinfo(  $file, PATHINFO_BASENAME );
                         //Util::Debug( $innfolder );
                         unlink( $file. "/proceed.txt" );
                         
                         if( !file_exists( $innfolder ) ){
                             mkdir( $innfolder, 0664, 1 );
                         }
                         
                         foreach( glob( $file . '/*') as $image ){
                             //Util::Debug($image);
                             //Util::Debug($innfolder . "/" . pathinfo( $image, PATHINFO_BASENAME  ));
                             copy( $image , $innfolder . "/" . pathinfo( $image, PATHINFO_BASENAME  ) );  
                             unlink($image);
                         }
                         
                         touch( $innfolder . '/proceed.txt' );
                         
                         rmdir( $file );
                         
                     }else{
                        
                        
                        
                        foreach( glob($this->infolder. "*") as  $oldfolder  ){
                            if (time()-filemtime($oldfolder) > 5 * 60) {
                                foreach( glob( $oldfolder . '/*') as $delimage ){
                                    unlink($delimage);    
                                }
                                rmdir( $oldfolder );
                            } 
                        }
                        
                        
                        exit();
                     }

                }
                
                
                
                
            }
            
            exit(0);
            
            
            
        }
    }
   

   CLI::Execute();

?>