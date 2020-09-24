<?PHP

   import( 'core.model' );
   
   define( 'OBJECT_LICENSETYPE_NOLICENSE',    0x00 );
   define( 'OBJECT_LICENSETYPE_PHOTOGRAPHER', 0x01 );
   define( 'OBJECT_LICENSETYPE_CUSTOMER',     0x02 );
   
   class DBObject extends Model implements ModelCaching {

      static $table = 'bildeinfo';

      static $fields = array(
         'bid' => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'aid' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'filtype' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'filnamn' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'tittel' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'tekst' => array(
            'alias'   => 'text',
            'type'    => DB_TYPE_STRING,
            'size'    => 65535,
            'default' => '',
         ),
         'dato'    => array(
            'alias'   => 'date',
            'type'    => DB_TYPE_DATE,
            'null'    => true,
            'default' => null,
         ),
         'sessionid' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'x' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'y' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'size' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'owner_uid' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
            'alias' => 'uid',
         ),
         'time'    => array(
            'type'    => DB_TYPE_TIME,
            'null'    => true,
            'default' => null,
         ),
         'identifier' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'null'    => true,
            'default' => null,
         ),
         'licensetype' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'licensefee' => array(
            'type'    => DB_TYPE_FLOAT,
            'size'    => 11,
            'default' => 0,
         ),
         'sorting' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'hashcode' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'sharekey' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'deleted_at'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'restore'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'exif_date'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'exif_x_res' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'exif_y_res' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'exif_make' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'exif_model' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'exif_orientation' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'exif_exposure_time' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'exif_f_value' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => null,
            'null'    => true,
         ),
         'exif_shutter_speed' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => null,
            'null'    => true,
         ),
         'exif_gps_altitude' => array(
            'type'    => DB_TYPE_FLOAT,
            'size'    => 11,
            'default' => null,
            'null'    => true,
         ),
         'exif_gps_latitude' => array(
            'type'    => DB_TYPE_FLOAT,
            'size'    => 11,
            'default' => null,
            'null'    => true,
         ),
         'exif_gps_longitude' => array(
            'type'    => DB_TYPE_FLOAT,
            'size'    => 11,
            'default' => null,
            'null'    => true,
         ),
         'quarantined_at'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
      );

      public function __postSetup() {

         // setup some defaults for new objects
         if( !$this->time ) {
            $this->time = date( 'Y-m-d H:i:s' );
            $this->date = date( 'Y-m-d' );
         }
         
         // ensure this object has a sharekey
         /*
         if( !$this->sharekey ) {
            import( 'math.uuid' );
            $this->sharekey = UUID::create();
            if( $this->bid ) $this->save();
         }
         */
         
         // run the parent setup
         return parent::__postSetup();

      }

      public function getImageId() {

         return $this->fieldGet( 'bid' );

      }

      public function setImageId( $imageid ) {

         return $this->fieldSet( 'bid', $imageid );

      }

      public function getOwnerId() {

         return $this->owner_uid;

      }

      public function setOwnerId( $uid ) {

         return $this->fieldSet( 'owner_uid', $uid );

      }

      public function getFileType() {

         return $this->filtype;

      }

      public function setFileType( $filetype ) {

         return $this->filtype = $filetype;

      }

      public function getFilename() {

         return $this->filnamn;

      }

      public function setFilename( $filename ) {

         return $this->filnamn = $filename;

      }

      public function getQuarantined() {

         return $this->quarantined_at;

      }

      public function setQuarantined( $quarantined ) {

         return $this->quarantined_at = $quarantined;

      }

      public function getTitle() {

         return  $this->tittel;

      }

      public function setTitle( $title ) {

         return $this->tittel = $title;

      }

      public function getDescription() {

         return $this->tekst ;

      }

      public function setDescription( $description ) {

         return $this->tekst = $description ;

      }

      public function getExif_Date( ) {
         return ($exif_date = $this->fieldGet('exif_date')) == '1970-01-01 00:00:00' ? null : $exif_date;
      }

      
      public function setAid( $aid ) {
         
         $this->fieldSet( 'aid', $aid );
         CacheEngine::erase( 'album-imgcount[%d]', $aid );
         
      }
      
      
      /**
       * Set image as deleted
       *
       */
      public function delete() {

         $this->deleted_at = date( "Y-m-d H:i:s" );
         $this->save();
         CacheEngine::erase( 'album-imgcount[%d]', $this->aid );

      }


      /**
       * Return the date and time of deletion
       *
       * @return unknown
       */
      public function deleted() {

         return $this->deleted_at;

      }

   }

?>