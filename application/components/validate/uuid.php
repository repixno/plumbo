<?PHP
   
   class ValidateUUID {
      
      static function validate( $uuid ) {
          return (bool) preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', $uuid );
      }
      
   }
   
?>