<?PHP

library( 'mobiledetect.Mobile_Detect' );

class SkapaFelix extends WebPage implements IView {

    protected $template = 'frontpage.index';
    private $thumbfolder = '/data/pd/felix/inspirasjon/thumb/';

    public function Execute(){

        if( date('Y-m-d') >  date('Y-m-d', strtotime('2017-07-31') ) ){  
            $this->template = 'frontpage.index';
    
            $detect = new Mobile_Detect;
            $mobile =$_COOKIE["isMobiledevice"];
            
            if( $mobile == 'true' && $detect->isTablet() ){
                 $this->template = 'frontpage.index';
            }
            else if( $detect->isMobile() && !$detect->isTablet() ){
               $this->template = 'frontpage.index_mobile';
            }
        
        }else{
            $this->template = 'frontpage.index_ended';
        }
        


        $cart = new Cart();
        $cart->clear();
        Login::logout();

    }

    public function stream($bid){
        $this->template = null;
        $image = new Image((int)$bid);

        $imagespath = '/data/bildearkiv/';
        $filesrc = $imagespath .  $image->getFilename();


        $filetype = $image->filtype;

        if( $filetype != 'jpg' ){
           $cachefile = str_replace( '.jpg', '.' . $filetype, $filesrc );

           copy( $filesrc,  $cachefile );

        }else{
           $cachefile = $filesrc;
        }

        $thumb = new Imagick( $cachefile );
        $thumb->thumbnailImage( 400,400, true);

        header( "Content-Type: image/$filetype" );
        echo $thumb;

    }

    public function Download( $id ){

         $folder = '/data/pd/felix/share/';
         $this->template = null;
         $filecontent = file_get_contents( $folder . $id . '.txt' );
         $filecontent = str_replace( 'data:image/png;base64,', '' , $filecontent );
         $filecontent = str_replace( 'data:image/jpeg;base64,', '' , $filecontent );
         $filecontent = base64_decode( $filecontent );
         header("Content-type: application/octet-stream");
         echo  $filecontent;

      }

     public function Download2( $orderid ){

        $folder = '/data/pd/felix/thumb/';
        $this->template = null;


        $id = DB::query( "SELECT id FROM order_felix where orderid  = ?", $orderid )->fetchSingle();


        $filecontent = file_get_contents( $folder . $id  );
        $filecontent = str_replace( 'data:image/png;base64,', '' , $filecontent );
        $filecontent = str_replace( 'data:image/jpeg;base64,', '' , $filecontent );
        $filecontent = base64_decode( $filecontent );

        $img = new Imagick();
        $img->readimageblob($filecontent);

        $width =  $img->getImageWidth();
        $height = $img->getImageHeight() ;


        if( $height <= 255 ){
           $template = new Imagick('/var/www/repix/sites/website/webroot/felix/flaske.png');
           $img->scaleImage( 127, 198, true );
           $x = 162;
           $y = 194;
        }else{
           $template = new Imagick('/var/www/repix/sites/website/webroot/felix/flaske125.png');
           $img->scaleImage( 136, 272, true );
           $x = 153;
           $y = 224;
        }

        $background = new Imagick();
        $background->newImage( $template->getImageWidth(), $template->getImageHeight(), new ImagickPixel('transparent'));
        $background->setImageFormat( "png" );
        //$img->newImage($width, $height, new ImagickPixel('transparent'));

        $background->compositeImage( $img, $img->getImageCompose(), $x , $y );
        $background->compositeImage( $template, $template->getImageCompose(), 0 , 0 );

        header("Content-type: application/octet-stream");
        echo  $background;

      }


    public function thumb( $id ){
        $this->template = null;
        $thumb = file_get_contents( $this->thumbfolder . $id );


        $img = str_replace('data:image/jpeg;base64,', '', $thumb);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);


        header('Content-Type: image/jpeg');

        echo $data;

        exit;

    }

    public function ShareFacebook( $id  = 0){
         $this->template = "frontpage.facebook";
         $this->id = $id;
      }

    public function ShareFacebooknew( $id  = 0){
      $this->template = "frontpage.facebooknew";
      $this->id = $id;
   }


    public function ThumbShare( $id ){

        $folder = '/data/pd/felix/share/';
        $this->template = null;

        $filecontent = file_get_contents( $folder . $id . '.txt' );
        $filecontent = str_replace( 'data:image/png;base64,', '' , $filecontent );
        $filecontent = str_replace( 'data:image/jpeg;base64,', '' , $filecontent );
        $filecontent = base64_decode( $filecontent );

        $img = new Imagick();
        $img->readimageblob($filecontent);

        $width =  $img->getImageWidth();
        $height = $img->getImageHeight() ;


        if( $height <= 255 ){
            $template = new Imagick('/var/www/repix/sites/website/webroot/felix/flaske.png');
            $img->scaleImage( 127, 198, true );
            $x = 162;
            $y = 194;
        }else{
            $template = new Imagick('/var/www/repix/sites/website/webroot/felix/flaske125.png');
            $img->scaleImage( 136, 272, true );
            $x = 153;
            $y = 224;
        }

        $background = new Imagick();
        $background->newImage( $template->getImageWidth(), $template->getImageHeight(), new ImagickPixel('transparent'));
        $background->setImageFormat( "png" );
        //$img->newImage($width, $height, new ImagickPixel('transparent'));

        $background->compositeImage( $img, $img->getImageCompose(), $x , $y );
        $background->compositeImage( $template, $template->getImageCompose(), 0 , 0 );

        header("Content-type: image/png");
        echo  $background;

    }


    public function ThumbShareNew( $orderid ){

        $folder = '/data/pd/felix/thumb/';
        $this->template = null;


        $id = DB::query( "SELECT id FROM order_felix where orderid  = ?", $orderid )->fetchSingle();


        $filecontent = file_get_contents( $folder . $id  );
        $filecontent = str_replace( 'data:image/png;base64,', '' , $filecontent );
        $filecontent = str_replace( 'data:image/jpeg;base64,', '' , $filecontent );
        $filecontent = base64_decode( $filecontent );

        $img = new Imagick();
        $img->readimageblob($filecontent);

        $width =  $img->getImageWidth();
        $height = $img->getImageHeight() ;


        if( $height <= 255 ){
            $template = new Imagick('/var/www/repix/sites/website/webroot/felix/flaske.png');
            $img->scaleImage( 127, 198, true );
            $x = 162;
            $y = 194;
        }else{
            $template = new Imagick('/var/www/repix/sites/website/webroot/felix/flaske125.png');
            $img->scaleImage( 136, 272, true );
            $x = 153;
            $y = 224;
        }

        $background = new Imagick();
        $background->newImage( $template->getImageWidth(), $template->getImageHeight(), new ImagickPixel('transparent'));
        $background->setImageFormat( "png" );
        //$img->newImage($width, $height, new ImagickPixel('transparent'));

        $background->compositeImage( $img, $img->getImageCompose(), $x , $y );
        $background->compositeImage( $template, $template->getImageCompose(), 0 , 0 );

        header("Content-type: image/png");
        echo  $background;

    }


    public function text(){
        $this->template = null;

        $tmpname =  uniqid();

        $printtext = $_GET['text'];

        $printtext = urldecode($printtext);


        $height = $_GET['height'] ? $_GET['height'] : 200;

        $txtcolor = new ImagickPixel( "#e01111" );

        $draw = new ImagickDraw();
        $draw->setFont( '/var/www/repix/sites/website/webroot/felix/fonts/felixscript.ttf' );
        $draw->setFontSize( 196 );
        $draw->setGravity( Imagick::GRAVITY_CENTER );
        $draw->setFillColor( $txtcolor );
        $draw->setStrokeColor($txtcolor);
        $draw->setStrokeWidth(8);
        $draw->setStrokeAntialias(true);
        $draw->setTextAntialias(true);

        $txtimg = new Imagick();

        $metrics = $txtimg->queryFontMetrics( $draw, $printtext );

        $txtimg->newImage( $metrics['textWidth'] + 36, $metrics['textHeight'] + 36,  new ImagickPixel('none'), "png");

        $txtimg->annotateImage($draw,+8,+8,0,$printtext);
        $txtimg->writeImage("/home/www/tmpbilder/testrext$tmpname.png" );

        system("convert /home/www/tmpbilder/testrext$tmpname.png -alpha Extract -blur 0x8 -shade 120x25 -alpha On -background gray50 -alpha background -auto-level /home/www/tmpbilder/aqua_shade$tmpname.png");
        $alpha = new Imagick("/home/www/tmpbilder/aqua_shade$tmpname.png");
        $txtimg->compositeImage( $alpha, imagick::COMPOSITE_HARDLIGHT, 0 , 0 );
        $txtimg->scaleImage( 200, $height, true);
        header("Content-type: image/png");
        echo $txtimg;

    }

















}
