<?php

   /**
    * Model for texts on a template order
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    */

   import( 'core.model' );
   
   class DBGiftOrderText extends Model {
      
      static $table = 'mal_text';
      
      static $fields = array(
         'malid'   => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => false,
            'alias'   => 'id',
            'default' => 0,
         ),
         'textid'   => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => 'false',
            'alias'     => 'zindex',
            'default'   => 0,
         ),
         'text' => array(
            'type'   => DB_TYPE_STRING,
            'size'    => 65535,
            'null'    => false,
            'default' => '',
         ),
         'font' => array(
            'type'   => DB_TYPE_STRING,
            'size'    => 65535,
            'null'    => false,
            'default' => '',
         ),
         'color' => array(
            'type'   => DB_TYPE_STRING,
            'size'    => 65535,
            'null'    => false,
            'default' => '',
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
         'page'   => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => 'false',
            'default'   => 0,
         ),
         'gravity' => array(
            'type'   => DB_TYPE_STRING,
            'size'    => 65535,
            'null'    => false,
            'default' => '',
         ),
         'shadow' => array(
            'type'   => DB_TYPE_STRING,
            'size'    => 65535,
            'null'    => false,
            'default' => '',
         ),
         'rotate'   => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => 'true',
            'default'   => 0,
         ),
      );
      
      
      /**
       * Get template order id
       *
       * @return integer
       */
      public function getId() {
         
         return $this->malid;
         
      }
      
      
      /**
       * Set template order id
       *
       * @param integer $id
       */
      public function setId( $id ) {
         
         $this->malid = $id;
         
      }
      
      
      /**
       * Get the order of text
       *
       * @return integer
       */
      public function getZIndex() {
         
         return $this->textid;
         
      }
      
      
      /**
       * Set the order of the text
       *
       * @param integer $zindex
       */
      public function setZIndex( $zindex ) {
         
         $this->textid = $zindex;
         
      }
      
      
      /**
       * Get the text string
       *
       * @return string
       */
      public function getText() {
         
         return $this->fieldGet( 'text' );
         
      }
      
      
      /**
       * Set the actual text string
       *
       * @param string $text
       */
      public function setText( $text ) {
         
         $this->fieldSet( 'text', $text );
         
      }
      
      
      /**
       * Get the font used
       *
       * @return string
       */
      public function getFont() {
         
         return $this->fieldGet( 'font' );
         
      }
      
      
      /**
       * Set the font used
       *
       * @param string $font
       */
      public function setFont( $font ) {
         
         $this->fieldSet( 'font', $font );
         
      }
      
      
      /**
       * Get the color used
       *
       * @return string
       */
      public function getColor() {
         
         return $this->fieldGet( 'color' );
         
      }
      
      
      /**
       * Set the color used
       *
       * @param string $color
       */
      public function setColor( $color ) {
         
         $this->fieldSet( 'color', $color );
         
      }
      
      
      /**
       * Get the page id for this text
       * 
       * @return integer
       */
      public function getPageId() {
         
         return $this->page;
         
      }
      
      
      /**
       * Set the page id for this text
       *
       * @param integer $pageId
       */
      public function setPageId( $pageId ) {
         
         $this->page = $pageId;
         
      }
      
      
      /**
       * Get the gravity for this text
       *
       * @return string
       */
      public function getGravity() {
         
         return $this->fieldGet( 'gravity' );
         
      }
      
      
      /**
       * Set the gravity for this text
       *
       * @param string $gravity
       */
      public function setGravity( $gravity ) {
         
         $this->fieldSet( 'gravity',  $gravity );
         
      }
      
   }


?>