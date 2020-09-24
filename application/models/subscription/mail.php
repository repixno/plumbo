<?php

   /**
    * Model for subscription mails
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'core.model' );

   class DBSubscriptionMail extends Model {
      
      static $table = 'subscription_mails';
      
      static $fields = array(
         'id' => array(
            'primary' => true,
            'type' => DB_TYPE_INTEGER,
            'size' => 11,
         ),
         'subject' => array(
            'type' => DB_TYPE_STRING,
            'null' => false,
            'size' => 65536,
         ),
         'ingress' => array(
            'type' => DB_TEXT_STRING,
            'size' => 16777216,
         ),
         'body' => array(
            'type' => DB_TYPE_STRING,
            'size' => 16777216,
         ),
         'sender' => array(
            'type' => DB_TYPE_STRING,
            'null' => false,
            'size' => 65536,
            'alias' => 'from',
         ),
         'promotion_text' => array(
            'type' => DB_TYPE_STRING,
            'size' => 16777216,
            'alias' => 'promotion',
         ),
         'type_of_notice' => array(
            'type' => DB_TYPE_STRING,
            'size' => 10,
            'alias' => 'typeofnotice',
         ),
         'template' => array(
            'type' => DB_TYPE_STRING,
            'null' => true,
            'size' => 255,
            'default' => '',
         ),
         
      );
      
      
      public function getSubject() {
         
         return $this->fieldGet( 'subject' );
         
      }
      
      
      public function setSubject( $value = '' ) {
         
         $this->fieldSet( 'subject', $value );
         
      }
      
      
      public function getIngress() {
         
         return $this->fieldGet( 'ingress' );
         
      }
      
      
      public function setIngress( $value ) {
         
         $this->fieldSet( 'ingress', $value );
         
      }
      
      
      public function getBody() {
         
         return $this->fieldGet( 'body' );
         
      }
      
      
      public function setBody( $value ) {

         $this->fieldSet( 'body', $value );
         
      }
      
      
      public function getSender() {
         
         return $this->fieldGet( 'sender' );
         
      }
      
      
      public function setSender( $value ) {
         
         $this->fieldSet( 'sender', $value  );
         
      }
      
      
      public function getPromotion() {
         
         return $this->promotion_text;
         
      }
      
      
      public function setPromotion( $value ) {
         
         $this->promotion_text = $value;
         
      }
      
      
      
      public function getNoticeType() {
         
         return $this->type_of_notice;
         
      }
      
      
      public function setNoticeType( $value ) {
         
         $this->type_of_notice = $value;
         
      }
      
      
      public function asArray() {
         
         return array(
			   'id' => $this->id,
			   'subject' => $this->subject,
			   'sender' => $this->sender,
			   'promotion_text' => $this->promotion,
			   'type_of_notice' => $this->noticetype,
			    'template' => $this->template,
			);
         
      }
      
      
   }


?>