<?php

   /**
    * Small wrapper class for < EF 2.8 carts
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    *
    */

   class EmulatedCart {

      /**
       * Get the emulated cart
       *
       * @return unknown
       */
      static function getCart() {
         
         return $_SESSION['cart'];
         
      }
      
      
      
      /**
       * Set the emailated cart
       *
       * @param array $cart
       */
      static function setCart( Array $cart ) {
         
         $_SESSION['cart'] = $cart;
         $_SESSION['emulatedcart'] = true;
         
         // Remove the old cart if one exists
         if( Login::isLoggedIn() ) {
            $filename = "/data/global/cart/".Login::userid();    
         } else {
            $filename = "/tmp/cart_".Session::id();
         }
         
         // Check if there actually is a file
         if( is_file( $filename ) && file_exists( $filename ) && filesize( $filename ) ) {
            unlink( $filename );  
         }
         
      }
      
      
      
      /**
       * Set the delivery info for order
       *
       * @param array $info
       */
      static function setDeliveryInfo( Array $info ) {
         
         $_SESSION["client_info"]["shopnowd"] = $info;
         
      }
      
      
      
      /**
       * Set the contact info for the cart
       *
       * @param array $info
       */
      static function setContactInfo( Array $info ) {
         
         $_SESSION["client_info"]["shopnowc"] = $info;
         
      }
      
      
      
      /**
       * Set the message if users adds one
       *
       * @param String $message
       */
      static function setMessage( $message = '' ) {
         
         $_SESSION["message"] = $message;
         
      }
      
      
      
      /**
       * Set the userid for this order
       * Used whn not logged in.
       * Needs to always be set.
       *
       * @param Integer $userid
       */
      static function setShopUID( $userid = 0 ) {
         
         $_SESSION['client_info']['shopuid'] = $userid;
         
      }
      
      
      
      /**
       * Retutn the shop uid
       *
       * @return integer
       */
      static function getShopUID() {
         
         return $_SESSION['client_info']['shopuid'];
         
      }
      
      
      
      /**
       * Set the old cart only contains service products
       *
       * @param boolean $value
       */
      static function setServiceProductsOnly( $value = false ) {
         
         if( $value ) {
            
            $_SESSION['cart']['serviceproducts'] = true;
            
         } else {
            
            unset( $_SESSION['cart']['serviceproducts'] );
            
         }
         
      }
      
      
      
      /**
       * Returns true or false depending
       * on if shopuid is set or not
       *
       * @return boolean
      */
      static function hasShopUID() {

         if( isset( $_SESSION['client_info']['shopuid'] ) ) {

            return true;

         }

         return false;

      }
      

      
      /**
       * Set appropriate sessionparams to use
       * later on EF < 3 side.
       *
       * @param array $attributes
       */
      static function CDDVDOrder( $attributes = array() ) {
         
         if( isset( $attributes['type'] ) ) {
            
            switch( $attributes['type'] ) {
               
               case 'CD':
               case 'DVD':
                  $_SESSION['backuporder'] = $attributes;
                  break;
               default:
                  break;
               
            }
            
         } else {
            unset( $_SESSION['backuporder'] );
         }
         
      }
         
         
       
         
         
   }


?>