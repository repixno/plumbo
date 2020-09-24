<?php

   import( 'website.album' );
   import( 'website.image' );
   
   import( 'pages.protected' );
   
   class AccountInbox extends ProtectedPage implements IValidatedView {
      
      protected $template = 'account.inbox.index';
      
      function Validate() {
         
      }
      
      function Execute() {
         
         $album = new Album( );
         
         $this->inbox = $album->getInbox();
         
         $this->images = $this->fetchImageList();
         
      }

	   private function fetchImageList() {

	      $imagelist = array();

         $images = new Image();
         
         foreach( $images->collection( array( 'bid' ), array( 'owner_uid' => Login::userid(), 'aid' => NULL, 'deleted_at' => NULL ) )->fetchAll() as $row ) {
            try {
               $image = new Image( array_shift( $row ) );
               $imagelist []= $image->asArray();
               
            } catch( Exception $e ) {
            }
         }

         return $imagelist;

	   }

   }
   
?>