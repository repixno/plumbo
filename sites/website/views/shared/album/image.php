<?php

   /**
    * Single Image view
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'website.image' );
   import( 'website.album' );

   class SharedAlbumImage extends WebPage implements IValidatedView {
      
      protected $template = 'shared.album.showimage';
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'request' => array(
                  'bid' => VALIDATE_INTEGER,
               ),
               'post' => array(
                  'albumpassword' => VALIDATE_STRING,
               ),
               'fields' => array(
                  'bid' => VALIDATE_INTEGER, 
               )
            ),
            'stream' => array(
               'fields' => array(
                  VALIDATE_INTEGER,
                  VALIDATE_STRING
                  )
               )
         
         );
         
      }
      
      public function Execute( $bid = 0 ) {
         
         if( isset( $_POST['albumpassword'] ) ) {
            
            // HACK: direct query required since Image loads its Album object in the constructor.
            $aid = (int) DB::query( 'SELECT aid FROM bildeinfo WHERE bid = ?', $bid )->fetchSingle();
            
            // finally, add the token for this album. adding a token does not in its own regard
            // grant access to the album, it purely stores it the PermissionManager's future use.
            PermissionManager::current()->addTokenFor( $aid, 'album', $_POST['albumpassword'] );
            
         }
         
         try {
            
            $image = new Image( $bid );
            
            if( empty( $image ) || !$image instanceof Image ) return false;
            
            $this->image = $image->asArray();
            
            $album = new Album( $image->aid );
            $images = $album->getImages();
            
            $imageids = array();
            foreach( $images as $image ) {
               $imageids[] = $image['id'];
            }
            
            $position = array_search( $bid, $imageids );
            
            $this->imagenumber = $position + 1;
            $this->imagecount = count( $imageids );
            
            // Get album data but censor all but album access
            $albumdata = $album->asArray( false );
            if( is_array( $albumdata ) ) {
               $this->album = array( 'access' => $albumdata['access'] );
            }
            
            if( $position > 0 ) {
               try {
                  $previmage = new Image( $imageids[$position - 1] );
                  $this->previmage = $previmage->asArray();
               } catch( Exception $e ) {}
            }
            
            if( $position < count( $imageids ) - 1 ) {
               try {
                  $nextimage = new Image( $imageids[$position + 1] );
                  $this->nextimage = $nextimage->asArray();
               } catch( Exception $e ) {}
            }
            
         } catch( NoPasswordException $e ) {
            
            $this->setTemplate( 'shared.album.password' );
            
         }
         
      }
      
      public function stream( $imageid, $publickey = null ) {

         try {
            
            $this->setTemplate();
            
            if ( isset( $publickey ) ) {
            
               $this->validatePublicKey( $imageid, $publickey );
               
            }
         
            $image = new Image( $imageid );
            
            $imagedata = $image->asArray();
            relocate( $imagedata[ 'screensize' ] );
         
         } catch( Exception $e ) {
            
            $url = WebsiteHelper::staticBaseUrl() . '/gfx/404/nopermission.jpg';
            relocate( $url );
            
         }
         
      }
      
      private function validatePublicKey( $imageid, $publickey ) {
         
         if( !empty( $publickey ) ) {
            
   		   import( 'math.signer' );

            $access = DB::query( 'SELECT b.access FROM bildeinfo AS a LEFT JOIN bildealbum AS b ON a.aid=b.aid WHERE a.bid=? AND a.deleted_at IS NULL', $imageid )->fetchSingle();
            $signature = Signer::sign( $imageid, 'share' );
            
            if( $access == 2 && $signature == $publickey ) {
               
               PermissionManager::current()->grantAccessTo( $imageid, 'image', PERMISSION_SHARED );
               
            }
            
         }
         
      }      
      
   }



?>