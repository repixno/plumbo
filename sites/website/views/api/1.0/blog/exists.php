<?PHP
   
   import( 'website.blog.index' );
   import( 'pages.json' );
   
   class BlogCreateAPI extends JSONPage implements IView {
      
      /**
       * Checks whether a blog for the current user exists
       * 
       * @api-name blog.exists
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      
      public function Execute() {
         
         try {
            
            $blog = Blog::fromUserID( Login::userid() );
            if( !$blog ) {
               
               $this->result = true;
               $this->message = 'No blog';
               
            } else {
               
               
               $this->result = false;
               $this->message = 'OK';
               
            }
            
         } catch( Exception $e ) {
            
            $this->message = 'An unknown error occured when creating your blog. Please try again later!';
            $this->result = false;
            
         }
         
         return false;
         
      }
      
   }

   
?>