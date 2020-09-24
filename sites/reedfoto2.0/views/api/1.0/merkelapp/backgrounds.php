<?PHP


   import( 'pages.json' );

   class ListBackgrounds extends JSONPage implements NoAuthRequired, IView {

         public function Execute() {

                $images = array();

                
                $category = $_POST['backgroundcat'];  
                
                foreach( glob('/var/www/repix/sites/static/webroot/gfx/merkelapp/backgroundfiles/' .$category. '/*.jpg') as $res){
                         $images[] = basename( $res );
                }
                $this->result = true;
                $this->images = $images;
                $this->message = 'OK';
                return true;
         }      
   }



?>
