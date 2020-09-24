<?php

   model( 'order.merkelapptip' );

   class UserMerkelappTip extends DBMerkelappTip  {
      
      
      static function tippedFriends( $userid ){
         
         $friends = array();
         foreach ( DB::query( "SELECT * FROM merkelapp_tip WHERE userid=?", $userid )->fetchAll( DB::FETCH_ASSOC ) as $item ) {

            $friends[] = array(
               'id' => $item[ 'id' ],
               'userid' => $item[ 'userid' ],
               'friends_email' => $item[ 'friends_email' ],
               'coupon_code' => $item[ 'coupon_code' ],
               'date_tipped' => $item[ 'date_tipped' ],
               );

         }
         
         return $projects;
         
      }
      
      
      public function checkFriend( $email ){
         
         try{
            return DB::query( "SELECT * FROM merkelapp_tip WHERE friends_email ilike ?", $email )->fetchSingle();
         }catch ( Exception  $e){
            return false;
         }
         
         
      }
      
   }


?>