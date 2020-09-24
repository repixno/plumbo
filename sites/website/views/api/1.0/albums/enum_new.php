<?php

   /**
    * Enumerate user's albums
    * Supports limit and offset
    * 
    * @author Tor Inge Lovland <tor.inge@eurofoto.no
    * 
    *
    */
   
   import( 'pages.json' );
   import( 'website.album' );
   
   class APIAlbumsEnum extends JSONPage implements IView {
      
      public function Validate() {
         
         return array(
            "execute" => array(
               "post" => array(
                  "offset" => VALIDATE_INTEGER,
                  "limit" => VALIDATE_INTEGER,
               ),
               "fields" => array(
                  VALIDATE_INTEGER,
                  VALIDATE_INTEGER,
               )
            )
         );
         
      }

      /**
       * Returns a list of albums
       * 
       * @api-name albums.enum
       * @api-auth required
       * @api-javascript yes
       * @api-post-optional offset Integer The Album offset to start at, default = 0 (first album in the set)
       * @api-post-optional limit Integer The maximum number of albums to return in the collection
       * @api-result albums Array List of Album objects.
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      
      public function Execute( $offset = 0, $limit = 0 ) {
         
         // Check post values
         if( isset( $_POST["offset"] ) ) {
            $offset = $_POST["offset"];
         }
         if( isset( $_POST["limit"] ) ) {
            $limit = $_POST["limit"];
         }
         
   		
   		$albumlist = Album::enum( $offset, $limit, true, true, true, true );
   		
   		$this->result = true;
   		$this->albums = $albumlist;
   		$this->message = "OK";
         
      }
      
   }

?>