<?PHP
   
   import( 'templating.controller' );
   
   class MailSend {
      
      private $engine = 'phptal';
      private $recipient = '';
      private $subject = '';
      private $template = '';
      private $from = null;
      private $fields = array();
      
      static function Simple( $recipient, $subject, $template, $fields, $engine = null, $from = null ) {
         
         $mailsend = new MailSend();
         return $mailsend->Send( $recipient, $subject, $template, $fields, $engine, $from );
         
      }
      
      public function Send( $recipient = null, $subject = null, $template = null, $fields = null, $engine = null, $from = null ) {
         
         // set any overridden parameters.
         if( isset( $recipient ) ) $this->setRecipient( $recipient );
         if( isset( $subject ) ) $this->setSubject( $subject );
         if( isset( $template ) ) $this->setTemplate( $template );
         if( isset( $fields ) ) $this->setFields( $fields );
         if( isset( $engine ) ) $this->setEngine( $engine );
         if( isset( $from ) ) $this->setFrom( $from );
         
         // turn off error-reporting
         $oldreporting = error_reporting( E_ERROR | E_COMPILE_ERROR );
         
         // create a TemplateController instance and draw it
         $controller = new TemplatingController( $this->engine );
         
         // fetch the fields
         $fields = $this->fields;
         
         // add a few custom fields
         $fields['subject'] = $this->subject;
         $fields['recipient'] = $this->recipient;
         
         // expand the template name(s)
         $texttemplate = $controller->findTemplate( Dispatcher::$tmplPath, 'emails.'.$this->template, 'txt' );
         $htmltemplate = $controller->findTemplate( Dispatcher::$tmplPath, 'emails.'.$this->template, 'html' );
         
         // include the mail library
         library( 'pear.mail.mail' );
         
         // include mime support
         library( 'pear.mail.mime' );
         
         // create a new MIME encoder
         $mime = new Mail_mime( PHP_EOL );
         
         // set some build-params
         $mime->_build_params['html_charset'] = 'UTF-8';
         $mime->_build_params['text_charset'] = 'UTF-8';
         $mime->_build_params['head_charset'] = 'UTF-8';
         
         // render any templates and append them
         if( file_exists( $texttemplate ) ) $mime->setTXTBody( $controller->drawTemplate( $texttemplate, $fields ) );
         if( file_exists( $htmltemplate ) ) $mime->setHTMLBody( $controller->drawTemplate( $htmltemplate, $fields ) );
         
         // make sure we have a valid from
         if( !isset( $this->from ) ) {
            
            config( 'website.email' );
            
            $portal = Dispatcher::getPortal();
            $email = Settings::Get( 'email', 'from', array() );
            
            if( isset( $email[$portal] ) ) {
               
               $this->setFrom( $email[$portal] );
               
            } elseif( isset( $email['default'] ) ) {
               
               $this->setFrom( $email['default'] );
               
            } else {
               
               $this->setFrom( '"Eurofoto AS" <post@eurofoto.no>' );
               
            }
            
         }
         
         // prepare the mail headers
         $hdrs = array(
            'From'    => $this->from,
            'Subject' => $this->subject,
         );
         
         $body = $mime->get();
         $hdrs = $mime->headers( $hdrs );
         $mail = Mail::factory('mail');
         
         // send the email, returning the result to the caller
         $result = $mail->send( $this->recipient, $hdrs, $body );
         
         // turn error-reporting back on
         error_reporting( $oldreporting );
         
         // return result
         return $result;
         
      }
      
      public function setFrom( $from ) {
         
         $this->from = $from;
         
      }
      
      public function setRecipient( $recipient ) {
         
         $this->recipient = $recipient;
         
      }
      
      public function setSubject( $subject ) {
         
         $this->subject = $subject;
         
      }
      
      public function setTemplate( $template ) {
         
         $this->template = $template;
         
      }
      
      public function setFields( $fields ) {
         
         $this->fields = is_array( $fields ) ? $fields : array();
         
      }
      
      public function setEngine( $engine ) {
         
         $this->engine = $engine;
         
      }
      
   }
   
?>