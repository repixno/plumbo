<?PHP
   
   class EF2x {
      
      static $languagemap = array(
                'en_US' => 0x01,
                'nb_NO' => 0x02,
             );

      static function getLanguageResource( $languageresourceid ) {
         
         $languageid = isset( EF2x::$languagemap[i18n::$language] ) 
                     ? EF2x::$languagemap[i18n::$language] 
                     : EF2x::$languagemap['en_US'];
         
         return DB::query( 'SELECT message FROM language_resource WHERE lang_res_id = ? AND language = ?', $languageresourceid, $languageid )->fetchSingle();
         
      }
      
   }
   
?>