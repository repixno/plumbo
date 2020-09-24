<?PHP
   
   model( 'site.blog.image' );
   
   class BlogImage extends DBSiteBlogImage {
      
      public function asArray() {
         
         try {
            
            // you've been temporarily granted public access to this image, since its posted to a blaaag.
            PermissionManager::current()->grantAccessTo( $this->imageid, 'image', PERMISSION_PUBLIC );
            
            // now load the image
            $image = new Image( $this->imageid );
            
            // and return the struct.
            return array(
               'refid' => $this->postimageid,
               'image' => $image->asArray(),
               'created' => $this->created,
               'createdby' => User::getNameFromUid( $this->createdby ),
            );
            
         } catch( Exception $e ) {
            
            return false;
            
         }
         
      }
      
   }
   
?>