<?php

   /**
    * Enumerate albums shared to user
    * 
    *
    */
   
   import( 'pages.json' );
   import( 'website.album' );
   
   class APIAlbumsSharedTo extends JSONPage implements IValidatedView {
      
      /**
       * Validator
       *
       * @return Array
       */
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
                  'limit'  => VALIDATE_INTEGER

               ),
               'fields' => array(
                  'limit'  => VALIDATE_INTEGER
               )
            )
         );
         
      }
      
      /**
       * Returns a list of albums shared to user
       * 
       * @api-name albums.sharedto
       * @api-auth required
       * @api-javascript yes
       * @api-post-optional limit Integer Number of albums
       * @api-param-optional limit Integer Number of albums
       * @api-param-optional sortby string Sorting field, (title, created, views, quantity, year, description or default)
       * @api-param-optional sorttype Integer Sorting order, ascending (0) or descending (1)
       * @api-post-optional sortby string Sorting field, (title, created, views, quantity, year, description or default)
       * @api-post-optional sorttype Integer Sorting order, ascending (0) or descending (1)
       * @api-result albums Array List of Album objects.
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      
      public function Execute( $limit = 10 ) {
         
         // todo: implement orderby, ordertype
         // SELECT bildealbum.aid FROM tilgangtilalbum_dedikert left join bildealbum on tilgangtilalbum_dedikert.aid=bildealbum.aid WHERE tilgangtilalbum_dedikert.uid = 736073 order by bildealbum.year desc 
         
         $limit = $_POST['limit'] ? $_POST['limit'] : $limit;
         $sortby = $_POST['sortby'] ? $_POST['sortby'] : $sortby;
         $sorttype = $_POST['sorttype'] ? $_POST['sorttype'] : $sorttype;
         
         
         // Setup valid sortby values
         switch( $sortby ) {
            
            case 'title':
               $sortby = 'bildealbum.namn';
               break;
            case 'created':
               $sortby = 'bildealbum.created_time';
               break;
            case 'views':
               break;
            case 'quantity':
               $sortby = 'bildealbum.images';
               break;
            case 'year':
               break;
            case 'description':
               break;
            default:
               $sortby = 'bildealbum.sorting, bildealbum.aid';
               break;
            
         }
         
         // Do we want to sort DESC?    
         if( !empty( $sorttype ) ) {
            $sortby = $sortby.' DESC';
         }
         
         try {
            
            //$res = array_reverse( DB::query( "SELECT aid FROM tilgangtilalbum_dedikert WHERE uid = ?", Login::userid() )->fetchAll() );
            $res = array_reverse( DB::query( sprintf( 'SELECT bildealbum.aid FROM tilgangtilalbum_dedikert left join bildealbum on tilgangtilalbum_dedikert.aid=bildealbum.aid WHERE tilgangtilalbum_dedikert.uid = ? order by %s', $sortby ), Login::userid() )->fetchAll() );
                  
            $sharedlist = array();
            
            while( count( $res ) > 0 && count( $sharedlist ) < $limit ) {
               
               list( $albumid ) = array_pop( $res );
               
               if( !isset( $sharedlist[ $albumid ] ) ) {
                  
                  try {
                     
                     $album = new Album( $albumid );
                     $sharedlist[ $albumid ]= $album->asArray();
                     
                  } catch( Exception $e ) {}
                  
               }
            }
         
            $this->albums = array_values( $sharedlist );
            $this->result = true;
            $this->message = 'OK';
         
         } catch (Exception $e) {
            
            $this->result = false;
            $this->message = 'OK';   
            
         }
      }
      
      
   }

?>