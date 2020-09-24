<?php

   /**
    * Enumerate user's albums
    * Supports limit and offset
    * Also supports sortby and sorttype ASC or DESC
    * 
    * Sortby is switched and check to be valid.
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
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
                  "sortby" => VALIDATE_STRING,
                  "sorttype" => VALIDATE_INTEGER,
                  "indexby" => VALIDATE_STRING,
               ),
               "fields" => array(
                  VALIDATE_INTEGER,
                  VALIDATE_INTEGER,
                  VALIDATE_STRING,
                  VALIDATE_INTEGER,
                  VALIDATE_STRING,
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
       * @api-post-optional sortby string Sorting field, (title, created, views, quantity, year, description or default)
       * @api-post-optional sorttype Integer Sorting order, ascending (0) or descending (1)
       * @api-post-optional indexby string Index field. What db-field the return array is keyed by (year or empty)
       * @api-param-optional offset Integer The Album offset to start at, default = 0 (first album in the set)
       * @api-param-optional limit Integer The maximum number of albums to return in the collection
       * @api-param-optional sortby string Sorting field, (title, created, views, quantity, year, description or default)
       * @api-param-optional sorttype Integer Sorting order, ascending (0) or descending (1)
       * @api-param-optional indexby string Index field. What db-field the return array is keyed by (year or empty)
       * @api-result albums Array List of Album objects.
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      
      public function Execute( $offset = 0, $limit = 0, $sortby = 'default', $sorttype = 0, $indexby = '' ) {
         
         // Check post values
         if( isset( $_POST["offset"] ) ) {
            $offset = $_POST["offset"];
         }
         if( isset( $_POST["limit"] ) ) {
            $limit = $_POST["limit"];
         }
         if( isset( $_POST["sortby"] ) ) {
            $sortby = $_POST["sortby"];
         }
         if( isset( $_POST["sorttype"] ) ) {
            $sorttype = $_POST["sorttype"];
         }
         
         
         // Setup valid sortby values
         switch( $sortby ) {
            
            case 'title':
               $sortby = 'namn';
               break;
            case 'created':
               $sortby = 'created_time';
               break;
            case 'views':
               break;
            case 'quantity':
               $sortby = 'images';
               break;
            case 'year':
               break;
            case 'description':
               break;
            default:
               $sortby = 'sorting, aid';
               break;
            
         }
         
         // Do we want to sort DESC?    
         if( !empty( $sorttype ) ) {
            $sortby = $sortby.' DESC';
         }
         
         // Setup valid indexby values
         switch( $indexby ) {          
            
            case 'year':
               $indexby = 'year';
               break;
            default:
               $indexby = '';
               break;
            
         }
         
         $albumlist = array();
         try {
            $albums = new Album();
         } catch( Exception $e ) {
            $this->result = false;
            $this->message = "Failed to load album";
            return false;
         }
			
         
			try {
			   
			   if( !empty( $offset ) && !empty( $limit ) ) {

			      foreach( $albums->collection( 'aid', array( 'uid' => Login::userid(), 'deleted_at' => NULL ), $sortby, $limit, $offset )->fetchAllAs('Album') as $album ) {

			         if( $album->getOwnerId() == Login::userid() ) {
			            
			            if ( !empty( $indexby ) ) {
			               $albumlist[$albums->$indexby][] = $album->asArray();
			            } else {
			               $albumlist[] = $album->asArray();
			            }
			         }

			      }

			   } else if( empty( $offset ) && !empty( $limit ) ) {

			      foreach( $albums->collection( 'aid', array( 'uid' => Login::userid(), 'deleted_at' => NULL ), $sortby, $limit, 0 )->fetchAllAs('Album') as $album ) {

			         if( $album->getOwnerId() == Login::userid() ) {
			           
			            if ( !empty( $indexby ) ) {
			               $albumlist[$albums->$indexby][] = $album->asArray();
			            } else {
			               $albumlist[] = $album->asArray();
			            }
			         }

			      }

			   } else if( !empty( $offset ) && empty( $limit ) ) {

			      foreach( $albums->collection( 'aid', array( 'uid' => Login::userid(), 'deleted_at' => NULL ), $sortby, 0, $offset )->fetchAllAs('Album') as $album ) {

			         if( $album->getOwnerId() == Login::userid() ) {
			            if ( !empty( $indexby ) ) {
			               $albumlist[$albums->$indexby][] = $album->asArray();
			            } else {
			               $albumlist[] = $album->asArray();
			            }
			         }

			      }

			   } else {

			      foreach( $albums->collection( 'aid', array( 'uid' => Login::userid(), 'deleted_at' => NULL ), $sortby, 0, 0 )->fetchAllAs('Album') as $album ) {
      
			         if( $album->getOwnerId() == Login::userid() ) {
			            if ( !empty( $indexby ) ) {
			               $albumlist[$albums->$indexby][] = $album->asArray();
			            } else {
			               $albumlist[] = $album->asArray();
			            }
			         }

			      }

			   }
			   
   		} catch( Exception $e ) {
   		   
   		   $this->result = false;
   		   $this->message = "Invalid limit or offset: ";
   		   return false;
   		   
   		}
   		
   		$this->result = true;
   		$this->albums = $albumlist;
   		$this->message = "OK";
         
      }
      
   }

?>