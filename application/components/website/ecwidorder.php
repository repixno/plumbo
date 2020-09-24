<?php

   /**
    * @author Svein Arild Bergset <sab@interweb.no>
    *
    */

   import( 'core.util' );
   model( 'user.orders' );

   class EcwidOrder extends DBUserOrders {

      public function getStatus() {

         $queryString = "
            SELECT
               distinct(ordreskjemaid) AS schema
            FROM
               historie_ordrelinje
            WHERE
               ordrenr=? AND
               ordreskjemaid IS NOT NULL
            ORDER BY
               ordreskjemaid
            ";

         $status = array(
            'isdeleted' => false,
            'isreceived' => true,
            'isproduction' => true,
            'isproduced' => true,
            'issent' => true
            );
         $curStatus = 4;
         $numDeleted = $numTotal = 0;
         $counter = 0;
         foreach ( DB::query( $queryString, $this->ordernum )->fetchAll() as $schema ) {

            $counter++;

            $schemaStatus = $this->findStatus( $schema[0] );

            // Find the least progressed status.
            if ( $schemaStatus <= $curStatus ) {

               switch ( $schemaStatus ) {

                  case 1:
                     $status[ 'isproduction' ] = false;
                     $status[ 'isproduced' ] = false;
                     $status[ 'issent' ] = false;
                     break;
                  case 2:
                     $status[ 'isproduced' ] = false;
                     $status[ 'issent' ] = false;
                     break;
                  case 3:
                     $status[ 'issent' ] = false;
                     break;
                  case 4:
                     break;

               }

               $numTotal++;

               $curStatus = $schemaStatus;

            }

            // Schema is deleted or simply just don't exist anymore.
            if ( $schemaStatus == 98 || $schemaStatus == 99 ) {

               $numDeleted++;
               $numTotal++;

            }

         }

         // The whole order is deleted if all subschemas are deleted for that order.
         // Also run this if no order lines have corresponding schemas (order is not received).
         if ( !$counter || $this->deleted || ( $numTotal > 0 && $numDeleted == $numTotal ) ) {

            foreach ( $status as $key=>$val ) {

               $status[ $key ] = false;

            }

            $status[ 'isdeleted' ] = true;

         }

         // Undelete order if order is not received (was set in previous block).
         if ( !$counter && !$this->deleted ) {

            $status[ 'isdeleted' ] = false;

         }

         return $status;

      }

      private function findStatus( $id ) {

         //1 = Ikke til produksjon
         //2 = I produksjon
         //3 = Produsert
         //4 = Pakket
         //98 = deleted
         //99 = general error

         if ( !is_numeric( $id ) ) {

            return 99;

         }

         $data = DB::query( "SELECT * FROM ordreskjema WHERE skjemaid=?", $id )->fetchAssoc();

         if ( !$data["skjemaid"] ) {

            return 1;

         }

         if ( DB::query( "SELECT count(*) FROM historie_ordre WHERE ordrenr=? AND deleted IS NOT NULL", $data["ordrenr"] )->fetchSingle() ) {

            return 98;

         }

         if ( DB::query( "SELECT count(*) FROM historie_pakke_skjema WHERE skjemaid=? AND tidspunkt IS NOT NULL", $id )->fetchSingle() ) {

            return 4;

         }

         if ( DB::query( "SELECT count(*) FROM skjema_tracker WHERE skjemaid=? AND action=2858", $id )->fetchSingle() ) {

            return 3;

         }

         if ( DB::query( "SELECT count(*) FROM skjema_tracker WHERE skjemaid=? AND action=2857", $id )->fetchSingle() ) {

            return 2;

         }

         return 1;

      }

      public function isDeleted() {

         return $this->deleted ? true : false;

      }

      public function getItems( $ordernum = null ) {

         $id = isset( $ordernum ) ? $ordernum : $this->ordernum;

         //$orderData = DB::query( "SELECT * FROM historie_ordre WHERE ordrenr=?", $id )->fetchAll( DB::FETCH_ASSOC  );

         $orders = array();
         foreach ( DB::query( "SELECT * FROM historie_ordrelinje WHERE ordrenr=?", $id )->fetchAll( DB::FETCH_ASSOC ) as $item ) {

            $orders[] = array(
        //       'artikkelnr' => $item['artikkelnr'],
               'title' => $item[ 'tekst' ],
               'description' => $item[ '' ],
               'quantity' => $item[ 'antall' ],
               'unitprice' => $item[ 'pris' ],
               'price' => ( $item[ 'antall' ] * $item[ 'pris' ] ),
               'attributes' => $item['attributes']
               );

         }

         return $orders;

      }
      
      public function getDelivery( $ordernum = null ){
         
         $id = isset( $ordernum ) ? $ordernum : $this->ordernum;
         
         $delivery = DB::query( "SELECT * FROM historie_kunde WHERE ordrenr=?", $id )->fetchAll( DB::FETCH_ASSOC );
         
         return $delivery;
         
         
      }
      
      
      static function fromOrderNo( $orderno ) {
         
         $id = DB::query( "SELECT id FROM historie_ordre WHERE ordrenr = ?", $orderno )->fetchSingle();
         
         if( $id > 0 ) {
            
            try {
               
               $order = new Order( $id );
               if( $order->uid == Login::userid() ) {
                  return $order;
               }
               
            } catch( Exception $e ) {
               
               return false;
               
            }
            
         }
         
         return false;
         
      }


   }

?>