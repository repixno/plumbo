<?PHP


   import( 'pages.json' );

   class ListTemplates extends JSONPage implements NoAuthRequired, IView {

         private $templatefolder = '/var/www/repix/data/tedit/templates/';
         
         public function Execute() {

                $folders = array();
                $category = $_POST['cat'];
                $category = 'Baby';
                
                try{
                  foreach( glob( $this->templatefolder .$category. '/*') as $res){
                          
                          $thumb = file_get_contents(  $res . '/thumb.txt' );
                          $thumbarray = json_decode($thumb);
                          
                           $folders[] = array( 'name' => basename( $res ),
                                                'tumb' => $thumbarray);
                  }
                
                }catch( Exception $e ){
                  util::Debug( $e->getMessage() );
                }
                $this->result = true;
                $this->folders = $folders;
                $this->message = 'OK';
                return true;
         }      
   }



?>
