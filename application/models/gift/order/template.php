<?php

   /**
    * Model for mal_order.
    * Contains info about template, pages
    * and what image they contain.
    * 
    * @author Andreas F�rnstrand <andreas.farnstrand@interweb.no>
    *
    */

   import( 'core.model' );

   
   class DBGiftOrderTemplate extends Model {
      
      static $table = 'mal_order';
      
      static $fields = array(
         'malorderid'   => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => false,
            'alias'   => 'id',
            'default' => 0,
         ),
         'artikkelnr'   => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => 'false',
            'alias'     => 'artnr',
            'alias'     => 'refid',
            'default'   => 0,
         ),
         'malid'  => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => 'false',
            'alias'     => 'templateid',
            'default'   => 0,
         ),
         'uid' => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => 'false',
            'alias'     => 'userid',
            'default'   => 0,
         ),
         'bid' => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => 'false',
            'alias'     => 'imageid',
            'default'   => 0,
         ),
         'page'   => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => 'false',
            'default'   => 0,
         ),
         'x'   => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => 'false',
            'default'   => 0,
         ),
         'y'   => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => 'false',
            'default'   => 0,
         ),
         'dx'  => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => 'false',
            'default'   => 0,
         ),
         'dy'  => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => 'false',
            'default'   => 0,
         ),
         'rotate' => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => false,
            'alias'     => 'rotation',
            'default'   => 0,
         ),
         'text' => array(
            'type'   => DB_TYPE_STRING,
            'size'    => 65535,
            'null'    => false,
            'default' => '',
         ),
         'user_mod'  => array(
            'type'      => DB_TYPE_BOOLEAN,
            'null'      => true,
            'default'   => true,
         ),
         'editor_x'      => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => true,   
            'default'   => null
         ),
         'editor_y'      => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => true,   
            'default'   => null
         ),
         'printsize_x'      => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => true,   
            'default'   => null
         ),
         'printsize_y'      => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => true,   
            'default'   => null
         ),
         'printtype' => array(
            'type'   => DB_TYPE_STRING,
            'size'    => 65535,
            'null'    => false,
            'default' => '',
         ),
         'malorderref' => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => 'false',
            'default'   => 0,
         ),
      );
      
      
      
      /**
       * Get a new id from sequence
       * and save template order
       *
       * @return unknown
       */
      public function save() {

         if ( !$this->malorderid ) {

            $this->malorderid = DB::query( "SELECT nextval('mal_order_seq')" )->fetchSingle();

         }

         return parent::save();

      }
      
     
      /**
       * Return template order id
       *
       * @return integer
       */
      public function getId() {
         
         return $this->malorderid;
         
      }
      
      
      /**
       * Set template order id
       *
       * @param integer $id
       */
      public function setId( $id ) {
         
         $this->malorderid = $id;
         
      }
      
      
      /**
       * Set text
       *
       * @param string $text
       */
      public function setText( $text ) {
         
         $this->fieldSet( 'text', $text );
         
      }
      
      
      /**
       * Get text
       *
       * @return string
       */
      public function getText() {
         
         return $this->fieldGet( 'text' );
         
      }
      
      
      /**
       * Get reference id
       *
       * @return integer
       */
      public function getRefId() {
         
         return $this->artikkelnr;
         
      }
      
      
      /**
       * Set reference id
       *
       * @param integer $refid
       */
      public function setRefId( $refid ) {
         
         $this->artikkelnr = $refid;
         
      }
      
      
      /**
       * Get templateid
       *
       * @return integer
       */
      public function getTemplateId() {
         
         return $this->malid;
         
      }
      
      
      /**
       * Set template id
       *
       * @param integer $templateId
       */
      public function setTemplateId( $templateId ) {
         
         $this->malid = $templateId;
         
      }
      
      
      /**
       * Get user id
       *
       * @return integer
       */
      public function getUserId() {
         
         return $this->uid;
         
      }
      
      
      /**
       * Set user id
       *
       * @param integer $userId
       */
      public function setUserId( $userId ) {
         
         $this->uid = $userId;
         
      }
      
      
      /**
       * Get the image id
       *
       * @return integer
       */
      public function getImageId() {
         
         return $this->bid;
         
      }
      
      
      /**
       * Set the image id being used
       *
       * @param integer $imageId
       */
      public function setImageId( $imageId ) {
         
         $this->bid = $imageId;
         
      }
      
      
      /**
       * Get the page used
       *
       * @return integer
       */
      public function getPageId() {
         
         return $this->page;
         
      }
      
      
      /**
       * Get the page id
       *
       * @param integer $pageId
       */
      public function setPageId( $pageId ) {
         
         $this->page = $pageId;
         
      }
      
      
   }


?>