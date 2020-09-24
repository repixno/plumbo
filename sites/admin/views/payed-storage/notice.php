<?php

   /**
    * Edit/setup user deletion/quarantine email notices.
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */


   import( 'pages.admin' );
   
   import( 'math.signer' );
   import( 'mail.send' );
   
   import( 'validate.email' );
   import( 'website.user' );
   
   model( 'subscription.batch' );
   model( 'subscription.mail' );

   import( 'templating.phptal' );
   
   config( 'email.subscription.notice' );
   
   class AdminPayedStorageNotice extends AdminPage implements IView {
      
      protected $template = 'payed-storage.edit-notice';
      
      public function Execute( $id = 0 ) {
         
         try {
            
            $batch = new DBSubscriptionMailBatch( $id );
            $mailid = $batch->mail_id;

            $types_of_notice = array(
               0  => array(
                  'id' => 'QUARANTINE',
                  'name' => 'QUARANTINE',
               ),
               1  => array(
                  'id' => 'DELETION',
                  'name' => 'DELETION'
               ),
            );
            
            $periods = array(
               0 => array(
                  'id' => 'WEEK',
                  'name' => 'WEEK',
               ),
               1 => array(
                  'id' => 'MONTH',
                  'name' => 'MONTH',
               ),
            );
            
            $mailitems = array();
            $dbmails = new DBSubscriptionMail();
            foreach( $dbmails->collection( 'id', array(), 'id ASC'  )->fetchAllAs('DBSubscriptionMail') as $mailitem ) {
               
               $item = array(
                  'id' => $mailitem->id,
                  'template' => $mailitem->template,
                  'subject' => $mailitem->subject,
               );
               
               if( $mailitem->id == $batch->mail_id ) {
                  $item['selected'] = true;
               } else {
                  $item['selected'] = false;
               }
               
               $mailitems []= $item;
               
			   }
			   
			   
			   // Setup notice types
			   foreach( $types_of_notice as $key => $values ) {
			      if( $values['name'] == $batch->type_of_notice ) {
			         $types_of_notice[$key]['selected'] = true;
			      } else {
			         $types_of_notice[$key]['selected'] = false;
			      }
			   }
			   
			   
			   // Setup periods
			   foreach( $periods as $key => $values ) {
			      if( $values['name'] == $batch->period ) {
			         $periods[$key]['selected'] = true;
			      } else {
			         $periods[$key]['selected'] = false;
			      }
			   }
			   
			   if( strlen( $batch->recipients_processing ) > 0 ) {
			      $this->error = __( 'Warning! This notice has already been processed. You cannot edit it' );
			      $this->editable = false;
			   } else {
			      $this->editable = true;
			   }
			   
			   $this->mailitems = $mailitems;
			   $this->batch = $batch->asArray();
			   $this->types_of_notice = $types_of_notice;
			   $this->periods = $periods;
            
         } catch ( Exception $e ) { /* Do something here */ }
         
      }
      
      
      
      /**
       * Save a edited or new notice to db
       *
       */
      public function save() {
         
         $post = $_POST;
         
         $id = (int) $_POST['id'];
         $mailitemid = (int) $_POST['mailid'];
         $senddate = $_POST['send_date'];
         $expiredate = $_POST['expire_date'];
         $typeofnotice = $_POST['type_of_notice'];
         $period = $_POST['period'];
         
         // Save edited
         if( $id > 0 ) {
            
            try {
               
               $batch = new DBSubscriptionMailBatch( $id );
               
               if( isset( $batch->recipients_processing ) || isset( $batch->recipients_processed ) ) {
                  
                  if( isset( $batch->started_sending ) || isset( $batch->finished_sending ) ) {
                     $this->error = __( 'This is notice is already being sent or is already sent.' );
                  } else {
                     
                     $this->error = __( 'This notice is already being processed or is already processed.' );
                  }
                  
               } else {
               
                  $batch->mail_id = $mailitemid;
                  $batch->send_date = $senddate;
                  $batch->expire_date = $expiredate;
                  $batch->type_of_notice = $typeofnotice;
                  $batch->period = $period;
                  $batch->save();
                  
               }
               
            } catch( Exception $e ) {
               
               relocate( '/payed-storage/notices' );
               die();
            }
            
            relocate( '/payed-storage/notice/'.$id );
            
            
         } else { // New one
            
            try {
               
               $batch = new DBSubscriptionMailBatch();
               $batch->mail_id = $mailitemid;
               $batch->num_recipients = 0;
               $batch->send_date = $senddate;
               $batch->expire_date = $expiredate;
               $batch->type_of_notice = $typeofnotice;
               $batch->period = $period;
               $batch->save();
               
               relocate( '/payed-storage/notices' );
               die();
               
            } catch( Exception $e ) {
               
               util::Debug( 'something failed' );
               die();
               relocate( '/payed-storage/notices' );
               die();
               
            }
            
         }
         
      }
      
      
      /**
       * Delete a notice from db
       *
       * @param integer $id
       */
      public function delete( $id = 0 ) {
         
         $this->setTemplate( '' );
         
         if( $id > 0 ) {
            
            $batch = new DBSubscriptionMailBatch( $id );
            if( strlen( $batch->recipients_processing ) == 0 || ( strlen( $batch->recipients_processing ) > 0 && strlen( $batch->recipients_processed ) > 0 && strlen( $batch->started_sending ) == 0 ) ) {
               $batch->delete();
               DB::query( "DELETE FROM subscription_mail_recipients WHERE mail_item_id = ?", $id );
            }
            
         }
         
         relocate( '/payed-storage/notices' );
         die();
         
      }
      
      
      /**
       * Test a notice
       *
       * @param ineger $id
       */
      public function test( $id = 0 ) {

         $this->setTemplate( 'payed-storage.notice-test' );
         
         $portals = array(
            0  => array(
               'name' => 'Eurofoto',
               'code'   => 'EF-997',
            ),
            1  => array(
               'name'   => 'VGNett',
               'code' => 'VG-997',
            ),
            2  => array(
               'name' => 'SOLNO',
               'code' => 'SN-997',
            ),
            3  => array(
               'name' => 'Aftenposten',
               'code'   => 'AM-997',
            ),
			4  => array(
               'name' => 'Sparfoto',
               'code'   => 'SK-001',
            )
         );
         
         if( $id > 0 ) {
            
            try {
               
               $batch = new DBSubscriptionMailBatch( $id );
               $this->batch = $batch->asArray();
               $this->portals = $portals;
               $this->sendresult = Session::pipe( 'sendresult' );
               
            } catch( Exception $e ) {
               
            }
            
         }
         
         
      }
      
      
      /**
       * Send a test notice email
       * to given usernames
       *
       * @param integer $id
       */
      public function send( $id = 0 ) {
         
         $this->setTemplate( '' );
         $id = (int) $_POST['id'];
         $usernames = trim( $_POST['email'] );
         $usernames = explode( ';', $usernames );
         $portaloverride = $_POST['portal'];
         $usernamesstring = '';
         
         if( count( $usernames ) == 0 ) {
            $usernames = array();
         }
         
         if( $id > 0 ) {

            try {

               foreach( $usernames as $username ) {
               
                  $username = trim( $username );
                  if( !ValidateEmail::validate( $username ) ) continue;
                  
                  $batch = new DBSubscriptionMailBatch( $id );
                  $userdata = User::fromUsername( $username );
                  $user = new User( $userdata->uid );
                  
                  $mail = new DBSubscriptionMail( $batch->mail_id );
                  
                  $subject = $mail->subject;
                  $ingress = $mail->ingress;
                  $body = $mail->body;
                  $from = $mail->sender;
                  $promotiontext = $mail->promotion;
                  $template = 'subscription.'.$mail->template;
                  $senddate = $this->formatDate( date( 'Y-m-d' ) );
                  $portaldata = Settings::GetSection( 'notice' );
                  $portalkeys = array_keys( $portaldata['baseurl'] );
                  $utmdate = date( 'Y-m-d' );
                  $expiredate = $batch->expire_date;
                  $expiredate = $this->formatDate( $expiredate );
                  $portal = $portaloverride;
   
                  if( !in_array( $portal, $portalkeys ) ) {
                     $portal = 'EF-997';
                  }
   
                  $baseurl = $portaldata['baseurl'][$portal];
                  $color = $portaldata['color'][$portal];
                  $topimage = $portaldata['topimage'][$portal];
   
                  $fields = array(
                     'email' => $username,
                     'name' => $user->getFullname(),
                     'numpics' => 100,
                     'portal' => $portaloverride,
                     'hash' => md5( $username ),
                     'expiredate' => $expiredate,
                     'subject' => $subject,
                     'ingress' => $ingress,
                     'body' => $body,
                     'promotiontext' => $promotiontext,
                     'from' => $from,
                     'baseurl' => $baseurl,
                     'senddate' => $senddate,
                     'topimage' => $topimage,
                     'color' => $color,
                     'utmdate' => $utmdate,
                  );
   
                  $mailsubject = $this->replaceFields( $subject, $fields );
   
                  $fields['ingress'] = $this->replaceFields( $fields['ingress'], $fields );
                  $fields['body'] = $this->replaceFields( $fields['body'], $fields );
                  $fields['promotiontext'] = $this->replaceFields( $fields['promotiontext'], $fields );
   
                  Dispatcher::setTemplatePathSite( getRootPath(), 'website' );
                  $_SERVER['HTTP_HOST'] = 'eurofoto.no';

				  
                  MailSend::Simple( $username, $mailsubject, $template, $fields, 'phptal', $from );
                  //$this->mailresult = 'Mail sent to '.$username;
                  
                  $usernamesstring .= ' '.$username;
               
               }
               
               Session::pipe( 'sendresult', 'Emails sent to:'.$usernamesstring );
               relocate( '/payed-storage/notice/test/'.$id );
               die();

            } catch( Exception $e ) {

            }

         }
         
      }
      
      
      public function replaceFields( $string, $fields ) {
         
         $search = array();
         $replace = array_values( $fields );
         
         foreach( $fields as $key => $value ) {
            $search[] = '%'.$key.'%';
         }
         
         return str_replace( $search, $replace, $string );
         
      }
      
      
      private function formatDate( $date ) {
      
         return phptal_support_formatdate( $date );
         
      }
      
   }


?>