<?PHP
   
   // import required libraries
   include "../../../bootstrap.php";
   
   // configure app
   config( 'website.config' );
   
   // create a request Dispatcher instance
   $dispatcher = new Dispatcher( 'Website' );
   
   // ...and route the request!
   $dispatcher->route( isset( $_GET['dispatch'] ) ? $_GET['dispatch'] : '' );
   
?>