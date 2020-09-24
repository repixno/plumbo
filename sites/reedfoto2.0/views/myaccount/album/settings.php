<?php

   /**
    * Get album settings
    *
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    *
    */

   import( 'website.album' );

   class MyAccountAlbumSettings extends UserPage implements IView {

      protected $template = '';

      public function Execute( $id = 0 ) {

         $album = new Album( $id );

         if( !$album->isLoaded() || !$album instanceof Album  ) return false;

         $this->settings = $album->asArray();


      }


      /**
       * Save album settings
       *
       */
      public function save( $id = 0 ) {

         $year = isset( $_REQUEST["year"] ) ? $_REQUEST["year"] : null;
         $purchaseAccess = isset( $_REQUEST["purchaseaccess"] ) ? $_REQUEST["purchaseaccess"] : null;
         $downloadAccess = isset( $_REQUEST["downloadaccess"] ) ? $_REQUEST["downloadaccess"] : null;

         if( $id > 0 ) {

            $album = new Album( $id );

            if( $album->isLoaded() && $album instanceof Album ) {

               if( Login::userid() == $album->uid ) {

                  if( !empty( $year ) )            $album->setYear( $year );
                  if( isset( $purchaseAccess ) )  $album->setPurchaseAccess( $purchaseAccess );
                  if( isset( $downloadAccess ) )  $album->setDownloadAccess( $downloadAccess );

                  $album->save();

               }

            }

         }

         relocate( '/myaccount/album/' . $id );

      }


   }

?>