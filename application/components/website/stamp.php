<?PHP

   model( 'site.stamp' );
   
   class Stamp extends DBSiteStamp {
      
      static function enumNotYetApproved() {
         
         $jobs =  array();
         
         $stamps = new Stamp();
   		foreach( $stamps->collection( 'stampid', array( 'approved' => null, 'declined' => null ), 'stampid ASC' )->fetchAllAs('Stamp') as $stamp ) {
   			
   		   //$date = reset( explode( '/', $stamp->jobname ) );
            $date = explode( '/' , $stamp->jobname );
            $date = $date[0];
   		   $imageid = DB::query( "select bid from  historie_mal  where filnamn  = '$stamp->imagename'" )->fetchSingle();
   		   
   		   PermissionManager::current()->grantAccessTo( $imageid, 'image', PERMISSION_OWNER );
   		   
   		   try{
   		      $image = new Image($imageid);
   		      $jobs []= array(
   		       'stampid'   => $stamp->stampid,
   		       'date'      => $stamp->imagename,
   		       'jobname'   => $stamp->jobname,
   		       'imagename' => $stamp->imagename,
   		       'filepath'  => $image->getURL( 200, 200),
   		       );
   		   }catch (Exception $e){
   		      mail( 'tor.inge@eurofoto.no' , 'stampbugs', "iamgeid: $imageid stampid: " .  $stamp->stampid);
   		   }
   		   
   		   #'/images/stamps/thumbs/width/200/'.$stamp->jobname.'/'.$stamp->imagename,

         
         }

         return $jobs;
         
      }
      
      static function getStampsByOrderid( $orderid ){
         
         $stampIds = array();
         
         foreach ( DB::query( "SELECT stampid FROM history_stamps where orderid = ?", $orderid)->fetchAll() as $stampid ){
            list( $stampid ) = $stampid;
            $stampIds[] = $stampid;
         }
         $jobs =  array();
         
   		foreach( $stampIds as $stamps ) {
   			
   		   $stamp = new Stamp( $stamps );
   		   
            $date = explode( '/' , $stamp->jobname );
            $date = $date[0];
   		   $imageid = DB::query( "select bid from  historie_mal  where filnamn  = '$stamp->imagename'" )->fetchSingle();
   		   
   		   PermissionManager::current()->grantAccessTo( $imageid, 'image', PERMISSION_OWNER );
   		   
   		   try{
   		      $jobs []= array(
   		       'stampid'   => $stamp->stampid,
   		       'date'      => $stamp->created,
   		       'jobname'   => $stamp->jobname,
   		       'imagename' => $stamp->imagename,
   		       'filepath'  => sprintf( 'http://www.eurofoto.no/images/stream/image/%d/200/200', $imageid),
   		       'declinereason' => $stamp->declinereason,
   		       'declined'      => $stamp->declined,
   		       'approved'      => $stamp->approved,
   		       'processed'     => $stamp->processed
   		       );
   		   }catch (Exception $e){
   		      mail( 'tor.inge@eurofoto.no' , 'stampbugs', "iamgeid: $imageid stampid: " .  $stamp->stampid);
   		   }
   		   

   		   #'/images/stamps/thumbs/width/200/'.$stamp->jobname.'/'.$stamp->imagename,

         }

         return $jobs;
      }
      
   }


?>