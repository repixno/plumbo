<?php
   
   /*
    
    CREATE TABLE reedfoto_album (
      id serial,
      identifier varchar(255),
      uniqueid integer,
      fname varchar(255),
      ename varchar(255),
      address varchar(255),
      zip integer,
      city varchar(255),
      aid integer,
      activated timestamp with time zone,
      activatedby integer
    )
    */
   
   import( 'website.reedfoto.reedfotoalbum');
   
   
   class FetchReedfotoDefault extends WebPage implements IView {
      
      protected  $template = 'fetchalbum.barcode';
      
      public function Execute() {
                  
         if( Login::isLoggedIn() ){
            $this->reedfoto = $this->enumReedfoto();
         }
      }
      
      
      
	  public function Norwaycup(){
		 
		 $this->template = "fetchalbum.barcodenc";
		 
		 
	  }
	  
      
      private function enumReedfoto( $limit = 6 ) {
         $res = array_reverse( DB::query( "SELECT aid FROM tilgangtilalbum_dedikert WHERE uid = ?", Login::userid() )->fetchAll() );

         $sharedList = array();
         while( count( $res ) > 0 && count( $sharedList ) < $limit ) {
            list( $albumid ) = array_pop( $res );
            if( !isset( $sharedList[$albumid] ) ) {
               try {
                  $album = new Album( $albumid );
		  if( $album->uid == 943910 ){
		     $sharedList[$albumid]= $album->asSimpleArray();
		  }
               } catch( Exception $e ) {}
            }
         }

         return array_values( $sharedList );

      }
      

   } 
?>