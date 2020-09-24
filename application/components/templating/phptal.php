<?PHP
   
   // requires settings-support
   import( 'core.settings' );
   
   // define the default encoding
   define( 'PHPTAL_DEFAULT_ENCODING', Settings::Get( 'templating', 'encoding', 'UTF-8' ) );
   
   // import required PHPTAL libraries
   library( 'phptal.PHPTAL' );
   library( 'phptal.PHPTAL.TranslationService' );
   library( 'phptal.PHPTAL.CommentFilter' );
   
   // requires the templateengine interface
   import( 'templating.interface' );
   
   // requires UUID validation
   import( 'validate.uuid' );
   
   // requires i18n functions
   import( 'core.i18n' );
   
   // import static configurations
   config( 'website.static' );
   
   /**
    * ITemplateEngine implementation for the PHPTAL engien
    *
    * @author Oyvind Selbek <oyvind@selbek.com>
    */
   class TemplateEngine_PHPTAL implements ITemplateEngine {
      
      public function Execute( $filename, $variables ) {
         
         // grab the settings for templates
         $tmppath = Settings::Get( 'templating', 'tmp', '/tmp/' );
         $reparse = Settings::Get( 'templating', 'reparse', true );
         $strpcmt = Settings::Get( 'templating', 'stripcomments', true );
         
         // instantiate a new PHPTAL object using specified template file
         $template = new PHPTAL( $filename );
         
         // setup some defaults
         $template->setPhpCodeDestination( $tmppath );
         $template->setPhpCodeExtension( 'tmp' );
         $template->setForceReparse( $reparse );
         
         // add a comment-filter to the template
         if( $strpcmt ) $template->setPreFilter( new PHPTAL_CommentFilter() );
         
         // setup the translator engine
         $template->setTranslator( new PHPTAL_TranslationEngine( $filename ) );
         
         // is it an array or object?
         if( is_object( $variables ) || is_array( $variables ) ) {
            
            // iterate it...
            foreach( $variables as $key => $value ) {
               
               // ...and add to the template
               $template->$key = $value;
               
            }
            
         // otherwise...
         } else {
            
            // just define it as the content
            $template->content = $variables;
            
         }
         
         // return the executed template
         return $template->execute();
         
      }
      
   }
   
   /**
    * The PHPTAL TranslationService wrapper class for in-template translations
    * 
    * @author Oyvind Selbek <oyvind@selbek.com>
    */
   class PHPTAL_TranslationEngine implements PHPTAL_TranslationService {
      
      private $_template;
      private $_currentLang;
      private $_context = array();
      
      public function __construct( $template ) {
         
         $this->_template = $template;
         
      }
      
      public function translate( $key, $htmlescape = true ) {
         
         i18n::setDefaultClass( 'TemplateEngine::PHPTAL' );
         i18n::setDefaultFile( sprintf( 'template://%s', str_replace( getRootPath(), '', $this->_template ) ) );
         i18n::setDefaultFunction( 'Translate' );
         
         $value = i18n::translate( $key );
         
         while( preg_match ('/\${(.*?)\}/sm', $value, $m ) ) {
            
            list( $src, $var ) = $m;
            if( !array_key_exists( $var, $this->_context ) ) {
               throw new Exception( sprintf( 'Interpolation error, var "%s" not set', $var ) );
            }
            
            $value = str_replace( $src, $this->_context[$var], $value );
            
         }
         
         return $value;
         
      }
      
      public function setLanguage() {
         
      }
      
      public function useDomain( $domain ) {
         
      }
      
      public function setEncoding( $encoding ) {
         
      }
      
      public function setVar( $key, $value ){
         
         $this->_context[$key] = $value;
         
      }
      
   }
   
   function phptal_support_menulist( $identifier, $includecontent = false, $includebodies = true ) {
      
      if( $identifier && $identifier != '/' ) {
         
         if( ValidateUUID::validate( $identifier ) ) {
            $menu = Menu::findItemFromIdentifier( $identifier );
         } else {
            $menu = Menu::findItemFromURL( $identifier );
         }
         
         if( !$menu ) {
            
            return array();
            
         } else {
            
            return Menu::getMenuTree( 0, $menu->id, $includecontent, $includebodies );
            
         }
         
      } else {
         
         return Menu::getMenuTree( 0, 0, $includecontent, $includebodies );
         
      }
      
   }
   
   function phptal_tales_menu( $src, $nothrow ) {
      
      list( $src, $includecontent, $includebodies, $evalmatch ) = explode( ',', $src );
      
      if( $evalmatch ) {
         return 'phptal_support_menulist('.PHPTAL_TalesInternal::path( trim( $src ), $nothrow ).", '$includecontent', '$includebodies')";
      } else {
         return "phptal_support_menulist('$src', '$includecontent', '$includebodies')";
      }
      
   }
   
   function phptal_support_menucontent( $identifier, $section="standard" ){
      
      $menu = Menu::findItemFromIdentifier( $identifier );
      
      return  $menu->getContentListSection( $section );
      

   }
   
   function phptal_tales_menucontent( $src ){
      
       list( $src, $section) = explode( ',', $src );
      
      return "phptal_support_menucontent('$src', '$section')";
      
   }
   
   
   
   function phptal_support_productfrommenu( $identifier, $section="standard" ){   
      $menu = Menu::findItemFromIdentifier( $identifier );
      $products = Array();
      foreach(  $menu->getContentListSection( $section ) as $ret ){
         $product = new Product( $ret['textentityid'] );
         $products[] = $product->asArray();
      }
      
      return  $products;
   }
   
   function phptal_tales_productfrommenu( $src ){
      list( $src, $section) = explode( ',', $src );
      return "phptal_support_productfrommenu('$src', '$section')";
   }
   
   function phptal_support_templateroot( $param ) {
      
      return sprintf( '%s/%s', Dispatcher::$tmplPath, $param );
      
   }
   
   function phptal_tales_templateroot( $src, $nothrow ) {
      
      return "phptal_support_templateroot( '$param' )";
      
   }
   
   function phptal_support_menutitle( $identifier ) {
      
      if( ValidateUUID::validate( $identifier ) ) {
         $menu = Menu::findItemFromIdentifier( $identifier );
      } else {
         $menu = Menu::findItemFromURL( $identifier );
      }
      
      if( !$menu ) {
         
         return '';
         
      } else {
         
         return $menu->title;
      }
      
   }
   
   function phptal_tales_menutitle( $src, $nothrow ) {
      
      list( $src, $evalmatch ) = explode( ',', $src );
      
      if( $evalmatch ) {
         return "phptal_support_menutitle(".PHPTAL_TalesInternal::path( trim( $src ), $nothrow ).")";
      } else {
         return "phptal_support_menutitle('$src')";
      }
      
   }
   
   function phptal_support_includeresource( $url, $timeout = 86400 ) {
      
      $identifier = 'cache_remote_resource['. md5( $url ).']';
      if( !$resource = CacheEngine::read( $identifier ) ) {
         
         try {
            $resource = file_get_contents( $url );
            CacheEngine::write( $identifier, $resource, $timeout );
         } catch ( Exception $e ) {
            $resource = '';
         }
         
      }
      
      return $resource;
      
   }
   
   function phptal_tales_includeresource( $src, $nothrow ) {
      
      list( $url, $timeout, $evalmatch ) = explode( ',', $src );
      
      $timeout = (int) $timeout;
      $timeout = max( $timeout, 60 );
      
      if( $evalmatch ) {
         return "phptal_support_includeresource(".PHPTAL_TalesInternal::path( trim( $url ), $nothrow ).", '".$timeout."')";
      } else {
         return "phptal_support_includeresource('".trim( $url )."', '".$timeout."')";
      }
      
   }
   
   function phptal_support_template( $filename ) {
      
      $controller = new TemplatingController();
      return $controller->findTemplate( Dispatcher::$tmplPath, $filename );
      
   }
   
   function phptal_tales_template( $src, $nothrow ) {
      
      return "phptal_support_template( '".trim($src)."')";
      
   }
   
   function phptal_support_sections( $identifier, $sectioned = false ) {
      
      if( ValidateUUID::validate( $identifier ) ) {
         $menu = Menu::findItemFromIdentifier( $identifier );
      } else {
         $menu = Menu::findItemFromURL( $identifier );
      }
      
      if( !$menu ) return array();
      
      // get the sections
      $sections = $menu->getContentObjects( true );
      
      // should we have sectioned output?
      if( $sectioned ) {
         
         // create a list of sections
         $allsections = array();
         if( count( $sections ) > 0 ) {
            foreach( $sections as $title => $items ) {
               $allsections[$title ? $title : 'default'] = $items;
            } 
         }
         
         // fill the sections-item
         return $allsections;
         
      } else {
         
         // fill the default items
         return is_array( $sections[''] ) ? $sections[''] : array();
         
      }
      
   }
   
   function phptal_tales_items( $src, $nothrow ) {
      
      list( $src, $evalmatch ) = explode( ',', $src );
      
      if( $evalmatch ) {
         return "phptal_support_sections(".PHPTAL_TalesInternal::path( trim( $src ), $nothrow ).")";
      } else {
         return "phptal_support_sections('$src')";
      }
      
   }

   function phptal_tales_sections( $src, $nothrow ) {
      list( $src, $evalmatch ) = explode( ',', $src );
      
      if( $evalmatch ) {
         return "phptal_support_sections(".PHPTAL_TalesInternal::path( trim( $src ), $nothrow ).", true)";
      } else {
         return "phptal_support_sections('$src', true)";
      }
   }
   
   function phptal_tales_latestproducts( $src, $nothrow ) {
      
      list( $limit ) = explode( ':', $src );
      $limit = min( max( 1, (int) $limit ), 255 );
      return "phptal_support_latestproducts('$limit')";
      
   }
   
   function phptal_support_latestproducts( $limit ) {
      
      return Product::listProductsByCreated( $limit );
      
   }
   
   function phptal_tales_mostpopularproducts( $src, $nothrow ) {
      
      list( $limit, $lastxdays ) = explode( ':', $src );
      $limit = min( max( 1, (int) $limit ), 255 );
      $lastxdays = min( max( 1, (int) $lastxdays ), 1000 );
      return "phptal_support_mostpopularproducts('$limit', '$lastxdays')";
      
   }
   
   function phptal_support_unicodedecode( $string ) {
      
      return unicode_decode( $string );
      
   }
   
   function phptal_tales_unicodedecode( $src, $nothrow ) {
      
      return "phptal_support_unicodedecode(".PHPTAL_TalesInternal::path( trim( $src ), $nothrow ).")";
      
   }
   
   function phptal_support_unicodeencode( $string ) {
      
      return unicode_encode( $string );
      
   }
   
   function phptal_tales_unicodeencode( $src, $nothrow ) {
      
      return "phptal_support_unicodeencode(".PHPTAL_TalesInternal::path( trim( $src ), $nothrow ).")";
      
   }
   
   function phptal_support_urldecode( $string ) {
      
      return urldecode( $string );
      
   }
   
   function phptal_tales_urldecode( $src, $nothrow ) {
      
      return "phptal_support_urldecode(".PHPTAL_TalesInternal::path( trim( $src ), $nothrow ).")";
      
   }
   
   function phptal_support_urlencode( $string ) {
      
      return urlencode( $string );
      
   }
   
   function phptal_tales_urlencode( $src, $nothrow ) {
      
      return "phptal_support_urlencode(".PHPTAL_TalesInternal::path( trim( $src ), $nothrow ).")";
      
   }
   
   function phptal_support_formatdate( $date, $short = false ) {
      
      $format = Settings::Get( 'datetime', $short ? 'dateformatshort' : 'dateformat', '%d. %B %Y' );
      return strftime( $format, strtotime( $date ) );
      
   }
   
   function phptal_tales_formatdate( $src, $nothrow ) {
      
      list( $date, $short ) = explode( ',', $src );
      $short = (bool) $short;
      return "phptal_support_formatdate(".PHPTAL_TalesInternal::path( trim( $date ), $nothrow ).",'$short')";
      
   }
   
   function phptal_support_formatdatetime( $date, $short = false ) {
      
      $format = Settings::Get( 'datetime', $short ? 'datetimeformatshort' : 'datetimeformat', '%d. %B %Y %H:%M:%S' );
      return strftime( $format, strtotime( $date ) );
      
   }
   
   function phptal_tales_formatdatetime( $src, $nothrow ) {
      
      list( $date, $short ) = explode( ',', $src );
      $short = (bool) $short;
      return "phptal_support_formatdatetime(".PHPTAL_TalesInternal::path( trim( $date ), $nothrow ).",'$short')";
      
   }
   
   function phptal_support_mostpopularproducts( $limit, $lastxdays ) {
      
      return Product::listProductsBySales( $limit, $lastxdays );
      
   }
   
   function phptal_tales_url( $src, $nothrow ) {
      
      return "phptal_support_url('$src')";
      
   }
   
   function phptal_tales_todaysphoto( $src, $nothrow ) {
      
      return "phptal_support_todaysphoto()";
      
   }
   
   function phptal_support_todaysphoto() {
      
      import( 'website.helper' );
      
      $imageid = (int) WebsiteHelper::getTodaysImage();
      if( $imageid > 0 ) {
         
         // load the image and return it
         try {
            $image = new Image( $imageid );
            return $image->asArray();
         } catch( Exception $e ) {}
         
      }
      
      return false;
      
   }
   
   function phptal_tales_lastgalleryphoto( $src, $nothrow ) {
      
      return "phptal_support_lastgalleryphoto()";
      
   }
   
   function phptal_support_lastgalleryphoto() {
      
      import( 'website.helper' );
      
      $imageid = (int) WebsiteHelper::getLastGalleryImage();
      if( $imageid > 0 ) {
         
         // load the image and return it
         try {
            $image = new Image( $imageid );
            return $image->asArray();
         } catch( Exception $e ) {}
         
      }
      
      return false;
      
   }
   
   function phptal_support_url( $identifier ) {
      
      if( ValidateUUID::validate( $identifier ) ) {
         $menu = Menu::findItemFromIdentifier( $identifier );
      } else {
         $menu = Menu::findItemFromURL( $identifier );
      }
      
      return $menu ? $menu->getTranslatedUrl( i18n::$language ) : $identifier;
      
   }
   
   function phptal_tales_urltitle( $src, $nothrow ) {
      return "phptal_support_urltitle('$src')";
   }
   
   function phptal_support_urltitle( $identifier ) {
      
      if( ValidateUUID::validate( $identifier ) ) {
         $menu = Menu::findItemFromIdentifier( $identifier );
      } else {
         $menu = Menu::findItemFromURL( $identifier );
      }
      
      return $menu ? $menu->getTranslatedTitle( i18n::$language ) : $identifier;
      
   }
   
   function phptal_tales_translatehtml( $src, $nothrow ) {
      
      return "phptal_support_translatehtml('$src')";
      
   }
   
   function phptal_support_translatehtml( $html ) {
      
      $html = preg_replace('~<(.*?)>~', '[$1]', $html );
      $html = preg_replace('~\[(.*?)\]~', '<$1>', __( $html ) );
      return str_replace( "'", "\\'", $html );
      
   }
   
   function phptal_support_cms( $identifier, $field ) {
      
      import( 'website.textentity' );
      $entity = TextEntity::fromIdentifier( $identifier );
      return $entity ? $entity->$field : '';
      
   }
   
   function phptal_tales_debug( $src, $nothrow ) {
      
      return 'phptal_support_debug('.PHPTAL_TalesInternal::path( trim( $src ), $nothrow ).')';
      
   }
   
   function phptal_support_debug( $data ) {
      
      util::Debug( $data ); die();
      
   }
   
   function phptal_tales_cms( $src, $nothrow ) {
      
      list( $field, $identifier ) = explode( ':', $src );
      $field = addslashes( $field );
      $identifier = addslashes( $identifier );
      
      return "phptal_support_cms('$identifier','$field')";
      
   }
   
   function phptal_support_static( $url ) {
      
      $hosts = Settings::Get( 'static', 'hosts', array() );
      
      if( count( $hosts ) > 0 ) {
         
         $host = $hosts[array_rand($hosts)];
         
      } else {
         
         $host = WebsiteHelper::staticBaseUrl();
         
      }
      
      return sprintf( '%s/%s', $host, $url );
      
   }
   
   function phptal_tales_static( $src, $nothrow ) {
      
      return "phptal_support_static('$src')";
      
   }
   
   /**
    * This modifier will return a money formated string (XXX.XX)
    * 
    * usage:
    *      formatprice: path/to/my/amount
    * 
    * this modifier use phptal_tales_path (path:) modifier to generate the
    * php code that will return the value of the modifier argument.
    * 
    * @param string $src
    *      The expression string
    * @param string $nothrow
    *      A boolean indicating if exceptions may be throw by phptal_path if
    *      the path does not exists.
    * @return string
    *      PHP code to include in the template
    */
   
   function phptal_tales_removetema( $src, $nothrow ){
      
      //return "date(Y)";
   
      return "str_replace( '/tema' , '' , " . PHPTAL_TalesInternal::path( trim( $src ), $nothrow ) . ")";
      
   }
   
   function phptal_tales_formatprice( $src, $nothrow ) {
      
      if( i18n::$language  ==  'sv_SE' &&  ( Dispatcher::getPortal() == 'VP-001' || Dispatcher::getPortal() == 'STU-SV'  ) ){
         return "sprintf('%s:-'," . PHPTAL_TalesInternal::path( trim( $src ), $nothrow ) . ")";
      }
      else{
         return 'number_format('.PHPTAL_TalesInternal::path( trim( $src ), $nothrow ).', 2, ",", " ")';
      }
      
   }
   
   function phptal_tales_formatdecimal( $src, $nothrow ) {
      @list( $src, $accuracy ) = explode( ';', $src );
      $accuracy = is_null( $accuracy ) ? 2 : (int) $accuracy;
      return 'number_format('.PHPTAL_TalesInternal::path( trim( $src ), $nothrow ).', strpos(strrev( round( '.PHPTAL_TalesInternal::path( trim( $src ), $nothrow ).', '.$accuracy.' ) ),"."), ","  , " " )';
   }
   
   function phptal_tales_mod( $src, $nothrow ) {
      @list( $src, $modulus ) = explode( ',', $src ); $modulus = (int) $modulus;
      return '('.PHPTAL_TalesInternal::path( trim( $src ), $nothrow ).' % '.$modulus.' == 0 ? true : false)';
   }
   
   function phptal_tales_shorten( $src, $nothrow ) {
      list( $string, $maxlength, $tail ) = @explode( ';', $src );
      $maxlength = (int) $maxlength ? $maxlength : 20;
      $tail = $tail ? $tail : '...';
      $string = PHPTAL_TalesInternal::path( trim( $string ), $nothrow );
      return 'strlen( '.$string.' ) > '.$maxlength.' ? substr('.$string.", 0, $maxlength ).'$tail' : $string";
   }
   
   function phptal_tales_nicestring( $src, $nothrow ) {
      return 'Util::urlize('.PHPTAL_TalesInternal::path( trim( $src ), $nothrow ).", ' ' )";
   }
   
   function phptal_tales_urlize( $src, $nothrow ) {
      return 'Util::urlize('.PHPTAL_TalesInternal::path( trim( $src ), $nothrow ).")";
   }
   
   function phptal_tales_lowercase( $src, $nothrow ) {
      return 'strtolower('.PHPTAL_TalesInternal::path( trim( $src ), $nothrow ).')';
   }
   
   function phptal_tales_uppercase( $src, $nothrow ) {
      return 'strtoupper('.PHPTAL_TalesInternal::path( trim( $src ), $nothrow ).')';
   }
   
   function phptal_tales_equal( $src, $nothrow ) {
      list( $path, $value, $evalmatch ) = explode( ',', $src );
      if( $evalmatch ) {
         return '('.PHPTAL_TalesInternal::path( trim( $path ), $nothrow )." == ".PHPTAL_TalesInternal::path( trim( $value ), $nothrow ).')';
      } else {
         return '('.PHPTAL_TalesInternal::path( trim( $path ), $nothrow )." == '$value'".')';
      }
   }
   
   function phptal_tales_lt( $src, $nothrow ) {
      list( $path, $value, $evalmatch ) = explode( ',', $src );
      if( $evalmatch ) {
         return '('.PHPTAL_TalesInternal::path( trim( $path ), $nothrow )." < ".PHPTAL_TalesInternal::path( trim( $value ), $nothrow ).')';
      } else {
         return '('.PHPTAL_TalesInternal::path( trim( $path ), $nothrow )." < '$value'".')';
      }
   }
   
   function phptal_tales_gt( $src, $nothrow ) {
      list( $path, $value, $evalmatch ) = explode( ',', $src );
      if( $evalmatch ) {
         return '('.PHPTAL_TalesInternal::path( trim( $path ), $nothrow )." > ".PHPTAL_TalesInternal::path( trim( $value ), $nothrow ).')';
      } else {
         return '('.PHPTAL_TalesInternal::path( trim( $path ), $nothrow )." > '$value'".')';
      }
   }
   
   function phptal_tales_lte( $src, $nothrow ) {
      list( $path, $value, $evalmatch ) = explode( ',', $src );
      if( $evalmatch ) {
         return '('.PHPTAL_TalesInternal::path( trim( $path ), $nothrow )." <= ".PHPTAL_TalesInternal::path( trim( $value ), $nothrow ).')';
      } else {
         return '('.PHPTAL_TalesInternal::path( trim( $path ), $nothrow )." <= '$value'".')';
      }
      
   }

   function phptal_support_removelinebreak( $string ) {
      return str_replace( array( "\r\n", "\r", "\n" ), '', $string );
   }
   function phptal_tales_removelinebreak( $src, $nothrow ) {
      return 'phptal_support_removelinebreak('.PHPTAL_TalesInternal::path( $src, $nothrow ).')';
   }
   
   function phptal_tales_gte( $src, $nothrow ) {
      list( $path, $value, $evalmatch ) = explode( ',', $src );
      if( $evalmatch ) {
         return '('.PHPTAL_TalesInternal::path( trim( $path ), $nothrow )." >= ".PHPTAL_TalesInternal::path( trim( $value ), $nothrow ).')';
      } else {
         return '('.PHPTAL_TalesInternal::path( trim( $path ), $nothrow )." >= '$value'".')';
      }
   }
   
   function phptal_tales_conditionalmatch( $src, $nothrow ) {
      list( $path, $match, $valuetrue, $valuefalse, $evalmatch ) = explode( ',', $src );
      if( $evalmatch ) {
         return '('.PHPTAL_TalesInternal::path( trim( $path ), $nothrow )." == ".PHPTAL_TalesInternal::path( trim( $match ), $nothrow )." ? '$valuetrue' : '$valuefalse')";
      } else {
         return '('.PHPTAL_TalesInternal::path( trim( $path ), $nothrow )." == '$match' ? '$valuetrue' : '$valuefalse')";
      }
   }
   
?>