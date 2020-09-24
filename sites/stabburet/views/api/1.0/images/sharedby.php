<?php

   /**
    * Enumerate user's shared images
    * 
    *
    */
   
   import( 'pages.json' );
   import( 'website.album' );
   
   class APIimagesSharedBy extends JSONPage implements IValidatedView {
      
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
       * Returns a list of images shared by user
       * 
       * @api-name images.sharedby
       * @api-auth required
       * @api-javascript yes
       * @api-post-optional limit Integer Number of images
       * @api-param-optional limit Integer Number of images
       * @api-result images Array List of Image objects.
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      
      public function Execute( $limit = 10 ) {
         
         $limit = $_POST['limit'] ? $_POST['limit'] : $limit;
      
         try {
            
            foreach( DB::query( "SELECT aid FROM tilgangtilalbum_dedikert WHERE aid IN ( SELECT aid FROM bildealbum WHERE uid = ? AND deleted_at IS NULL )", Login::userid() )->fetchAll() as $row ) {

               $album = new Album( $row[0] );

               foreach ($album->getImages() as $image) { 

                  if (count($images) < $limit) $images[] = $image;

               }

            }

            $this->images = $images;
            $this->result = true;
            $this->message = 'OK';
         
         } catch (Exception $e) {
            
            $this->result = false;
            $this->message = 'OK';   
            
         }
      }
      
      
   }

?>
