<?PHP
model( 'production.conversions' );

class Conversions extends DBConversions {
   
   /*
    * @return ready conversions orders
    */
   static function GetReadyArray(){
      
         $collection = new Conversions();
         $ret = array();
         foreach( $collection->collection( null, array(  'status' => 'ready' ))->fetchAll( DB::FETCH_ASSOC ) as $collections ) {
            $ret[] = $collections;
         }
         return $ret;
   }

   
 
}

?>