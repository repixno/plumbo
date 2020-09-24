<?php

   /**
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    * 
    */

   config( 'finance.currency' );

   class FinanceCurrency {
      
      static function getCurrency() {
         
         $currencies = Settings::Get( 'finance', 'currency' );
         
         $regionid = WebsiteHelper::region();
         if( !isset( $regionid ) ) $regionid = 1;
         
         // Get the currency code for given region
         $currencycode = DB::query( "SELECT export_valuta_code FROM region WHERE regionid=?", $regionid )->fetchSingle();
         
         if( !is_numeric( $currencycode ) ) $currencycode = 1;
         
         return $currencies[$currencycode];
      
      }
      
   }

?>