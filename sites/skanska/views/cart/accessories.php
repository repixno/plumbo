<?PHP
   
   Dispatcher::extendView( 'order-prints.accessories' );
   
   class CartAccessories extends OrderPrintsAccessories implements IView {
      
      protected $template = 'cart.accessories';
      
   	/*public function Execute() {
   		
   		if( Dispatcher::getPortal() == 'MY-KID' ) relocate( "/cart" );
   		
   	}*/
      
   }
   
?>