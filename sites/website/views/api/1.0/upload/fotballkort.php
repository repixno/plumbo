<?PHP
   
   import( 'pages.json' );
   model( 'reedfoto.fotballkort');
   
   
   class UploadImage extends JSONPage implements NoAuthRequired, IView {
    
        private $imagefolder = "/var/www/repix/data/reedfoto/norwaycup/images/2014/";
        
        public function Execute() {
        
            $imagefile = $_FILES['image'];
            $data = $_POST;
            $uniqcheck = null;
            
            while( $uniqcheck == null ){
               
               $uniqid = uniqid();
               $rand_start = rand(1,5);
               $imageid = substr($uniqid,$rand_start,8);
               
               if( !DB::query( 'SELECT id FROM project_fotballkort WHERE imageid = ?', $imageid )->fetchSingle() ){
                  $uniqcheck = true;
               }
               
            }
            
            
            
            try{
                if( file_exists( $imagefile['tmp_name' ] ) ){
                  
                    if( move_uploaded_file( $imagefile['tmp_name' ], $this->imagefolder . $imageid . '.jpg' ) ){
                        
                        $fotballkort = new fotballkortDB();
                        $fotballkort->imageid = $imageid;
                        $fotballkort->name = $data['name'];  
                        $fotballkort->team = $data['team'];  
                        $fotballkort->mobile = $data['phone'];  
                        $fotballkort->email = $data['email'];  
                        $fotballkort->country = $data['country'];  
                        $fotballkort->imported = date( 'Y-m-d H:i:s');
                        $fotballkort->save();
                        
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