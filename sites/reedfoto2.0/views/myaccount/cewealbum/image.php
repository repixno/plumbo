<?php

   /**

    * 
    */

    $api = new ceweApi();

   class MyAccountAlbumImage extends UserPage implements IValidatedView {
      
      protected $template = 'myaccount.cewealbum.showimage';
      
      public function Validate() {
         
         return array(
            "execute"   => array(
               "request"   => array(
                  "bid"       => VALIDATE_STRING,
                  "aid"       => VALIDATE_STRING,
               ),
               "fields" => array(
                  "bid"    => VALIDATE_STRING,
                  "aid"    => VALIDATE_STRING,
               )
            )
         
         );
         
      }
      
      public function Execute(  $bid = null, $aid = null ) {
         
         $api = new ceweApi();
         $image = $api->getApi('/photos/' . $bid );
         
         $this->image = $api->ceweImageArray($image, $aid );
         
         $images = $api->getApi( '/photoAlbums/' . $aid  . '/photos');
         
         $imagelist = array();
         foreach( $images->photos as $ret ){
			$imagelist[] = $ret->id;
		}
        
         $this->album = $api->ceweAlbumArray($cewealbums);
         
         
         $position = array_search( $bid, $imagelist );
         
         $this->imagenumber = $position + 1;
         $this->imagecount = count( $imagelist );
         
         if( $position > 0 ) {
            try {
               
               $previmage = $api->getApi('/photos/' . $imagelist[$position - 1] );
               $this->previmage = $api->ceweImageArray($previmage, $aid );
            } catch( Exception $e ) {}
         }
         
         if( $position < count( $imagelist ) - 1 ) {
            try {
               $nextimage = $api->getApi('/photos/' . $imagelist[$position + 1] );
               $this->nextimage = $api->ceweImageArray($nextimage, $aid);
               
            } catch( Exception $e ) {}
         }
         
      }
      
      
   }



?>