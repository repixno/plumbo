<?php

   /**
    * Put mediaclip order in cart
    *
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    *
    */

   import( 'website.mediacliporder' );
   import( 'website.cart' );
   import( 'website.product' );
   import( 'website.mediacliporderrequest' );


   class MediaclipAddToCart extends WebPage implements IView {

      protected $template = '';
      protected $localData = null;

      public function Execute( $mediacliporderid = 0 ) {

         $mcor = new MediaclipOrderRequest( isset( $this->localData ) ? $this->localData : $_GET );
         
         if( !$mcor instanceof MediaclipOrderRequest ) {
            throw new Exception( 'Failed to load MediaclipOrderRequest.' );
            die();
         }

         if( $mcor->valid() ) {

            //$productoptionid = $mcor->getProductOptionId();

            if( $productoptionid > 0 ) {
               $productoption = new ProductOption( $productoptionid );
            } else {
               $productoption = ProductOption::fromRefId( $mcor->getRefId() );
            }


            if( $productoption instanceof ProductOption && $productoption->isLoaded() ) {

               $mcor->save();
               //die();

               $attributes = array(
                  'userid' => $mcor->getUserId(),
                  'projectid' => $mcor->getOrderId(),
                  'title' => $mcor->getTitle()."<splitter>".$mcor->getRealProjectId(),
                  'extrapages' => $mcor->getExtraPages(),
               );

               $cart = new Cart();
               $cart->addItemByProductOptionId( $productoption->id, $mcor->getQuantity(), $attributes );
               $cart->save();

            } else {

               throw new Exception( 'Not a valid product option' );
               die();

            }


         } else if( MediaclipOrder::valid() ) {
	        die();
            $attributes = array(
               "userid" => MediaclipOrder::userId(),
               "projectid" => MediaclipOrder::projectId(),
               "title" => MediaclipOrder::title()."<splitter>".MediaclipOrder::projectId(),
               "extrapages" => MediaclipOrder::extraPages(),
            );

            //$productoptionid = 612;
            $productoptionid = MediaclipOrder::productOptionId();

            if( $productoptionid > 0 ) {

               $productOption = new ProductOption( $productoptionid );

            } else {

               $productOption = ProductOption::fromRefId( MediaclipOrder::refId() );

            }

            if( $productOption instanceof ProductOption && $productOption->isLoaded() ) {

               $cart = new Cart();
               $cart->addItemByProductOptionId( $productOption->id, MediaclipOrder::quantity(), $attributes );
               $cart->save();
               MediaclipOrder::clear();

            } else {

               throw new Exception( 'Failed to load productoption' );
               die();

            }

         } else {

            throw new Exception( 'Failed to validate mediaclip order. Missing elements.' );
            die();

         }
         $_SESSION['mediaclip']['reload'] = 1;
         relocate( sprintf('/cart/mediaclip/productoptions/%s', $attributes['projectid']));
         //relocate( '/cart/mediaclip/accessories' );
         //relocate( '/cart/' );

      }

   }


?>