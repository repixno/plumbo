<?php
   /**
    * @author Svein Arild Bergset <sab@interweb.no>
    *
    *
    */


   import( 'core.model' );

   class DBUserDiscountCampaign extends Model {

      static $table = 'campaigns';
      static $basename = 'discount';

      static $fields = array(

         'id'           => array(
            'primary'   => true,
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => 0,
         ),
         'name'         => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 255,
            'default'   => '',
         ),
         /* 'description'  => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 65536,
            'default'   => '',
         ), */
         'type'         => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 255,
            'default'   => '',
         ),
         'start'         => array(
            'type'      => DB_TYPE_DATETIME,
            'null'      => true,
            'default'   => null
         ),
         'stop'         => array(
            'type'      => DB_TYPE_DATETIME,
            'null'      => true,
            'default'   => null
         ),
         'active'      => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 2,
            'default'   => 0
         ),
         /* 'used_times'      => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 6,
            'default'   => 0
         ), */
         'code'         => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 255,
            'default'   => '',
         ),
         'created'         => array(
            'type'      => DB_TYPE_DATETIME,
            'null'      => true,
            'default'   => null
         ),
         /* 'portal'      => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => 0
         ), */
         'one_time'      => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 2,
            'default'   => 0
         )

      );

      public function getTitle() {

         return $this->fieldGet( 'name' );

      }

      public function setTitle( $title ) {

         return $this->fieldSet( 'name', $title );

      }

      
      public function asArray() {
         
         return array(
            'id' => $this->id,
            'type'  => $this->type,
            'start' =>        $this->start,
            'stop'    =>     $this->stop,
            'active'    =>  $this->active,
            'code'       =>  $this->code,
            'created'     =>    $this->created,
            'one_time'    =>  $this->one_time,
         );
         
      }
      public function getDescription() {

         return $this->fieldGet( 'description' );

      }

      public function setDescription( $desc ) {

         return $this->fieldSet( 'description', $desc );

      } 

   }

?>