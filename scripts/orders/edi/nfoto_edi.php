<?PHP

/******************************************
* Script for handling Telenor Capture  Logitra
***************************************/
require_once("class.get.files.repix.php");
chdir( dirname( __FILE__ ) );
include '../../../bootstrap.php';
config( 'website.config' );
import( 'system.cli' );

class XmlScript extends Script {

    private $orderfolder = "/home/repix/autodownload/production/nfoto.no/*/*/";
	// private $orderfolder = "/home/produksjon/Nfoto/Ordrer/*/*/";
    Public function Main(){
		
        foreach( glob($this->orderfolder   . 'edi.txt') as $order ){
			$i = 1;
            $nfotofile = $order;
            
            $value = file_get_contents($order);
			
            util::Debug( $nfotofile );
			
				$folder =  dirname( $nfotofile ) .	"/" ;
                	$folder2 =  dirname( $nfotofile ) .	"/" ;
                
			    util::Debug( $nfotofile );
			
            if (file_exists( $nfotofile ))
            {
              
            util::Debug( $nfotofile );
            util::Debug( $folder );
           

				     $orderinfo =  $order. "\r\n";
				//	$orderfolder2 ="/home/produksjon/LOGISTRA/8952";
			$orderfolder2 ="/home/repix/autodownload/LOGISTRA/8952";
			
			
			$lines = file("$orderfolder2/nfoto.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			
			if (!in_array($value, $lines)) {
                    //Put textcontent into Logistra folder on Zetta
                     file_put_contents("$orderfolder2/nfoto.txt", $value . "\n" ,  PHP_EOL . FILE_APPEND . FILE_IGNORE_NEW_LINES . FILE_SKIP_EMPTY_LINES | LOCK_EX);
			}

                
				//	$rem="edi_done.txt";
                    
                    // Rename the file
			//		rename("$nfotofile", "$folder$rem");
													
						}
						
						
      
		$i++;
								
						
					}
					
				}
			
				  
				
                
            }
            
            
   //    }
CLI::Execute();

?>
