<?PHP
   
    import( 'pages.protected' );
    import( 'website.order' );
    import( 'website.reedfoto.reedfotoalbum');
   
    class ReedfotoAdmin extends ProtectedPage implements IView {
      
        protected $template = null;
      
        public function Execute() {
         
			$this->setTemplate( 'partner.reedfoto.index' );
            
            //$orders = DB::query( "SELECT * from historie_ordre where kampanje_kode = 'RF-001' and uid != 941275  order by ordrenr desc" )->fetchAll(DB::FETCH_ASSOC );
            
            
            ///Util::Debug( $orders );
         
        }
        
        
        public function orders( $kundeid = null ){
            $this->setTemplate( 'partner.reedfoto.orders.index' );
            
            $orders = new Order();
		   // For some reason we only want orders with order number higher than this.
		   $newordercut = 389910;
		   
		   
		   $thisyear = date('Y');
           
           if( $_GET['from'] ){
            $fromdate = date('Y-m-d', strtotime( $_GET['from'] ) );
           }else{
             $fromdate = date('Y-m-d' , strtotime( $thisyear . '-01-01') );
           }
           if( $_GET['to'] ){
            $todate = date('Y-m-d', strtotime( $_GET['to'] ) );
           }else{
             $todate = date('Y-m-d' , strtotime('+1 day') );
           }
           
           $this->fromdate = $fromdate;
           $this->todate = $todate;
           
           
			$list = array();
            
            if($kundeid){
                $kundesort = array( '=', $kundeid );
				$kampanjekode = array( 'RF-001', '' );
				foreach ( $orders->collection( array( 'id' ), array(
																	'tidspunkt' => array('BETWEEN', array(  $fromdate, $todate )  ),
																	'deleted' => null,
																	'uid' => $kundesort,
																	'ordrenr' => array( '>', $newordercut ) ),
																	'tidspunkt DESC' )->fetchAllAs( 'Order' ) as $order ) {
					$list[] = array(
						'id' => $order->orderid,
						'orderid' => $order->ordernum,
						'date' => strftime( '%e. %B %Y', strtotime( $order->date ) ),
						'timestamp' => strtotime( $order->date ),
						'status' => $order->status,
						'pris' => $order->pris
					);
				}
				
				
				
				
				$this->omsetning = DB::query( "SELECT sum(pris)
												FROM
													historie_ordre
												WHERE tidspunkt between ? AND ?
												AND ordrenr > ?
												AND uid = ?
												", $fromdate, $todate, $newordercut, $kundeid )->fetchSingle();
				
            }
            else{
                $kundesort = array( '!=', 941275 );
				
				$this->omsetning = DB::query( "SELECT
												artikkelnr,
												sum( pris * antall ) as pris,
												sum( antall ) as antall,
												max( tekst ) as tittel
												FROM
													historie_ordrelinje
												WHERE
												ordrenr in(
													SELECT
														ordrenr
													FROM
														historie_ordre
													WHERE 
														tidspunkt between ? AND ?
													AND
														kampanje_kode = 'RF-001'
													AND
														ordrenr > ?
													AND
														uid != 941275
													)
												GROUP BY
													artikkelnr
												", $fromdate, $todate, $newordercut )->fetchALL( DB::FETCH_ASSOC );
				
				$this->totalomsetning = DB::query( "SELECT sum(pris)
												FROM
													historie_ordre
												WHERE tidspunkt between ? AND ?
												AND ordrenr > ?
												AND kampanje_kode = 'RF-001'
												AND uid != ?
												", $fromdate, $todate, $newordercut, 941275 )->fetchSingle();
				
				
				$kampanjekode = 'RF-001';
				foreach ( $orders->collection( array( 'id' ), array(
																	'tidspunkt' => array('BETWEEN', array(  $fromdate, $todate )  ),
																	'deleted' => null,
																	'kampanje_kode' => 'RF-001',
																	'uid' => $kundesort,
																	'ordrenr' => array( '>', $newordercut ) ),
																	'tidspunkt DESC' )->fetchAllAs( 'Order' ) as $order ) {
					$list[] = array(
						'id' => $order->orderid,
						'orderid' => $order->ordernum,
						'date' => strftime( '%e. %B %Y', strtotime( $order->date ) ),
						'timestamp' => strtotime( $order->date ),
						'status' => $order->status,
						'pris' => $order->pris
					);
				}
				
				
				
				
				
				
            }
			
			$this->orders = $list;
            
        }
		
		public function sendmail(){
			
			
			$ordrenr = $_POST['ordrenr'];
			$comment = $_POST['comment'];
			$email = $_POST['email'];
			
			
			$headers = 'From: '. $email . "\r\n" .
						'Reply-To: '. $email . "\r\n" .
						'X-Mailer: PHP/' . phpversion();
			
			
			mail( 'post@eurofoto.no, ' . $email , "Sletting ordre $ordrenr", "Sletteing av ordre $ordrenr \nKommentar:\n" . $comment ,  $headers );
			
			relocate('/partner/reedfoto/orders');
			
			
		}
		
		
		public function orderprojectinfo(){
			$this->template = 'partner.reedfoto.orders.projectstat';
			
			$orderarray = array();
			
			$orders = DB::query( "SELECT * FROM historie_ordrelinje where artikkelnr in (7380, 7384, 7485, 7385, 7379, 7386, 7382, 7381, 7383, 7291 ) order by artikkelnr" )->fetchAll( DB::FETCH_ASSOC );
			
			foreach( $orders as $order ){
				
				$attr =  unserialize( $order['attributes'] );
				
				$rfalbum = DB::query( "SELECT * from reedfoto_album where aid = ?", $attr['aid'] )->fetchAll( DB::FETCH_ASSOC );
				
				$orderarray[$rfalbum[0]['school']][$order['artikkelnr']] = array(
																		'ordrenr' => $order['ordrenr'],
																		'artikkelnr' => $order['artikkelnr'],
																		'antall' => $orderarray[$rfalbum[0]['school']][$order['artikkelnr']]['antall'] + $order['antall'],
																		'tekst' => $order['tekst'],
																		'pris' => $order['pris'],
																		'total' => ( $order['pris'] * $order['antall'] ) + $orderarray[$rfalbum[0]['school']][$order['artikkelnr']]['total']
																		);
				
			}
			
			$this->orderarray  = $orderarray;

			
		}
		
        public function orderinfo( $orderid = null ) { 
			$this->setTemplate( 'partner.reedfoto.orders.showorder' );
			
			$ordrenr = $_POST['ordrenr'] ? (int)$_POST['ordrenr'] : null;
						
			if( $ordrenr ){
				$orderid = DB::query( "SELECT id FROM historie_ordre WHERE ordrenr = ?", $ordrenr )->fetchSingle();
			}
            
			$order = new Order( $orderid );
            
            $kunde = DB::query('SELECT * from historie_kunde where ordrenr = ? ', $order->ordrenr )->fetchAll( DB::FETCH_ASSOC );
            
            $this->kunde = $kunde[0];
            
			// Don't anyone see anyone else's orders.
			/*if ( $order->kampanje_kode != 'RF-001' ) {
				$this->order = array();
				$this->message = 'You do not have permission to see this order.';
				return false;
			}**/
			
			
			//Util::Debug($order->items);
			
			foreach( $order->items as $item ){
				
				if( $item['attributes'] ){
					$attr =  unserialize( $item['attributes'] );
				}
				
				if( $attr['aid'] ){
					$fal = DB::query( "SELECT identifier, fname, ename, address, zip as postnr,city as poststad, aid, school as skule, grade as klasse FROM reedfoto_album WHERE aid = ?", $attr['aid'] )->fetchAll( DB::FETCH_ASSOC );
				}
				
				$portrett  = DB::query( "SELECT tittel FROM bildeinfo WHERE bid = ?", $attr['bid'] )->fetchSingle();
				
				$gruppe  = DB::query( "SELECT tittel FROM bildeinfo WHERE bid = ?", $attr['gruppebid'] )->fetchSingle();
				
				$this->elev = array(
					'elev' 		=> $fal[0],
					'portrett' 	=> $portrett,
					'gruppe'	=> $gruppe
				);
			}
			
			
			
			// Build order array.
			$ret = array(
				'id' => $order->orderid,
				'orderid' => $order->ordernum,
				'date' => strftime( '%e. %B %Y', strtotime( $order->date ) ),
				'timestamp' => strtotime( $order->date ),
				'comment' => $order->comment,
				'status' => $order->status,
				'items' => $order->items,
				'totalprice' => $order->price,
			);
		   $this->order = $ret;
		}
        
        
        
        public function kunde(){
            
            $this->setTemplate( 'partner.reedfoto.kunde' );
            if( $_POST['q'] ){
                
                
                $q = $_POST['q'];
                
                $columns = array('namn', 'adresse1', 'fnavn', 'enavn', 'postnr', 'stad', 'brukarnamn' );
                
                $kunde = array();
            
                foreach( $columns  as $key ){
                    
                    if( $key == 'brukarnamn'){
                        $query = sprintf( "SELECT * FROM kunde k, brukar b WHERE b.uid = k.uid AND b.kode = 'RF-001' AND b.%s ilike '%s'", $key, "%$q%" );
                    }else{
                         $query = sprintf( "SELECT * FROM kunde k, brukar b WHERE b.uid = k.uid AND b.kode = 'RF-001' AND k.%s ilike '%s'", $key, "%$q%" );
                    }
                   
                    $resultat = DB::query( $query )->fetchAll(DB::FETCH_ASSOC);
                    if( $resultat ){
                        foreach( $resultat as $fant ){
                            
                             $kunde[$fant['uid']] = $fant;
                        }
                    }
                }
                $this->kunde = $kunde;
            }    
        }
        
    public function etterbestilling($code=null){
        
        
        $this->setTemplate( 'partner.reedfoto.kundebilder' );

        $identifier = Reedfotoalbum::fromIdentifier( $code );
        $images = DB::query( "SELECT bid FROM bildeinfo WHERE aid = ?" , $identifier->aid )->fetchAll( DB::FETCH_ASSOC );
        $imagethumbbs = array();
        foreach( $images as $image ){
              $imagethumbbs[]  = sprintf('%s/partner/reedfoto/previewthumbnail/%d',
                          WebsiteHelper::rootBaseUrl(),
                          $image['bid']
                    );
        }
        
        $this->identifier = $identifier;
        $this->thumbnails = $imagethumbbs;
    
    
    }
        
    public function previewthumbnail($bid=0){
            
         $this->template = null;
         config( 'website.storage' );

         $thumb = DB::query("SELECT * FROM bildeinfo WHERE bid = ? ", $bid )->fetchAll( DB::FETCH_ASSOC );
         $thumb = $thumb[0];

         $rfalbum = DB::query( 'SELECT identifier FROM reedfoto_album WHERE aid = ?' , $thumb['aid'] )->fetchSingle();


         $imagespath = Settings::Get( 'storage', 'path');
         $cachefilename = "/home/www/tmpbilder/" . $thumb['hashcode'] . ".mc_preview.jpg";
         try{
            $cachefilename = $imagespath . $thumb['filnamn']  . ".preview.jpg";        
            header( "Content-Type: image/jpeg" );
            readfile ($cachefilename);
          
         }catch (Exception $e){
            $cachefilename = '/var/www/repix/sites/static/webroot/gfx/404/not_found_100px.jpg';
            header( "Content-Type: image/jpeg" );
            readfile( $cachefilename );
         } 
    }
      
      
    }
   
?>