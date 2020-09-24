<?PHP

   /******************************************
    * Script for handling EDI Files
    ***************************************/
   


   chdir( dirname( __FILE__ ) );
   include '../../bootstrap.php';

   config( 'website.config' );
   import( 'system.cli' );

   class EdiImportScript extends Script {
      
      private $ediFolder = '/home/produksjon/merkelapp/ordreksjema/print/';
      private $doneFolder = '/mnt/edi/Done';
      private $readyFolder = '/mnt/edi/Ready';
      
      
      
      
      Public function Main(){
         
         
         $file = "/home/produksjon/merkelapp/ordreskjema/buggamerkelappa.txt";
         
         $lines = file($file);
            
            // Loop through our array, show HTML source as HTML source; and line numbers too.
        foreach ($lines as $line_num => $line) {
                echo '/home/produksjon/merkelapp/ordreskjema/*/' . trim( $line ) . ".pdf\n";
                
                
                try{
                     system( 'cp /home/produksjon/merkelapp/ordreskjema/*/' . trim( $line )  . '.pdf /home/produksjon/merkelapp/ordreskjema/print/' . trim( $line ) . '.pdf' );
                }
                catch( Excpetion $e ){
                    
                }
        
        }
        
         


            
      }
      

   
   }
   

   CLI::Execute();

?>
