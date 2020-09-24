<?PHP

   import( 'core.model' );
   
   class DBFormSubmission extends Model {

      static $table = 'submissions';

      static $basename = 'site_form';

      static $fields = array(
         'submissionid' => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'uid' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'identifier' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'email' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'portal' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'data' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 16581375,
            'default' => '',
         ),
         'sent'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
      );
      
      public function save() {
         
         $this->sent = date( 'Y-m-d H:i:s' );
         return parent::save();
         
      }
      
   }

?>