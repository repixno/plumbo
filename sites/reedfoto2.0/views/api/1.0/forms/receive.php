<?PHP
   
   import( 'pages.json' );
   import( 'website.form.submission' );
   
   class FormReceiveAPI extends JSONPage implements IView, NoAuthRequired {
      
      public function Execute() {
         
         $identifier = $_POST['identifier'];
         unset( $_POST['identifier'] );
         
         $email = $_POST['email'];
         unset( $_POST['email'] );
         
         if( isset( $_POST['template'] ) ) {
            
            $this->setTemplate( $_POST['template'] );
            unset( $_POST['template'] );
            
         }
         
         $data = array();
         foreach( $_POST as $field => $value ) {
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
            $this->result = false;
            $this->message = 'Already sent!';
         
         } else {
            
            $portal = Dispatcher::getPortal();
            
            $submission = new FormSubmission();
            $submission->identifier = $identifier;
            $submission->uid = Login::userid();
            $submission->portal = $portal ? $portal : 'EF-997';
            $submission->email = $email;
            $submission->data = serialize( $data );
            $submission->save();
            
            $this->result = true;
            $this->message = 'OK';
         
         }
         
         $this->identifier = $identifier;
         $this->email = $email;
         $this->data = $data;
         
      }
      
   }
   
?>