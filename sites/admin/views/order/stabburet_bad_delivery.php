<?PHP
    
    /*******************************************
     *@author Tor Inge <tor.inge@eurofoto.no
     *
     *
     *
     *******************************************/
    
    import( 'pages.admin' );

    class AdminStabburetBad extends AdminPage implements IView {
        protected $template = 'order.stabburet_bad';
        
        public function Execute( ) {
                             
            if( $_POST['ordrenr'] ){
                
                $ordrenr = $_POST['ordrenr'];
              
                $query = "SELECT * FROM historie_kunde WHERE ordrenr = ?";
                $orders = DB::query( $query, $ordrenr )->fetchAll( DB::FETCH_ASSOC );
                
            	//$total_orders = DB::query( "SELECT epost FROM historie_kunde WHERE uid = ?", $ordrenr )->fetchSingle();
            	$to = DB::query( "SELECT epost FROM historie_kunde WHERE ordrenr = ?", $ordrenr )->fetchSingle();
				$customer = DB::query( "SELECT navn FROM historie_kunde WHERE ordrenr = ?", $ordrenr )->fetchSingle();
			//	$number = DB::query( "SELECT ordrenr FROM historie_kunde WHERE ordrenr = ?", $ordrenr )->fetchSingle();
            
               //Util::Debug( $to );
               //exit;
                
            }
            
                        $fromemail = "post@dinmerkelapp.no";
                        $bcc = "adele@eurofoto.no";
                      	//$to = $orders[0][epost];
                        $headers = 'From: '. $fromemail . "\r\n" .
                        
                        'Bcc:' . $bcc  . "\r\n" .
                        'Reply-To:' . $fromemail  . "\r\n" .
                        "Content-type: text/html; charset=utf-8\r\n" .
                        'X-Mailer: PHP/' . phpversion();
                        
                        $mailbody = file_get_contents( "/var/www/repix/sites/admin/templates/blue/order/stabburet_epost_til_kunde_delivery.html" ) ;
                        $mailbody = str_replace( '***NAVN***',$customer, $mailbody );
                          $mailbody = str_replace( '***ORDRENR***',$ordrenr, $mailbody );
                        
                        
                        mail($to, "Oppdatering Stabburet Leverpostei Ordre ", $mailbody, $headers);
                        
                        
                       
            
        }
        
        
        
    }
?>
