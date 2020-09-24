<?PHP
   
   // define the default language
   define( 'en_US', 'en_US' );
   
   // utilizes the core::db
   import( 'core.db' );
   
   // utilizes the core::settings
   import( 'core.settings' );
   
   // utilizes the cache::engine
   import( 'cache.engine' );
   
   // require session support
   import( 'core.session' );
   
   // import the Language.String model
   model( 'i18n.languagestring' );
   
   /**
    * Internationalization Class (i18n)
    * 
    * @author Oyvind Selbek <oyvind@selbek.com>
    */
   class i18n {
      
      // holds the current language
      static $language = en_US;
      static $languageid = 0;
      
      // whether to autolearn new vars
      static $autolearn = true;
      
      // holds the static language cache
      static $strings = null;
      
      // holds the class defaults
      static $defaultclass = null;
      static $defaultfile = null;
      static $defaultfunction = null;
      
      /**
       * Sets the default class to use
       *
       * @param string $class
       */
      static function setDefaultClass( $class ) {
         
         i18n::$defaultclass = $class;
         
      }
      
      /**
       * Sets the default file to use.
       *
       * @param string $file
       */
      static function setDefaultFile( $file ) {
         
         i18n::$defaultfile = $file;
         
      }
      
      /**
       * Sets the default function to use.
       *
       * @param string $function
       */
      static function setDefaultFunction( $function ) {
         
         i18n::$defaultfunction = $function;
         
      }
      
      /**
       * Sets the current language
       *
       * @param string $set The language in nn_XX-representation
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      static function setLanguage( $set ) {
         
         // calculate the identifier
         $identifier = sprintf( 'LanguageIDLookup-%s', $set );
         
         // is this a valid language?
         if( !CacheEngine::read( $identifier, false ) ) {
            
            // retreive the strings and store them in the i18n strings db
            if( $langid = DB::query( 'SELECT languageid FROM i18n_language WHERE code = ?', $set )->fetchSingle() ) {
               
               // write it as a valid language identifier
               CacheEngine::write( $identifier, $langid, 86400 );
               
            } else {
               
               // invalid language
               return false;
               
            }
            
         }
         
         // store the new language
         i18n::$language = $set;
         i18n::$languageid = $langid;
         
         // set the current language
         Session::set( 'language', $set );
         
         // attempt to set locale too
         setLocale( LC_TIME, $set.'.UTF-8' );
         
         // when switching languages, recache
         i18n::reCache();
         
         // success!
         return true;
         
      }
      
      /**
       * Returns the current languages name
       *
       * @return string The name of the current language
       */
      static function languageName() {
         
         return DB::query( "SELECT elementname FROM i18n_language WHERE code = ?", i18n::$language )->fetchSingle();
         
      }
      
      static function getLanguages() {
         $languages = array();
         foreach( DB::query( "SELECT code, elementname FROM i18n_language WHERE code = ?", i18n::$language )->fetchAll() as $row ) {
            $languages[array_shift($row)] = array_shift($row);
         }
         return $languages;
      }
      
      /**
       * Returns the current languages id
       *
       * @return string The name of the current language
       */
      static function languageId() {
         
         if( i18n::$languageid ) return i18n::$languageid;
         return i18n::$languageid = i18n::getLanguageId( i18n::$language );
         
      }
      
      /**
       * Returns the current languages code
       *
       * @return string The name of the current language
       */
      static function languageCode() {
         
         return i18n::$language;
         
      }
      
      /**
       * Returns the languageid of a given language 
       * code, using the lookup cache if available.
       *
       * @param string $language
       * @return integer language id or boolean false if not found
       */
      static function getLanguageId( $language = false ) {
         
         if( !$language && i18n::$languageid ) return i18n::$languageid;
         if( !$language ) $language = i18n::$language;
         
         // calculate the identifier
         $identifier = sprintf( 'LanguageIDLookup-%s', $language );
         
         // is this a valid language?
         if( !$langid = CacheEngine::read( $identifier, false ) ) {
            
            // retreive the strings and store them in the i18n strings db
            if( $langid = DB::query( 'SELECT languageid FROM i18n_language WHERE code = ?', $language )->fetchSingle() ) {
               
               // write it as a valid language identifier
               CacheEngine::write( $identifier, $langid, 86400 );
               
            } else {
               
               // invalid language
               return false;
               
            }
            
         }
         
         // return the id
         return $langid;
         
      }
      
      /**
       * translates a language variable
       *
       * @param string $langvar The string to translate
       * @param boolean $recache True to force a recache before translation
       * @return string The translated string, if found, the $langvar otherwise
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      static function translate( $langvar, $recache = false ) {
         
         // check if we need to recache
         if( is_null( i18n::$strings ) || $recache == true )  {
            
            // recache translations
            i18n::reCache();
            
         }
         
         // calculate the langvar hash
         $hash = md5( $langvar );
                        
         // is it blank
         if( trim( $langvar ) == '' ) {
            
            // just return it
            return $langvar;
            
         // do we have a translation for it?
         } elseif( isset( i18n::$strings[$hash] ) ) {
            
            // return the cached value
            return i18n::$strings[$hash];
            
         // is autolearning enabled?
         } elseif( i18n::$autolearn ) {
            
            // pre-process the backtrace
            $debug = debug_backtrace();
            
            // find the file/line/class/function
            $line = isset( $debug['1']['line'] ) ? $debug['1']['line'] : 1;
            $file = isset( $debug['1']['file'] ) ? str_replace( getRootPath(), '', $debug['1']['file'] ) : '';
            $class = isset( $debug['2']['class'] ) ? $debug['2']['class'] : '';
            $function = isset( $debug['2']['function'] ) ? $debug['2']['function'] : '';
            
            // create/update the language-string object
            $object = LanguageString::fromHash( $hash );
            $object->elementname = $langvar;
            $object->sourcefile = isset( i18n::$defaultfile ) ? i18n::$defaultfile : $file;
            $object->sourceline = isset( i18n::$defaultfile ) ? 1 : $line;
            $object->sourceclass = isset( i18n::$defaultclass ) ? i18n::$defaultclass : $class;
            $object->sourcefunction = isset( i18n::$defaultfunction ) ? i18n::$defaultfunction : $function;
            $object->save();
            
         } else {
            
            // just return it
            return $langvar;
            
         }
         
         // reset defaults
         i18n::$defaultclass = null;
         i18n::$defaultfile = null;
         i18n::$defaultfunction = null;
         
         // return the original variable
         return $langvar;
         
      }
      
      /**
       * Recaches the language array from the database
       * 
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      static function reCache( $force = false ) {
         
         // clear the current hash-table
         i18n::$strings = array();
         
         // create a unique cache-identifier for this language
         $cacheident = sprintf( 'langvars-%s', i18n::$language );
         
         // attempt to load the strings from the cache-engine
         $languagevars = CacheEngine::read( $cacheident, null );
         
         // did we find a cache?
         if( !$force && is_array( $languagevars ) ) {
            
            // set the languagevars
            i18n::$strings = $languagevars;
            
         } else {
            
            // retreive the strings and store them in the i18n strings db
            foreach( DB::query( "SELECT ls.hash,
                                        lt.translation
                                   FROM i18n_language l 
                              LEFT JOIN i18n_languagetranslation lt 
                                     ON l.languageid = lt.languageid
                              LEFT JOIN i18n_languagestring ls 
                                     ON ls.stringid = lt.stringid
                                  WHERE l.code = ? 
                                    AND lt.translation IS NOT NULL", i18n::$language )->fetchAll() as $row ) {
               
               // expand the variables
               list( $hash, $translation ) = $row;
               
               // build the hash-table
               i18n::$strings[$hash] = $translation;
               
            }
            
            // write the value to cache so we have it :)
            CacheEngine::write( $cacheident, i18n::$strings );
            
         }
         
      }
      
   }
   
   /**
    * General purpose translation method
    *
    * @param string The string to translate
    * @param mixed Optional params to sprintf onto the translated string
    * @return string The translated string
    * @author Oyvind Selbek <oyvind@selbek.com>
    */
   function __() {
      
      // get the argument list
      $args = func_get_args();
      
      // pop off the first var as the langvar
      $langvar = array_shift( $args );
      
      // make sure its a valid string before continuing
      if( is_string( $langvar ) && $langvar != '' ) {
         
         // call the i18n translation operation
         $result = i18n::translate( $langvar );
         
         // check if we just have one argument
         if( count( $args ) == 1 && is_array( $args[0] ) ) {
            $args = $args[0];
         }
         
         // prepend the translated variable back on the stack
         array_unshift( $args, $result );
         
         // format any outstanding methods into the var
         $result = call_user_func_array( 'sprintf', $args );
         
         // return the result
         return $result;
         
      } else {
         
         // return the original
         return $langvar;
         
      }
      
   }

?>