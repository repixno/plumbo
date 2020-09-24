<?php

   /**
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    *
    */

   import( 'core.model' );

   class DBSmsServices extends Model {

      static $table = 'user_sms_services';

      static $fields = array(
         'id'  => array(
            'primary' => true,
            'type'   => DB_TYPE_INTEGER,
            'size'   => 11,
            'default'   => 0,
         ),
         'uid' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'cellnr' => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 20,
            'default'   => '',
         ),
         'validation_code' => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 5,
            'default'   => '',
         ),
         'validated' => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'order_sent_notice' => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'sharing_notice' => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'created' => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),

      );

      
      static function fromUserid( $userid ) {
         $find = new DBSmsServices();
         
         $id = (int) $find->collection( array( 'id' ), array( 'uid' => $userid ), 'id DESC', 1 )->fetchSingle();

         if ( $id > 0 ) {

            $object = new DBSmsServices( $id );
            if( !$object->isLoaded() ) return null;
            return $object;

         } else {

            return null;

         }
      }

      public function getUserId() {

         return $this->fieldGet( 'uid' );

      }

      public function setUserId( $userid ) {

         return $this->fieldSet( 'uid', $userid );

      }

      /**
       * Set code as validated
       *
       * @return unknown
       */
      public function validate() {

         return $this->validated = date( 'Y-m-d H:i:s' );

      }


      /**
       * Subscribe to order sent SMS
       */
      public function subscribeOrder() {

         $this->order_sent_notice = date( 'Y-m-d H:i:s' );

      }

      /**
       * Unsubscribe to order sent sms
       */
      public function unSubscribeOrder() {

         $this->order_sent_notice = null;

      }

      /**
       * Subscribe to sharing sms
       */
      public function subscribeSharing() {

         $this->sharing_notice = date( 'Y-m-d H:i:s' );

      }


      /**
       * Unsubscribe to sharing sms
       */
      public function unSubscribeSharing() {

         $this->sharing_notice = null;

      }

      public function save() {

         if ( !$this->id ) {

            $this->id = DB::query( "SELECT nextval('user_sms_services_id_seq')" )->fetchSingle();

         }

         return parent::save();

      }

   }


?>