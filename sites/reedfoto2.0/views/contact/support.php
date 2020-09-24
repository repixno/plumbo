<?PHP
   
   config( 'website.feedback' );
   import( 'website.feedback' );

   class ContactFeedback extends WebPage implements IView {
      
      protected $template = 'contact.feedback';
      
      public function Execute() {
         
         if( !empty($_POST['my_url']) ){
            //debug spam
            mail( 'tor.inge@eurofoto.no', 'spam support', serialize( LOGIN::data() ) . ' ---- ---- ' . serialize( $_POST ) );
            die('Feil med sending, du kan også sende epost direkte til post@eurofoto.no');
            
         }; 
         
         $fromname = $_POST['name'];        
         $frommail = $_POST['email'];
         
         if( filter_var( $frommail, FILTER_VALIDATE_EMAIL ) && strlen( $fromname ) > 0 ){
            
            // format the from field and the full headers to send
            $from = sprintf( '"%s" <%s>', addslashes( $fromname ), $frommail );
            $headers = "From: $from\nUser-Agent: Thunderbird 2.0.0.4 (Macintosh/20070604)\nMIME-Version: 1.0\nContent-type: text/plain; charset=UTF-8\nContent-Transfer-Encoding: 7bit";
            
            // fetch the recipient
            $recipient =   Dispatcher::getCustomAttr('portalemail');
               
            if( !$recipient  ){
               $recipient = 'post@eurofoto.no';  
            }
            
            // did we get any data to post?
            if( count( $_POST ) > 0 ) {
               
               // format the message
               $message = $_POST['message'] . "\n  Fra: " . $fromname . "\n  epost: " . $frommail;
               $subject = $_POST['reason'] . " " . $_POST['orderid'];
               
               if( isset( $_POST['isspam'] ) ) {
                  
                  $this->success = true;
                  
               } else {
               
                  // send the message
                  $this->success = mail( $recipient, $subject, $message, $headers );
                  
               }
               
               // choose the correct template
               $this->setTemplate( 'contact.complete' );
               
            }
         }
         else{
            $this->setTemplate( 'contact.feedback' );
            $this->success = true;
         }
         
      }
      
   }
   
?>