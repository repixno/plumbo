<?php

   /**
    * Model for a batch job of sending notices
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'core.model' );

   class DBSubscriptionMailBatch extends Model {
      
      static $table = 'subscription_mail_items';
      
      static $fields = array(
         'id' => array(
            'primary' => true,
            'type' => DB_TYPE_INTEGER,
            'size' => 11,
            'null' => false,
         ),
         'mail_id' => array(
            'type' => DB_TYPE_INTEGER,
            'size' => 11,
            'null' => false,
            'alias' => 'mailid',
         ),
         'num_recipients' => array(
            'type' => DB_TYPE_INTEGER,
            'size' => 11,
            'alias' => 'numrecipients',
         ),
         'send_date' => array(
            'type' => DB_TYPE_DATETIME,
            'null' => true,
            'default' => null,
            'alias' => 'senddate',
         ),
         'recipients_processed' => array(
            'type' => DB_TYPE_DATETIME,
            'null' => true,
            'default' => null,
            'alias' => 'processed',
         ),
         'started_sending' => array(
            'type' => DB_TYPE_DATETIME,
            'null' => true,
            'default' => null,
            'alias' => 'started',
         ),
         'finished_sending' => array(
            'type' => DB_TYPE_DATETIME,
            'null' => true,
            'default' => null,
            'alias' => 'ended',
         ),
         'recipients_processing' => array(
            'type' => DB_TYPE_DATETIME,
            'null' => true,
            'default' => null,
            'alias' => 'processing',
         ),
         'expire_date' => array(
            'type' => DB_TYPE_DATETIME,
            'null' => true,
            'default' => null,
            'alias' => 'expire',
         ),
         'type_of_notice' => array(
            'type' => DB_TYPE_STRING,
            'null' => false,
            'size' => 255,
         ),
         'period' => array(
            'type' => DB_TYPE_STRING,
            'null' => false,
            'size' => 255,
         ),
         'enforce_started' => array(
            'type' => DB_TYPE_DATETIME,
            'null' => true,
            'default' => null,
         ),
         'enforce_finished' => array(
            'type' => DB_TYPE_DATETIME,
            'null' => true,
            'default' => null,
         ),
         'num_enforced_users' => array(
            'type' => DB_TYPE_INTEGER,
            'size' => 11,
            'null' => true,
            'default' => null,
         ),
         'limited_started' => array(
            'type' => DB_TYPE_DATETIME,
            'null' => true,
            'default' => null,
         ),
      );
      
      
      
      
      public function asArray() {
         
         return array(
            'id' => $this->id,
            'mail_id' => $this->mail_id,
            'num_recipients' => $this->num_recipients,
            'send_date' => $this->send_date,
            'recipients_processed' => $this->recipients_processed,
            'started_sending' => $this->started_sending,
            'finished_sending' => $this->finished_sending,
            'recipients_processing' => $this->recipients_processing,
            'expire_date' => $this->expire_date,
            'type_of_notice' => $this->type_of_notice,
            'period' => $this->period,
            'enforce_started' => $this->enforce_started,
            'enforce_finished' => $this->enforce_finished,
            'num_enforced_users' => $this->num_enforced_users
         );
         
      }
      
   }


?>