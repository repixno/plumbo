<?PHP
   
   import( 'website.blog.index' );
   import( 'pages.json' );
   
   class BlogShortnameAvailableAPI extends JSONPage implements IView {
      
      /**
       * Checks whether a given shortname is available.
       * 
       * @api-name blog.shortname.available
       * @api-post shortname String The shortname to check for.
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      
      public function Execute() {
         
         $shortname = trim( $_POST['shortname'] );
         
         try {
            
            $blog = Blog::fromUserID( Login::userid() );
            if( !$blog ) {
               
               $this->result = false;
               $this->message = 'No blog';
               
            } else {
               
               $query = $blog->collection( array( 'blogid' ), array( 'shortname' => $shortname ) );
               if( $query->count() > 0 ) {
                  
                  $blogid = $query->fetchSingle();
                  if( $blogid != $blog->blogid ) {
                     
                     $this->result = false;
                     $this->message = 'Blog shortname is already taken';
                     return false;
                     
                  }
                  
               }
               
               $this->result = true;
               $this->message = 'OK';
               return true;
               
            }
            
         } catch( Exception $e ) {
            
            $this->message = 'An unknown error occured when updating your blog. Please try again later!';
            $this->result = false;
            
         }
         
         return false;
         
      }
      
   }
   
?>