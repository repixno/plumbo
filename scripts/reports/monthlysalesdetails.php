<?PHP
   
   chdir( dirname( __FILE__ ) );
   include "../../bootstrap.php";
   
   config('website.config');
   import( 'system.cli' );
   
   // some basic configuration first
   $periodstart = '2009-01';
   $periodend = '2010-06';
   
   // some debug output is nice.
   echo "Fetching updated pricelist...";
   
   // load the default pricelist view and execute it
   Dispatcher::extendView( 'website|prices.default' );
   $pricelist = new PriceList();
   $pricelist->Execute();
   
   // iterate through the pricelist and create an artnr list
   foreach( $pricelist->pricelist as $key => $sections ) {
      
      switch( $key ) {
         
         default: 
            break;
         
         case 'products':
            foreach( $sections as $category ) {
               $artnrlist[$category['menu']['title']] = array();
               foreach( $category['sections'] as $subsection ) {
                  
                  foreach( $subsection['products'] as $product ) {
                     
                     $product = $product['product'];
                     $artnrlist[$category['menu']['title']][(int)$product['option']['prodno']] = true;
                     
                  }
               }
            }
            
            break;
            
         case 'prints':
         case 'enlargements':
            
            $artnrlist[UcFirst($key)] = array();
            foreach( $sections as $product ) {
               $artnrlist[UcFirst($key)][(int)$product['prodno']] = true;
            }
            break;
            
      }
      
   }
   
   echo "OK!\n";
   
   echo "Creating period-list...";
   
   // create an output table to fill the data into
   $period = $periodstart;
   $periods = array();
   do {
      $periods[$period] = 0.00;
      $period = date( 'Y-m', strtotime( '+1 month', strtotime( $period ) ) );
   } while( $period != $periodend );
   
   echo "OK!\n";
   
   // a little patch to merge enlargements and enlargements :P
   $artnrlist['Forstørrelser'] = array_flip( array_merge( array_keys( $artnrlist['Forstørrelser'] ), array_keys( $artnrlist['Enlargements'] ) ) );
   unset( $artnrlist['Enlargements'] );
   
   // now, iterate through this list, and create the report
   foreach( $artnrlist as $section => $artnrs ) {
      
      echo "Processing $section...";
      
      ksort( $artnrs );
      $artnrs = implode( ',', array_keys( $artnrs ) );
      
      $outputtable['All_'.$section]['periods'] = $periods;
      $query = DB::query( "SELECT SUM( ol.pris * ol.antall ) as sum, 
                                  DATE_PART('year', o.tidspunkt)||'-'||to_char(date_part('month', o.tidspunkt), 'FM00') as dato 
                             FROM historie_ordrelinje ol 
                        LEFT JOIN historie_ordre o 
                               ON o.ordrenr = ol.ordrenr 
                            WHERE ol.artikkelnr 
                               IN (".$artnrs.") 
                              AND o.tidspunkt BETWEEN '".$periodstart."-01 00:00:00' AND '".$periodend."-01 00:00:00'
                              AND o.pris > 0
                              AND o.deleted IS NULL
                              AND o.exported IS NOT NULL
                              AND o.serverlock IS NOT NULL
                              AND o.serverlock != ''
                              AND o.customerlock IS NOT NULL 
                         GROUP BY dato 
                         ORDER BY dato" );
      while( list( $sum, $period ) = $query->fetchRow() ) {
         $outputtable['All_'.$section]['periods'][$period] = number_format( $sum, 2, ',', '' );
      }  $outputtable['All_'.$section]['artnrs'] = $artnrs;
      
      $outputtable['New_'.$section]['periods'] = $periods;
      $query = DB::query( "SELECT SUM( ol.pris * ol.antall ) as sum, 
                                  DATE_PART('year', o.tidspunkt)||'-'||to_char(date_part('month', o.tidspunkt), 'FM00') as dato 
                             FROM historie_ordrelinje ol 
                        LEFT JOIN historie_ordre o 
                               ON o.ordrenr = ol.ordrenr 
                        LEFT JOIN brukar b 
                               ON b.uid = o.uid
                             JOIN kunde k
                               ON k.uid = b.uid
                            WHERE ol.artikkelnr 
                               IN (".$artnrs.") 
                              AND o.tidspunkt BETWEEN '".$periodstart."-01 00:00:00' AND '".$periodend."-01 00:00:00'
                              AND o.pris > 0
                              AND o.deleted IS NULL
                              AND o.exported IS NOT NULL
                              AND o.serverlock IS NOT NULL
                              AND o.serverlock != ''
                              AND o.customerlock IS NOT NULL 
                              AND DATE_PART('year', k.first_buy )||'-'||to_char(date_part('month', k.first_buy ), 'FM00')
                                = DATE_PART('year', o.tidspunkt )||'-'||to_char(date_part('month', o.tidspunkt ), 'FM00')
                         GROUP BY dato 
                         ORDER BY dato" );
      
      while( list( $sum, $period ) = $query->fetchRow() ) {
         $outputtable['New_'.$section]['periods'][$period] = number_format( $sum, 2, ',', '' );
      }  $outputtable['New_'.$section]['artnrs'] = $artnrs;
      
      $outputtable['Vol_'.$section]['periods'] = $periods;
      $query = DB::query( "SELECT SUM( ol.antall ) as sum, 
                                  DATE_PART('year', o.tidspunkt)||'-'||to_char(date_part('month', o.tidspunkt), 'FM00') as dato 
                             FROM historie_ordrelinje ol 
                        LEFT JOIN historie_ordre o 
                               ON o.ordrenr = ol.ordrenr 
                            WHERE ol.artikkelnr 
                               IN (".$artnrs.") 
                              AND o.tidspunkt BETWEEN '".$periodstart."-01 00:00:00' AND '".$periodend."-01 00:00:00'
                              AND o.pris > 0
                              AND o.deleted IS NULL
                              AND o.exported IS NOT NULL
                              AND o.serverlock IS NOT NULL
                              AND o.serverlock != ''
                              AND o.customerlock IS NOT NULL 
                         GROUP BY dato 
                         ORDER BY dato" );
      while( list( $sum, $period ) = $query->fetchRow() ) {
         $outputtable['Vol_'.$section]['periods'][$period] = $sum;
      }  $outputtable['Vol_'.$section]['artnrs'] = $artnrs;
      
      echo "OK!\n";
      
   }
   
   // sort the initial data
   ksort( $outputtable );
   
   echo "Creating totals...";
   
   // loop through the periods and fetch more stats
   $outputtable['All_Turnover']['periods'] = $periods;
   $query = DB::query( "SELECT SUM( o.pris ) as sum, 
                               DATE_PART('year', o.tidspunkt)||'-'||to_char(date_part('month', o.tidspunkt), 'FM00') as dato 
                          FROM historie_ordre o 
                         WHERE o.tidspunkt BETWEEN '".$periodstart."-01 00:00:00' AND '".$periodend."-01 00:00:00'
                           AND o.pris > 0
                           AND o.deleted IS NULL
                           AND o.exported IS NOT NULL
                           AND o.serverlock IS NOT NULL
                           AND o.serverlock != ''
                           AND o.customerlock IS NOT NULL 
                      GROUP BY dato 
                      ORDER BY dato" );
   while( list( $sum, $period ) = $query->fetchRow() ) {
      $outputtable['All_Turnover']['periods'][$period] = number_format( $sum, 2, ',', '' );
   }  $outputtable['All_Turnover']['artnrs'] = '-';
   
   // loop through the periods and fetch more stats
   $outputtable['New_Turnover']['periods'] = $periods;
   $query = DB::query( "SELECT SUM( o.pris ) as sum, 
                               DATE_PART('year', o.tidspunkt)||'-'||to_char(date_part('month', o.tidspunkt), 'FM00') as dato 
                          FROM historie_ordre o 
                     LEFT JOIN brukar b 
                            ON b.uid = o.uid
                          JOIN kunde k
                            ON k.uid = b.uid
                         WHERE o.tidspunkt BETWEEN '".$periodstart."-01 00:00:00' AND '".$periodend."-01 00:00:00'
                           AND o.pris > 0
                           AND o.deleted IS NULL
                           AND o.exported IS NOT NULL
                           AND o.serverlock IS NOT NULL
                           AND o.serverlock != ''
                           AND o.customerlock IS NOT NULL 
                           AND DATE_PART('year', k.first_buy )||'-'||to_char(date_part('month', k.first_buy ), 'FM00')
                             = DATE_PART('year', o.tidspunkt )||'-'||to_char(date_part('month', o.tidspunkt ), 'FM00')
                      GROUP BY dato 
                      ORDER BY dato" );
   while( list( $sum, $period ) = $query->fetchRow() ) {
      $outputtable['New_Turnover']['periods'][$period] = number_format( $sum, 2, ',', '' );
   }  $outputtable['New_Turnover']['artnrs'] = '-';
   
   echo "OK!\n";
   
   echo "Finding new customers per month...";
   // loop through the periods and fetch more stats
   $outputtable['New_Customers']['periods'] = $periods;
   $query = DB::query( "SELECT COUNT(DISTINCT(k.uid)) as antall, 
                               DATE_PART('year', k.first_buy)||'-'||to_char(date_part('month', k.first_buy), 'FM00') as periode
                          FROM brukar b 
                     LEFT JOIN kunde k 
                            ON b.uid=k.uid 
                     LEFT JOIN historie_ordre o 
                            ON o.uid=k.uid 
                         WHERE b.deleted is NULL 
                           AND b.registrert is not NULL 
                           AND o.tidspunkt BETWEEN '".$periodstart."-01 00:00:00' AND '".$periodend."-01 00:00:00'
                           AND o.pris > 0
                           AND o.deleted IS NULL
                           AND o.exported IS NOT NULL
                           AND o.serverlock IS NOT NULL
                           AND o.serverlock != ''
                           AND o.customerlock IS NOT NULL
                           AND DATE_PART('year', k.first_buy)||'-'||to_char(date_part('month', k.first_buy), 'FM00')
                             = DATE_PART('year', o.tidspunkt)||'-'||to_char(date_part('month', o.tidspunkt), 'FM00')
                      GROUP BY periode
                      ORDER BY periode;" );
   while( list( $sum, $period ) = $query->fetchRow() ) {
      $outputtable['New_Customers']['periods'][$period] = $sum;
   }  $outputtable['New_Customers']['artnrs'] = '-';
   echo "OK!\n";

   echo "Finding all customers in that month...";
   // loop through the periods and fetch more stats
   $outputtable['All_Users']['periods'] = $periods;
   $query = DB::query( "SELECT ( SELECT COUNT(qb.uid) 
                                   FROM brukar qb
                                   JOIN kunde qk
                                     ON qk.uid = qb.uid
                                  WHERE qb.deleted IS NULL 
                                    AND qb.passord != 'nopass'
                                    AND qb.registrert IS NOT NULL
                                    AND qb.registrert <= maxdato 
                               ) as antall,
                               data.periode
                         FROM ( SELECT DATE_PART('year', b.registrert)||'-'||to_char(date_part('month', b.registrert), 'FM00') as periode,
                                       MAX(b.registrert) as maxdato
                                  FROM brukar b 
                             LEFT JOIN kunde k 
                                    ON k.uid = b.uid
                                 WHERE b.deleted is NULL 
                                   AND b.registrert IS NOT NULL
                                   AND b.passord != 'nopass'
                                   AND b.registrert BETWEEN '".$periodstart."-01 00:00:00' AND '".$periodend."-01 00:00:00'
                              GROUP BY periode
                              ORDER BY periode
                              ) as data" );
   while( list( $sum, $period ) = $query->fetchRow() ) {
      $outputtable['All_Users']['periods'][$period] = $sum;
   }  $outputtable['All_Users']['artnrs'] = '-';
   echo "OK!\n";

   echo "Finding customers who bought something per month...";
   $outputtable['All_BoughtSomething']['periods'] = $periods;
   $query = DB::query( "SELECT COUNT(DISTINCT(a.uid)),
                               DATE_PART('year', a.tidspunkt)||'-'||to_char(date_part('month', a.tidspunkt), 'FM00') as periode
                          FROM historie_ordre as a 
                         WHERE a.deleted is null 
                           AND a.exported is not null 
                           AND a.pris > 0 
                           AND a.serverlock is not null 
                           AND a.serverlock!='' 
                           AND a.customerlock is not null 
                           AND a.tidspunkt BETWEEN '".$periodstart."-01 00:00:00' AND '".$periodend."-01 00:00:00'
                      GROUP BY periode
                      ORDER BY periode;" );
   while( list( $sum, $period ) = $query->fetchRow() ) {
      $outputtable['All_BoughtSomething']['periods'][$period] = $sum;
   }  $outputtable['All_BoughtSomething']['artnrs'] = '-';
   echo "OK!\n";
   
   echo "Finding all active customers...";
   $outputtable['All_ActiveCustomers']['periods'] = $periods;
   $query = DB::query( "SELECT (SELECT COUNT(DISTINCT(uid)) FROM historie_ordre WHERE tidspunkt BETWEEN fradato AND tildato AND pris > 0
                           AND deleted IS NULL
                           AND exported IS NOT NULL
                           AND serverlock IS NOT NULL
                           AND serverlock != ''
                           AND customerlock IS NOT NULL ) as antall, periode FROM
                           (
                           SELECT periode, maxperiod - interval '1 year' as fradato, maxperiod as tildato FROM(
                           
                           SELECT 
                           DATE_PART('year', o.tidspunkt)||'-'||to_char(date_part('month', o.tidspunkt), 'FM00') as periode,
                           MAX(o.tidspunkt) as maxperiod
                           FROM historie_ordre o
                           WHERE o.tidspunkt BETWEEN '".$periodstart."-01 00:00:00' AND '".$periodend."-01 00:00:00' 
                           AND o.pris > 0
                                                      AND o.deleted IS NULL
                                                      AND o.exported IS NOT NULL
                                                      AND o.serverlock IS NOT NULL
                                                      AND o.serverlock != ''
                                                      AND o.customerlock IS NOT NULL 
                           GROUP BY periode ORDER BY periode
                           ) as data ) as subquery;" );
   while( list( $sum, $period ) = $query->fetchRow() ) {
      $outputtable['All_ActiveCustomers']['periods'][$period] = $sum;
   }  $outputtable['All_ActiveCustomers']['artnrs'] = '-';
   echo "OK!\n";
     
   echo "Finding all orders...";
   $outputtable['All_Orders']['periods'] = $periods;
   $query = DB::query( "SELECT COUNT(*),
           DATE_PART('year', a.tidspunkt)||'-'||to_char(date_part('month', a.tidspunkt), 'FM00') as periode
      from historie_ordre as a 
     where a.deleted is null 
       and a.exported is not null 
       and a.serverlock is not null 
       and a.serverlock!='' 
       and a.customerlock is not null 
       and a.tidspunkt BETWEEN '".$periodstart."-01 00:00:00' AND '".$periodend."-01 00:00:00'
       GROUP BY periode
       ORDER BY periode" );
   while( list( $sum, $period ) = $query->fetchRow() ) {
      $outputtable['All_Orders']['periods'][$period] = $sum;
   }  $outputtable['All_Orders']['artnrs'] = '-';
   echo "OK!\n";
   
   echo "Finding existing customers with x orders...";
   $outputtable['Customers_under3']['periods'] = $periods;
   $outputtable['Customers_exactly3']['periods'] = $periods;
   $outputtable['Customers_over3']['periods'] = $periods;
   $outputtable['NOKOrders_under3']['periods'] = $periods;
   $outputtable['NOKOrders_exactly3']['periods'] = $periods;
   $outputtable['NOKOrders_over3']['periods'] = $periods;
   $query = DB::query( "SELECT set1.dato, under3, akkurat3, over3, NOKunder3, NOKakkurat3, NOKover3 FROM 
(
SELECT dato, COUNT(uid) as under3, SUM(pris) as NOKunder3 FROM (

SELECT uid, count, dato, pris FROM (

   SELECT uid, COUNT(ordrenr), SUM(pris) as pris, dato FROM (
        
        SELECT to_char( o.tidspunkt, 'YYYY-MM' ) as dato,
               o.uid, o.ordrenr, o.pris
          FROM historie_ordre o 
         WHERE o.tidspunkt BETWEEN '2009-01-01 00:00:00' AND '2010-06-01 00:00:00'
           AND o.pris > 0
           AND o.deleted IS NULL
           AND o.exported IS NOT NULL
           AND o.serverlock IS NOT NULL
           AND o.serverlock != ''
           AND o.customerlock IS NOT NULL 
        ) as data1

        GROUP BY uid, dato
   ) as data2

WHERE data2.count < 3
) as data3

GROUP BY data3.dato
ORDER BY data3.dato
) as set1

LEFT JOIN
(
SELECT dato, COUNT(uid) as akkurat3, SUM(pris) as NOKakkurat3 FROM (

SELECT uid, count, dato, pris FROM (

   SELECT uid, COUNT(ordrenr), dato, SUM(pris) as pris FROM (

        SELECT to_char( o.tidspunkt, 'YYYY-MM' ) as dato,
              o.uid, o.ordrenr, o.pris
                          FROM historie_ordre o 
                         WHERE o.tidspunkt BETWEEN '2009-01-01 00:00:00' AND '2010-06-01 00:00:00'
                           AND o.pris > 0
                           AND o.deleted IS NULL
                           AND o.exported IS NOT NULL
                           AND o.serverlock IS NOT NULL
                           AND o.serverlock != ''
                           AND o.customerlock IS NOT NULL 
        ) as data1

        GROUP BY uid, dato
   ) as data2

WHERE data2.count = 3
) as data3

GROUP BY data3.dato
ORDER BY data3.dato
) as set2

ON set1.dato = set2.dato

LEFT JOIN (
SELECT dato, COUNT(uid) as over3, SUM(pris) as NOKover3 FROM (

SELECT uid, count, dato, pris FROM (

   SELECT uid, COUNT(ordrenr), SUM(pris) as pris, dato FROM (

        SELECT to_char( o.tidspunkt, 'YYYY-MM' ) as dato,
              o.uid, o.ordrenr, o.pris
                          FROM historie_ordre o 
                         WHERE o.tidspunkt BETWEEN '2009-01-01 00:00:00' AND '2010-06-01 00:00:00'
                           AND o.pris > 0
                           AND o.deleted IS NULL
                           AND o.exported IS NOT NULL
                           AND o.serverlock IS NOT NULL
                           AND o.serverlock != ''
                           AND o.customerlock IS NOT NULL 
        ) as data1

        GROUP BY uid, dato
   ) as data2

WHERE data2.count > 3
) as data3

GROUP BY data3.dato
ORDER BY data3.dato
) as set3

ON set3.dato = set1.dato;
" );
   while( list( $period, $under3, $exactly3, $over3, $nokunder3, $nokakkurat3, $nokover3 ) = $query->fetchRow() ) {
      $outputtable['Customers_under3']['periods'][$period] = $under3;
      $outputtable['Customers_exactly3']['periods'][$period] = $exactly3;
      $outputtable['Customers_over3']['periods'][$period] = $over3;
      $outputtable['NOKOrders_under3']['periods'][$period] = number_format( $nokunder3, 2, ',', '' );
      $outputtable['NOKOrders_exactly3']['periods'][$period] = number_format( $nokakkurat3, 2, ',', '' );
      $outputtable['NOKOrders_over3']['periods'][$period] = number_format( $nokover3, 2, ',', '' );
   }
   echo "OK!\n";
   
   echo "Finding lost customers...";
   $outputtable['Lost_Customers']['periods'] = $periods;
   $query = DB::query( "SELECT dato,
                              ( SELECT COUNT(uid) FROM (
                              ( SELECT DISTINCT(uid) FROM historie_ordre WHERE tidspunkt BETWEEN minus13 AND maxperiod
                                      AND pris > 0
                                      AND deleted IS NULL
                                      AND exported IS NOT NULL
                                      AND serverlock IS NOT NULL
                                      AND serverlock != ''
                                      AND customerlock IS NOT NULL)
                              EXCEPT
                              ( SELECT DISTINCT(uid) FROM historie_ordre WHERE tidspunkt BETWEEN minus12 AND maxperiod
                                      AND pris > 0
                                      AND deleted IS NULL
                                      AND exported IS NOT NULL
                                      AND serverlock IS NOT NULL
                                      AND serverlock != ''
                                      AND customerlock IS NOT NULL)
                              ) as subdata ) as customerslost
                            FROM (
                                   SELECT to_char( tidspunkt, 'YYYY-MM' ) as dato,
                                                 MAX(tidspunkt) - interval '13 months' as minus13,
                                                 MAX(tidspunkt) - interval '12 months' as minus12,
                                                 MAX(tidspunkt) + interval '1 minute' as maxperiod
                                     FROM historie_ordre
                                    WHERE tidspunkt BETWEEN '2009-01-01 00:00:00' AND '2010-06-01 00:00:00'
                                      AND pris > 0
                                      AND deleted IS NULL
                                      AND exported IS NOT NULL
                                      AND serverlock IS NOT NULL
                                      AND serverlock != ''
                                      AND customerlock IS NOT NULL 
                           GROUP BY dato
                           ORDER BY dato
                           ) as data;" );
   while( list( $period, $sum ) = $query->fetchRow() ) {
      $outputtable['Lost_Customers']['periods'][$period] = $sum;
   }  $outputtable['Lost_Customers']['artnrs'] = '-';
   echo "OK!\n";
   
   /*
   DROP TABLE IF EXISTS revenuestats_temptable;
   CREATE TABLE revenuestats_temptable (lower int, higher int);
   INSERT INTO revenuestats_temptable VALUES (0, 250), (250,750), (750, 1500), (1500, 5000), (5000, 999999999);
   */
   
   /*
   SELECT lower, higher, (
      SELECT COUNT(uid) FROM ( 
      
         SELECT uid, COUNT(ordrenr) as ordercount, SUM(pris) as revenue FROM (
      
              SELECT o.uid, o.ordrenr, o.pris
                                FROM historie_ordre o 
                               WHERE o.tidspunkt BETWEEN '2000-01-01 00:00:00' AND NOW()
                                 AND o.pris > 0
                                 AND o.deleted IS NULL
                                 AND o.exported IS NOT NULL
                                 AND o.serverlock IS NOT NULL
                                 AND o.serverlock != ''
                                 AND o.customerlock IS NOT NULL 
              ) as source
      
              GROUP BY uid
      
      ) AS data
       
      WHERE data.revenue BETWEEN lower AND higher
      
      AND data.ordercount = 1) as oneorder,
      (
      SELECT COUNT(uid) FROM ( 
      
         SELECT uid, COUNT(ordrenr) as ordercount, SUM(pris) as revenue FROM (
      
              SELECT o.uid, o.ordrenr, o.pris
                                FROM historie_ordre o 
                               WHERE o.tidspunkt BETWEEN '2000-01-01 00:00:00' AND NOW()
                                 AND o.pris > 0
                                 AND o.deleted IS NULL
                                 AND o.exported IS NOT NULL
                                 AND o.serverlock IS NOT NULL
                                 AND o.serverlock != ''
                                 AND o.customerlock IS NOT NULL 
              ) as source
      
              GROUP BY uid
      
      ) AS data
       
      WHERE data.revenue BETWEEN lower AND higher
      AND data.ordercount = 2) as twoorders,
      
      (
      SELECT COUNT(uid) FROM ( 
      
         SELECT uid, COUNT(ordrenr) as ordercount, SUM(pris) as revenue FROM (
      
              SELECT o.uid, o.ordrenr, o.pris
                                FROM historie_ordre o 
                               WHERE o.tidspunkt BETWEEN '2000-01-01 00:00:00' AND NOW()
                                 AND o.pris > 0
                                 AND o.deleted IS NULL
                                 AND o.exported IS NOT NULL
                                 AND o.serverlock IS NOT NULL
                                 AND o.serverlock != ''
                                 AND o.customerlock IS NOT NULL 
              ) as source
      
              GROUP BY uid
      
      ) AS data
       
      WHERE data.revenue BETWEEN lower AND higher
      AND data.ordercount > 2) as above2orders

FROM ( SELECT lower, higher FROM revenuestats_temptable ORDER BY lower ASC ) as periods;
*/
  
   /*
    


SELECT lower, higher, (
      SELECT COUNT(uid) FROM ( 
      
         SELECT uid, COUNT(ordrenr) as ordercount, SUM(pris) as revenue FROM (
      
              SELECT o.uid, o.ordrenr, o.pris
                                FROM historie_ordre o 
                               WHERE o.tidspunkt < '2010-05-31 23:59:59'
                                 AND o.pris > 0
                                 AND o.deleted IS NULL
                                 AND o.exported IS NOT NULL
                                 AND o.serverlock IS NOT NULL
                                 AND o.serverlock != ''
                                 AND o.customerlock IS NOT NULL 
                                 AND o.uid IN (
                                                   SELECT o.uid
                                FROM historie_ordre o 
                               WHERE o.tidspunkt BETWEEN  '2009-05-31 23:59:59' AND '2010-05-31 23:59:59'
                                 AND o.pris > 0
                                 AND o.deleted IS NULL
                                 AND o.exported IS NOT NULL
                                 AND o.serverlock IS NOT NULL
                                 AND o.serverlock != ''
                                 AND o.customerlock IS NOT NULL 
                                )
              ) as source
      
              GROUP BY uid
      
      ) AS data
       
      WHERE data.revenue BETWEEN lower AND higher
      
      AND data.ordercount = 1) as oneorder,
      (
      SELECT COUNT(uid) FROM ( 
      
         SELECT uid, COUNT(ordrenr) as ordercount, SUM(pris) as revenue FROM (
      
              SELECT o.uid, o.ordrenr, o.pris
                                FROM historie_ordre o 
                               WHERE o.tidspunkt < '2010-05-31 23:59:59'
                                 AND o.pris > 0
                                 AND o.deleted IS NULL
                                 AND o.exported IS NOT NULL
                                 AND o.serverlock IS NOT NULL
                                 AND o.serverlock != ''
                                 AND o.customerlock IS NOT NULL 
                                 AND o.uid IN (
                                                   SELECT o.uid
                                FROM historie_ordre o 
                               WHERE o.tidspunkt BETWEEN  '2009-05-31 23:59:59' AND '2010-05-31 23:59:59'
                                 AND o.pris > 0
                                 AND o.deleted IS NULL
                                 AND o.exported IS NOT NULL
                                 AND o.serverlock IS NOT NULL
                                 AND o.serverlock != ''
                                 AND o.customerlock IS NOT NULL 
                                )
              ) as source
      
              GROUP BY uid
      
      ) AS data
       
      WHERE data.revenue BETWEEN lower AND higher
      AND data.ordercount = 2) as twoorders,
      
      (
      SELECT COUNT(uid) FROM ( 
      
         SELECT uid, COUNT(ordrenr) as ordercount, SUM(pris) as revenue FROM (
      
              SELECT o.uid, o.ordrenr, o.pris
                                FROM historie_ordre o 
                               WHERE o.tidspunkt < '2010-05-31 23:59:59'
                                 AND o.pris > 0
                                 AND o.deleted IS NULL
                                 AND o.exported IS NOT NULL
                                 AND o.serverlock IS NOT NULL
                                 AND o.serverlock != ''
                                 AND o.customerlock IS NOT NULL 
                                 AND o.uid IN (
                                                   SELECT o.uid
                                FROM historie_ordre o 
                               WHERE o.tidspunkt BETWEEN  '2009-05-31 23:59:59' AND '2010-05-31 23:59:59'
                                 AND o.pris > 0
                                 AND o.deleted IS NULL
                                 AND o.exported IS NOT NULL
                                 AND o.serverlock IS NOT NULL
                                 AND o.serverlock != ''
                                 AND o.customerlock IS NOT NULL 
                                )
              ) as source
      
              GROUP BY uid
      
      ) AS data
       
      WHERE data.revenue BETWEEN lower AND higher
      AND data.ordercount > 2) as above2orders

FROM ( SELECT lower, higher FROM revenuestats_temptable ORDER BY lower ASC ) as periods;
*/
   
   /*
   DROP TABLE revenuestats_temptable;
   */
   
   
   echo "\n\nSECTION;".implode(';', array_keys( $periods ) )."\n";
   
   foreach( $outputtable as $section => $data ) {
      
      echo "$section;"./*$data['artnrs'].";".*/implode( ';', $data['periods'] )."\n";
      
   }
   
?>