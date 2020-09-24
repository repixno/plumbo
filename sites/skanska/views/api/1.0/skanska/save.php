<?PHP
   import( 'pages.json' );
   model( 'order.leverpostei');
   import( 'website.cart' );
   import( 'website.giftpagetemplate' );
   import( 'website.giftordertemplate' );
   import( 'website.giftordertext' );
   import( 'website.giftorderclipart' );

   
   class SaveStabburetProject extends JSONPage implements NoAuthRequired, IView {
         //private $thumbfolder = '/var/www/repix/sites/website/webroot/static/stabburet/lokk/'; 
         private $thumbfolder = '/data/pd/stabburet/lokk/'; 
         private $folder = '/home/toringe/stabburet/'; 
         
         public function Execute() {

                $message = 'OK';
                    
                $data = $_POST['imgsrc'];
                $name = $_POST['name'];
                $year = $_POST['year'];
                $imgpos = $_POST['imgpos'];
                $imageid = $_POST['imageid'];
                $thumb = $_POST['thumb'];
                $malsize = $_POST['malsize'];
                $lokksize = $_POST['lokksize'];
                
                
                $refid = 7756;
                
                /*$savefolder = $this->folder . '/' . $name;
                
                if( !file_exists( $savefolder) ){
                    mkdir( $savefolder );
                }*/
                
                $thumbid = md5( uniqid() );
                
                
                $thumbfolder = $this->thumbfolder  . date( 'Y-m-d')  . '/'  ;
                
                if( !file_exists( $thumbfolder ) ){
                  mkdir( $thumbfolder );
                }
                
                file_put_contents( $thumbfolder . $thumbid . '.txt' , $thumb );
                
                
               $leverpostei = new DBLeverpostei();
               $leverpostei->imageid = $imageid;
               $leverpostei->thumbid = $thumbid;
               $leverpostei->productid = $refid;
               $leverpostei->name = $name;
               $leverpostei->year = $year;
               $leverpostei->malsize = $malsize;
               $leverpostei->lokksize = $lokksize;
               $leverpostei->imagepos = $imgpos;
               $leverpostei->created = date( 'Y-m-d H:i:s' );
               $leverpostei->save();
               
               $templateOrder = new GiftOrderTemplate();
               if( $lokksize == 'small'){
                  $malid = 3160;
                  $pageId = 0;
               }else{
                  $malid = 3160;
                  $pageId = 0;
               }
               
               $imgpos = str_replace(  array( '"[', ']"' ) ,'', $imgpos );
               $coordinates = explode( ',', $imgpos );
               
               $templateOrder->userid     = Login::userid();
               $templateOrder->templateid = $malid;
               $templateOrder->refid      = $refid;
               $templateOrder->pageid     = $pageId;
               $templateOrder->imageid    = $imageid;
               $templateOrder->x          = $coordinates[0];
               $templateOrder->y          = $coordinates[1];
               $templateOrder->dx         = $coordinates[2];
               $templateOrder->dy         = $coordinates[3];
               $templateOrder->rotate     = $coordinates[4];
               $templateOrder->editor_x   = 400;
               $templateOrder->editor_y   = 480;
               $templateOrder->printtype   = "stabburet_" . $leverpostei->id;
               $templateOrder->text       =  $name . '-|-' . $year;
               // Save page data to db
               $templateOrder->save();
               Session::set( 'stabburet-lokkid', $leverpostei->id );
                
               //add lokk to cart
               $cart = new Cart();
               
               //no more than one lid pr. order so we must clear cart
               if( count( $cart ) > 0 ){
                  $cart->clear();
                  $cart = new Cart();
               }
               
               
               // Try to put in the cart
               $attributes['templateorderid'] = $templateOrder->id;
               $cart->addItem( $refid, 1, $attributes );
               $cart->save();
            
                
                
               $this->result = true;
               $this->data =  $leverpostei->id;
               $this->message = $message;
               return true;
         }      
   }



?>
