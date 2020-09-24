<?PHP

/******************************************
* Script for handling Telenor Capture  Logitra
***************************************/

chdir( dirname( __FILE__ ) );
include '../../../bootstrap.php';
config( 'website.config' );
import( 'system.cli' );

class XmlScript extends Script {

    private $orderfolder = "/mnt/fotonorden/orderxml/";
	
    Public function Main(){
		
        foreach( glob($this->orderfolder   . '*') as $order ){
			$i = 1;
            $xmlfile = $order;		
            if (file_exists( $xmlfile ))
            { 
                $orderxml = simplexml_load_file( $xmlfile );
                $partner = $orderxml->partner->name; //Navn
                $originatingauthorizationid = $orderxml->originatingauthorizationid; //Navn
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
                $kontaktperson= $orderxml->partner->name;
                $lablinkorderid =  (string)$orderxml['uuid']  ;
				$telenorordrenr2 = substr($orderxml['uuid'],  0 , 8);
                $xmlfile2= substr($xmlfile, 0, strlen($xmlfile) );
					
				foreach( $orderxml->orderlines->orderline  as $orderline ){
                    
    }
    
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
 
                if ($originatingauthorizationid=="aeedde27") {
                         
                   $move1 = "/mnt/fotonorden/FerdigIris/";
                    util::Debug( $xmlfile );
                    util::Debug( $move );
                    util::Debug( $xmlfile );
                    util::Debug( $file ); //filnavn
                    util::Debug( $partner ); //partner
                    util::Debug( $move1 ); //IRIS
                    util::Debug( $originatingauthorizationid ); //partner
                    //exit;
                     $orderfolder1 = "/home/produksjon/LOGISTRA/9127";
			    $orderfolderdest2 = "/home/produksjon/LOGISTRA/6462";
				     $orderinfo =  $logistra_array. "\r\n";
					
			rename ( $xmlfile,  "$move1$file" ) ;
                    
                     file_put_contents("$orderfolder1/irisfoto.txt", $orderinfo , FILE_APPEND | LOCK_EX);
					   file_put_contents("$orderfolderdest2/irisfoto.txt", $orderinfo , FILE_APPEND | LOCK_EX);  
                    
                }
                  
                  if ($originatingauthorizationid=="46452712") {   
                     
                       $move2 = "/mnt/fotonorden/FerdigFotoNorden/";
                        //  util::Debug( $xmlfile2 );
                        util::Debug( $xmlfile );
                        util::Debug( $move );
                        util::Debug( $xmlfile );
                        util::Debug( $file ); //filnavn
                        util::Debug( $partner ); //partner
                        util::Debug( $partner ); //partner
                        util::Debug( $move2 ); //FOTONORDEN
                        util::Debug( $originatingauthorizationid ); //partner
                        //exit;
                     $orderfolder3 = "/home/produksjon/LOGISTRA/9041";
					 $orderfolderdest4 = "/home/produksjon/LOGISTRA/9460";
				     $orderinfo =  $logistra_array. "\r\n";
					
		 rename ( $xmlfile,  "$move2$file" ) ;
                    
                     file_put_contents("$orderfolder3/fotonorden.txt", $orderinfo , FILE_APPEND | LOCK_EX);
					 file_put_contents("$orderfolderdest4/fotonorden.txt", $orderinfo , FILE_APPEND | LOCK_EX);	
                  }
													

		$i++;		
						
					}
					
				}
			
				  
				die();
                
            } 
            
        }
CLI::Execute();

?>
