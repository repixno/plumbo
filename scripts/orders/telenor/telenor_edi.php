<?PHP

/******************************************
* Script for handling Telenor Capture  Logitra
***************************************/

chdir( dirname( __FILE__ ) );
include '../../../bootstrap.php';
config( 'website.config' );
import( 'system.cli' );

class XmlScript extends Script {

    private $orderfolder = "/mnt/telenor/orderxml/";
	
	

    Public function Main(){
		
        foreach( glob($this->orderfolder   . '*') as $order ){
			$i = 1;
            $xmlfile = $order;
			
			
			
            if (file_exists( $xmlfile ))
            {
              
				
                
                $orderxml = simplexml_load_file( $xmlfile );
                $partner = $orderxml->partner->name; //Navn
                $lb_navn = $orderxml->delivery->address->name; // Adresse 
                $lb_adresse = $orderxml->delivery->address->address1; // adresse 
                $lb_postalcode= $orderxml->delivery->address->postalcode; // postnr
                $lb_city= $orderxml->delivery->address->city; //sted
                $lablinkmobile = $orderxml->user->mobile;  // mobil
                $lablinkmemail = $orderxml->user->email; // email
                $lb_customerno = $orderxml->order; // customer number
				
				
				$lablinkorderstr =  $orderxml->orderparts->orderpart->files->file->clientref;  // mobil
                	$telenorordrenr = substr($lablinkorderstr, 8 ,8);	
					
					$file = basename($xmlfile);
					
                $kontaktperson="Telenor Capture" ;
                $lablinkorderid =  (string)$orderxml['uuid']  ;
				$telenorordrenr2 = substr($orderxml['uuid'],  0 , 8);
                
                	$xmlfile2= substr($xmlfile, 0, strlen($xmlfile) );
					
					
				foreach( $orderxml->orderlines->orderline  as $orderline ){
					
                     $logistra_array = (            
                        $telenorordrenr2 . ";" .  //Telenor kundenr
                        $lablinkorderid . ";" .  // Lablink Ordrenr
                        $lb_navn . ";" .         //Navn
                        $lb_adresse . ";" .      // Adresse
                        'Adresse2'. ";" . //Adresse2
                        $lb_postalcode . ";" . //Postnr
                        $lb_city . ";" . //Sted
                        'NO'. ";" . //Land
                        $kontaktperson . ";" . //Kontaktperson
                        'Telefon'. ";" . //Telefon
                        $lablinkmobile . ";" . //Mobilnr
                        $lablinkmemail . ";" . // Epost
						 'Bring'. ";" . //Transportør
                        'Fraktalterativ' . ";" . //Traktalternativ
                        'Frakttjeneste'. ";" . //Frakttjeneste
                        'Vekt'  . ";" . //Vekt
                        'Antall-kolli' . ";" . //Antall-kolli
                        'Volum' . ";" . //Volum
                        'Varebeskrivelse'. ";" . //Varebeskrivelse
                        'Mottakers-referanse'. ";" . //Mottakers-referanse
                        'Avsenders-referanse'. ";" . //Avsenders-referanse
                        'Melding-til-mottaker' . ";"  //Melding-til-mottaker
						
						
						
						//$lablinkorderid2= substr($lablinkorderstr, 8 ,35);
						
						
						
						
                   );
                     
                       $move = "/mnt/telenor/finish/";
					 
					
					//  util::Debug( $xmlfile2 );
					  util::Debug( $xmlfile );
					    util::Debug( $move );
						 util::Debug( $xmlfile );
					  util::Debug( $file ); //filnavn
					  // 	 $finish ="/home/produksjon/LOGISTRA/ferdig";
					// exit;
					
                     $orderfolder = "/home/produksjon/LOGISTRA/7888";
                      $orderfolder2 = "/home/produksjon/LOGISTRA/9461";
                         $orderfolder3 = "/home/produksjon/LOGISTRA/7888/7981";
				     $orderinfo =  $logistra_array. "\r\n";
					
				rename ( $xmlfile,  "$move$file" ) ;
                    
                     file_put_contents("$orderfolder/telenor.txt", $orderinfo , FILE_APPEND | LOCK_EX);
                 file_put_contents("$orderfolder2/telenor.txt", $orderinfo , FILE_APPEND | LOCK_EX);
                  file_put_contents("$orderfolder3/telenor.txt", $orderinfo , FILE_APPEND | LOCK_EX);

					
					
													
						}
						
						// Move the file
      
		$i++;
						
						
						
						
					}
					
				}
			
				  
				die();
                
            } 
            
        }
CLI::Execute();

?>
