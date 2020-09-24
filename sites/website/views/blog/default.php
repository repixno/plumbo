<?PHP
   
   import( 'website.blog.index' );
   import( 'math.zbase32' );
   
   class BlogViewer extends WebPage implements IView {
      
      protected $template = 'blog.index';
      
      public function Execute( $shortname = '', $year = null, $month = null, $postid = null ) {
         
         $postid = isset( $postid ) ? zbase32::decode( $postid ) : null;
         
         if( $shortname ) $blog = Blog::fromShortname( $shortname );
         if( $blog instanceof Blog && $postid > 0 ) {
            
            $this->viewPost( $blog, $postid );
            
         } else if( $blog instanceof Blog ) {
            
            $this->listBlog( $blog, $year, $month );
            
         } else {
            
            $this->showIndex();
            
         }
         
      }
      
      private function viewPost( Blog $blog, $postid = 0 ) {
         
         try {
            $post = new BlogPost( $postid );
            if( $post->isLoaded() ) {
               
               $this->setTemplate( 'blog.view' );
               
               $this->blog = $blog->asArray();
               $this->post = $post->asArray();;
               
            }
            
         } catch ( Exception $e ) {
            
            $this->Execute();
            
         }
         
      }
      
      private function listBlog( Blog $blog, $year, $month ) {
         
         $this->setTemplate( 'blog.list' );
         
         $this->blog = $blog->asArray();
         $this->posts = $blog->getPosts( $year, $month );
         
      }
      
      private function showIndex() {
         
         
         
      }
      
   }
   
?>