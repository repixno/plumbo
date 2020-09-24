<?PHP
   
   import( 'website.blog.index' );
   import( 'pages.json' );
   
   class BlogSetDescriptionAPI extends JSONPage implements IView {
      
      /**
       * Sets the description of the blog.
       * 
       * @api-name blog.set.description
       * @api-post description String The description of the blog.
       * @api-result blog The updated blog object
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      
      public function Execute() {
         
         $description = trim( $_POST['description'] );
         
         try {
            
            $blog = Blog::fromUserID( Login::userid() );
            if( !$blog ) {
               
               $this->result = false;
               $this->message = 'No blog';
               
            } else {
               
               $blog->description = $description;
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