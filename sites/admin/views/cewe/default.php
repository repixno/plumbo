<?php

   /**
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'pages.admin' );
   
   model( 'user.orders' );
   
   class AdminCewe extends AdminPage implements IView {
      
      protected $template = 'cewe.default';
      
      public function Execute() {
         
         $failed = array();
         
         $res = DB::query( "
            SELECT
               ordrenr,
               tidspunkt,
               kampanje_kode,
               to_production,
               source,
               cewe_export
            FROM
               historie_ordre
            WHERE
               cewe_export = 'failed'
            ORDER BY
               ordrenr
            DESC
         " );
         
         if( $res->count() > 0 ) {
            
            while( list( $orderid, $orderedtime, $portal, $productiontime, $source, $error ) = $res->fetchRow() ) {
               
               $productiontime = isset( $productiontime ) ? date( 'Y-m-d H:i:s', strtotime( $productiontime ) ) : null;
               
               $failed []= array(
                  'orderid'      => $orderid,
                  'time'         => date( 'Y-m-d H:i:s', strtotime( $orderedtime ) ),
                  'portal'       => $portal,
                  'toproduction' => $productiontime,
                  'source'       => $source,
               );
               
            }
            
            
         }
         
         $this->numfailed = count( $failed );
         $this->failed = $failed;
         
      }
      
      
      /**
       * Reset the cewe order to original state
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       *
       */
      public function reset() {
         
         $orderid = (int) $_POST['orderid'];
         
         if( $orderid > 0 ) {
            
            try {
               
               $order = DBUserOrders::fromFieldValue( array( 'ordrenr' => $orderid ), 'DBUserOrders' );
               if( $order instanceof DBUserOrders && $order->isLoaded() ) {
                  
                  if( $order->cewe_export == 'failed' ) {
                     $order->cewe_export = 'ready';
                     $order->save();
                  }
                  
                  echo json_encode( array(
                     'result' => true,
                     'message' => 'OK',
                  ) );
                  die();
                  
               }
               
               
            } catch( Exception $e ) {
               
               echo json_encode( array(
                     'result' => false,
                     'message' => 'Failed to load order',
               ));
               die();
               
               
            }
             
         } else {
            
            echo json_encode( array(
               'result' => false,
               'message' => 'Missing id',
            ));
            die();
            
         }
         
         echo json_encode( array(
            'result' => false,
            'message' => 'Unknown error',
         ));
         die();
         
      }
      
   }


?>