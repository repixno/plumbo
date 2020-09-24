<?php

   /**
    * Components for calculating different product set options
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   config( 'website.cart' );
   
   define( 'SIZE_OF_CD', 700 );
   define( 'SIZE_OF_DVD', 4486 );
   
   
   class ProductSet {
      

      /**
       * Calculate and return the products set quantity
       * Some sets are dynamic, like in the case of
       * CD:s and DVD:s
       * 
       * @param integer $id
       * @return integer
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      static function getSetQuantity( $id ) {
         
         $type = ProductSet::getSetType( $id );
         
         switch( $type ) {
            
            case 'CD':
         
               // Get file info
               $res = DB::query( "
                  SELECT 
                     count(*) as antall, 
                     sum(size) as totalsize 
                  FROM 
                     bildeinfo 
                  WHERE 
                     owner_uid=? AND 
                     deleted_at is null AND
                     quarantined_at IS NULL
                  ", Login::userid() 
               );
            
               list( $qty, $totalsize ) = $res->fetchRow();

               // Calculate all sizes and quantities
               $imagequantity = $qty;
               $size = $totalsize;
               $mbsize = sprintf( "%.02f",( $size/1024/1024) );
               $discquantity = (int) ceil( $mbsize / SIZE_OF_CD );
               return $discquantity;            
               
               break;
               
            case 'DVD':
               // Get file info
               $res = DB::query( "
                  SELECT 
                     count(*) as antall, 
                     sum(size) as totalsize 
                  FROM 
                     bildeinfo 
                  WHERE 
                     owner_uid=? AND 
                     deleted_at is null AND
                     quarantined_at IS NULL
                  ", Login::userid() 
               );
            
               list( $qty, $totalsize ) = $res->fetchRow();

               // Calculate all sizes and quantities
               $imagequantity = $qty;
               $size = $totalsize;
               $mbsize = sprintf( "%.02f",( $size/1024/1024) );
               $discquantity = (int) ceil( $mbsize / SIZE_OF_DVD );
               return $discquantity;
               
               break;
            default:
               break;
         }
         
         return 0;
         
      }
      
      
      /**
       * Get the type of product this set is.
       *
       * @param integer $id
       * @return string
       */
      static function getSetType( $id ) {
         
         $settings = Settings::Get( 'cart', 'set' );
         $types = $settings['types'];
         
         return $types[$id];
         
      }
      
      
      /**
       * Is this product of a set type?
       * Check if product is in correct
       * config file.
       *
       * @param integer $id
       * @return boolean
       */
      static function isSetProduct( $id ) {
         
         $settings = Settings::Get( 'cart', 'set' );
         $settings = $settings['optionidarray'];
         if( is_array( $settings ) ) {
            if( in_array( $id, $settings ) ) {
               return true;
            }
         }
         
         return false;
         
      }
      
   }

?>