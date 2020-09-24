<?PHP
   
   import( 'pages.admin' );
   import( 'website.textentity' );
   import( 'website.article' );
   import( 'website.product' );

   class ContentAttachmentUploader extends AdminPage implements IView {
      
      protected $template = 'content.attachments.upload';
      
      public function Execute( $entityid ) {
         
         $this->entityid = $entityid;
         
         if( isset( $_POST['slotid' ] ) ) {
            
            try {
               $entity = new TextEntity( $entityid );
               $entity = new $entity->type( $entityid );
               $entity->addAttachment( $_POST['slotid'], $_FILES['attachment']['name'], $_FILES['attachment']['tmp_name'] );
               $entity->save();
               
               $this->uploaded = array(
                  'filename' => Util::urlize( $_FILES['attachment']['name'] ),
                  'slotid' => $_POST['slotid'],
               );
            } catch( Exception $e ) {
               
            }
            
         }
         
      }
      
   }

?>