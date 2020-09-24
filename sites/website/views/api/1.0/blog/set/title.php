<?PHP
   
   import( 'website.blog.index' );
   import( 'pages.json' );
   
   class BlogSetTitleAPI extends JSONPage implements IView {
      
      /**
       * Sets the title of the blog.
       * 
       * @api-name blog.set.title
       * @api-post title String The title of the blog. Cannot ble blank.
       * @api-result blog The updated blog object
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      
      public function Execute() {
         
         $title = trim( $_POST['title'] );
         
         if( !$title ) {
            
            $this->result = false;
            $this->message = 'The blog title cannot ble blank';
            return false;
            
         }
         
         try {
            
            $blog = Blog::fromUserID( Login::userid() );
            if( !$blog ) {
               
               $this->result = false;
               $this->message = 'No blog';
               
            } else {
               
               $blog->title = $title;
               $blog->save();
               
               $this->blog = $blog->asArray();
               $this->result = true;
               $this->message = 'OK';
               
            }
            
         } catch( Exception $e ) {
            
            $this->message = 'An unknown error occured when updating your blog. Please try again later!';
            $this->result = false;
            
         }
         
         return false;
         
      }
      
   }
   
?>