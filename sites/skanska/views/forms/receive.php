<?PHP
   
   import( 'website.form.submission' );
   
   class FormsReceive extends WebPage implements IView {
      
      protected $template = 'forms.default';
      
      public function Execute() {
         
         $identifier = $_REQUEST['identifier'];
         unset( $_REQUEST['identifier'] );
         
         $email = $_REQUEST['email'];
         unset( $_REQUEST['email'] );
         
         if( isset( $_REQUEST['template'] ) ) {
            
            $this->setTemplate( $_REQUEST['template'] );
            unset( $_REQUEST['template'] );
            
         }
         
         if( isset( $_REQUEST['redirect'] ) ) {
            
            $redirect = $_REQUEST['redirect'];
            unset( $_REQUEST['redirect'] );
            
         } else {
            
            $redirect = false;
            
         }
         
         $data = array();
         foreach( $_REQUEST as $field => $value ) {
            $data[$field] = $value;
         }
         
         if( !isset( $_POST['allowmultisub'] ) )
         $prevsubmission = Model::fromFieldValue( 
            array( 
               'identifier' => $identifier, 
               'email' => $email,
            ), 'FormSubmission'
         );
         
         if( $prevsubmission instanceof FormSubmission && $prevsubmission->isLoaded() ) {
            
            $this->alreadysent = true;
            
         } else {
            
            $portal = Dispatcher::getPortal();
            
            $submission = new FormSubmission();
            $submission->identifier = $identifier;
            $submission->uid = Login::userid();
            $submission->portal = $portal ? $portal : 'EF-997';
            $submission->email = $email;
            $submission->data = serialize( $data );
            $submission->save();
            
         }
         
         $this->identifier = $identifier;
         $this->email = $email;
         $this->data = $data;
         
         if( $redirect ) {
            
            relocate( $redirect );
            
         }
         
      }
      
   }
   
?>