<?php


   import( 'core.model' );
   model( 'album.index' );
   
   class DBGalleryAlbum extends DBAlbum {
      
      static $table = 'public_album';
      
      static $fields = array(
         'aid' => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => false,
            'default' => 0,
            'alias'   => 'albumid',
         ),
         'uid' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => false,
            'default' => 0,
            'alias'   => 'userid',
         ),
         'views' => array(
            'type'   => DB_TYPE_INTEGER,
            'size'   => 11,
            'null'   => true,
            'default' => 0,
         ),
         'key' => array(
            'type'   => DB_TYPE_STRING,
            'size'   => 65535,
            'default'   => 'eurofoto',
         ),
         /*
         'title'  => array(
            'type'   => DB_TYPE_STRING,
            'size'   => 65535,
            'default'   => '',
            'alias'  => 'albumtitle',
         ),
         'description'  => array(
            'type'   => DB_TYPE_STRING,
            'size'   => 65535,
            'default'   => '',
         ),
         */
         'owner_name'   => array(
            'type'   => DB_TYPE_STRING,
            'size'   => 65535,
            'default'   => '',
         ),
         'category'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
         ),
         'tidspunkt' => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
            'alias'   => 'sharingtime'
         ),
         'country'   => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
         ),
      );
      
      /*
      public function getTitle() {
         
         return utf8_encode( $this->albumtitle );
         
      }
      
      public function setTitle( $title = '' ) {
         
         $this->fieldSet( 'albumtitle', utf8_decode( $title ) );
         
      }
      
      public function getDescription() {
         
         return utf8_encode( $this->description );
         
      }
      
      public function setDescription( $description = '' ) {
         
         $this->fieldSet( 'description', utf8_decode( $description ) );
         
      }
      */
      
      public function getOwnerName() {
         
         return $this->owner_name;
         
      }
      
      public function setOwnerName( $ownername ) {
         
         $this->fieldSet( 'owner_name', $ownername );
         
      }
      
   }



?>