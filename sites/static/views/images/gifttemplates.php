<?PHP
   
   Dispatcher::extendView( 'images.products' );
   
   class GiftTemplateImageViewer extends ProductImageViewer implements IView {
      
      protected $resourcepath = '%s/data/images/gifttemplates/%s';
      
   }
   
?>