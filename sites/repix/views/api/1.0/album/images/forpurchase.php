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
   
   Dispatcher::extendView( 'api.%s.album.images.enum', '1.0' );
   
   /**
    * Returns a list of images for purchase within a given albumid
    * 
    * @api-name album.images.forpurchase
    * @api-auth required
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
         
   class APIAlbumImagesEnumForPurchase extends APIAlbumImagesEnum implements IValidatedView {
      
      public function Execute( $aid = 0, $offset = 0, $limit = 0, $sortby = 'default', $sorttype = 0, $returnfields = '' ) {
         
         $aid = parent::Execute( $aid, $offset, $limit, $sortby, $sorttype );
         
         try {
            
            $album = new Album( $aid );
            
         } catch( Exception $e ) {
            
            $this->result = false;
            $this->message = 'Failed to load album';
            $this->images = false;
            
            return false;
            
         }
         
         if( $album->getOwnerId() != Login::userid() ) {
            
            if( !$album->purchaseAccess ) {
               
               $this->message = 'No purchase access to this album';
               $this->result = false;
               $this->images = false;
               return false;
               
            }
            
         }
         
         return true;
         
      }
      
   }
   
?>