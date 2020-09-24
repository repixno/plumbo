<?php
   
   import( 'pages.admin' );
   import( 'website.feedback' );
   
   class FeedbackManager extends AdminPage implements IView {
      
      public function Execute( $feedbackid = null ) {
         
         if( isset( $feedbackid ) ) {
            
            return $this->view( $feedbackid );
            
         }
         
         $this->setTemplate( 'feedback.list' );
         
         $entries = array();
         $collection = new Feedback();
         foreach( $collection->collection( array( 'feedbackid' ), array( 'resolved' => null ), 'logged DESC' )->fetchAllAs('Feedback') as $feedback ) {
            $entries[] = $feedback->asArray();
         }
         
         $this->entries = $entries;
         
      }
      
      public function View( $feedbackid = 0 ) {
         
         $this->setTemplate( 'feedback.view' );
         
         $feedback = new Feedback( $feedbackid );
         $this->feedback = $feedback->asArray();
         
      }
      
   }
   
?>