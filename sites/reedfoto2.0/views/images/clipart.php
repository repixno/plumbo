<?PHP
   
   Dispatcher::extendView( 'images.products' );
   
   class ClipartImageViewer extends ProductImageViewer implements IView {
      
      protected $resourcepath = '%s/data/images/clipart/%s';
      
   }
   
?>