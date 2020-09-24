<?php

   class StreamMerkelappImage extends WebPage implements IValidatedView {

      protected $template = false;
      private $tmpfolder = '/home/www/tmpbilder/';
      
      public function Validate() {

         return array(
            'execute' => array(
               'fields' => array(
                  'image' => VALIDATE_STRING,
               )
            )

         );

      }

      public function Execute( $image = '' ) {

         try {
               
               
               header( "Content-Type: image/jpeg" );
               //header( 'content-length: ' . filesize( $this->tmpfolder . $image ) );
               // setup caching headers
               //header( "ETag: \"" . $image . "\"");
               //header( "Accept-Ranges: bytes");
               //header( sprintf( 'Expires: %s GMT', gmdate( 'D, d M Y H:i:s', time() + ( 86400 * 60 ) ) ) );
               //header( 'Cache-Control: public' );
               //header( 'Pragma: public' );
               
               //readfile ( $this->tmpfolder . $image );
               
               $thumb = new Imagick( $this->tmpfolder . $image );
               
               $width = $thumb->getImageWidth();
               $height = $thumb->getImageHeight();
               
               $ratio = max( $width, $height) / min( $width , $height );
               
               if( $width > $height ){
                   $tmpheight = 254 / $ratio;
                   $tmpwidth = 254;
               }else{
                   $tmpheight = 254 * $ratio;
                   $tmpwidth = 254;
               }
               
               
               
               $thumb->cropThumbnailImage( 254, $tmpheight  );

               echo $thumb;


         } Catch (Exception $e) {

           util::Debug( $e );
            //header( 'HTTP/1.0 404 Not Found' );
            //$this->setTemplate( 'errors.notfound' );

         }

      }

   }

?>