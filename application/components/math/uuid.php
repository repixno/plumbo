<?PHP
   
   class UUID {
      
      /**
		 * Creates a random string in UUID-Format (RFC4122)
		 *
		 * @param $oldformat Boolean true to create old-format UUIDs
		 * @return string the generated UUID-string in RFC4122-format
		 */
		static function create() {
         
	      return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            mt_rand( 0, 0x0fff ) | 0x4000,
            mt_rand( 0, 0x3fff ) | 0x8000,
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ) 
         );
         
      }
      
   }
   
?>