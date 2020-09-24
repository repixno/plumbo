<?PHP
   
   import( 'website.blog.index' );
   import( 'website.blog.post' );
   import( 'website.blog.image' );
   import( 'pages.json' );
   
   class BlogAddImageAPI extends JSONPage implements IView {
      
      /**
       * Removes an image from a blog post by image reference id (NOT imageid!)
       * 
       * @api-name blog.image.remove
       * @api-post postid integer The id of the post to add to
       * @api-post refid integer The reference id of the image to remove
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      
      public function Execute() {
         
         $postid = (int) $_POST['postid'];
         $refid = (int) $_POST['refid'];
         
         try {
            
            $blog = Blog::fromUserID( Login::userid() );
            if( !$blog ) {
               
               $this->message = 'No blog';
               $this->result = false;
               return false;
               
            } else {
               
               $post = new BlogPost( $postid );
               if( $post->createdby != Login::userid() ) {
                  
                  $this->message = 'Access denied!';
                  $this->result = false;
                  return false;
                  
               }
               
               $blogimage = new BlogImage( $refid );
               if( $blogimage->postid != $post->postid ) {
                  
                  $this->message = 'Invalid reference!';
                  $this->result = false;
                  return false;
                  
               }
               
               if( $blogimage instanceof BlogImage && $blogimage->isLoaded() ) {
                  
                  $blogimage->delete();

                  $this->message = 'OK';
                  $this->result = true;
                  return true;

               } else {
                  
                  $this->message = 'Unable to update post, missing data!';
                  $this->result = false;
                  return false;
                  
               }
               
            }
            
         } catch( Exception $e ) {
            
            $this->message = 'An unknown error occured when updating your post. Please try again later!';
            $this->result = false;
            
         }
         
         return false;
         
      }
      
   }
   
?>