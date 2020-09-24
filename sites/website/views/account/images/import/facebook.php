<?php
   
   import( 'pages.protected' );
   
   class AccountImagesImportFacebook extends ProtectedPage implements IView {

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
                  'access_token' => VALIDATE_STRING,
               )
            )
         );
   
      }
   
      /**
       * Execute
       * https://graph.facebook.com/oauth/authorize?type=user_agent&client_id=123026291052567&redirect_uri=http://mercedes.eurofoto.no/account/images/import/facebook&scope=user_photos
       */
   
      function Execute( ) {
   
         $token = $_GET[ 'access_token' ];
         
         // no token? redirect to ?-url by #-url
   
         if ( empty( $token ) ) {
   
            // todo: secure this function to avoid undefined-issue when not logged in etc.
            
            die(  '<script type="text/javascript">' .
                  'var pathArray = window.location.href.split( "#" );' .
                  'window.location.href = "/account/images/import/facebook/?" + (pathArray[1]);' .
                  '</script>' );
   
         } else {
   
            $friends = array();
   
            try {
               
               // fetch owner
   
               $me = json_decode( file_get_contents( sprintf ( 'https://graph.facebook.com/me?access_token=%s', $token ) ) );
               
               // fetch facebook albums by owner
   
               $facebookalbums = json_decode( file_get_contents( sprintf( 'https://api.facebook.com/method/fql.query?access_token=%s&format=json&query='.
               'SELECT%%20aid,name,created,description,size,object_id%%20FROM%%20album%%20WHERE%%20owner=%s', 
               $token, $me->id ) ) );
               
               $albums = array();
               
               foreach( $facebookalbums as $facebookalbum ) {
                  
                  $images = array();
                  
                  // setup album array
                  
                  $album = array();
                    
                  $album['name'] = $facebookalbum->name;
                  $album['id'] = $facebookalbum->object_id;
                  $album['aid'] = $facebookalbum->aid;
                  $album['created'] = strftime('%d-%m-%Y', $facebookalbum->created);
                  $album['description'] = $facebookalbum->description;
                  $album['size'] = $facebookalbum->size;
                  
                  // fetch facebook photos by album id and loop them into a image array
                                    
                  $facebookphotos = json_decode( file_get_contents( sprintf( 'https://api.facebook.com/method/fql.query?access_token=%s&format=json&query='.
                  'SELECT%%20src_big,src_small,src,caption,created,object_id%%20FROM%%20photo%%20WHERE%%20aid=%s', 
                  $token, $facebookalbum->aid ) ) );
                  
                  foreach( $facebookphotos as $facebookphoto ) {
                     
                     // setup image array
                        
                     $image = array();
                                       
                     $image['id'] = $facebookphoto->object_id;
                     $image['small'] = $facebookphoto->src_small;
                     $image['medium'] = $facebookphoto->src;
                     $image['large'] = $facebookphoto->src_big;
                     $image['description'] = $facebookphoto->caption;
                     $image['created'] = strftime('%d-%m-%Y', $facebookphoto->created);
                     
                     $images[] = $image;
                     
                  }
                  
                  $album['images'] = $images;
                  
                  $albums[] = $album;
                  
               }
               
               $this->albums = $albums; 
   
            } catch ( Exception $e ) {
   
               die($e->getMessage());
               
            }
   
         }

      }

   }
      
?>