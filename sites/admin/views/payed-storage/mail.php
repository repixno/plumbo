<?php

   /**
    * 
    * Edit notice emails
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'pages.admin' );
   model( 'subscription.mail' );

   class PayedStorageEditMail extends AdminPage implements IValidatedView {
      
      public function Validate() {
         
         return array(
            "execute" => array(
               "fields" => array(
                  VALIDATE_INTEGER,
               ),
            ),
            "save" => array(
               "post" => array(
                  "id" => VALIDATE_INTEGER,
                  "subject" => VALIDATE_STRING,
                  "sender" => VALIDATE_STRING,
                  "promotion_text" => VALIDATE_STRING,
                  "notice_type" => VALIDATE_INTEGER,
                  "template" => VALIDATE_INTEGER,
               )
            )
         );
         
      }
      
      public function Execute( $id = 0 ) {
         
         $this->setTemplate( 'payed-storage.edit-mail' );
         
         if( $id < 1 ) {
            relocate( '/payed-storage' );
         }
         
         $notice_types = array(
            0 => array(
               'id' => 0,
               'name' => 'week',
            ),
            1 => array(
               'id' => 1,
               'name' => 'month', 
            ),
         );
         
         $templates = array(
            0 => array(
               'id' => 0,
               'name' => 'finaldeletionwarningnew',
            ),
            1 => array(
               'id' => 1,
               'name' => 'quarantine',
            )
         );
         
         $mail = new DBSubscriptionMail( $id );
         if( $mail instanceof DBSubscriptionMail && $mail->isLoaded() ) {
            
            $this->mail = $mail->asArray();
            
            foreach( $notice_types as $key => $value ) {
               
               if( $value['name'] == $mail['type_of_notice'] ) {
                  $notice_types[$key]['selected'] = true;
               } else {
                  $notice_types[$key]['selected'] = false;
               }
               
            }
            
            foreach( $templates as $key => $value ) {
               
               if( $value['name'] == $mail['template'] ) {
                  $templates[$key]['selected'] = true;
               } else {
                  $templates[$key]['selected'] = false;
               }
               
            }
            
            $this->notice_types = $notice_types;
            $this->templates = $templates;
            
            
         } else {
            
            relocate( '/payed-storage' );
            
         }
         
         
      }
      
      
      public function save() {
         
         $notice_types = array(
            0 => array(
               'id' => 0,
               'name' => 'week',
            ),
            1 => array(
               'id' => 1,
               'name' => 'month', 
            ),
         );
         
         $templates = array(
            0 => array(
               'id' => 0,
               'name' => 'finaldeletionwarningnew',
            ),
            1 => array(
               'id' => 1,
               'name' => 'quarantine',
            )
         );
         
         $mail = new DBSubscriptionMail( $_POST['id'] );
         if( $mail instanceof DBSubscriptionMail && $mail->isLoaded() ) {
            
            $noticetype = $notice_types[$_POST['notice_type']];
            $noticetype = $noticetype['name'];
            
            $template = $templates[$_POST['template']];
            $template = $template['name'];
            
            $mail->subject = $_POST['subject'];
            $mail->sender = $_POST['sender'];
            $mail->promotion = $_POST['promotion_text'];
            $mail->noticetype = $noticetype;
            $mail->template = $template;
            
            $mail->save();
            
         }
         
         relocate( '/payed-storage/mail/'.$_POST['id'] );
         die();
         
      }
      
   }


?>