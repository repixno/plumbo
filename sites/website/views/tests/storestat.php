<?PHP

   import( 'pages.admin' );
   config( 'website.stores');

   class storeDeliveryTest extends AdminPage implements IView {
      
      protected $template = false;
      
      public function Execute() {
            
         
         $store_array = Settings::Get( 'storedelivery', 'Japan Photo' );
         
         
         echo '<table>';
                         
         foreach ( $store_array['stores'] as $store ){
            
            $from = '2011-05-01';
            
            $to = date('Y-m-d' , strtotime( $from . '+ 1 month' ));
            
            //util::Debug( $store['name'] );
            echo '<tr colspan="2">
                     <td> ' . $store['name'] .  ' </td>      
                  <tr>
                  <tr>
                     <td>hentegebyr</td>
                     <td>produktsalg</td>
                  </tr>';
            
            
            
            while( $from < date('Y-m-d')){
               $to = date('Y-m-d' , strtotime( $from . '+ 1 month' ));
                  echo '<tr colspan="2">
                     <td> ' . "$from - $to" .  ' </td>      
                  <tr>';

               
                  
               $query1 = "SELECT sum(hol.antall * hol.pris)  FROM site_store_order sto
                                       INNER JOIN  historie_ordre ho
                                          ON ho.ordrenr = sto.orderid AND tidspunkt between ? AND ?
                                       LEFT JOIN historie_ordrelinje hol
                                          ON hol.ordrenr = ho.ordrenr
                                          AND artikkelnr = 127
                                       WHERE sto.storeuserid =?;";
   
               
               
               $queryProduktsalg = "SELECT sum(hol.antall * hol.pris)  FROM site_store_order sto
                                       INNER JOIN  historie_ordre ho
                                          ON ho.ordrenr = sto.orderid AND tidspunkt between ? AND ?
                                       LEFT JOIN historie_ordrelinje hol
                                          ON hol.ordrenr = ho.ordrenr
                                          AND artikkelnr != 127
                                       WHERE sto.storeuserid =?;";
               
               /*$query = 'SELECT count(*)  FROM site_store_order sto
                                       LEFT JOIN historie_ordre ho
                                          ON ho.ordrenr = sto.orderid
                                       LEFT JOIN historie_ordrelinje hol   
                                          ON hol.ordrenr = ho.ordrenr
                                          AND artikkelnr = 127
                                       WHERE sto.storeuserid = ?';*/
               
               $hentegebyr = DB::query( $query1, $from, $to, $store['userid'] )->fetchSingle();
               $produktsalg = DB::query( $queryProduktsalg, $from, $to, $store['userid'] )->fetchSingle(); 
               
               echo "<tr>
                        <td>$hentegebyr NOK</td>
                        <td>$produktsalg NOK</td>
                     </tr>";

              $from = $to;
                                                     
               //util::Debug( "Hentegebyr: NOK $hentegebyr Produktsalg: NOK $produktsalg" );
            }
            echo '<tr colspan="2">
                     <td>&nbsp;</td>
                  <tr>';
         }
         
         
         
         echo '</table>';
      }
      
      
      public function store(){
         
         $store_array = Settings::Get( 'storedelivery', 'Japan Photo' );
         
         $tablehead = "<tr><td>&nbsp;</td>";
         $tableingress = "<tr><td>&nbsp;</td>";
         foreach ( $store_array['stores'] as $store ){
            $tableingress .=  "<td>porto</td><td>produkt</td>";
            $tablehead .=  "<td colspan=2>" . $store['name'] . "</td>";
         }
         
         $tablehead .= "</tr>";
         $tableingress .= "</tr>";
         
         $from = '2011-05-01';
         
         while( $from < date('Y-m-d')){
            $to = date('Y-m-d' , strtotime( $from . '+ 1 month' ));
            $tablecontent .= "<tr><td>$from</td>";
            foreach ( $store_array['stores'] as $store ){
               
               $query = "SELECT ordrenr FROM historie_ordre WHERE kommentar ilike '%" . utf8_decode( $store['name'] ) ."%' AND tidspunkt BETWEEN '$from' AND '$to'";
               
               $hente = "SELECT sum( antall * pris ) FROM historie_ordrelinje WHERE ordrenr in ( $query )  AND artikkelnr = 127;";
               $products = "SELECT sum( antall * pris ) FROM historie_ordrelinje WHERE ordrenr in ( $query )  AND artikkelnr != 127;";

               $hentegebyr = DB::query( $hente )->fetchSingle();
               $produktsalg = DB::query( $products  )->fetchSingle(); 
               
               $tablecontent .=  "<td>" . str_replace( '.', ',' , $hentegebyr ) . "</td><td>" . str_replace( '.', ',' ,$produktsalg) . "</td>";
            }
            $tablecontent .= "</tr>";
            
            $from = $to;
         }
         
         //util::Debug($portals);
         
         
         echo "<TABLE>" ;
         echo $tablehead .  $tableingress . $tablecontent ;   
         
         
         echo "</TABLE>";
         
      }
      
      
     
      
      
      
   }
   
?>