<?PHP
   
   import( 'pages.admin' );
   import( 'pages.json' );
   
   import( 'website.textentity' );
   import( 'website.article' );
   import( 'website.product' );
   
   class ContentAttachmentDeleteAPI extends JSONPage implements AdminRequired, IView {
      
      public function Execute( $entityid, $slotid ) {
         
         try {
            $entity = new TextEntity( $entityid );
            $entity = new $entity->type( $entityid );
            $entity->removeAttachment( $slotid );
            $entity->save();
            
            $this->result = true;
            $this->message = 'OK';
            
         } catch( Exception $e ) {
            
            $this->message = $e->getMessage();
            
         }
         
      }
      
   }


?>