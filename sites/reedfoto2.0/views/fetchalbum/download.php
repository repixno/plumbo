<?PHP
    config( 'website.storage' );
    config( 'website.order' );
    class downloadRFImage extends Webpage implements IView {
      
        protected $template = null;
      
        public function execute( $code ){
         
            $secure = Settings::Get( 'order' , 'securecode' );
            $imagespath = Settings::Get( 'storage', 'path');
            
            $base = base64_decode($code);
            
            list( $expired, $bid, $securecode ) =  explode( '|', $base );
            
            if(  sha1( $secure . $bid . $expired ) !== $securecode && strtotime("now") < $expired  ){
                throw new SecurityException( 'You dont have permission to download this image!' );
            }
            else{
                
                $filename = $imagespath . DB::query( "SELECT filnamn FROM bildeinfo WHERE bid = ?", $bid )->fetchSingle();
                if (!is_file( $filename )) { die("<b>404 File not found!</b>"); }
                $len = filesize( $filename );
                //Begin writing headers
                header("Pragma: public");
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: public");
                header("Content-Description: File Transfer");
                
                //Use the switch-generated Content-Type
                header("Content-Type: image/jpg");
                
                //Force the download
                $header="Content-Disposition: attachment; filename=ReedFoto-$bid.jpg;";
                header( $header );
                header("Content-Transfer-Encoding: binary");
                header("Content-Length: " . $len);
                readfile( $filename );
            }
            
        }
       
   }