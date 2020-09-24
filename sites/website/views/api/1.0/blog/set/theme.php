<?PHP
   
   import( 'website.blog.index' );
   import( 'pages.json' );
   
   class BlogSetThemeAPI extends JSONPage implements IView {
      
      /**
       * Sets the theme of the blog.
       * 
       * @api-name blog.set.theme
       * @api-post theme String The theme of the blog. Leave blank for default theme.
       * @api-result blog The updated blog object
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      
      public function Execute() {
         
         $theme = trim( $_POST['theme'] );
         
         try {
            
            $blog = Blog::fromUserID( Login::userid() );
            if( !$blog ) {
               
               $this->result = false;
               $this->message = 'No blog';
               
            } else {
               
               $blog->theme = $theme;
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