<?PHP

/******************************************
* Script for handling Telenor Capture  Logitra
***************************************/

chdir( dirname( __FILE__ ) );
include '../../bootstrap.php';
config( 'website.config' );
import( 'system.cli' );

class XmlScript extends Script {

    private $orderfolder = "/home/produksjon/Nfoto/Ordrer/*/*/";
	
    Public function Main(){
		
        foreach( glob($this->orderfolder   . 'edi.txt') as $order ){
			$i = 1;
            $nfotofile = $order;
            
            $value = file_get_contents($order);
			
            util::Debug( $nfotofile );
			
			
            if (file_exists( $nfotofile ))
            {
              
			
            util::Debug( $orderline );
			util::Debug( $nfotofile );

				     $orderinfo =  $order. "\r\n";
					$orderfolder2 ="/home/produksjon/LOGISTRA/test";
			
			
			
			$lines = file("$orderfolder2/nfoto.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			
			if (!in_array($value, $lines)) {
                    //Put textcontent into Logistra folder on Zetta
                     file_put_contents("$orderfolder2/nfoto.txt", $value . "\n" , FILE_APPEND | LOCK_EX);
			}

                
					$rem="_done";
                    
                    // Rename the file
					rename("$nfotofile", "$nfotofile$rem");
													
						}
						
						
      
		$i++;
								
						
					}
					
				}
			
				  
				
                
            }
            
            
   //    }
CLI::Execute();

?>
