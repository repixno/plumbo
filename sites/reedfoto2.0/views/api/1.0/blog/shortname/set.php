<?PHP
   
   import( 'website.blog.index' );
   import( 'pages.json' );
   
   class BlogShortnameSetAPI extends JSONPage implements IView {
      
      /**
       * Sets the shortname of the blog.
       * 
       * @api-name blog.shortname.set
       * @api-post shortname String The shortname of the blog.
       * @api-result blog The updated blog object
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
               
               $blog->shortname = $shortname;
               $blog->save();
               
               $this->blog = $blog->asArray();
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