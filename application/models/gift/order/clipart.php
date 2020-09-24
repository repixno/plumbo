<?php


   import( 'core.model' );
   
   
   class DBGiftOrderClipart extends Model {
      
      static $table = 'mal_clipart';
      
      static $fields = array(
         'malid'   => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => false,
            'alias'   => 'id',
            'default' => 0,
         ),
         'clipid'   => array(
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
         'clipnr'  => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => 'false',
            'alias'     => 'zindex',
            'default'   => 0,
         ),
         'page'   => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => 'false',
            'default'   => 0,
         ),
      );
      
      
      /**
       * Get the template order id
       *
       * @return integer
       */
      public function getId() {
         
         return $this->malid;
         
      }
      
      /**
       * Set the template order id
       *
       * @param integer $id
       */
      public function setId( $id ) {
         
         $this->malid = $id;
         
      }
      
      
      /**
       * Get the ZIndex of this clipart
       *
       * @return integer
       */
      public function getZIndex() {
         
         return $this->clipnr;
         
      }
      
      
      /**
       * Set the zindex of this clipart
       *
       * @param integer $zindex
       */
      public function setZIndex( $zindex ) {
         
         $this->clipnr = $zindex;
         
      }
      
      
      /**
       * Get the page id
       *
       * @return integer
       */
      public function getPageId() {
         
         return $this->page;
         
      }
      
      
      /**
       * Set the page id
       *
       * @param integer $pageId
       */
      public function setPageId( $pageId ) {
         
         $this->page = $pageId;
         
      }
      
   }
   

?>