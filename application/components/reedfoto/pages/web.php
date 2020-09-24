<?PHP
   
   import( 'reedfoto.login' );
   import( 'cache.engine' );
   import( 'session.usersessionarray' );
   import( 'website.menu' );
   
   class WebPage extends BasePage implements IPostalize {
      
      static $canonical = '';
      
      public function Initialize( $noLoginCheck = false ) {
         
         // forward the parent request
         if( !parent::Initialize() ) return false;
         
         // draw default http headers
         $this->drawDefaultHTTPHeaders();
         
         // login check disabled?
         if( !$noLoginCheck ) {
            
            // is this a login? if so, forward the request prior to continuing
            if( isset( $_POST['username'] ) && isset( $_POST['password'] ) ) {
               
               // attempt to login using the username and password given to us
               Login::byUsernameAndPassword(
                  $_POST['username'], 
                  $_POST['password'], 
                  isset( $_POST['autologin'] ) ? true : false 
               );
               
            } elseif( !Login::isLoggedIn() && isset( $_COOKIE['autologin'] ) ) {
               
               // attempt to login with secure autologin token
               Login::bySecureToken( $_COOKIE['autologin'] );
               
            }
            
         }
         
         // store the current hostname
         $this->request = util::MergeArrays( $this->request, array(
            'hostname' => $_SERVER['HTTP_HOST'], 
            'staticroot' => WebsiteHelper::staticBaseUrl(),
            'systemroot' => WebsiteHelper::rootBaseUrl( false ),
            'adminroot' => WebsiteHelper::adminBasePath(),
            'referer' => isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '',
         ) );
         
         // detect current language settings
         $langcode = i18n::languageCode();
         $langshort = current( explode( '_', $langcode ) );
         if( $langshort == 'nb' ) $langshort = 'no';
         if( $langshort == 'en' ) $langshort = 'us';
         if( $langshort == 'ja' ) $langshort = 'jp';
         if( $langshort == 'da' ) $langshort = 'dk';
         if( $langshort == 'sv' ) $langshort = 'se';
         
         // setup the structure in the page
         $this->i18n = array(
            'language' => $langcode,
            'country' => $langshort,
         );
         
         // re-hash the pages logindata
         $this->updatePageSessionData();
         
         // store the application version
         $this->version = Settings::get( 'application', 'version', 1 );
         
         // customattr support in templates
         $domainSettings = Settings::Get( 'domainMap', $_SERVER['HTTP_HOST'], array() );
         if( isset( $domainSettings['customattr'] ) ) {
            if( !empty( $domainSettings['customattr'] ) ) {
               if( is_array( $domainSettings['customattr'] ) ) {
                  foreach( $domainSettings['customattr'] as $customattr => $value ) {
                     if( !empty( $customattr ) && trim( $customattr ) ) {
                        $this->$customattr = $value;
                     }
                  }
               } elseif( !empty( $domainSettings['customattr'] ) ) {
                  $this->$domainSettings['customattr'] = true;
               }
            }
         }
         
         // store the current template folder
         $this->templates = $domainSettings['template'];
         
         // forward any parent constructor and return
         return true;
         
      }
      
      public function updatePageSessionData() {
         
         // store session data in the template
         $session = Login::Data();
         $session['usertype'] = array( $session['usertype'] ? $session['usertype'] : 'normal' => true );
         $this->session = $session;
         
      }
      
      public function setActiveSection( $activeSection = false ) {
         
         $this->activeSection = $activeSection;
         
      }
      
      public function drawDefaultHTTPHeaders() {
         
         // make sure we're not an instance of AllowHTTPCache
         if( $this instanceof AllowHTTPCache ) return false;
         
         // output default cache-control headers (no caching allowed)
         header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
         header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
         header( 'Cache-Control: no-store, no-cache, must-revalidate' );
         header( 'Cache-Control: post-check=0, pre-check=0', false );
         header( 'Pragma: no-cache' );
         
         // return success
         return true;
         
      }
      
      public function quickRoute( $viewpath, $classname, $params = array(), $method = 'Execute' ) {
         
         $result = parent::quickRoute( $viewpath, $classname, $params, $method );
         
         if( isset( $this->sitesection ) ) {
            
            $this->activeSection = key( $this->sitesection );
            
         }
         
         return $result;
         
      }
      
      public function Postalize() {
         
         // is there a canonical path to this page?
         if( WebPage::$canonical ) {
            
            $this->canonical = WebPage::$canonical;
            
         }
         
         // if we have an active section, and this is not a json-page
         if( $this->activeSection && !($this instanceof JSONPage) ) {
            
            // store the active section for the template to access
            $this->sitesection = array( $this->activeSection => true );
            
         }
         
         // return success!
         return true;
         
      }
      
   }
   
   interface AllowHTTPCache {}
   
?>