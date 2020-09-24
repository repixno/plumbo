<?php
   
   import( 'pages.protected' );
   
   class AccountImagesImportFickr extends ProtectedPage implements IView {

      protected $template = 'account.images.import';
      
      /**
       * Validator
       *
       * @return Array
       */
      
      function Validate() {
      
         return array(
            'execute' => array(
               'get' => array(
                  'frob' => VALIDATE_STRING
               )
            )
         );
   
      }
   
      /**
       * Execute
       *
       * @param String $frob
       */
   
      function Execute( $frob = '') {
         
         require_once( sprintf( '%s/application/library/phpflickr/phpFlickr.php', getRootPath() ) );
           
         $f = new phpFlickr('21ee1d702fe2e8939f50a5e40c9f48a3','e3e994d5668f8c3c');
                  
         if (empty($_GET['frob'])) {
            
            $f->auth();
            
         } else {
            
            $f->auth_getToken($_GET['frob']);
            
         }
   
         $token = $f->auth_checkToken();

         $nsid = $token['user']['nsid'];
          
         $photos_url = $f->urls_getUserPhotos($nsid);
         
         $photos = $f->photos_search( array( 'user_id' => $nsid, 'per_page' => 1000 ) );

         $albums = array();
         
         $album = array();
         $album['name'] = 'flickr';
      
         foreach ( (array)$photos['photo'] as $photo ) {

            $image = array();
            $image['description'] = $photo['title'];
            $image['small'] = sprintf( 'http://farm%s.static.flickr.com/%s/%s_%s_s.jpg', $photo['farm'], $photo['server'], $photo['id'], $photo['secret'] );
            $image['medium'] = sprintf( 'http://farm%s.static.flickr.com/%s/%s_%s_m.jpg', $photo['farm'], $photo['server'], $photo['id'], $photo['secret'] );
            $image['large'] = sprintf( 'http://farm%s.static.flickr.com/%s/%s_%s.jpg', $photo['farm'], $photo['server'], $photo['id'], $photo['secret'] );
            $images[] = $image;
            
            
            $size++;
            
            
         }
         
         $album['size'] = $size;
          
         $album['images'] = $images;
         
         $albums[] = $album;
            
         $this->albums = $albums;
         

      }

   }
      
?>