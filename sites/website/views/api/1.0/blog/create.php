<?PHP
   
   import( 'website.blog.index' );
   import( 'pages.json' );
   
   class BlogCreateAPI extends JSONPage implements IView {
      
      /**
       * Creates a new, or returns the current blog for a given user
       * 
       * @api-name blog.create
       * @api-post title String Your blogs title
       * @api-post-optional shortname String Your blog title
       * @api-result url String URL to your blog
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      
      public function Execute() {
         
         $title = $_POST['title'];
         $shortname = $_POST['shortname'];
         
         $this->message = 'No title given';
         if( !$title ) return false;
         if( !$shortname ) {
            $shortname = Util::urlize( $title );
         }
         
         try {
            
            $name = Blog::fromShortname( $shortname );
            if( $blog ) {
               
               $this->result = false;
               $this->message = 'Shortname is already taken';
               return false;
               
            }
            
            $blog = Blog::fromUserID( Login::userid() );
            if( !$blog ) {
               
               $blog = new Blog();
               $blog->title = $title;
               $blog->shortname = $shortname;
               $blog->save();
               
            }
            
            $this->url = sprintf( '/blog/%s', $blog->shortname );
            $this->result = true;
            $this->message = 'OK';
            return true;
            
         } catch( Exception $e ) {
            
            $this->message = 'An unknown error occured when creating your blog. Please try again later!';
            $this->result = false;
            
         }
         
         return false;
         
      }
      
   }

   
?>