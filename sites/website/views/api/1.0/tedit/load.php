<?PHP


   import( 'pages.json' );

   class SaveProject extends JSONPage implements NoAuthRequired, IView {
    
        private $folder = '/var/www/repix/data/tedit/templates/'; 
         
         public function Execute(){
            
                $category = 'Baby'; 
                $name = $_POST['name'];
                
                
                
                $data = file_get_contents(  $this->folder . $category . '/' . $name . '/theme.txt'  );
                $array = json_decode($data);
                
                $thumbdata = file_get_contents(  $this->folder . $category . '/' . $name . '/thumb.txt'  );
                $thumbarray = json_decode($thumbdata);
                
                foreach( $array as $pageid=>$page ){
                  foreach( $page->objects as $id=>$res ){
                      
                      $host = parse_url ( $res->src );
                      $host = $host['host'];
                      $array[$pageid]->objects[$id]->src = str_replace( $host,   $_SERVER[HTTP_HOST] , $res->src );
                      
                  }
                }
                    
                
                
                //$data2 = unserialize( $data );
                $this->result = true;
                $this->data = $array;
                $this->thumbs = $thumbarray;  
                $this->message = 'OK';
                return true;
         }      
   }



?>
