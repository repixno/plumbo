<?php

   /**
    * Share a album to the public 
    * 
    * @author Oyvind Selbek <oyvind@selbek.com>
    * 
    */

   import( 'pages.json' );
   import( 'website.user' );
   import( 'website.album' );
   import( 'website.galleryalbum' );
   
   class APIAlbumSharePublic extends JSONPage implements IValidatedView {
      
      public function Validate() {
       
         return array(
            'enable' => array(
               'post' => array(
                  'albumid' => VALIDATE_INTEGER,
               ),
               'fields' => array(
                  'albumid' => VALIDATE_INTEGER,
               )
            ),
            'disable' => array(
               'post' => array(
                  'albumid' => VALIDATE_INTEGER,
               ),
               'fields' => array(
                  'albumid' => VALIDATE_INTEGER,
               )
            )
         );
         
      }

      
      /**
       * Enable album sharing (public)
       * 
       * @api-name album.share.public.enable
       * @api-auth required
       * @api-example
       * @api-post-optional albumid Integer The id of the album to share
       * @api-param-optional albumid Integer The id of the album to share
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */     
      public function Enable( $id = 0 ) {
         
         if( empty( $id ) ) {
            $id = (int) $_POST["albumid"];
         }
         
         $this->result = false;
         $this->message = "Required input parameter missing or invalid (albumid)";
         if( empty( $id ) ) return false;
         
         try {
            
            $album = new Album( $id );
            
         } catch( Exception $e ) {
            
            $this->result = false;
            $this->message = 'No such album or no access to this album';
            
            return false;
            
         }
         
         $this->result = false;
         $this->message = 'Failed to load album';
         
         if( is_null( $album ) || !$album->isLoaded() || !$album instanceof Album ) return false;
         
         $this->result = false;
         $this->message = 'No access to this album';
         
         if( $album->ownerid != Login::userid() ) return false;
         
         try {
            
            $galleryalbum = new GalleryAlbum( $id );
            
            $this->result = false;
            $this->message = 'Album is already public!';
            
         } catch( Exception $e ) {
            
            config( 'website.gallery' );
            $portal = Dispatcher::getPortal();
            $portalkeymap = Settings::get( 'gallery', 'portalkeymap', array() );
            $portalkey = isset( $portalkeymap[$portal] ) ? $portalkeymap[$portal] : $portalkeymap['default'];
            
            DB::query( 'INSERT INTO public_album (aid, uid, key, tidspunkt) VALUES (?,?,?,NOW())', $id, $album->ownerid, $portalkey );
            
            $galleryuser = new User( $album->ownerid );
            
            $galleryalbum = new GalleryAlbum( $id );
            $galleryalbum->title = $album->title;
            $galleryalbum->description = $album->description;
            $galleryalbum->ownername = $galleryuser->fullname;
            $galleryalbum->category = 1;
            $galleryalbum->sharingtime = date( 'Y-m-d H:i:s' );
            $galleryalbum->country = $galleryuser->country;
            $galleryalbum->save();
            
            $album->publicshare_time = date( 'Y-m-d H:i:s' );
            $album->password = '';
            $album->access = 2;
            $album->save();
            
            $this->result = true;
            $this->message = 'OK';
            
         }
         
         return true;
         
      }

      /**
       * Disable album sharing (public)
       * 
       * @api-name album.share.public.disable
       * @api-auth required
       * @api-example
       * @api-post-optional albumid Integer The id of the album to disable sharing for
       * @api-param-optional albumid Integer The id of the album to disable sharing for
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */    
      public function Disable( $id = 0 ) {
         
         if( empty( $id ) ) {
            $id = (int) $_POST["albumid"];
         }
         
         $this->result = false;
         $this->message = "Required input parameter missing or invalid (albumid)";
         if( empty( $id ) ) return false;
         
         try {
            
            $album = new Album( $id );
            
         } catch( Exception $e ) {
            
            $this->result = false;
            $this->message = 'No such album or no access to this album';
            return false;
            
         }
         
         $this->result = false;
         $this->message = 'Failed to load album';
         if( is_null( $album ) || !$album->isLoaded() || !$album instanceof Album ) return false;
         
         $this->result = false;
         $this->message = 'No access to this album';
         if( $album->ownerid != Login::userid() ) return false;
         
         try {
            
            $galleryalbum = new GalleryAlbum( $id );
            $galleryalbum->deleteFromObjectCache();
            
            GalleryAlbum::deleteFromObjectCacheByClassAndId( 'galleryalbum', $id );
            
            DB::query( 'DELETE FROM public_album WHERE aid = ?', $id );
            
            $album->publicshare_time = null;
            $album->access = 0;
            $album->save();
            
            $this->result = true;
            $this->message = 'OK';
            
         } catch( Exception $e ) {
            
            $this->result = false;
            $this->message = 'Album is already deleted!';
            
         }
         
         return true;
         
      }
      
   }
   
?>