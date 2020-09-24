<?PHP
   chdir( dirname( __FILE__ ) );
   include '../../bootstrap.php';

   config( 'website.config' );
   config( 'website.countries' );
   import( 'system.cli' );
   
   class OrderExportProspectScript extends Script {
      
      Public function Main(){
         
            $today = date( 'Y-m-d' );
            $yesterday = date('Y-m-d', strtotime($today . ' - 1 day' )); 
            
            $exportfile = '"ID";"First name";"Last name";"Adress1";"Adress2";"PostalCode";"City";"Cellphone";"Email";"Approval";"Portal"' . "\n";
            
            $user = DB::query( "SELECT b.uid, k.fnavn, k.mnavn, k.enavn, b.brukarnamn, k.newsletter, b.kode, k.contactemail, k.telefon_mobil, k.adresse1, k.adresse1, k.postnr, k.stad
                                 FROM
                                    brukar b, kunde k
                                 WHERE
                                    k.uid = b.uid AND
                                    b.kode in ( 'SK-001' ) AND
                                    b.deleted is null
                                    ORDER by b.kode
                                    "  )->fetchAll( DB::FETCH_ASSOC );
            
            /*NOT EXISTS ( SELECT uid FROM historie_ordre where uid = b.uid AND tidspunkt > '2011-01-01' )*/
            
            Util::Debug( count( $user ) );
            
            foreach( $user as $ret ){
               
               $userid = $ret['uid'];
               $firstname = $ret['fnavn'];
               if( $ret['k.mnavn'] ){
                  $firstname .= ' ' . $ret['k.mnavn'];
               }
               $lastname = $ret['enavn'];
               $newsletter  = $ret['newsletter'] == 1? 1:0;
               
               $kode = $ret['kode'];
               
               $Adress1 = $ret['adresse1'];
               $Adress2 = $ret['adresse2'];
               $PostalCode = $ret['postnr'];
               $City = $ret['stad'];
               $Cellphone = $ret['telefon_mobil'];
               
               switch( $kode ){
                  case 'SK-001':
                     $portal="SPARFOTO";
                     $email = $ret['brukarnamn'];
                     break;
                  case 'DM-001':
                     $portal = 'Dinmerkelapp';
                     $email = $ret['brukarnamn'];
                     break;
                  case 'UP-001':
                     $portal = 'UKEPLAN';
                     $email = $ret['brukarnamn'];
                     break;
                  case 'TK-001':
                     $portal = "Takkekort";
                     $email = $ret['contactemail'];
                     break;
                  case 'FK-001':
                     $portal = "Fotokalenderne";
                     $email = $ret['contactemail'];
                     break;
                  case 'EF-VG':
                  case 'VG-997':
                     $portal = "VGfoto";
                     $email = $ret['brukarnamn'];
                     break;
                  case 'AM-997':
                     $portal = "Aftenposten foto";
                     $email = $ret['brukarnamn'];
                     break;
                  default:
                     $portal = 'Eurofoto';
                     $email = $ret['brukarnamn'];
               }
               
               $exportfile .= sprintf( "%s;%s;%s;%s;%s;%s;%s;%s;%s;%s;%s\n", $this->removelinebreak( $userid ), $this->removelinebreak( $firstname ), $this->removelinebreak( $lastname ), $this->removelinebreak( $Adress1 ) , $this->removelinebreak( $Adress2 ), $this->removelinebreak( $PostalCode ), $this->removelinebreak ( $City ) , trim ( $Cellphone ), trim ( $email ), $newsletter, trim( $portal ) );
            }
            
            $filename = "Prospect_SPARFOTO_" . $today . ".csv";
            file_put_contents( "/home/toringe/prospects/" . $filename , $exportfile );
            
            $strServer = "sftp.bringdialog.no";
            $strServerPort = "22";
            $strServerUsername = "japan_photo";
            $strServerPassword = "wz67B6qp";
            
            //connect to server
            $resConnection = ssh2_connect($strServer, $strServerPort);
            
            if(ssh2_auth_password($resConnection, $strServerUsername, $strServerPassword)){
                    //Initialize SFTP subsystem
                    $resSFTP = ssh2_sftp($resConnection);
                    
                     $resFile = fopen("ssh2.sftp://{$resSFTP}/Sparfoto/" . $filename, 'w');
                     fwrite($resFile, $exportfile);
                     fclose($resFile);
            }else{
                    echo "Unable to authenticate on server";
            }
            
      }
      
      
      
      Private function removelinebreak( $string ){
         
         $string = trim( preg_replace( '/\s+/', ' ', $string ) );  
         
         
         return $string;
         
      }
   }
         
   CLI::Execute();

?>