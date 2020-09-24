<?PHP

   /**
    * @author Tor Inge Løvland <tor.inge@eurofoto.no>
    */
   
   Import( 'core.model' );

   class DBAlbumDownloads extends Model {
   
   
   static $table = 'album_downloads';

      static $fields = array(
         'id' => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => false,
            'default' => 0,
         ),
         'job_name' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => null,
            'null'    => true,
         ),
         'uid'    => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'aid'    => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'hash'    => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => null,
            'null'    => true,
         ),
         'ordered'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'completed'  => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'downloaded'=> array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),       
      );

   }








?>