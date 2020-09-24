<?

   /**
    * Model for a subscription types
    * 
    */

   import( 'core.model' );

   class DBSubscriptionTypes extends Model {
      
      static $table = 'subscription_types';
      
      
      static $fields = array(
         'id' => array(
            'primary'=> true,
            'type'   => DB_TYPE_INTEGER,
            'size'   => 11,
            'null'   => false,
         ),
         'name' => array(
            'type'   => DB_TYPE_STRING,
            'size'   => 65536,
            'null'   => true,
         ),
         'description'=> array(
            'type'   => DB_TYPE_STRING,
            'size'   => 65536,
            'null'   => true,
         ),
         'product_id'=> array(
            'type'   => DB_TYPE_INTEGER,
            'size'   => 11,
            'null'   => false,
         ),
         'created'   => array(
            'type'   => DB_TYPE_DATETIME,
            'null'   => true,
            'default'=> null,
         ),
         'default_months' => array(
            'type'   => DB_TYPE_INTEGER,
            'size'   => 11,
            'null'   => true,
            'alias'  => 'months',
         ),
         'default_days' => array(
            'type'   => DB_TYPE_INTEGER,
            'size'   => 11,
            'null'   => true,
         ),
         'void' => array(
            'type'   => DB_TYPE_DATETIME,
            'null'   => true,
            'default'=> null,
            'alias'  => 'days',
         ),
      );
      
      
      
      
      
      
   }














?>