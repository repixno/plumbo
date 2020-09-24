<?PHP
model( 'order.row' );

class OrderRow extends DBOrderRow {

   static function asArray( $orderid ){
      $collection = new Conversions();
      $ret = array();
      foreach( $collection->collection( null, array( 'ordrenr' => $orderid ))->fetchAll( DB::FETCH_ASSOC ) as $collections ) {
         $ret[] = $collections;
      }
      return $ret;
   }
   
   
   static function Classic( $orderid ){
      $orderrow = new OrderRow();
      return (int) $orderrow->collection( array( 'id' ), array( 'artikkelnr' => 465, 'ordrenr' => $orderid ), null, 1 )->fetchSingle();
   }
   
   
   
   
}

?>