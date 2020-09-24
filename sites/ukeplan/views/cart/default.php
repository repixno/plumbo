<?php

   /**
    * Cart functionality for user without javascript
    *
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    *
    */

   import( 'session.usersessionarray' );
   import( 'website.cart' );

   class CartDefault extends Webpage implements IView {

      protected $template = 'cart.index';

      public function Execute() {
      
         $discountfeedback = Session::pipe( 'discountfeedback' );
         Session::pipe( 'cart_redirecturl');
         
         if( isset( $discountfeedback ) && is_array( $discountfeedback ) ) {
            list( $key, $value ) = each( $discountfeedback );
            $this->discountfeedback = $key;
         }
         
         if( Dispatcher::getPortal() == 'UP-001'){
            $this->checkouturl = 'kasse';
         }else{
            $this->checkouturl = 'checkout';
         }
         
         $cart = new Cart();
         
      }


      /**
       * Add given productno and quantity to the user cart.
       * Relocate to given page or error page.
       *
       * @param string $prodno
       * @param integer $quantity
       * @param string $relocate
       */
      public function add( $prodno = '', $quantity = 1, $relocate = '' ) {

         if( isset( $_REQUEST['prodno'] ) ) $prodno = $_REQUEST['prodno'];
         if( isset( $_REQUEST['quantity'] ) ) $quantity = $_REQUEST['quantity'];
         if( isset( $_REQUEST['attributes'] ) ) $attributes = $_REQUEST['attributes'];


         $product = ProductOption::fromProdNo( $prodno );
         if( !$product instanceof ProductOption || !$product->isLoaded() ) {
            relocate( '/cart' );
            die();
         }

         if( $quantity > 0 ) {
            $cart = new Cart();
            $cart->addItem( $prodno, $quantity );
            $cart->save();
         }

         relocate( '/cart' );

      }
      
      
      
      /**
       * Add product and quantity to cart
       *
       * @param integer $productoptionid
       * @param integer $quantity
       * @param array $attributes
       */
      public function addItemByProductOptionId( $productoptionid = 0, $quantity = 1, $attributes = array() ) {
         
         if( isset( $_REQUEST['productoptionid'] ) ) $productoptionid = $_REQUEST['productoptionid'];
         if( isset( $_REQUEST['quantity'] ) ) $quantity = $_REQUEST['quantity'];
         if( isset( $_REQUEST['attributes'] ) ) $attributes = $_REQUEST['attributes'];
         
         
         if( !empty( $attributes['initialer'] )  ){
            
             $attributes['text'] =  $attributes['initialer'];
         }
         if( !empty( $attributes['name'] )  ){
            
             $attributes['text'] =  $attributes['name'];
         }
         $cart = new Cart();
         $cart->addItemByProductOptionId( $productoptionid, $quantity, $attributes );
         $cart->save();
         
         relocate( '/cart' );
         
      }


      /**
       * Clear the whole cart
       *
       */
      public function clear() {

         $cart = new Cart();
         $cart->clear();
         relocate( '/cart' );

      }


      /**
       * Show an error message
       *
       * @param specify error $error
       */
      private function error( $error = '' ) {

         if( empty( $error ) ) $error = 'Unspecified error happened. Ouch!!!';

         $this->setTemplate( 'errors.default' );
         $this->error = $error;

      }

      
      /**
       * Activate a giftcard
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       *
       */
      public function giftcard() {
         
         import( 'website.order.giftcard' );
         
         $code = isset( $_POST['code'] ) ? $_POST['code'] : null;
         
         if( isset( $code ) ) {
            
            $giftcard = Giftcard::fromCode( $code );
            
            if( $giftcard instanceof Giftcard && $giftcard->isLoaded() && Login::isLoggedIn() ) {
               
               $now = date( 'Y-m-d H:i:s' );
               
               $giftcard->userid = Login::userid();
               $giftcard->registered = date( 'Y-m-d H:i:s' );
               $giftcard->save();
               $cart = new Cart();
               $cart->useGiftcard( $giftcard->asArray() );
               $cart->save();
               
            }
            
         }
         
         relocate( '/cart' );
         
      }
      
      
      public function printGiftcard() {
         
         $cart = new Cart();
         $cart->togglePrintGiftcard();
         $cart->save();
            
      }
      
      
      public function coupon() {

          $discountCode = str_replace(' ', '' , $_REQUEST[ 'code' ]);

         // Must be logged in.
         if ( !Login::isLoggedIn() ) {

            $this->status = array( 'isnotloggedin' => true );
            return false;

         }

         $user = new User( Login::userid() );
         $resStatus = array();

         // Checking and register discount code.
         if ( !empty( $discountCode ) ) {

            $res = $user->checkDiscount( $discountCode );
            $status = $res['code'];
            
            $cart = new Cart();

            switch ( $status ) {

               case 2:
                  $resStatus[ 'saved' ] = true;
                  //$cart->addCartDiscount( $res['id'] );
                  $discount = $cart->addCartDiscount( $res );
                  $cart->save();
                  break;
               case 1:
                  $resStatus[ 'saved' ] = true;
                  //$cart->addCartDiscount( $res['id'] );
                  $discount = $cart->addCartDiscount( $res );
                  $cart->save();
                  break;
               case 0:
                  $resStatus[ 'unknown' ] = true;
                  break;
               default:
                  $resStatus['unknown'] = true;

            }

         } else {

            $resStatus[ 'empty' ] = true;

         }
         
         $this->status = $resStatus;
         if( count( $discount['info'] ) > 0 ) {
            
            $cart->setDiscount( $discount );
            $cart->save();
            
         }
         Session::pipe( 'discountfeedback', $resStatus );
         relocate( '/cart' );
         die();

      }

   }


?>