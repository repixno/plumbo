<?PHP
   
   Dispatcher::extendView( 'images.products' );
   
   class MenuImagesViewer extends ProductImageViewer implements IView {
      
      protected $resourcepath = '%s/data/images/menuimages/%s';
      
   }
   
?>