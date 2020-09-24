<?php


   /**
    * 
    * DB model for historie_bilde
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'core.model' );

   class DBOrderImage extends Model {
      
      static $table = 'historie_bilde';
      
      static $fields = array(
         'id'           => array(
            'primary'      => true,
            'type'         => DB_TYPE_INTEGER,
            'size'         => 11,
            'null'         => false,
         ),
         'ordrenr'      => array(
            'type'         => DB_TYPE_INTEGER,
            'size'         => 11,
            'null'         => true,
            'default'      => null,
            'alias'        => 'orderid',
         ),
         'bid'          => array(
            'type'         => DB_TYPE_INTEGER,
            'size'         => 11,
            'null'         => true,
            'default'      => null,
            'alias'        => 'imageid',
         ),
         'antall'       => array(
            'type'         => DB_TYPE_INTEGER,
            'size'         => 11,
            'null'         => true,
            'default'      => null,
            'alias'        => 'quantity',
         ),
         'artikkelnr'   => array(
            'type'         => DB_TYPE_INTEGER,
            'size'         => 11,
            'null'         => true,
            'default'      => null,
            'alias'        => 'artnr',
         ),
         'lot'          => array(
            'type'         => DB_TYPE_INTEGER,
            'size'         => 11,
            'null'         => true,
            'default'      => null,
         ),
         'cart_version' => array(
            'type'         => DB_TYPE_INTEGER,
            'size'         => 11,
            'null'         => true,
            'default'      => null,
            'alias'        => 'cartversion',
         ),
         'filename'     => array(
            'type'         => DB_TYPE_STRING,
            'size'         => 65535,
            'null'         => true,
            'default'      => null,
         ),
         'text'         => array(
            'type'         => DB_TYPE_STRING,
            'size'         => 65535,
            'null'         => true,
            'default'      => null,
         ),
         'not_exist'    => array(
            'type'         => DB_TYPE_BOOLEAN,
            'size'         => 1,
            'null'         => true,
            'default'      => null,
         ),
         'tittel'       => array(
            'type'         => DB_TYPE_STRING,
            'size'         => 65535,
            'null'         => true,
            'default'      => null,
         ),
         'exif_date'    => array(
            'type'         => DB_TYPE_DATETIME,
            'null'         => true,
            'default'      => null,
            'alias'        => 'exifdate',
         ),
         'x'          => array(
            'type'         => DB_TYPE_INTEGER,
            'size'         => 11,
            'null'         => true,
            'default'      => null,
         ),
         'y'          => array(
            'type'         => DB_TYPE_INTEGER,
            'size'         => 11,
            'null'         => true,
            'default'      => null,
         ),
         'dx'          => array(
            'type'         => DB_TYPE_INTEGER,
            'size'         => 11,
            'null'         => true,
            'default'      => null,
         ),
         'dy'          => array(
            'type'         => DB_TYPE_INTEGER,
            'size'         => 11,
            'null'         => true,
            'default'      => null,
         ),
         'fitin'           => array(
            'type'         => DB_TYPE_INTEGER,
            'size'         => 11,
            'null'         => true,
            'default'      => null,
         ),
         'crop_width'      => array(
            'type'         => DB_TYPE_INTEGER,
            'size'         => 11,
            'null'         => true,
            'default'      => null,
         ),
         'crop_height'          => array(
            'type'         => DB_TYPE_INTEGER,
            'size'         => 11,
            'null'         => true,
            'default'      => null,
         ),
         'manualpath'      => array(
            'type'         => DB_TYPE_STRING,
            'size'         => 65535,
            'null'         => true,
            'defalt'       => null,
         
         ),
         
      );
      
      
      
      /**
       * Set the filename on disc for this image
       *
       * @param string $name
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setFilename( $name ) {
         
         $this->fieldSet( 'filename', $name );
         
      }
      
      
      
      /**
       * Get the disc filename for this image
       *
       * @return string
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function getFilename() {
         
         return $this->fieldGet( 'filename' );
         
      }
      
      
      
      /**
       * Set the image text.
       *
       * @param string $text
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setText( $text ) {
         
         $this->fieldSet( 'text', $text );
         
      }
      
      
      
      /**
       * get the image text
       *
       * @return string
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function getText() {
         
         return $this->fieldGet( 'text' );
         
      }
      
      
      
      /**
       * Set the image title
       *
       * @param string $title
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setTitle( $title ) {
         
         $this->tittel = $title;
         
      }
      
      
      
      /**
       * Get the image title
       *
       * @return string
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function getTitle() {
         
         return $this->tittel;
         
      }
      
   }


?>