<?PHP
   
   config( 'ukeplan.feedback' );
   import( 'website.feedback' );

   class ContactFeedback extends WebPage implements IView {
      
      protected $template = 'contact.feedback';
      
      public function Execute(){
         if( !empty($_POST['my_url']) ){
            //debug spam
            //mail( 'tor.inge@eurofoto.no', 'spam', serialize( LOGIN::data() ) . ' ---- ---- ' . serialize( $_POST ) );
            die('Feil med sending, du kan også sende epost direkte til post@ukeplan.no');
            
         }; 
         // define default email settings
         if( Dispatcher::getPortal() == 'UP-DK' ){
            $settings = Settings::GetSection( 'email', array( 'sender' => 'Ugeplan', 'from' => 'post@ugeplan.dk' ) );
         }else{
            $settings = Settings::GetSection( 'email', array( 'sender' => 'Ukeplan', 'from' => 'post@ukeplan.no' ) );
         }
         $fromname = $settings['sender'];
         $frommail = $settings['from'];
         
         // are we logged in?
         if( login::isLoggedIn() ) {
            
            $fromname = Login::data('fullname');
            $frommail = Login::data('username');
            
         }
         
         // do we have any overriding form data?
         if( $_POST['name'] )  $fromname = $_POST['name'];        
         if( $_POST['email'] ) $frommail = $_POST['email'];
         
         // format the from field and the full headers to send
         $from = sprintf( '"%s" <%s>', addslashes( $fromname ), $frommail );
         $headers = "From: $from\nUser-Agent: Thunderbird 2.0.0.4 (Macintosh/20070604)\nMIME-Version: 1.0\nContent-type: text/plain; charset=UTF-8\nContent-Transfer-Encoding: 7bit";
         
         // fetch the recipient
         $recipient = Settings::get( 'feedback', 'recipient', 'root' );
         
         if( strpos( $_POST['usercomment'], '<a href="' ) !== false ) {
            $_POST['isspam'] = true;
         }
         
         if( strpos( $_POST['usercomment'], '[url=' ) !== false ) {
            $_POST['isspam'] = true;
         }
         
         // did we get any data to post?
         if( count( $_POST ) > 0 ) {
            
            // save the feedback data
            $feedback = new Feedback();
            $feedback->userid = Login::userid();
            $feedback->fields = serialize( $_POST );
            $feedback->save();
            
            // format the message
            $message = print_r( array( 'user' => Login::data(), 'request' => array( 'useragent' => $_SERVER['HTTP_USER_AGENT'], 'referer' => $_SERVER['HTTP_REFERER'], 'remoteip' => $_SERVER['REMOTE_ADDR'] ), 'data' => $_POST ), true );
            $subject = isset( $_POST['type'] ) ? $_POST['type'] : 'Feedback';
            
            
            $logindata = Login::data();
            
            $message = $_POST['']
            
            
            $subject .=  " " . $_POST['subject'];
            
            if( isset( $_POST['isspam'] ) ) {
               
               $this->success = true;
               
            } else {
            
               // send the message
               $this->success = mail( $recipient, $subject, $message, $headers );
               
            }
            
            if( $_POST['type']  == "Designservice" ){
               $this->setTemplate( 'contact.designservice' );
            }else{
               // choose the correct template
               $this->setTemplate( 'contact.complete' );
            }
            
            
            
         }
         
      }
      
   }
   
?>