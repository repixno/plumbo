<?PHP
  
   // bootstrap the platform
   chdir( dirname( __FILE__ ) ); 
   include '../../configuration/common/security.php';

   define( 'IMAGE_ROOT', '/data/bildearkiv/' );

   class MediaClipThumbnails {
      
      public function Index( $urlargs = array() ) {

         $type = $urlargs[1];
         $image = str_replace('|', '/', str_replace( '..', '', base64_decode( $urlargs[2] ) ) );
         $signature = $urlargs[3];

         if ( MediaClipThumbnails::validateSignature( $image, $signature ) ) {

            $ext = '';

            switch( $type ) {
               case 'thumbnail':
               case 'thumb':
                  $ext = '.preview.jpg';
                  break;
               default:
                  break;
            }  

            $imagepath = sprintf( '%s%s%s', IMAGE_ROOT, $image, $ext );

            $size = getimagesize( $imagepath );

            if ( $size ) {

                header( sprintf( 'Content-type: %s', $size['mime'] ) );
                header( "Accept-Ranges: bytes");
                header( sprintf( 'Expires: %s GMT', gmdate( 'D, d M Y H:i:s', time() + ( 86400 * 60 ) ) ) );
                header( 'Cache-Control: public' );
                header( 'Pragma: public' );

		readfile( $imagepath );

            }

          }

      }

      public function validateSignature( $data, $signature, $twofactorykey = '' ) {

         $signkey = SecurityKeys::get( SECURITY_KEY_SEEDKEY );

         return md5( $data . $signkey . $twofactorkey ) == $signature ? true : false;

      }
      
   }

   function import( $module ) {

      include sprintf( '%s%s.php', '../../application/components/', str_replace( '.', '/', $module ) ); 

   }

   MediaClipThumbnails::Index( explode( '/', $_GET['q'] ) );

?>
