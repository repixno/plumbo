<?PHP
   chdir( dirname( __FILE__ ) );
 include '../../../bootstrap.php';

   config( 'website.config' );
   config( 'website.countries' );
   import( 'system.cli' );
   
   class OrderExportProspectScript extends Script {
      
      Public function Main(){
         
           $now = date( 'Y-m-d H:i:s' );
       //  $yesterday = date( 'Y-m-d H:i:s' , strtotime( date( 'Y-m-d H:i:s' )  . '-1  day' ) );
         $yesterday = date( 'Y-m-d H:i:s' , strtotime( date( 'Y-m-d H:i:s' )  . '-50  day' ) );    
       
         
                 $user = DB::query( "SELECT DISTINCT(ho.uid),
											  	ho.ordrenr,
													hk.dnavn,
													hk.dadresse1,
													hk.dpostnr,
													hk.dsted,
													hk.dtelefon,
													hk.depost
													
                                     
                                     
                                FROM historie_ordrelinje hol
                           LEFT JOIN historie_ordre ho
                                  ON ho.ordrenr = hol.ordrenr
                           LEFT JOIN kunde k
                                  ON ho.uid = k.uid
                           LEFT JOIN historie_delivery hd 
                                  ON hd.ordrenr = ho.ordrenr
                           LEFT JOIN historie_kunde hk
                                  ON hk.ordrenr = ho.ordrenr
                                     WHERE ho.to_production IS NOT NULL
										
													  AND  ho.uid='1370892'
                               
											
                                  AND ho.tidspunkt BETWEEN ? AND ?
                                  
                            ORDER BY ho.uid", $yesterday, $now )->fetchAll( DB::FETCH_ASSOC ); 
        
            
           Util::Debug( count( $query ) );
            
            foreach( $user as $ret ){
					$userid = $ret['uid'];
					$ordrenr = $ret['ordrenr'];
					$dnavn = $ret['dnavn'];
					$dadresse1 = $ret['dadresse1']; 
					$dpostnr = $ret['dpostnr'];
					$dsted = $ret['dsted'];
					$dmphone = $ret['dtelefon'];
					$depost = $ret['depost'];
               
               
             //  $newsletter  = $ret['newsletter'] == 1? 1:0;
               
               //$kode = $ret['kode'];
					
					
			
               
					
               
              /* switch( $kode ){
                  case 'SK-001':
                     $portal="SPARFOTO";
                     $email = $ret['brukarnamn'];
                     break;
                  case 'DM-001':
                     $portal = 'Dinmerkelapp';
                     $email = $ret['brukarnamn'];
                     break;
                 

                 
                  default:
                     $portal = 'Eurofoto';
                     $email = $ret['brukarnamn'];
               }*/
               
               $exportfile .= sprintf( "%s;%s;%s;%s;%s;%s;%s;%s;\n",
												  $this->removelinebreak( $userid ) ,
												$this->removelinebreak( $ordrenr ),
												$this->removelinebreak( $dnavn ),
												$this->removelinebreak( $dadresse1 ),
												$this->removelinebreak( $dpostnr ),
												$this->removelinebreak( $dsted ),
												
													$this->removelinebreak( $dmphone) ,
												$this->removelinebreak( $depost) );
																}
				
				
				
				
			
            
            // $filename = "Kunder_" . $today . ".edi";
			
			$filename = "test" . ".txt";
            
            $dest= "/home/produksjon/LOGISTRA/7876/";
				$dest2= "/home/produksjon/LOGISTRA/9464/";
            
		
		
			
		$lines = file("/home/produksjon/LOGISTRA/7876/utestemme.txt", FILE_IGNORE_NEW_LINES  | FILE_SKIP_EMPTY_LINES);
		if (!in_array($exportfile, $lines)) {
		
        //    file_put_contents( "/home/produksjon/LOGISTRA/test3/" . $filename , utf8_decode($exportfile. $exportfile2) );
				
				  file_put_contents("$dest/utestemme.txt", $exportfile .  FILE_IGNORE_NEW_LINES  . FILE_SKIP_EMPTY_LINES . LOCK_EX);
				  file_put_contents("$dest2/utestemme.txt", $exportfile .  FILE_IGNORE_NEW_LINES  . FILE_SKIP_EMPTY_LINES . LOCK_EX);
				}
	//		rename($pathen, $outfile);
			
			
			//iconv -f iso-8859-1 -t utf-8 <$pathen >$outfile;
			
			
            
         //  die();
         
          
          
          
          
          
         	$i++; 




      }
      
      
      
      Private function removelinebreak( $string ){
         
         $string = trim( preg_replace( '/\s+/', ' ', $string ) );  
         
         
         return $string;
         
      }
   }
         
   CLI::Execute();

?>
