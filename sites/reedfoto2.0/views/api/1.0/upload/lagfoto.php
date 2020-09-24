<?PHP
   import( 'pages.json' );
   import( 'website.order.manual.default' );
   
   
   class UploadLagfoto extends JSONPage implements NoAuthRequired, IView {
    
        private $imagefolder = "/data/global/norwaycup/lagfoto/";
        private $reedfotouser = 941275;
        
        public function Execute() {
        
            //die( "error..." );
            $imagefile = $_FILES['image'];
            $data = $_POST;
            
            $uniqid = uniqid();
            $rand_start = rand(1,5);
            $imageid = substr($uniqid,$rand_start,8);
            
            
            try{
                if( file_exists( $imagefile['tmp_name' ] ) ){
                    if( move_uploaded_file( $imagefile['tmp_name' ], $this->imagefolder . $imageid . '.jpg' ) ){
                        
                        $articles['prints'][] = array(
                                                   'prodno' =>  "0002",
                                                   'quantity' =>  $data['quantity'],
                                                   'file' => $this->imagefolder . $imageid . '.jpg',
                                                   'fitin' => 0
                                                  );
                        
                        $articles['productionmethod'][] = 352;
                        $articles['papertype'][] = 11;
                        
                        $orderdata = array(
                            'userid' => $this->reedfotouser,
                            'fullname' => $data['name'],
                            'address'  => $data['address'],
                            'zipcode'  => $data['zipcode'],
                            'city'  => $data['city'],
                            'article' => $articles,
                            'comment' =>  $data['team'] . " - - " . $data['comment']
                        );
                        
                        if( $data['delivery_at_ekeberg'] == 1 ){
                           $orderdata['delivery'] = 'local';
                        }
                        else{
                           $orderdata['delivery'] = 'ship';
                        }
                        
                        
                        $order = new ManualOrder();
                        $orderid = $order->executeManualOrder( $orderdata );
                        $this->result = true;
                        $this->message = $imageid;
                        return;
                    }else{
                        $this->result = false;
                        $this->message = "upload failed";
                    }
                }
                
            }catch( Exception $e ){
                
                $this->result = false;
                $this->message = $e->getMessage();
            }
            
            //$this->result = false;
            //$this->message = 'Upload failed: ';        
        }
   }
   
?>