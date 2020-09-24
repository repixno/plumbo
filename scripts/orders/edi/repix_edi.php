<?PHP
## Script som lager edi txt fil til logistra
## Autor @adeleskjerdalstorøy
   chdir( dirname( __FILE__ ) );
   include '../../../bootstrap.php';

   config( 'website.config' );
   config( 'website.countries' );
   import( 'system.cli' );
   
   class OrderExportProspectScript extends Script {
      
      Public function Main(){
         
         $now = date( 'Y-m-d H:i:s' );
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
         AND ho.kampanje_kode IN ('FOTOPIX', 'FOTONO', 'UP-001' , 'ST-001' , 'RP-001' , 'Netlife' )
         AND ho.uid not in ('1030157 ')
         AND ho.pris   > 1		
         AND ho.tidspunkt BETWEEN ? AND ?
         ORDER BY ho.uid", $yesterday, $now )->fetchAll( DB::FETCH_ASSOC ); 
        // ekskluder smil foto 1030157 fra lablinken
        
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
               
               // Om bestillinga kjem fra Hovsmarked skal den lese ut ein angitt epost til logistra
               if ($userid == '1039217') {
               $depost = 'post@hovs.no';
               } else {
               $depost = $ret['depost'];
               }  
                          
               
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
               // Angitt lokalasjon for filene
               $dest= "/home/produksjon/LOGISTRA/7886/";
               $dest2= "/home/produksjon/LOGISTRA/9463/";
               $lines = file("/home/produksjon/LOGISTRA/7886/repix.txt", FILE_IGNORE_NEW_LINES  | FILE_SKIP_EMPTY_LINES);
               $lines = array_unique($lines);
               if (!in_array($exportfile, $lines)) {
               file_put_contents("$dest/repix.txt", $exportfile , PHP_EOL . FILE_SKIP_EMPTY_LINES . FILE_APPEND  | LOCK_EX);
               file_put_contents("$dest2/repix.txt", $exportfile , PHP_EOL . FILE_SKIP_EMPTY_LINES . FILE_APPEND  | LOCK_EX);
               }
               
               
               $i++; 
               
               }
      
      Private function removelinebreak( $string ){
         
         $string = trim( preg_replace( '/\s+/', ' ', $string ) );  
         
         
         return $string;
         
      }
   }
         
   CLI::Execute();

?>
