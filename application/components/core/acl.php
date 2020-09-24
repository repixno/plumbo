<?PHP
   
   class ACL {
      
      static function set( $key ) {
         
         Session::merge( 'acl', array( $key => true ) );
         
      }
      
      static function has( $key ) {
         
         $acl = Session::fetch( 'acl', array( $key => true ) );
         return isset( $acl[$key] ) ? true : false;
         
      }
      
      static function setByClass( $class, $key ) {
         
         ACL::set( sprintf( '%s_%s', $class, $key ) );
         
      }
      
      static function hasByClass( $class, $key ) {
         
         return ACL::has( sprintf( '%s_%s', $class, $key ) );
         
      }
      
   }
   
?>