<?php

   import( 'website.album' );

   class MyaccountAlbumImageSort extends UserPage implements IValidatedView {


      public function Validate() {

         return array(
            'execute' => array(
               'fields' => array(
                  VALIDATE_STRING,
                  VALIDATE_STRING,
                  VALIDATE_STRING,
               )
            )
         );

      }


      public function Execute( $albumid, $sorting = 'created', $order = 'desc' ) {

         // Save session sort order.
         UserSessionArray::clearItems( 'imagesort' );
         UserSessionArray::setItem( 'imagesort', $sorting, 'sort' );
         UserSessionArray::setItem( 'imagesort', $order, 'order' );

         // Save sort order.
         $album = new Album( $albumid );
         $album->filesorttype = $sorting;
         $album->filesortorder = $order;
         $album->save();

         relocate( "/myaccount/album/$albumid" );

      }


   }


?>