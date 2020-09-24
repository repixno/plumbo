<?PHP
   
   import( 'core.model' );
   
   class DBFeedback extends Model {
      
      static $table = 'feedback';
      
      static $basename = 'site';
      
      static $fields = array(
         'feedbackid' => array(
            'type'   => DB_TYPE_INTEGER,
            'size'   => 11,
            'primary' => true,
            'default'   => 0,
         ),
         'userid' => array(
            'type'   => DB_TYPE_INTEGER,
            'size'   => 11,
            'default'   => 0,
         ),
         'fields' => array( 
            'type'    => DB_TYPE_STRING,
            'size'    => 16777216,
            'default' => '',
         ),
         'ipaddress' => array( 
            'type'    => DB_TYPE_STRING,
            'size'    => 15,
            'default' => '',
         ),
         'serverhost' => array( 
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'logged'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'replied'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'solved'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
      );
      
      protected function __setup() {
         
         $result = parent::__setup();
         $this->logged = date( 'Y-m-d H:i:s' );
         $this->ipaddress = $_SERVER['REMOTE_ADDR'];
         $this->serverhost = $_SERVER['HTTP_HOST'];
         return $result;
         
      }
      
   }
   
?>