<?PHP
   
   include "../../bootstrap.php";
   config('website.config');
   
   import( 'website.image' );
   
   $_SERVER['REMOTE_ADDR'] = '78.41.122.141';
   
   Login::byUserId( 509961 );
   
   $image = new Image( 142802290 );
   print_r( $image->asArray() );
   
?>