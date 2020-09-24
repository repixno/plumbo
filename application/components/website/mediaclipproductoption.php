<?php

   /**
    * Wrapper class for mediaclip
    * product oprions and configs
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */


   // Get configs
   config( 'website.mediaclip' );
   config( 'website.cart' );
   
   // Get classes
   import( 'website.product' );
   
   class MediaclipProductOption extends ProductOption {
      
      
      /**
       * Create a Mediaclip Product Option from prodno
       *
       * @param string $prodno
       * @return unknown
       */
      static function fromProdNo( $prodno ) {
         
         try {
            
            return MediaclipProductOption::fromFieldValue( 
               array( 
                  'prodno' => $prodno 
               ), 
               'MediaclipProductOption'
            );
            
            
         } catch ( Exception $e ) {
            
            return false;
            
         }
         
      }
      
      
      /**
       * Create a productoption from refid
       *
       * @param integer $refid
       * @return object
       */
      static function fromRefId( $refid ) {
         
         try {
            
            return MediaclipProductOption::fromFieldValue(
               array(
                  'refid' => $refid
               ),
               'MediaclipProductOption'
            );
            
         } catch( Exception $e ) {
            
            return false;
            
         }
         
      }
      
      /**
       * Get the proper refid for extra
       * pages on this product
       *
       * @return integer
       */
      public function extraPagesRefId() {
         
         $data = Settings::get( 'mediaclip', 'extrapages' );
         return $data[$this->cover()];
         
      }
      
      
      
      /**
       * Get type of cover for this product
       *
       * @return string
       */
      public function cover() {
         
         $type = 'hardcover';
         if( $this->isTag( 'softcover' ) ) {
            $type = 'softcover';
         }
         
         return $type;
         
      }
      
      
      
      /**
       * Check if param is a tag in MediaclipProductOption
       *
       * @param string $tag
       * @return boolean
       */
      private function isTag( $tag = '' ) {
         
         if( isset( $tag ) ) {
            
            if( stristr( $this->tags, $tag ) ) {
               
               return true;
               
            }
            
         }
         
         return false;
         
      }
      
   }


?>