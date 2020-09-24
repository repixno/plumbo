<?PHP


   import( 'pages.json' );
   import( 'mail.send');
   import( 'website.order.merkelapptip' );

   class tipFriendMerkelapp extends JSONPage implements NoAuthRequired, IView {

      public function Execute() {
         
         $userid = Login::userid();
         $user = new User( $userid );
         $userarray = $user->asArray();
         $friends_email = $_POST['email'];
         
         $fields =  array(
            'userid'     => $userid
         );
         $subject = $userarray['fullname'] . " har sendt deg et tips!";
         if( !empty( $friends_email ) ){
            
            $tip = new UserMerkelappTip();
            $tip->userid = $userid;
            $tip->friends_email = $friends_email;
            $tip->date_tipped = date( 'Y-m-d H:i:s' );
            $tip->save();
            
            MailSend::Simple( $friends_email, $subject, 'order.emailtip', $fields, 'phptal', 'post@dinmerkelapp.no' );
         }
         $this->result = true;
         $this->message = 'OK';
         
         return true;
         

         }


         
   }



?>
