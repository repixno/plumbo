<?PHP

   /**
    * Model for table history stamps
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    *
    */

   class DBSiteStamp extends Model {
      
      static $table = 'history_stamps';
      
      static $fields = array(
         'stampid' => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'primary'   => true,
            'null'      => false,
            'default'   => 0,
         ),
         'orderid' => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => true,
            'default'   => null,
         ),
          'userid' => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => true,
            'default'   => null,
         ),
         'jobname' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'imagename' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'created'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'approved'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'declined'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'processed'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'declinereason' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 65535,
            'null'    => true,
            'default' => null,
         ),
         'processedby' => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => true,
            'default'   => null,
         ),
      
      );
      
   }


?>