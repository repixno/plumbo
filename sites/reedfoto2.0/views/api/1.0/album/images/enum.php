<?php


   /**
    * Enumerate images paginated by offset and limit
    * works with post of regular fields.
    * 
    * @author Andreas F�rnstrand <andreas.farnstrand@interweb.no>
    *
    */
   
   import( 'pages.json' );
   import( 'website.album' );
   import( 'website.image' );
   import( 'website.user' );
   
   class APIAlbumImagesEnum extends JSONPage implements IValidatedView {
      
      public function Validate() {
         
         return array(
            "execute" => array(
               "post" => array(
                  "albumid" => VALIDATE_INTEGER,
                  "offset" => VALIDATE_INTEGER,
                  "limit" => VALIDATE_INTEGER,
                  "sortby" => VALIDATE_STRING,
                  "sorttype" => VALIDATE_INTEGER,
                  "returnfields" => VALIDATE_STRING
               ),
               "fields" => array(
                  VALIDATE_INTEGER,
                  VALIDATE_INTEGER,
                  VALIDATE_INTEGER,
                  VALIDATE_STRING,
                  VALIDATE_INTEGER,
                  VALIDATE_STRING
               )
            ),
            "basiclist" => array(
               "post" => array(
                  "albumid" => VALIDATE_INTEGER,
                  "offset" => VALIDATE_INTEGER,
                  "limit" => VALIDATE_INTEGER,
                  "sortby" => VALIDATE_STRING,
                  "sorttype" => VALIDATE_INTEGER,
               ),
               "fields" => array(
                  VALIDATE_INTEGER,
                  VALIDATE_INTEGER,
                  VALIDATE_INTEGER,
                  VALIDATE_STRING,
                  VALIDATE_INTEGER,
               )
            )
         );
         
      }
      
      /**
       * Returns a basic list of images within a given albumid (stripped down structure with id,title,thumb and screensize thumb)
       * 
       * @api-name album.images.enum.basiclist
       * @api-auth required
       * @api-example
       * @api-post-optional albumid Integer The albumid to enumerate
       * @api-post-optional offset Integer The image offset to start at, default = 0 (first image in the set)
       * @api-post-optional limit Integer The maximum number of images to return in the collection
       * @api-post-optional sortby string Sorting field, (default, title, date or time)
       * @api-post-optional sorttype Integer Sorting order, ascending (0) or descending (1)
       * @api-param-optional albumid Integer The albumid to enumerate
       * @api-param-optional offset Integer The image offset to start at, default = 0 (first image in the set)
       * @api-param-optional limit Integer The maximum number of images to return in the collection
       * @api-param-optional sortby string Sorting field, (default, title, date or time)
       * @api-param-optional sorttype Integer Sorting order, ascending (0) or descending (1)
       * @api-result images Array List of Image objects.
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      
      public function BasicList( $aid = 0, $offset = 0, $limit = 0, $sortby = 'default', $sorttype = 0 ) {
         
         $this->Execute( $aid, $offset, $limit, $sortby, $sorttype );
         
         $result = $this->images;
         $images = array();
         if( is_array( $result ) ) {
            
            foreach( $result as $image ) {
               
               $images[] = array(
                  'id'    => $image['id'],
                  'title' => $image['title'],
                  'thumb' => $image['thumbnail'],
                  'screen'=> $image['screensize'],
               );
               
            }
            
            $this->result = true;
            $this->images = $images;
            
         }
         
      }

      /**
       * Returns a list of images within a given albumid
       * 
       * @api-name album.images.enum
       * @api-auth required
       * @api-example
       * @api-post-optional albumid Integer The albumid to enumerate
       * @api-post-optional offset Integer The image offset to start at, default = 0 (first image in the set)
       * @api-post-optional limit Integer The maximum number of images to return in the collection
       * @api-post-optional sortby string Sorting field, (default, title, date or time)
       * @api-post-optional sorttype Integer Sorting order, ascending (0) or descending (1)
       * @api-param-optional albumid Integer The albumid to enumerate
       * @api-param-optional offset Integer The image offset to start at, default = 0 (first image in the set)
       * @api-param-optional limit Integer The maximum number of images to return in the collection
       * @api-param-optional sortby string Sorting field, (default, title, date or time)
       * @api-param-optional sorttype Integer Sorting order, ascending (0) or descending (1)
       * @api-result images Array List of Image objects.
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
       
      public function Execute( $aid = 0, $offset = 0, $limit = 0, $sortby = 'default', $sorttype = 0, $returnfields = '' ) {
         
         if( isset( $_POST['albumid'] ) ) {
            $aid = $_POST['albumid'];
         }
         
         if( isset( $_POST['offset'] ) ) {
            $offset = $_POST['offset'];
         }
         
         if( isset( $_POST['limit'] ) ) {
            $limit = $_POST['limit'];
         }
         
         if( isset( $_POST['sortby'] ) ) {
            $sortby = $_POST['sortb'];
         }
         
         if( isset( $_POST['sorttype'] ) ) {
            $sorttype = $_POST['sorttype'];
         }
         
         if ( isset( $_POST['returnfields'] ) ) {
            $returnfields = $_POST['returnfields'];
         }
         
         // Check valid sort param
         switch( $sortby ) {
            
            case 'title':
               $sortby = 'tittel';
               break;
            case 'date':
               $sortby = 'dato';
               break;
            case 'time':
               break;
            default:
               $sortby = 'sorting,bid';
               break;
            
         }
         
         // Check sorting type. ASC or DESC.
         if( $sorttype == 1 ) {
            $sortby = $sortby.' DESC';
         }
         
         if( empty( $aid ) ) {
            
            $album = new Album( 0 );
            $aid = null;
            
         } else {
         
            try {
               
               $album = new Album( $aid );
               
            } catch( Exception $e ) {
               
               $this->result = false;
               $this->message = 'No access to this album';
               
               return false;
               
            }
            
         }
         
         $allImages = array();
   	   $images = new Image();
         // Get all images in collection
         if( $aid ) {
            foreach( $images->collection( array( 'bid' ), array( 'aid' => $aid, 'deleted_at' => null ), $sortby, $limit, $offset )->fetchAll() as $imagedata ) {
               try { 
                  list( $imageid ) = $imagedata;
                  $image = new Image( $imageid );
                  $allImages[] = $image->asArray();
               } catch (Exception $e) {}
            }
         } else {
            foreach( $images->collection( array( 'bid' ), array( 'aid' => null, 'owner_uid' => Login::userid(), 'deleted_at' => null ), $sortby, $limit, $offset )->fetchAll() as $imagedata ) {
               try { 
                  list( $imageid ) = $imagedata;
                  $image = new Image( $imageid );
                  $allImages[] = $image->asArray();
               } catch (Exception $e) {}
            }
         }
         
         if ( !empty( $returnfields ) ) {
            
            $returnimages = array();
            
            if ( strpos( $returnfields, ',' ) > 0 ) {
               $fields = explode( ',', $returnfields );
            } else {
               $fields[] = $returnfields;
            }
         
            foreach ( array_keys( $allImages ) as $key ) {
               foreach( $fields as $field ) {
                  $returnimages[ $key ][ $field ] = $allImages[ $key][ $field ];
               }
            }
            
            $allImages = $returnimages;
         }
         
   		// Setup pagination
         $this->result = true;
         $this->images = $allImages;
         $this->message = 'OK';
         
         // return the albumid
         return $aid;
         
      }
      
   }
   
?>