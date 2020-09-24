<?PHP

import( 'pages.admin' );
   config( 'website.stores');

   class StabburetStats extends AdminPage implements IView {
      
      protected $template = false;
      
      public function Execute( $year = false ) {
         
         
         if( !$year ){
            $year = '2019';
         }
         
         
       $startdate = date( 'Y-m-d', strtotime($year . '-07-29 '));
       $finaldate = date( 'Y-m-d', strtotime( $startdate . ' + 4 months'));
       
       
       $enddate = $startdate;
         
       while( $enddate < date('Y-m-d', strtotime( '+1 day'))  && $enddate < $finaldate ){
           
           $enddate = date( 'Y-m-d', strtotime( $startdate . '+ 2 day' ) );
           
           $allorders = DB::query( 'SELECT malid, min(hm.artikkelnr) as artikkelnr,   count(*) from historie_ordre ho, historie_mal hm where
                                  hm.ordrenr = ho.ordrenr and hm.artikkelnr in(  7196 ,7303,7302,7301,7304,7305,7746,7745) AND ho.tidspunkt between ? and ? GROUP by malid order by malid', $startdate , $enddate )->fetchAll( DB::FETCH_ASSOC );
           
           
           $ordercount  = array();
           foreach( $allorders as $thisorder ){
                  $ordercount[$thisorder['malid']] = $thisorder['count'];
            
           }

           
          // util::Debug( $startdate . ' mal:' . $allorders[0]['malid'] . ' Antall:'  . $allorders[0]['count']  . ' -- ' . ' mal:' . $allorders[1]['malid'] . ' Antall:'  . $allorders[1]['count']);
           
           
           $tables .= "<tr><td><b>$startdate</b></td>
           
                     <td align='right'>".$ordercount[2553]. "</td>
                     <td align='right'>" .$ordercount[2552] . "</td>
                     
                     <td align='right'>" .$ordercount[2694] . "</td>
                     
                     <td align='right'>" .$ordercount[2699] . "</td>
                     
                     <td align='right'>" .$ordercount[2698]. "</td>
                     
                     <td align='right'>" .$ordercount[2697]. "</td>
                     
                     <td align='right'>" .$ordercount[2695] . "</td>
                     
                     <td align='right'>" .$ordercount[3164] . "</td>
                      <td align='right'>" .$ordercount[3163] . "</td>
                     
                     </tr>";  
           
           //lite lokk
           $total[0] += $ordercount[2553];
           // stort lokk
           $total[1] += $ordercount[2552];
           //matboks
           $total[2] += $ordercount[2694];
           // drikkeflaske
           $total[3] += $ordercount[2699];
           // kopp
           $total[4] += $ordercount[2698];
           //nokkelring
        
           $total[5] += $ordercount[2697];
           //magnet
           $total[6] += $ordercount[2695];
           //lue
           $total[7] += $ordercount[3164];
        $total[8] += $ordercount[3163];
           
              
           
           
           $startdate = $enddate;

           
           
           
        }
         
         $tables .= "<tr><td><b>Total</b></td>" ;
         
         
         foreach( $total as $order ){
            $tables .= '<td style="text-align: right; width:85px">' . $order . "</td>";
         }
         
         
         $tables .= "</tr>";  
         
        
        echo '<table border="1"><tr>
                  <td>Dato</td>
                  <td>Lite lokk</td>
                  <td>Stort lokk</td>
                  <td>Matboks</td>
                  <td>Drikkeflaske</td>
                  <td>Kopp</td>
                  <td>Nøkkelring</td>
                  <td>Magnet</td>
                  <td>Lue</td>
                  <td>Gympose</td>
               <tr>';
        echo $tables;
        echo "</table>";
         
         
      }
      
      
      public function kunder( $year = false){
       $tables = "";
       
       
       if( !$year ){
            $year = '2019';
         }
         
       $startdate = date( 'Y-m-d', strtotime($year . '-08-01 '));
       $finaldate = date( 'Y-m-d', strtotime( $startdate . ' + 4 months'));
         
       $enddate = $startdate;
         
       while( $enddate < date('Y-m-d', strtotime( '+1 day')) && $enddate < $finaldate ){
           
           $enddate = date( 'Y-m-d', strtotime( $startdate . '+ 1 months' ) );

           $newsletter_not_reg = DB::query( "SELECT count(distinct(b.uid)) from brukar b, kunde k  where
                                           b.uid = k.uid AND
                                           b.kode ilike 'ST-001' AND
                                           k.newsletter = 't' AND
                                           b.brukarnamn not in ( select brukarnamn from brukar WHERE kode  in( '', 'VG-997', 'MB-997', 'SN-997',   'DM-001' , 'FE-001' , 'TK-001' ,  'AM-997' ,  'UP-001' , 'IF-NO' , 'NSK-001' ,  'EF-VG' ,  'SK-001' ,  'AP-997'  , 'SS-333' ,  'IF-FI' ) ) AND
                                           registrert BETWEEN  ? AND ?", $startdate , $enddate )->fetchSingle();
           
           $newsletter_all = DB::query( "SELECT count(distinct(b.uid)) from brukar b, kunde k where
                                       b.uid = k.uid AND
                                       b.kode ilike 'ST-001' AND
                                       k.newsletter = 't' AND
                                       registrert BETWEEN  ? AND ?", $startdate , $enddate )->fetchSingle();
           
           $all = DB::query( "SELECT count(distinct(b.uid)) from brukar b, kunde k  where
                            b.uid = k.uid AND
                            b.kode ilike 'ST-001' AND
                            registrert BETWEEN  ? AND ?", $startdate , $enddate )->fetchSingle();
           
           $regged = DB::query( "SELECT count(distinct(b.brukarnamn)) from brukar b, kunde k  where
                               b.uid = k.uid AND
                               b.kode ilike 'ST-001' AND
                               registrert BETWEEN  ? AND ? AND
                               b.brukarnamn in (  select brukarnamn from brukar WHERE kode  in( '', 'VG-997', 'MB-997', 'SN-997',   'DM-001' , 'FE-001' , 'TK-001' ,  'AM-997' ,  'UP-001' , 'IF-NO' , 'NSK-001' ,  'EF-VG' ,  'SK-001' ,  'AP-997'  , 'SS-333' ,  'IF-FI' ) )", $startdate , $enddate )->fetchSingle();
           
          // util::Debug( $startdate . ' mal:' . $allorders[0]['malid'] . ' Antall:'  . $allorders[0]['count']  . ' -- ' . ' mal:' . $allorders[1]['malid'] . ' Antall:'  . $allorders[1]['count']);
           $jadda =  (int)$newsletter_all - (int)$newsletter_not_reg;
           $tables .= "<tr><td><b>$startdate</b></td><td>$all</td><td>$regged</td><td>".$newsletter_not_reg."</td><td>" . $jadda . "</td></tr>";
           $startdate = $enddate;
           
        }
        
        echo '<table border="1"';
        echo "<tr><td>&nbsp;</td><td colspan=\"2\">Registert</td><td colspan=\"2\">Nyhetsbrev</td></tr>";
        echo "<tr><td>Dato</td><td>Alle</td><td>tidligere registert</td><td>ikke tidligere registert</td><td>tidligere registert</td><tr>";
        echo $tables;
        echo "</table>";
      }
      
   }
   
   
   
   
   
?>
