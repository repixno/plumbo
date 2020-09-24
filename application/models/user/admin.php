<?PHP
   
   model( 'user.customer' );
   
   class DBAdmin extends DBCustomer implements ModelCaching {
      
      static $table = 'admins';
      
      static $fields = array(
         'level' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
      );
      
   }
   
?>