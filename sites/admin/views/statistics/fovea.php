<?PHP
   
import( 'pages.admin' );

class FoveaprMonth extends AdminPage implements IView {

    protected $template = 'statistics.fovea';
    protected $userid = 983136;
      
    public function Execute( $year = null, $month = null, $numyears = 2, $offsetdata = false ) {
         
         
         $from = date( 'Y-m-d', strtotime( "$year-$month-01" ) );
         $to = date( 'Y-m-d', strtotime( $from . ' + 1 month' ) );
         
         Util::Debug( $from );
         Util::Debug( $to );
         
     
         $orders = DB::query( "SELECT * FROM historie_ordre ho, historie_kunde hk WHERE
                                ho.ordrenr = hk.ordrenr AND
                                ho.uid = ? AND
                                ho.tidspunkt BETWEEN ? AND ?", $this->userid, $from , $to )->fetchAll( DB::FETCH_ASSOC );
         
         $orderarray = array();
         
         foreach( $orders as $order ){
            if( $order['dland'] == 160 ){
                $orderarray['Norge'][] = $order['ordrenr'];
            }else{
                $orderarray['Sverige'][] = $order['ordrenr'];
            }
         }
         
         
         
         foreach( $orderarray as $key=>$ret){
            
            //$articles[$key]['sum'] = count( $ret );
            
            $list = implode( ',',  $ret );
            
            $exclude  = "352";
            
            $bestillt = array();
            $sendt = array();
            
            $bestillt = DB::query( "SELECT artikkelnr, max( tekst) as tekst , sum( antall) as antall FROM
                                    historie_ordrelinje WHERE
                                    ordrenr in ($list) AND
                                    artikkelnr not in ( $exclude )
                                    GROUP BY artikkelnr order by artikkelnr"   )->fetchAll( DB::FETCH_ASSOC );
            
            
            $sendt = DB::query( "SELECT artikkelnr, sum( antall) as antall FROM
                                    historie_ordrelinje WHERE
                                    ordrenr in ($list) AND
                                    artikkelnr not in ( $exclude ) AND
                                    ordrenr in ( SELECT ordrenr FROM historie_pakke_skjema WHERE tidspunkt is not null )
                                    GROUP BY artikkelnr order by artikkelnr"   )->fetchAll( DB::FETCH_ASSOC );
            
            $total_sendt = DB::query( "SELECT count(*) FROM
                                    historie_ordre WHERE
                                    ordrenr in ($list) AND
                                    ordrenr in ( SELECT ordrenr FROM historie_pakke_skjema WHERE tidspunkt is not null )"
                                    )->fetchSingle();
            
            $articles[$key] = array(
                    'sum' => count( $ret ),
                    'sendt_sum' => $total_sendt,
                    'artikkler' => array(
                        'bestillt' => $bestillt,
                        'sendt' => $sendt 
                    )
            );
            
            
            //$articles 
            
            
         }
         
         //Util::Debug( $articles );
         
         $this->foveaarticles = $articles;
         
    }
    
    
    public function sendt( $year = null, $month = null, $numyears = 2, $offsetdata = false ) {
         
         
         $from = date( 'Y-m-d', strtotime( "$year-$month-01" ) );
         $to = date( 'Y-m-d', strtotime( $from . ' + 1 month' ) );
         
         Util::Debug( $from );
         Util::Debug( $to );
         
     
         $orders = DB::query( "SELECT * FROM historie_pakke_skjema ho, historie_kunde hk WHERE
                                ho.ordrenr = hk.ordrenr AND
                                ho.ordrenr in ( SELECT ordrenr from historie_ordre where uid = ? )  AND
                                ho.tidspunkt BETWEEN ? AND ?", $this->userid, $from , $to )->fetchAll( DB::FETCH_ASSOC );
         
         $orderarray = array();
         
         foreach( $orders as $order ){
            if( $order['dland'] == 160 ){
                if( is_array($orderarray['Norge']) ){
                    if( !in_array( $order['ordrenr'], $orderarray['Norge'] )){
                        $orderarray['Norge'][] = $order['ordrenr'];
                    }
                }else{
                    $orderarray['Norge'][] = $order['ordrenr'];
                }
                
            }else{
                if( is_array($orderarray['Sverige']) ){
                    if( !in_array( $order['ordrenr'], $orderarray['Sverige'] )){
                    $orderarray['Sverige'][] = $order['ordrenr'];
                    }
                }else{
                    $orderarray['Sverige'][] = $order['ordrenr'];
                }
            }
         }
         
         
         foreach( $orderarray as $key=>$ret){
            
            //$articles[$key]['sum'] = count( $ret );
            
            $list = implode( ',',  $ret );
            
            $exclude  = "352";
            
            $bestillt = array();
            $sendt = array();
            
            $bestillt = DB::query( "SELECT artikkelnr, max( tekst) as tekst , sum( antall) as antall FROM
                                    historie_ordrelinje WHERE
                                    ordrenr in ($list) AND
                                    artikkelnr not in ( $exclude )
                                    GROUP BY artikkelnr order by artikkelnr"   )->fetchAll( DB::FETCH_ASSOC );
            
            
            $sendt = DB::query( "SELECT artikkelnr, sum( antall) as antall FROM
                                    historie_ordrelinje WHERE
                                    ordrenr in ($list) AND
                                    artikkelnr not in ( $exclude ) AND
                                    ordrenr in ( SELECT ordrenr FROM historie_pakke_skjema WHERE tidspunkt is not null )
                                    GROUP BY artikkelnr order by artikkelnr"   )->fetchAll( DB::FETCH_ASSOC );
            
            $total_sendt = DB::query( "SELECT count(*) FROM
                                    historie_ordre WHERE
                                    ordrenr in ($list) AND
                                    ordrenr in ( SELECT ordrenr FROM historie_pakke_skjema WHERE tidspunkt is not null )"
                                    )->fetchSingle();
            
            $articles[$key] = array(
                    'sum' => 0,
                    'sendt_sum' => $total_sendt,
                    'artikkler' => array(
                        'bestillt' => $bestillt,
                        'sendt' => $sendt 
                    )
            );
            
            
            //$articles 
            
            
         }
         
         //Util::Debug( $articles );
         
         $this->foveaarticles = $articles;
         
    }
}
   
?>