<?PHP


   import( 'pages.json' );

   class ListClipart extends JSONPage implements NoAuthRequired, IView {

         private $clipartfolder = '/var/www/repix/sites/website/webroot/teditor/library/Clipart/';

         
         public function Execute() {

                $images = array();
                $category = $_POST['category'];
                //$category = 'Baby';
                foreach( glob( $this->clipartfolder .$category. '/*.png') as $res){
                         $images[] = basename( $res );
                }
                $this->result = true;
                $this->images = $images;
                $this->message = 'OK';
                return true;
         }      
   }



?>
