<?php

   /**
    * Remove an item from cart
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    *
    */

   class KasseRemove extends WebPage implements IVIew {

      
      /**
       * Remove item from cart
       *
       * @param string $prodno
       * @param integer $referenceid
       */
      public function Execute( $prodno = '', $referenceid = '' ) {
         
         $cart = new Cart();
         $cart->removeItem( $prodno, $referenceid );
         $cart->save();
         $this->cart = $cart->enum();
         
         relocate("/klarna_verifiser");
         //echo 'OK';
         exit;
         
      }
      
      
      /**
       * Remove redeye removal from product
       *
       * @param string $prodno
       * @param integer $referenceid
       */
      public function redeye( $prodno = null, $referenceid = null ) {
         
         $cart = new Cart();
         $cart->removeRedEye( $prodno, $referenceid );
         $cart->save();
         echo 'OK';
         exit;
         
      }
      
      /**
       * Remove redeye removal from product
       *
       * @param string $prodno
       * @param integer $referenceid
       */
      public function varnish( $prodno = null, $referenceid = null ) {
         
         $cart = new Cart();
         $cart->removeVarnish( $prodno, $referenceid );
         $cart->save();
         echo 'OK';
         exit;
         
      }
      
            /**
       * Remove redeye removal from product
       *
       * @param string $prodno
       * @param integer $referenceid
       */
      public function upgrade( $prodno = null, $referenceid = null ) {
         
         $cart = new Cart();
         $cart->removeUpgrade( $prodno, $referenceid );
         $cart->save();
         echo 'OK';
         exit;
         
      }
      
      /**
       * Remove maskit from product
       *
       * @param string $prodno
       * @param integer $referenceid
       */
      public function maskit( $prodno = null, $referenceid = null ) {
         
         $cart = new Cart();
         $cart->removeMaskit( $prodno, $referenceid );
         $cart->save();
         echo 'OK';
         exit;
         
      }   
      
      
      
   }


?>