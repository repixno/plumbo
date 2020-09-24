<?php

   /**
    * Validate a cell phone nr
    * as a norwegian number.
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    *
    */


   class ValidateCellPhone {
      
      /**
       * Validate given number
       *
       * @param string $strCellNr
       * @return boolean
       */
      static function validate( $strCellNr ) {
         
         if( !strlen( $strCellNr ) || strlen( $strCellNr ) != 8 ) {
            
            return false;
            
         }
         
         $firstdigit = substr( $strCellNr, 0, 1 );
         
         if( $firstdigit != 4 && $firstdigit != 9 ) {
            
            return false;
            
         }
         
         return true;
         
      }
      
   }


?>