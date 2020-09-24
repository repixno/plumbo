<?php

   /**
    * Model for a subscription notice mail recipient
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'core.model' );

   class DBSubscriptionMailRecipient extends Model {
      
      static $table = 'subscription_mail_recipients';
      
      static $fields = array(
         'id' => array(
            'primary' => true,
            'type' => DB_TYPE_INTEGER,
            'size' => 11,
            'null' => false,
         ),
         'uid' => array(
            'type' => DB_TYPE_INTEGER,
            'size' => 11,
            'null' => false,
            'alias' => 'userid',
         ),
         'email' => array(
            'type' => DB_TYPE_STRING,
            'size' => 65536,
            'null' => false,
            'alias' => 'username',
         ),
         'name' => array(
            'type' => DB_TYPE_STRING,
            'size' => 65536,
            'null' => true,
            'default' => null,
         ),
         'num_pics' => array(
            'type' => DB_TYPE_INTEGER,
            'size' => 11,
            'alias' => 'numpics',
         ),
         'portal' => array(
            'type' => DB_TYPE_STRING,
            'size' => 255,
            'null' => false, 
         ),
         'type_of_notice' => array(
            'type' => DB_TYPE_STRING,
            'size' => 10,
            'null' => false,
            'alias' => 'typeofnotice',
         ),
         'created' => array(
            'type' => DB_TYPE_DATETIME,
            'null' => false,
         ),
         'sent' => array(
            'type' => DB_TYPE_DATETIME,
            'null' => true,
            'default' => null,
         ),
         'mail_item_id' => array(
            'type' => DB_TYPE_INTEGER,
            'size' => 11,
            'null' => false,
            'alias' => 'mailitemid',
         ),
         'hash' => array(
            'type' => DB_TYPE_STRING,
            'size' => 255,
            'null' => true,
            'default' => null,
         ),
         'opened' => array(
            'type' => DB_TYPE_DATETIME,
            'null' => true,
            'default' => null,
         ),
         'processed' => array(
            'type' => DB_TYPE_DATETIME,
            'null' => true,
            'default' => null,
         ),
         'enforced' => array(
            'type' => DB_TYPE_DATETIME,
            'null' => true,
            'default' => null,
         )
         
      );
      
      static function fromUserIDAndNoticeType( $userid, $noticetype = 'month' ) {
         
         $user = Model::fromFieldValue( array( 'uid' => $userid, 'type_of_notice' => $noticetype ), 'DBSubscriptionMailRecipient' );
         if( $user instanceof DBSubscriptionMailRecipient && $user->isLoaded() ) {
            
            return $user;
            
         } else {
            
            return false;
            
         }
         
      }
      
      public function __setup() {
         
         $result = parent::__setup();
         $this->created = date( 'Y-m-d H:i:s' );
         return $result;
         
      }
      
      
      public function getName() {
         
         return utf8_encode( $this->fieldGet( 'name' ) );
         
      }
      
      
      public function setName( $value ) {
         
         $this->fieldSet( 'name', utf8_decode( $value ) );
         
      }
      
      
   }


?>