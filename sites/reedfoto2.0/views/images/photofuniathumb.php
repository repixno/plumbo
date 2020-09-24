<?php

   class PhotofuniaThumbViewer extends WebPage implements IView {

      private $baseUrl = 'http://www.photofunia.com';

      public function Execute( $id ) {

         $effectsXML = CacheEngine::read( 'photofuniaeffects' );
         // Parse XML.
         $xml = simplexml_load_string( $effectsXML );
         $effects = $xml->xpath( "/groups/item/effects/item" );

         $path = '';
         foreach ( $effects as $effect ) {

            $xmlid = null;
            foreach ( $effect as $key=>$value ) {

               if ( $key == 'id' ) {

                  $xmlid = $value;

                  if ( $xmlid != $id ) {

                     continue( 2 );

                  }

               }

               $val = (string) $value;

               if ( $key == 'icon' ) {

                  $path = $val;

               }

            }

         }

         if ( !empty( $path ) ) {

            header( 'Content-Type: image/jpeg' );
            readfile( $this->baseUrl . $path );
            exit;

         } else {

            header( 'HTTP/1.0 404 Not Found' );
            exit;

         }

      }

   }

?>
