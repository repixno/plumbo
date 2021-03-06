<?php
   import( 'pages.json' );
   import( 'mail.send');
   model( 'site.newsletterprospect' );
   
   class APINewsletterProspext extends JSONPage implements NoAuthRequired, IView {
      
        public function Execute() {
         
            $email = $email ? $email : $_POST['email'];
            
            $portal = Dispatcher::GetPortal();
            
            $check = DB::query( "SELECT id FROM site_newsletterprospect WHERE email = ? AND portal = ?" ,$email , $portal )->fetchSingle();
            
            try{
                if( $check ){
                    $this->email = $email;
                    $this->portal = $portal;
                    $this->result = true;
                    $this->message = "Registered";
                }
                else{
                    $card = new DBNewsletterprospect();
                    $card->email = $email;
                    $card->portal = $portal;
                    $card->registered = date( 'Y-m-d H:i:s' );
                    $card->save();
                 
                    $this->email = $email;
                    $this->portal = $portal;
                    $this->result = true;
                    $this->message = "OK";
                    
                    
                    if( $portal == 'TK-001' ){
                     
                        $this->email = "email sendt";
                        $fields =  array(
                           'email'     => $email ,
                           'kode'   => 'TAKKNY10',
                        );
                        MailSend::Simple( $email, "10% avslag på din neste ordre", 'campaign.index', $fields, 'phptal' , '"Takkekort.no" <post@takkekort.no>' );
                    }
                    
                }
            }catch( Exception $e ){
                $this->result = false;
                //$this->message = "Noe gikk galt";
                $this->message = $e->getMessage();
            }
            
            
         
      }
      
   }


?>