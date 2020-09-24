<?PHP
   
   import( 'website.login' );
   import( 'website.menu' );
   import( 'website.cart' );
   
   import( 'cache.engine' );
   
   import( 'session.usersessionarray' );
   // DISABLED: fastpass (search for more hits)
   // import( 'services.getsatisfaction.fastpass' );
   
   class WebPage extends BasePage implements IPostalize {
      
      static $canonical = '';
      
      public function Initialize( $noLoginCheck = false ) {
         
         // forward the parent request
         if( !parent::Initialize() ) return false;
         
         // draw default http headers
         $this->drawDefaultHTTPHeaders();
         
         // login check disabled?
         if( !$noLoginCheck ) {
            
            // fetch apache headers
            $headers = apache_request_headers();
            if( isset( $headers['X-AuthToken'] ) ) {
               if( !Login::bySecureToken( $headers['X-AuthToken'] ) ) {
                  return false;
               }
            }
            
            // check for auth in GET
            if( isset( $_GET['X-AuthToken'] ) ) {
               if( !Login::bySecureToken( $_GET['X-AuthToken'] ) ) {
                  return false;
               }
            }
            
            // check for auth in POST
            if( isset( $_POST['X-AuthToken'] ) ) {
               if( !Login::bySecureToken( $_POST['X-AuthToken'] ) ) {
                  return false;
               }
            }
            
            // is this a login? if so, forward the request prior to continuing
            if( isset( $_POST['email'] ) && isset( $_POST['password'] ) ) {
               
               // attempt to login using the username and password given to us
               Login::byPortalUsernameAndPassword(
                  Dispatcher::getPortal(),
                  $_POST['email'], 
                  $_POST['password'], 
                  isset( $_POST['autologin'] ) ? true : false 
               );
               
            } elseif( !Login::isLoggedIn() && isset( $_COOKIE['ef3autologin'] ) ) {
               
               // attempt to login with secure autologin token
               Login::bySecureToken( $_COOKIE['ef3autologin'] );
               
            }
            
         }
         
         // fix for shared header on Safari
		 
         $ua = $_SERVER['HTTP_USER_AGENT'];
         if( stripos( $ua, 'Safari' ) !== false && stripos( $ua, 'Chrome' ) === false && stripos( $ua, 'Version' ) !== false  && ( $_SERVER['HTTP_HOST'] == 'www.eurofoto.no' || $_SERVER['HTTP_HOST'] == 'eurofoto.no') ) {
          	
	         if( isset( $_GET['storeId'] ) ) {
         		
       			setCookie( 'safarifix', '1' ); // , time()+60*60*24*30 does it stick forever?
       			
	         } elseif( !Login::isLoggedIn() ) {
         		
	          	if( !isset( $_COOKIE['safarifix'] ) ) {
	          		
	          		setCookie( 'safarifix', '1' ); // , time()+60*60*24*30 does it stick forever?
       				header( 'location: https://www.japanphoto.no/EFRedirect?redirectUrl=http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] );
	          		die();
	          		
	          	}	
	         }
         }
         
         // store the current hostname
         $this->request = util::MergeArrays( $this->request, array(
            'hostname' => $_SERVER['HTTP_HOST'], 
            'staticroot' => WebsiteHelper::staticBaseUrl(),
            'systemroot' => WebsiteHelper::rootBaseUrl( false ),
            'adminroot' => WebsiteHelper::adminBasePath(),
            'referer' => isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '',
            'params' => array(
               'get' => $_GET,
               'post' => $_POST,
            ),
         ) );
         
         // store the templateroot
         $this->templateroot = rtrim( Dispatcher::$tmplPath, '/' );
         
         // fetch and store the current siteid
         $this->siteid = Session::get( 'siteid', 1 );
         
         // store the current portal
         $portal = Dispatcher::getPortal();
         $this->portal = $portal ? $portal : 'EF-997';
         
         if( $this->portal == 'EF-997' ){
            $this->jplogin = 'true';
         }
         
         
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
         
         /*
         // automated login, getsatisfaction
         $fastpass = GetSatisfactionFastpass::Fetch();
         if( $fastpass ) {
            $this->fastpass = $fastpass;
         }
         */
         
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
         
         // Store cart
         $cart = new Cart();
         $cart = $cart->asArray();
         if( is_array( $cart ) ) {
            
            foreach( $cart as $key => $val ) {
               if( is_array( $val ) && empty( $val ) ) {
                  $cart[$key] = null;
               }
            }
            
            $this->cart = $cart;
            
         }
         
         
         // fetch user preferences
         $userpreferences = CacheEngine::read( sprintf( 'userpreferences_%s', Login::userid() ) );
         
         if ( $userpreferences === null ) {

            /*$userpreferences = array();
            
            foreach ( DB::query( 'select key, value from site_user_preferences where userid = ?', Login::userid() )->fetchAll() as $preference ) {
               
               $userpreferences[ $preference[ 0 ] ] = $preference[ 1 ];
                          
            }*/
            
            $user = new User ( Login::userid() );
            
            $userpreferences = $user->getUserPreferences();
            
            CacheEngine::write( sprintf( 'userpreferences_%s', Login::userid() ), $userpreferences );
            
         }
         
         $this->userpreferences = $userpreferences;
         
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
      
      static function Exists( $uri ) {
         
         // load the current cacheengine instance through the factory
         $cacheengine = CacheEngineFactory::current();
         
         // check whether this page exists by checking for a cached item
         if( $cacheengine->read( sprintf( 'exists:%s', $uri ) ) == '' ) return false;
         if( $cacheengine->read( sprintf( 'data:%s', $uri ) ) == '' ) return false;
         
         // page exists
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