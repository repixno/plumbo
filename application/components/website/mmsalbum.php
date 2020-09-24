<?php

   /**
    * @author André Nordstrand <andre@iw.no>
    * 
    */
	
   import( 'core.util' );
   model( 'album.mms' );

   class MMSAlbum extends DBMMS {
      
      public function asArray() {
         
         return array(
            'id'        => $this->mmsid,
            'bid'       => $this->bid,
            'code'      => $this->mmskode,
            'uid'       => $this->uid,
            'path'      => $this->path,
            'thumbnail' => $this->getThumbnail(),
			);
         
      }
      
      public function getThumbnail() {
         
         return sprintf( '%s/bildearkiv/%s/%smmsimage0.jpg',
            WebsiteHelper::rootBaseUrl(),
            $this->path,
            $this->code
         );
         
      }
      
   }

?>