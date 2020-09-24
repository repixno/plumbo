<?



   import( 'pages.admin' );
   config( 'website.stores');

class storeDeliveryStat extends AdminPage implements IView {

   protected $template = '';
   
   public function Execute($from = ''){
      
      $from = $_GET['from'];
      $to = $_GET['to'];
      
      echo '<form method="get">Fra:<input type="text" name="from" value="'. $from .'">Til:<input type="text" name="to" value="'. $to .'"><input type="submit"></form>';
       
      
     
      if( $from && $to ){
         
         $from = date( 'Y-m-d', strtotime( $from ) );
         $to = date( 'Y-m-d', strtotime( $to ) );
         
         //Util::Debug( $from );
         //0Util::Debug( $to );

         $storedelivery = DB::query("SELECT shopid, count(*), sum( ho.pris ) from historie_ordre ho LEFT JOIN historie_delivery hd ON ho.ordrenr = hd.ordrenr WHERE ho.tidspunkt between ? AND  ? AND hd.shopid > 0 AND ho.kampanje_kode not in ( 'UP-001', 'DM-001', 'FC-001' ) GROUP by hd.shopid order by hd.shopid;", $from, $to )->fetchAll( DB::FETCH_ASSOC );
         
         $toptable = "<tr>
            <td>
               Butikk
            </td>
            <td>
               Antall ordre
            </td>
            <td>
               Omsetning
            </td>
          </tr>";
         
         
         
         $store_array = Settings::Get( 'storedelivery', 'Japan Photo' );
         
         
         $stores = array();
         foreach ( $store_array['stores'] as $store ){
            
            $stores[$store['shopId']] = $store['name'];
            
         }
         
         //util::Debug( $stores );
         
         
         foreach ( $storedelivery as $res ){         
            
            $store = $stores[$res['shopid']];
            $antall = $res['count'];
            $omsetning = $res['sum'];
            
            
            $content .= "<tr><td>$store</td><td>$antall</td><td>$omsetning</td></tr>";
    
         }
         
         
         echo "<table>$toptable$content</table>";
         
         //util::Debug(  );
      
      }
      
      
   }
   
   
   
   
}

























?>