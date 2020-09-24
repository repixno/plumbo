<?PHP
   
   model( 'site.blog.post' );
   model( 'site.blog.index' );
   import( 'website.blog.image' );
   
   class BlogPost extends DBSiteBlogPost {
      
      public function asArray() {
         
         try {
            
            $shortname = Blog::ShortNameFromBlogId( $this->blogid );
            list( $year, $month ) = explode( '-', $this->created );
            
            return array(
               'id' => $this->postid,
               'yours' => $this->createdby == Login::userid(),
               'title' => $this->title,
               'intro' => $this->intro,
               'body' => $this->body,
               'images' => $this->getImages(),
               'url' => sprintf( '/blog/%s/%s/%s/%s/%s',
                              $shortname,
                              $year, $month,
                              zbase32::encode( $this->postid ),
                              util::urlize( $this->title ) 
                           ),
               'created' => $this->created,
               'createdby' => User::getNameFromUid( $this->createdby ),
               'updated' => $this->updated,
               'updatedby' => User::getNameFromUid( $this->updatedby ),
            );
            
         } catch( Exception $e ) {
            
            return false;
            
         }
         
      }
      
      public function getImages() {
         
         $images = array();
         $collection = new BlogImage();
         foreach( $collection->collection( null, array( 'postid' => $this->postid ), 'created ASC' )->fetchAllAs( 'BlogImage' ) as $blogimage ) {
            $image = $blogimage->asArray();
            if( $image && is_array( $image ) ) {
               $images[] = $image;
            }
         }
         
         return $images;
         
      }
      
   }
   
?>