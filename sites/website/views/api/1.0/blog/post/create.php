<?PHP
   
   import( 'website.blog.index' );
   import( 'website.blog.post' );
   import( 'pages.json' );
   
   class BlogPostCreateAPI extends JSONPage implements IView {
      
      /**
       * Creates a new blog post and posts it
       * 
       * @api-name blog.post.create
       * @api-post title String Your blogposts title
       * @api-post-optional intro String Your blogposts introduction text
       * @api-post-optional body String Your blogposts main textbody
       * @api-result post The newly created blogpost object
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      
      public function Execute() {
         
         $title = trim( $_POST['title'] );
         $intro = trim( $_POST['intro'] );
         $body = trim( $_POST['body'] );
         
         try {
            
            $blog = Blog::fromUserID( Login::userid() );
            if( !$blog ) {
               
               $this->result = false;
               $this->message = 'No blog';
               
            } else {
               
               if( $blog->uid != Login::userid() ) {
                  
                  $this->message = 'Access denied!';
                  $this->result = false;
                  return false;
                  
               }
               
               $post = $blog->addPost( $title, $intro, $body );
               if( $post ) {
                  
                  $this->post = $post->asArray();
                  $this->result = true;
                  $this->message = 'OK';
                  
               } else {
                  
                  $this->message = 'Unable to add post, missing data!';
                  $this->result = false;
                  
               }
               
            }
            
         } catch( Exception $e ) {
            
            $this->message = 'An unknown error occured when creating your post. Please try again later!';
            $this->result = false;
            
         }
         
         return false;
         
      }
      
   }

   
?>