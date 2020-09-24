<?php

   class LanguageSettings extends WebPage implements IView {
      
      protected $template = false;
      
      public function Execute( $language = 'en_US' ) {
         
         // set the current language
         i18n::setLanguage( $language );
         
         // relocate back
         if( isset( $_SERVER['HTTP_REFERER'] ) ) {
         
            relocate( $_SERVER['HTTP_REFERER'] );
            
         } else {
            
            relocate( '/' );
            
         }
         
      }
      
   }
   
?>