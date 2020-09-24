<?PHP
   
   import( 'core.model' );
   
   class LanguageString extends Model {
      
      static $basename = 'i18n';
      
      static $fields = array(
         'stringid' => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'elementname' => array( 
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'hash' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 32,
            'default' => '',
         ),
         'sourcefile' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'sourceline' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'sourceclass' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'sourcefunction' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
      );
      
      static function fromHash( $hash ) {
         
         // attempt to find the managedelement decendant by elementname
         $res = DB::query( "SELECT stringid FROM i18n_languagestring WHERE hash = ? LIMIT 1", $hash );
         
         // did we find a row?
         if( count( $res ) ) {
            
            // retrieve the row id and classname...
            list( $stringid ) = $res->fetchRow();
            
            // ...and instantiate it
            return new LanguageString( $stringid );
            
         } else {
            
            // instantiate the default class
            $languagestring = new LanguageString();
            $languagestring->hash = $hash;
            return $languagestring;
            
         }
         
      }
      
   }
   
?>