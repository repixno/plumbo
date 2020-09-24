<?PHP
   import( 'pages.json' );
   import( 'website.felix');
   import( 'website.cart' );
   
   class LoadFelixProject extends JSONPage implements NoAuthRequired, IView {

         private $jsonfolder = '/data/pd/felix/inspirasjon/canvas/'; 
         private $thumbfolder = '/data/pd/felix/inspirasjon/thumb/'; 
         
         public function Execute() {
            
            
            $id = $_POST['id'];

            
            if( $id ){
                
                $json = file_get_contents( $this->jsonfolder . $id );
                $array = json_decode($json);
                $data = array();

                foreach( $array->objects as $key=>$ret ){
                    
                    if( $key == 0 ){
                        $data['background'] = basename( $ret->src );
                    }else{
                        if( $ret->type == 'image' ){
                            $data['clipart'] = $ret->src;
                        }
                        else if ( $ret->type == 'text' ){
                            $data['text'] = $ret->text;
                        }
                    }
                }
                
                $this->result = true;
                $this->message = "OK";
                $this->data = $data;
                return true;
            }else{
                $this->result = false;
                $this->message = "FAIL";
                return false;
            }
            
         }      
   }



?>