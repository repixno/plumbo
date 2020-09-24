<?PHP
   
   exit;
   
   include "../../bootstrap.php";
   config('website.config');
   
   $query = DB::query( 'SELECT aid, uid FROM bildealbum WHERE created_time > ? AND created_time < ? AND deleted_at IS NULL AND sharekey IS NOT NULL', '2010-05-18 12:00:00', '2010-05-19 06:15:00' );
   while( list( $aid, $uid ) = $query->fetchRow() ) {
      
      $numimages = (int) DB::query( 'SELECT COUNT(bid) FROM bildeinfo WHERE aid = ?', $aid )->fetchSingle();
      DB::query( "UPDATE bildeinfo SET deleted_at = '2010-05-19 01:02:03' WHERE aid = ? AND deleted_at IS NULL", $aid );
      DB::query( "UPDATE bildealbum SET deleted_at = '2010-05-19 01:02:03' WHERE aid = ? AND deleted_at IS NULL", $aid );
      DB::query( 'INSERT INTO fuckedup_album (aid, uid, numimages, deleted_at) VALUES (?, ?, ?, NOW())', $aid, $uid, $numimages );
      
   }
   
   $query = DB::query( 'SELECT bid FROM bildeinfo WHERE deleted_at IS NULL AND "time" > ? AND "time" < ?', '2010-05-18 12:00:00', '2010-05-19 06:15:00' );
   while( list( $bid ) = $query->fetchRow() ) {
      
      DB::query( "UPDATE bildeinfo SET deleted_at = '2010-05-19 02:03:04' WHERE bid = ? AND deleted_at IS NULL", $bid );
      
   }
   
?>