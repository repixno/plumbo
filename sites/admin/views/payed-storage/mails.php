<?php

   /**
    * 
    * Display all notice emails
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'pages.admin' );
   model( 'subscription.mail' );
   
   
   class AdminPayedStorage extends AdminPage implements IView {
      
      protected $template = 'payed-storage.mails';
      
      public function Execute() {
         
         $mailssetup = array();
         
         $mails = new DBSubscriptionMail();
         foreach( $mails->collection( 'id', array(), 'id ASC' )->fetchAllAs('DBSubscriptionMail') as $mail ) {
			      $mailssetup []= $mail->asArray();
			}
			
			$this->mails = $mailssetup;
         
      }
      
   }


?>