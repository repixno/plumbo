<?PHP
   import( 'pages.json' );
   library( 'fonts.yctinttf' );
   
   class SaveSmilesOnTiles extends JSONPage implements NoAuthRequired, IView {
    
         private $folder = '/var/www/repix/sites/studentskylt/webroot/static/editorfonts/'; 
         
         public function Execute() {
            
            
            $fontlist = array();
            
            foreach( glob( $this->folder . '*ttf' ) as $font ){  
                
                //create yctin_ttf object
                $ttf = new ycTIN_TTF();
                 
                //open font file
                if ($ttf->open($font)) {
                    //Util::Debug( basename( $font ) );
                    //get name table
                    $rs = $ttf->getNameTable();
                    $rs = reset( $rs );
                 
                    $fontlist[$rs[1]][$rs[2] ] = basename( $font );
                    //display result
                    //Util::debug( $rs[1] . "---" .  $rs[2] ) ;
                }
                
            }

            $this->result = true;
            $this->data =  $fontlist;
            $this->message = "OK";
            
         }      
   }



?>