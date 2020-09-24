<?PHP
   
   Dispatcher::extendView( 'images.products' );
   
   class GiftTemplateWebImageViewer extends ProductImageViewer implements IView {
      
      protected $resourcepath = '%s/data/images/gifttemplatesweb/%s';
      
   }
   
?>