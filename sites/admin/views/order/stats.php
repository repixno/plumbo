<?PHP
   
    import( 'pages.admin' );
    import( 'website.product' );
    import( 'website.article' );
    import( 'website.order' );
   
    class AdminProdctionUtestemme extends AdminPage implements IView {
      
        protected $template = 'order.stats';
        
        public function Execute( $portals = '', $fromdate = null, $todate=null, $bet = null ){
            
            if( $portals == 'EF' ){
                $portal = "kampanje_kode in ( '', 'VG-997', 'AM-997', 'MD-001' )";     
            }
            else if( $portals == 'UKEPLAN' ){
                 $portal = "kampanje_kode in ( 'UP-001', 'VP-001', 'UP-DK' )";
            }
            else if( $portals == 'MERKELAPP' ){
                 $portal = "kampanje_kode in ( 'DM-001' )";
            }
            else if( $portals == 'SPARKJOP' ){
                 $portal = "kampanje_kode in ( 'SK-001')";
            }
            else if( $portals == 'FOTOCLICK' ){
                 $portal = "kampanje_kode in ( 'FC-001')";
            }
            else if( $portals == 'TAKKEKORT' ){
                 $portal = "kampanje_kode in ( 'TK-001')";
            }
            else if( $portals == 'REEDFOTO' ){
                 $portal = "kampanje_kode in ( 'RF-001')";
            }
            else{
                $portal = "kampanje_kode in ( '' )";
            }
            
            
            if( !$todate ){
                $todate = date( 'Y-m-d', strtotime( "-4 days" ) );
            }
            if( !$fromdate ){
                $fromdate = date( 'Y-m-d', strtotime( "-14 days" ) );
            }
            
            $fromordrenr = DB::query( "SELECT MAX(ordrenr) FROM historie_ordre WHERE tidspunkt < ? " , $fromdate)->fetchSingle();
            Util::Debug( $todate );
            
            $orders = DB::query( "SELECT * FROM historie_ordre WHERE
                                            ordrenr not in ( SELECT ordrenr from historie_pakke_skjema WHERE ordrenr > ? AND tidspunkt is not null )
                                            AND
                                            tidspunkt BETWEEN  ? AND ?
                                            AND
                                            $portal
                                            AND
                                            deleted is null
                                            ORDER BY ordrenr
                                            ", $fromordrenr, $fromdate, $todate )->fetchAll( DB::FETCH_ASSOC );
            
            $ready_orders = array();
            
            foreach( $orders as $order ){
                
                    
                    $betaltlagring = DB::query( 'SELECT count(*) FROM historie_ordrelinje WHERE ordrenr = ?  AND artikkelnr in ( 857, 859 )',  $order['ordrenr']  )->fetchSingle();
                    
                    if( $bet == null ){
                        if( $betaltlagring > 0 ) continue;
                    }else{
                        if( $betaltlagring == 0 ) continue;
                    }
                   
                
                    $skjema = DB::query( 'SELECT count(*), max(tidspunkt), skjemaid FROM skjema_tracker
                                            WHERE
                                                ordrenr = ?
                                            AND
                                                skjemaid in ( SELECT skjemaid FROM ordreskjema WHERE ordrenr = ? AND station != 99)
                                            group by skjemaid' , $order['ordrenr'], $order['ordrenr'])->fetchAll( DB::FETCH_ASSOC );
                    
                    
                    foreach( $skjema as $ret ){
                        
                        if( $ret['count'] == 1 ){
                            $produsert = null;
                             break;
                        }else{
                            if( $ret['max'] > $produsert ){
                                $produsert = $ret['max'];
                            }
                        }
                    }
                    
                    $notice = DB::query( 'SELECT * from ordre_notice where ordrenr = ?', $order['ordrenr'] )->fetchAll( DB::FETCH_ASSOC );
                    
                    if( $produsert ){
                        $produsert = date( 'Y-m-d H:i:s' , strtotime( $produsert ) );
                    }
                    
                    $ready_orders[] = array(
                        'ordrenr' => $order['ordrenr'],
                        'bestillt' =>  date( 'Y-m-d H:i:s' , strtotime( $order['tidspunkt'] ) ),
                        'produsert' => $produsert,
                        'notice'    => $notice  
                    );
            }
            
            $this->bet = $bet;
            
            $this->selectedportal = $portals;
            $this->orders = $ready_orders;
            $this->todate = $todate;
            $this->fromdate = $fromdate;
        }
        
        
        
        public function Deleteorder( $ordrenr, $portal, $fromdate, $todate  ){
            
            
            $orderid = DB::query( "SELECT id FROM historie_ordre WHERE ordrenr = ?", $ordrenr )->fetchSingle();
            
            $this->template = null;
            
            $order = new Order( $orderid );
            $order->pris = 0;
            $order->deleted = date( 'Y-m-d H:i:s' );
            $order->save();
            
            DB::query( "UPDATE historie_ordrelinje SET pris=0 WHERE ordrenr=?", $ordrenr  );
            
            relocate( sprintf( '/order/stats/%s/%s/%s'  , $portal , $fromdate, $todate ) );
            
            
        }
        
        
        public function sendOrder( $ordrenr, $portal, $fromdate, $todate  ){
            
            $pakke_skjema_id = DB::query( "SELECT skjemaid FROM historie_pakke_skjema WHERE ordrenr = ?", $ordrenr  )->fetchSingle();
            
            $pakkeid = DB::query("SELECT nextval('pakkeid_seq');")->fetchSingle();
            
            $userid = Login::userid();
            if( $pakke_skjema_id ){
                DB::query( "UPDATE historie_pakke_skjema SET tidspunkt = ?, medarbeidar = ?, pakkeid = ?WHERE ordrenr = ?", date( 'Y-m-d H:i:s' ), $userid , $pakkeid,  $ordrenr  );
            }
            
            relocate( sprintf( '/order/stats/%s/%s/%s'  , $portal , $fromdate, $todate ) );
            
        }
        
        
    }