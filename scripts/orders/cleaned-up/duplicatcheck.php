<?PHP

   /******************************************
    ***************************************/
   chdir( dirname( __FILE__ ) );
   include '../../bootstrap.php';

    config( 'website.config' );
   import( 'system.cli' );


   class MerkelappImportScript extends Script {
      
      public $webspoolFolder = '/home/produksjon/';
      
      Public function Main(){
        
            $a = array();
            $ia = array();
            
            $innaktive = explode("\n", file_get_contents( $this->webspoolFolder . 'Innaktive.csv'));
            $aktive  = explode("\n", file_get_contents( $this->webspoolFolder . 'Aktive.csv'));
            
            $inactivetxt = file_get_contents( $this->webspoolFolder . 'Innaktive.csv');
            
            $i=0;
            
            foreach( $aktive as $res ){
                $resarr = explode( ';' , $res );
                $a[] = $resarr;
            }
            foreach( $innaktive as $res2 ){
                $res2arr = explode( ';' , $res2);
                $ia[] = $res2arr;
            }
            //Util::Debug( $ia );
            $combined = array_merge( $a, $ia );
            
            Util::Debug( $this->returndup( $combined )   );
            
            Util::Debug( count( $combined ) );
            Util::Debug( count( array_unique( $combined )) );
            
            Util::Debug( $i );
        
        

      }
   
   
   
    public function returndup( $array ) {
                    $results = array();
                    $duplicates = array();
                    foreach ($array as $key=>$item) {
                        if (in_array($item, $results)) {
                            $duplicates[] = $item;
                        }
                        $results[] = $item;
                    }
                    return $duplicates;
                }
    }
   

   CLI::Execute();

?>