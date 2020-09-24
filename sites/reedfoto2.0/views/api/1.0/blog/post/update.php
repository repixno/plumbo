<?PHP
   
   import( 'website.blog.index' );
   import( 'website.blog.post' );
   import( 'pages.json' );
   
   class BlogPostUpdateAPI extends JSONPage implements IView {
      
      /**
       * Updates an existing blog post with new content
       * 
       * @api-name blog.post.update
       * @api-post postid integer The id of the post to update
       * @api-post-optional title String Your blogposts title
       * @api-post-optional intro String Your blogposts introduction text
       * @api-post-optional body String Your blogposts main textbody
       * @api-result post The updated blogpost object
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
                  
                  $altered = false;
                  
                  if( isset( $_POST['title'] ) ) {
                     $post->title = trim( $_POST['title'] );
                     $altered = true;
                  }
                  
                  if( isset( $_POST['intro'] ) ) {
                     $post->intro = trim( $_POST['intro'] );
                     $altered = true;
                  }
                  
                  if( isset( $_POST['body'] ) ) {
                     $post->body = trim( $_POST['body'] );
                     $altered = true;
                  }
                  
                  if( $altered ) {
                     $post->save();
                  }
                  
                  $this->post = $post->asArray();
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