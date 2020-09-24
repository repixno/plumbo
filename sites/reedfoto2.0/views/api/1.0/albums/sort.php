<?php

   /**
    * Takes a javascript array as params and sets
    * sorting according to album id order in array.
    *
    * @author Andreas F�rnstrand <andreas.farnstrand@interweb.no>
    *
    */

   import( 'pages.json' );
   import( 'website.album' );

   class APIAlbumsSort extends JSONPage implements IView {

      /**
       * Sort albums by a array of album ID's
       * 
       * @api-name albums.sort
       * @api-auth required
       * @api-post order String JSON-array of album ID's in the order you want albums
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */  
      public function Execute() {

         $orders = array();
         $order = array();

         $order = $_POST['order'];
         $orders = json_decode( $order );

         if( count( $orders ) == 0 ) {
            $this->result = false;
            $this->message = 'Required input parameter missing or invalid (id)';
            return false;
         }

         $this->result = false;
         $this->message = 'Failed to set sort order on album. No access.';

         foreach( $orders as $index => $id ) {

            if( is_numeric( $id ) && is_numeric( $index ) ) {

               $album = new Album( $id );
               if( !$album instanceof Album && !$album->isLoaded() ) return false;

               $album->sorting = $index;
               $album->save();

            }

         }

         $this->result = true;
         $this->message = 'OK';
         return true;

      }

   }


?>