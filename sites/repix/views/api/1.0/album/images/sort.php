<?php


   /**
    * Takes a javascript array as params and sets
    * sorting according to image id order in array.
    *
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    *
    */

   import( 'pages.json' );
   import( 'website.image' );

   class APIImageSort extends JSONPage implements IView {

       /**
       * Sorts images in an album by a array of image ID's
       * 
       * @api-name album.images.sort
       * @api-auth required
       * @api-post order String JSON-array of image ID's in the order you want the images in the album
       * @api-post albumid Integer ID of the album to order
       * @api-post-optional offset Integer page order offset
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */     
      public function Execute() {

         $orders = array();
         $order = array();

         $order = $_POST['order'];
         $albumid = (int) $_POST['albumid'];
         $orders = json_decode( $order );
         $pageoffset = (int) $_POST[ 'offset' ];

         $this->result = false;
         $this->message = 'Required input parameter missing or invalid (albumid)';
         if( !$albumid > 0 ) return false;

         if( count( $orders ) == 0 ) {
            $this->result = false;
            $this->message = 'No image id given';
            return false;
         }

         $this->result = false;
         $this->message = 'Failed to set sort order on image. No access.';
         if( !is_array( $orders ) ) {
            $orders = explode( ',', $orders );
         }

         if( count( $orders ) == 0 ) {
            $this->result = false;
            $this->message = 'No image id given';
            return false;
         }


         foreach( $orders as $index => $id ) {

            if( is_numeric( $id ) && is_numeric( $index ) && is_numeric( $pageoffset ) ) {

               $image = new Image( $id );
               if( !$image instanceof Image && !$image->isLoaded() ) return false;

               $this->result = false;
               $this->message = 'No access to this image';
               if( $image->getOwnerId() != Login::userid() ) return false;

               $this->result = false;
               $this->message = 'Album id does not match';
               if( $albumid != $image->aid ) return false;

               $image->sorting = $index + $pageoffset;
               $image->save();

            }

         }

         $this->result = true;
         $this->message = 'OK';
         return true;

      }

   }

?>