<?PHP
   import( 'pages.json' );
   import( 'website.felix');
   import( 'website.cart' );
   
   class InspirasjonFelixProject extends JSONPage implements NoAuthRequired, IView {

         private $jsonfolder = '/data/pd/felix/inspirasjon/canvas/'; 
         private $thumbfolder = '/data/pd/felix/inspirasjon/thumb/'; 
         
         public function Execute() {
            
            
            $productid = $_POST['productid'];
            $canvas = urldecode( $_POST['canvas'] );
            $thumb = $_POST['thumb'];
            
            if( $thumb && $canvas ){

                $felix = new Felix();
                $felix->productid = $productid;
                $felix->created = date('Y-m-d H:i:s');
                $felix->save();
                
                file_put_contents( $this->jsonfolder . $felix->id,  $canvas );
                file_put_contents( $this->thumbfolder . $felix->id,  $thumb );


                
                $this->result = true;
                $this->message = "OK";
                $this->felixid = $felix->id;
                return true;
            }else{
                $this->result = false;
                $this->message = "FAIL";
                return false;
            }
            
         }      
   }



?>