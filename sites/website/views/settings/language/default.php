<?php
   
   import( 'website.textentity' );
   
   class LanguageSettings extends WebPage implements IView {
      
      protected $template = false;
      
      public function Execute( $language = 'en_US' ) {
         
         // relocate back
         if( isset( $_SERVER['HTTP_REFERER'] ) ) {
            
            $url = parse_url( $_SERVER['HTTP_REFERER'] );
            $menuitem = Menu::findItemFromURL( $url['path'] );
            
            if( $menuitem->partialMatch ) {
               
               $entity = preg_split( '/\//', $_SERVER['HTTP_REFERER'], 0, PREG_SPLIT_NO_EMPTY );
               $entity = end( $entity );
               
               $this->menudepth = count( $entity ) - 1;
               
               $entity = TextEntity::findEntityInMenuSet( $entity, $menuitem->getContentIds(), i18n::languageCode() );
               if( $entity instanceof TextEntity && $entity->isLoaded() ) {
                  
                  // set the current language
                  i18n::setLanguage( $language );
                  
                  relocate( '%s/%s', rtrim( $menuitem->getTranslatedUrl( $language ), '/' ), $entity->getUrlName( $language ) );
                  
               } else {
                  
                  // set the current language
                  i18n::setLanguage( $language );
                  
                  relocate( $menuitem->getTranslatedUrl( $language ) );
                  
               }
               
            } elseif( $menuitem ) {
               
               // set the current language
               i18n::setLanguage( $language );
               
               relocate( $menuitem->getTranslatedUrl( $language ) );
               
            } else {
               
               // set the current language
               i18n::setLanguage( $language );
               
               relocate( $_SERVER['HTTP_REFERER'] );
               
            }
            
         } else {
            
            // set the current language
            i18n::setLanguage( $language );
            
            relocate( '/' );
            
         }
         
      }
      
   }
   
?>