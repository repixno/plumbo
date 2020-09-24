<?PHP
   
   import( 'website.blog.index' );
   import( 'website.blog.post' );
   import( 'website.blog.image' );
   import( 'pages.json' );
   
   class BlogAddImageAPI extends JSONPage implements IView {
      
      /**
       * Adds an image to an existing blog post
       * 
       * @api-name blog.image.add
       * @api-post postid integer The id of the post to add to
       * @api-post imageid integer The id of the image to add
       * @api-result image The added image object
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      
      public function Execute() {
         
         $postid = (int) $_POST['postid'];
         $imageid = (int) $_POST['imageid'];
         
         try {
            
            $blog = Blog::fromUserID( Login::userid() );
            if( !$blog ) {
               
               $this->result = false;
               $this->message = 'No blog';
               
            } else {
               
               $post = new BlogPost( $postid );
               if( $post->createdby != Login::userid() ) {
                  
                  $this->message = 'Access denied!';
                  $this->result = false;
                  return false;
                  
               }
               
               if( $post instanceof BlogPost ) {
                  
                  try {
                     
                     $image = new Image( $imageid );
                     
                  } catch( Exception $exception ) {
                     
                     $this->result = false;
                     $this->message = $exception->getMessage();
                     return false;
                     
                  }
                  
                  $blogimage = new BlogImage();
                  $blogimage->postid = $postid;
                  $blogimage->imageid = $imageid;
                  $blogimage->save();
                  
                  $this->image = $image->asArray();
                  $this->result = true;
                  $this->message = 'OK';
                  
               } else {
                  
                  $this->message = 'Unable to update post, missing data!';
                  $this->result = false;
                  
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