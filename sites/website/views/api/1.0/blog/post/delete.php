<?PHP
   
   import( 'website.blog.index' );
   import( 'website.blog.post' );
   import( 'pages.json' );
   
   class BlogPostDeleteAPI extends JSONPage implements IView {
      
      /**
       * Deletes an existing blog post
       * 
       * @api-name blog.post.delete
       * @api-post postid integer The id of the post to update
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      
      public function Execute() {
         
         $postid = (int) $_POST['postid'];
         
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
                  
                  $post->delete();
                  
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