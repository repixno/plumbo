<?PHP
   
   import( 'website.product' );

   class ProductListingTest extends WebPage implements IView {
      
      protected $template = false;
      
      public function Execute() {
         
         $products = Product::listProductsBySales();
         util::Debug( $products );
         
         $products = Product::listProductsByCreated();
         util::Debug( $products );
         
      }
      
   }
   
?>