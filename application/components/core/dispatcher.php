<?PHP
   
   // import required components
   import( 'core.settings' );
   
   // require session support
   import( 'core.session' );
   
   // load the required interfaces
   import( 'core.interfaces' );
   
   // load the language support
   import( 'core.i18n' );
   
   // load the basepage class
   import( 'pages.base' );
   
   // load string processing support
   import( 'string.string' );
   
   // import the templating controller
   import( 'templating.controller' );
   
   // define a few controller-only Exceptions
   class DispatcherException extends CriticalException {}
   class ViewLoadException extends DispatcherException {}
   
   /**
    * Provide exception-level handling for PHP errors
    * 
    * @author Oyvind Selbek <oyvind@selbek.com>
    */
   function ExceptionHandler( $errno, $errstr, $errfile = '', $errline = 0, $errcontext = array() ) {
      
      // forward the exception to a regular Controller-exception (standard critical exception)
      throw new DispatcherException( sprintf( 'An error occured in <b>%s</b> on line <b>%d</b>:<br /><pre>%s</pre>', $errfile, $errline, $errstr ), $errno );
      
      // return failure
      return false;
      
   }  
   
   // Set the above handler as the default handler
   set_error_handler( 'ExceptionHandler', error_reporting() );
   
   /**
    * Request Dispatcher class for web applications
    * 
    * @author Oyvind Selbek <oyvind@selbek.com>
    */
   class Dispatcher {
      
      // path stores
      static $viewPath = '';
      static $rootPath = '';
      static $tmplPath = '';
      static $execPath = '';
      
      // appName store
      static $appName = '';
      static $appHash = '';
      
      // add execstart
      static $execStart = 0;
      
      /**
       * Constructor, stores the application name
       *
       * @param string $ApplicationName The name of the webapp
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      public function __construct( $ApplicationName ) {
         
         // store the application-name in the class
         $this->setAppName( $ApplicationName );
         
         // start the session
         Session::initialize( $this->getAppHash() );
         
      }
      
      static function getPortal() {
         
         // import the current host name
         $hostname = $_SERVER['HTTP_HOST'];
         
         // validate the default domain name
         if( !$hostname ) $hostname = Settings::get( 'default', 'hostname' );
         
         // read the domain map
         $sites = Settings::getSection( 'domainMap' );
         
         // make sure our hostname is configured
         if( !isset( $sites[$hostname] ) ) {
            
            // throw an exception if not
            throw new DispatcherException( sprintf( 'Domain name not found in domainMap: %s', $hostname ), 1001 );
            
         } else {
         
            // read the hostname-config's portal
            return $sites[$hostname]['portal'];
            
         }
         
      }
      
       static function getSiteId() {
         
         // import the current host name
         $hostname = $_SERVER['HTTP_HOST'];
         
         // validate the default domain name
         if( !$hostname ) $hostname = Settings::get( 'default', 'hostname' );
         
         // read the domain map
         $sites = Settings::getSection( 'domainMap' );
         
         // make sure our hostname is configured
         if( !isset( $sites[$hostname] ) ) {
            
            // throw an exception if not
            throw new DispatcherException( sprintf( 'Domain name not found in domainMap: %s', $hostname ), 1001 );
            
         } else {
         
            // read the hostname-config's portalid
            return $sites[$hostname]['siteid'];
            
         }
         
      }
      
      static function getPortalId() {
         
         // import the current host name
         $hostname = $_SERVER['HTTP_HOST'];
         
         // validate the default domain name
         if( !$hostname ) $hostname = Settings::get( 'default', 'hostname' );
         
         // read the domain map
         $sites = Settings::getSection( 'domainMap' );
         
         // make sure our hostname is configured
         if( !isset( $sites[$hostname] ) ) {
            
            // throw an exception if not
            throw new DispatcherException( sprintf( 'Domain name not found in domainMap: %s', $hostname ), 1001 );
            
         } else {
         
            // read the hostname-config's portalid
            return $sites[$hostname]['customattr']['portalid'];
            
         }
         
      }
      
      static function getLoginGroup() {
         
         // import the current host name
         $hostname = $_SERVER['HTTP_HOST'];
         
         // validate the default domain name
         if( !$hostname ) $hostname = Settings::get( 'default', 'hostname' );
         
         // read the domain map
         $sites = Settings::getSection( 'domainMap' );
         
         // make sure our hostname is configured
         if( !isset( $sites[$hostname] ) ) {
            
            // throw an exception if not
            throw new DispatcherException( sprintf( 'Domain name not found in domainMap: %s', $hostname ), 1001 );
            
         } else {
         
            // read the hostname-config's portal
            return isset( $sites[$hostname]['logingroup'] ) ? $sites[$hostname]['logingroup'] : '';
            
         }
         
      }
      
      static function getTrackingCode() {
         
         // import the current host name
         $hostname = $_SERVER['HTTP_HOST'];
         
         // validate the default domain name
         if( !$hostname ) $hostname = Settings::get( 'default', 'hostname' );
         
         // read the domain map
         $sites = Settings::getSection( 'domainMap' );
         
         // make sure our hostname is configured
         if( !isset( $sites[$hostname] ) ) {
            
            // throw an exception if not
            throw new DispatcherException( sprintf( 'Domain name not found in domainMap: %s', $hostname ), 1001 );
            
         } else {
         
            // read the hostname-config's portal
            return isset( $sites[$hostname]['trackingcode'] ) ? $sites[$hostname]['trackingcode'] : '';
            
         }
         
      }
      
      static function getCustomAttr( $attr ) {
         
         // import the current host name
         $hostname = $_SERVER['HTTP_HOST'];
         
         // validate the default domain name
         if( !$hostname ) $hostname = Settings::get( 'default', 'hostname' );
         
         // read the domain map
         $sites = Settings::getSection( 'domainMap' );
         
         // make sure our hostname is configured
         if( !isset( $sites[$hostname] ) ) {
            
            // throw an exception if not
            throw new DispatcherException( sprintf( 'Domain name not found in domainMap: %s', $hostname ), 1001 );
            
         } else {
         
            // read the hostname-config's portal
            
            return isset( $sites[$hostname]['customattr'][$attr] ) ? $sites[$hostname]['customattr'][$attr] : '';
            
         }
         
      }
      
      static function getSiteSetting( $attr ) {
         
         // import the current host name
         $hostname = $_SERVER['HTTP_HOST'];
         
         // validate the default domain name
         if( !$hostname ) $hostname = Settings::get( 'default', 'hostname' );
         
         // read the domain map
         $sites = Settings::getSection( 'domainMap' );
         
         // make sure our hostname is configured
         if( !isset( $sites[$hostname] ) ) {
            
            // throw an exception if not
            throw new DispatcherException( sprintf( 'Domain name not found in domainMap: %s', $hostname ), 1001 );
            
         } else {
         
            // read the hostname-config's portal
            return isset( $sites[$hostname]['settings'][$attr] ) ? $sites[$hostname]['settings'][$attr] : '';
            
         }
         
      }
      
      static function getSiteAttr( $attr ) {
         
         // import the current host name
         $hostname = $_SERVER['HTTP_HOST'];
         
         // validate the default domain name
         if( !$hostname ) $hostname = Settings::get( 'default', 'hostname' );
         
         // read the domain map
         $sites = Settings::getSection( 'domainMap' );
         
         // make sure our hostname is configured
         if( !isset( $sites[$hostname] ) ) {
            
            // throw an exception if not
            throw new DispatcherException( sprintf( 'Domain name not found in domainMap: %s', $hostname ), 1001 );
            
         } else {
         
            // read the hostname-config's portal
            return isset( $sites[$hostname][$attr] ) ? $sites[$hostname][$attr] : $sites[$hostname][$attr];
            
         }
         
      }
      
        static function getOS() {
            
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
            $os_platform    =   "Unknown OS Platform";
            $os_array       =   array(
                                    '/windows nt 10/i'     =>  'Windows 10',
                                    '/windows nt 6.3/i'     =>  'Windows 8.1',
                                    '/windows nt 6.2/i'     =>  'Windows 8',
                                    '/windows nt 6.1/i'     =>  'Windows 7',
                                    '/windows nt 6.0/i'     =>  'Windows Vista',
                                    '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                                    '/windows nt 5.1/i'     =>  'Windows XP',
                                    '/windows xp/i'         =>  'Windows XP',
                                    '/windows nt 5.0/i'     =>  'Windows 2000',
                                    '/windows me/i'         =>  'Windows ME',
                                    '/win98/i'              =>  'Windows 98',
                                    '/win95/i'              =>  'Windows 95',
                                    '/win16/i'              =>  'Windows 3.11',
                                    '/macintosh|mac os x/i' =>  'Mac OS X',
                                    '/mac_powerpc/i'        =>  'Mac OS 9',
                                    '/linux/i'              =>  'Linux',
                                    '/ubuntu/i'             =>  'Ubuntu',
                                    '/iphone/i'             =>  'iPhone',
                                    '/ipod/i'               =>  'iPod',
                                    '/ipad/i'               =>  'iPad',
                                    '/android/i'            =>  'Android',
                                    '/blackberry/i'         =>  'BlackBerry',
                                    '/webos/i'              =>  'Mobile'
                                );
        
            foreach ($os_array as $regex => $value) { 
                if (preg_match($regex, $user_agent)) {
                    $os_platform    =   $value;
                }
            }   
            return $os_platform;
        
        }
      
      static function getExecPath() {
         
         $aliases = Settings::GetSection( 'aliases', array() );
         $execPath = isset( $aliases[Dispatcher::$execPath] ) 
                   ? $aliases[Dispatcher::$execPath] 
                   : Dispatcher::$execPath;
         
         return preg_split( '/\//', $execPath, 0, PREG_SPLIT_NO_EMPTY );
         
      }
      
      /**
       * Executes the Web Application
       *
       * @param string $ExecPath Optional root path to start in
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      public function Route( $ExecPath = null, $terminateOnCompletion = false ) {
         
         // enable error-tracking
         try {
            
            // store exec-start
            Dispatcher::$execStart = microtime( true );
            
            // setup the request params
            Dispatcher::setupRequest();
            
            // Detect whether we're presently offline
            $offline = Settings::get( 'application', 'offline', false );
            if( $offline ) throw new Exception( 'Offline', 500 );
            
            // forward ther request to __loadView() and echo it
            echo $this->__findView( Dispatcher::getExecPath() );
            
         // in case of *any* problem:
         } catch ( Exception $e ) {
            
            try {
               
               // draw a HTML content-type header
               header( 'Content-Type: text/html' );
               
               // create a TemplateController instance
               $engine = new TemplatingController();
               
               // find the correct template
               switch( $e->getCode() ) {
                  
                  // offline?
                  case 500:
                     $template = $engine->findTemplate( Dispatcher::$tmplPath, 'errors.offline' );
                     break;
                  
                  // other problems
                  default:
                     $template = $engine->findTemplate( Dispatcher::$tmplPath, 'errors.verybad' );
                     break;
                  
               }
               
               echo $engine->drawTemplate( $template, array( 'message' => $e->getMessage() ) );
               
            } catch( Exception $e ) {
               
               echo "Something really bad happened. Rest assured we're already working to fix this problem!<br /><br />When reporting this error, please include this message:<br /><b>".$e->getMessage()."</b>";
               
            }
            
         }
         
         // should we terminate?
         if( $terminateOnCompletion ) {
            
            // kill execution
            die();
            
         }
         
      }
      
      static function setupRequest() {
         
         // import the current root path
         $rootpath = getRootPath();
         
         // import the current host name
         $hostname = $_SERVER['HTTP_HOST'];
         
         // validate the default domain name
         if( !$hostname ) $hostname = Settings::get( 'default', 'hostname' );
         
         // read the domain map
         $sites = Settings::getSection( 'domainMap' );
         
         // make sure our hostname is configured
         if( !isset( $sites[$hostname] ) ) {
            
            // throw an exception if not
            throw new DispatcherException( sprintf( 'Domain name not found in domainMap: %s', $hostname ), 1001 );
            
         }
         
         // read the hostname-config
         $site = $sites[$hostname];
         
         // support for redirects in site configuration
         if( isset( $site['redirect'] ) && $site['redirect'] ) {
            relocate( $site['redirect'] );
            die();
         }
         
         // setup siteid if given for this site
         if( isset( $site['siteid'] ) ) {
            Session::set( 'siteid', $site['siteid'] );
         }
         
         // make sure we have an execPath
        if( is_null( $ExecPath ) ) {
            
            // do we have a defined execution path?
            if( isset( $_GET['dispatch'] ) ) {
               
               // if so, use the apache-exec-path
               $ExecPath = $_GET['dispatch'];
               
               // unset it
               unset( $_GET['dispatch'] );
               
            } else {
               
               // if not, start with a blank one
               $ExecPath = '';
               
            }
            
         }
         
         // find redirect settings
         $redirects = Settings::GetSection( 'redirects', array() );
         if( count( $redirects ) > 0 ) {
            $validatedPath = '/'.implode( '/', preg_split( "/\//", $ExecPath, 0, PREG_SPLIT_NO_EMPTY ) ).'/';
            foreach( $redirects as $match => $target ) {
               $matchlen = strlen( $match );
               if( substr( $validatedPath, 0, $matchlen ) == $match ) {
                  $ExecPath = implode( '/', preg_split( "/\//", $target.substr( $validatedPath, $matchlen ), 0, PREG_SPLIT_NO_EMPTY ) );
                  WebPage::$canonical = '/'.$ExecPath;
                  break;
               }
            }
         }
         
         // Set the various view/template/root paths
         Dispatcher::setRootPathSite( $rootpath, $site['siteroot'] );
         Dispatcher::setViewPathSite( $rootpath, $site['siteroot'] );
         Dispatcher::setTemplatePathSite( $rootpath, $site['siteroot'] );
         Dispatcher::$execPath = $ExecPath;
         
         
         if( Dispatcher::getPortal() == 'UP-DK' ){
            i18n::setLanguage('da_DK');
         }
         else{
            // Set the active language
            if( !i18n::setLanguage( Session::get( 'language', isset( $site['language'] ) ? $site['language'] : 'en_US' ) ) ) {
               i18n::setLanguage( isset( $site['language'] ) ? $site['language'] : 'en_US' );
            }
         }
         
      }
      
      static function setRootPathSite( $rootpath, $rootpathsite ) {
         
         Dispatcher::$rootPath = sprintf( '%s/sites/%s/webroot', $rootpath, $rootpathsite );
         
      }
      
      static function setViewPathSite( $rootpath, $viewpathsite ) {
         
         Dispatcher::$viewPath = sprintf( '%s/sites/%s/views', $rootpath, $viewpathsite );
         
      }
      
      static function setTemplatePathSite( $rootpath, $templatepathsite ) {
         
         Dispatcher::$tmplPath = sprintf( '%s/sites/%s/templates', $rootpath, $templatepathsite );
         
      }
      
      /**
       * Prepares a view by loading a required view to extend
       *
       * @param string $viewpath the dot-path to the original view
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      static function extendView( $viewpath ) {
         
         // prepare the viewpath
         if( strpos( $viewpath, '|' ) !== false ) {
            list( $siteprefix, $viewpath ) = explode( '|', $viewpath, 2 );
         } else { $siteprefix = ''; };
         
         $diskfile = implode( '/', explode( '.', $viewpath ) );
         if( func_num_args() > 1 ) {
            $args = func_get_args();
            array_shift( $args );
            array_unshift( $args, $diskfile );
            $diskfile = call_user_func_array( 'sprintf', $args );
         }
         
         // prepare the filename
         if( $siteprefix ) {
            $diskfile = sprintf( '%s/../../%s/views/%s.php', Dispatcher::$viewPath, $siteprefix, $diskfile );
         } else {
            $diskfile = sprintf( '%s/%s.php', Dispatcher::$viewPath, $diskfile );
         }
         
         // verify that the file exists
         if( file_exists( $diskfile ) ) {
            
            // include it, once
            include_once( $diskfile );
            
         }
         
      }

      /**
       * Sets the Application Name
       *
       * @param string $ApplicationName Application Name to set
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      public function setAppName( $ApplicationName ) {
         
         Dispatcher::$appName = $ApplicationName;
         Dispatcher::$appHash = md5( $ApplicationName );
         
      }
      
      /**
       * Gets the currently set Application Name
       *
       * @return string Application Name
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      public function getAppName() {
         
         // return the application name
         return Dispatcher::$appName;
         
      }
      
      /**
       * Gets the current Application Name hash
       *
       * @return string 32-char MD5-hash of the Application Name
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      public function getAppHash() {
         
         // return the application hash
         return Dispatcher::$appHash;
         
      }

      /**
       * Finds a given view-path on disk and creates a split Element/Param-list
       *
       * @param array $Elements Expanded list of elements to check for
       * @param array $Params List of parameters to call function with
       * @return returns the output buffer of the view
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      private function __findView( $Elements, $Params = array() ) {
         
         // Do we have a valid Element-path?
         if( ( $path = strtolower( implode( '/', $Elements ) ) ) != '' ) {
            
            // if so, check if it exists on disk as a direct filename
            if( file_exists( sprintf( '%s/%s.php', Dispatcher::$viewPath, $path ) ) ) {
               return $this->__loadView( sprintf( '%s.php', $path ), $Elements, $Params );
            }
            
            // check if it exists on disk as a folder default filename
            if( file_exists( sprintf( '%s/%s/default.php', Dispatcher::$viewPath, $path ) ) ) {
               return $this->__loadView( sprintf( '%s/default.php', $path ), $Elements, $Params );
            }
            
         } else {
            
            // just load the default root view
            return $this->__loadView( 'default.php', $Elements, $Params );
            
         }
         
         // pop off one item as a param
         array_unshift( $Params, array_pop( $Elements ) );
         
         // loop and try to find the module on the next go
         return $this->__findView( $Elements, $Params );
         
      }
      
      /**
       * Loads a view from disk and executes it
       *
       * @param string $DiskFile The relative path to the module in the viewroot
       * @param array $Elements The original array making up the element-path
       * @param array $Params The parameters to pass to the module once loaded
       * @return string The output buffer of the view
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      private function __loadView( $DiskFile, $Elements, $Params ) {
         
         // GO!
         try {
            
            // prepare the filename
            $diskfile = sprintf( '%s/%s', Dispatcher::$viewPath, $DiskFile );
            
            // prepare the function name
            $function = array_shift( $Params );
            
            // initialize the pageelements array
            $pageelements = array();
            
            // does the file exist on disk?
            if( file_exists( $diskfile ) ) {
               
               // if so, load it into ram
               include_once( $diskfile );
               
               // by default, we've found no valid class
               $validclass = null;
               
               // iterate all declared classes
               foreach( get_declared_classes() as $class ) {
                  
                  // look for a BasePage implementation
                  if( is_subclass_of( $class, 'BasePage' ) ) {
                     
                     // instanciate it
                     $object = new $class();
                     
                     // only use objects that are IView decendants
                     if( !$object instanceof IView ) continue;
                     
                     // set the validclass
                     $validclass = $class;
                     
                  }
                  
               }
                  
               // make sure we've found at least one valid class
               if( is_null( $validclass ) ) {
                  
                  // throw an exception
                  throw new ViewLoadException( sprintf( 'No classes implements IView in: %s', $DiskFile ) );
                  
               // execute it!
               } else {
                  
                  // import the validclass
                  $class = $validclass;
                  
                  // if a parameter is given that is NOT a function, pass 
                  // it to execute as a parameter and default to execute.
                  if( !method_exists( $object, $function ) ) {
                     if( $function != '' ) {
                        array_unshift( $Params, $function );
                     }
                     $function = 'execute';
                  }
                  
                  // if the method exists, call it with the params
                  if( $object instanceof IBaseView ) {
                     
                     // store the current parent dispatcher instance
                     $object->setDispatcher( $this );
                     
                     // run the initialization routine if available.
                     if( !$object->initialize() ) {
                        
                        // throw a security exception if we don't have the correct access to the page
                        throw new SecurityException( 'You do not have access to this page', 403 );
                        
                     }
                     
                  }
                  
                  // if the method exists, call it with the params
                  if( method_exists( $object, $function ) ) {
                     
                     // should we do XML rendering?
                     $allowXML = Settings::get( 'application', 'allowxml', false );
                     $renderXML = ( $allowXML && isset( $_GET['xml'] ) );
                     
                     /*
                     // do we have any files posted?
                     if( isset( $_FILES ) && count( $_FILES ) ) {
                        
                        // re-order the $_FILES array
                        $__FILES = array();
                        foreach( $_FILES as $key => $index ) {
                           foreach( $index as $field => $values ) {
                              foreach( $values as $name => $value ) {
                                 $__FILES[$key][$name][$field] = $value;
                              }
                           }
                        }
                        
                        // create a new direct pointer
                        $_FILES = $__FILES;
                        
                     }
                     */
                     
                     // is the view validated?
                     if( $object instanceof IValidatedView ) {
                        
                        // cleanup the runtime environment
                        unset( $GLOBALS['HTTP_GET_VARS'] );
                        unset( $GLOBALS['HTTP_ENV_VARS'] );
                        unset( $GLOBALS['HTTP_POST_VARS'] );
                        unset( $GLOBALS['HTTP_POST_FILES'] );
                        unset( $GLOBALS['HTTP_COOKIE_VARS'] );
                        unset( $GLOBALS['HTTP_SERVER_VARS'] );
                        unset( $GLOBALS['HTTP_SESSION_VARS'] );
                        
                        // retrieve a validation ruleset
                        $ruleset = $object->validate();
                        
                        // validate all input parameters using the given ruleset
                        $_GET = $this->validateInput( $_GET, $ruleset, $function, 'get' );
                        $_ENV = $this->validateInput( $_ENV, $ruleset, $function, 'env' );
                        $_POST = $this->validateInput( $_POST, $ruleset, $function, 'post' );
                        $_FILES = $this->validateInput( $_FILES, $ruleset, $function, 'files' );
                        $_COOKIE = $this->validateInput( $_COOKIE, $ruleset, $function, 'cookie' );
                        $_REQUEST = $this->validateInput( $_REQUEST, $ruleset, $function, 'request' );
                        
                        // validate the passed field parameters as well
                        $Params = $this->validateInput( $Params, $ruleset, $function );
                        
                     } else {
                        
                        $_GET = $this->removeSlashes( $_GET );
                        $_ENV = $this->removeSlashes( $_ENV );
                        $_POST = $this->removeSlashes( $_POST );
                        $_FILES = $this->removeSlashes( $_FILES );
                        $_COOKIE = $this->removeSlashes( $_COOKIE );
                        $_REQUEST = $this->removeSlashes( $_REQUEST );
                        
                     }
                     
                     // start buffering
                     ob_start();
                     
                     // check for SecurityException
                     try {
                        
                        // call $object->$function( $params[0], $params[1]... );
                        $result = Dispatcher::callObjectMethod( $object, $function, $Params );
                        
                        // does the page implement postalize?
                        if( $object instanceof IPostalize ) {
                           
                           // run the postilization routine if available.
                           if( !$object->postalize() ) {
                              
                              // throw a security exception if we don't have the correct access to the page
                              throw new SecurityException( 'You do not have access to this page', 403 );
                              
                           }
                           
                        }
                        
                        // end buffering and get it
                        $content = ob_get_clean();
                        
                        // retreieve the template
                        $template = $object->getTemplate();
                        
                        // retrieve the engine
                        $engine = $object->getTemplateEngine();
                        
                     } catch( SecurityException $exception ) {
                        
                        // define errormessage and errorcode
                        $extrafields['errormessage'] = $exception->getMessage();
                        $extrafields['errorcode'] = $exception->getCode();
                        
                        // define the template
                        $template = 'errors.no_access';
                        $content = '';
                        $engine = 'phptal';
                        
                     }

                     // allow no template processing
                     if( !$template ) {
                        
                        // check if the object is a custom render view
                        if( $object instanceof ICustomRenderView ) {
                           
                           // apply a custom render view
                           return $object->render( $content, $result );
                           
                        } else {
                        
                           // just return the output
                           return $content;
                           
                        }
                        
                     } else {
                        
                        // retrieve the fields
                        $fields = $object->getFields();
                        
                        // append extrafields
                        if( is_array( $extrafields ) ) {
                           $fields = array_merge( $fields, $extrafields );
                        }
                        
                        // store the stdout-buffer
                        $fields['content'] = $content;
                        
                        // store the result
                        // add the exectime and version
                        $fields['statistics'] = util::MergeArrays( $fields['statistics'], array(
                           'exectime' => round( microtime(true) - Dispatcher::$execStart, 3 ),
                           'version'  => Settings::get( 'site', 'version', '1.0' )
                        ) );
                        
                        // store the year/month/day in the request information
                        $fields['request'] = util::MergeArrays( $fields['request'], array(
                           'date' => array(
                              'full' => date('Y-m-d H:i:s'),
                              'fulldate' => date('Y-m-d'),
                              'year' => date('Y'),
                              'month' => date('m'),
                              'day' => date('d'),
                              'fulltime' => date('H:i:s'),
                              'hour' => date('H'),
                              'minute' => date('i'),
                              'second' => date('s'),
                           ),
                           'result' => $result,
                        ) );
                        
                        // do XML rendering?
                        if( $renderXML ) {
                           
                           return Dispatcher::drawXMLDocument( $fields );
                           
                        } else {
                           
                           // create a TemplateController instance and draw it
                           return Dispatcher::drawTemplate( $engine, $template, $fields );
                           
                        }
                        
                     }
                        
                  } else {
                     
                     // throw an exception
                     throw new ViewLoadException( sprintf( 'No known methods to execute in: %s', $class ) );
                     
                  }
                  
               }
               
               // throw an exception
               throw new ViewLoadException( sprintf( 'No classes implements IView in: %s', $DiskFile ) );
               
            } else {
               
               // throw an exception
               throw new ViewLoadException( sprintf( 'File does not exist on disk: %s', $DiskFile ) );
               
            }
            
         // catch all problems
         } catch( Exception $exception ) {
            
            // end any ongoing output buffers
            while( ob_get_level() > 0 ) ob_end_clean();
            
            $tracedata = $exception->getTrace();
            $trace = '<table width="100%"><tr><th>id</th><th>file</th><th>line</th><th>code</th></tr>';
            foreach( $tracedata as $id => $traceline ) {
               
               $tracelineid = count( $tracedata ) - $id;
               
               $args = array();
               foreach( $traceline['args'] as $argument ) {
                  $buffer = SecretInformation::filterString( trim( str_replace( 
                               ' ', '&nbsp;',
                               print_r( $argument, true ) 
                            ) ) );
                  $args[] = strlen( $buffer ) < 640 ? $buffer : substr( $buffer, 0, 640 ).'<a href="javascript:;" onclick="$(this).parent().html( $(\'#codetrace_'.$tracelineid.'\').html() );">…</a><div style="display:none;" id="codetrace_'.$tracelineid.'">'.utf8_encode(htmlentities(utf8_decode($buffer),null, null, false)).'</div>';
               }  $args = count( $args ) > 0 ? (count( $args ) == 1 && !is_array( $traceline['args'][0] ) && !is_object( $traceline['args'][0] ) ? '&nbsp;'.( is_numeric( $args[0] ) ? '<span style="color:red;">'.$args[0].'</span>' : '<span style="color:orange">"'.$args[0].'"</span>' ).'</b>&nbsp;' : nl2br( '<table style="margin:0"><tr><td>'.implode( '</td><td>', $args ).'</td></tr></table>' ) ) : '';
               
               $trace.= sprintf( '<tr><td style="text-align:right">#%d</td><td>%s</td><td style="text-align:right">%d</td><td><em style="color:navy">%s</em>%s<b style="color:green">%s</b>(%s)</td></tr>',
                           $tracelineid,
                           $traceline['file'], 
                           $traceline['line'],
                           $traceline['class'],
                           $traceline['type'],
                           $traceline['function'],
                           $args
                        );
            }
            $trace .= '</table>';
            
            // was this a security problem?
            if( $exception instanceof SecurityException  ) {
               
               /*
               // redirect to the frontpage
               relocate( '/' );
               
               // prepare the template-fields
               $fields = array( 
                  'content' => $exception->getMessage(),
                  'trace' => 'Please return to the front page to login before accessing this page!',
                  'code' => 0,
                  'file' => 'SecurityCheck',
                  'line' => 1,
               );
               */
               // prepare the template-fields
               $fields = array( 
                  'content' => 'SECURITYEXCEPTION: '.$exception->getMessage(),
                  'visual' => Login::isAdmin() ? $trace : 'N/A',
                  'trace' => $exception->getTraceAsString(),
                  'code' => $exception->getCode(),
                  'file' => $exception->getFile(),
                  'line' => $exception->getLine(),
               );
               
            } else {
               
               // prepare the template-fields
               $fields = array( 
                  'content' => $exception->getMessage(),
                  'visual' => Login::isAdmin() ? $trace : 'N/A',
                  'trace' => $exception->getTraceAsString(),
                  'code' => $exception->getCode(),
                  'file' => $exception->getFile(),
                  'line' => $exception->getLine(),
               );
               
            }
            
            // draw a HTML content-type header
            header( 'Content-Type: text/html; charset=UTF-8' );
            
            // create a TemplateController instance
            $engine = new TemplatingController();
            
            // expand the template name
            $template = $engine->findTemplate( Dispatcher::$tmplPath, 'errors.default' );
            
            // return the rendered template
            return $engine->drawTemplate( $template, $fields );
            
         }

      }
      
      public function removeSlashes( $element ) {
         
         if( is_array( $element ) ) {
            foreach( $element as $key => $value ) {
               $element[$key] = $this->removeSlashes( $value );
            }
            return $element;
         } else {
            return stripslashes( $element );
         }
         
      }
      
      /**
       * Highly efficient object method caller.
       * At least up to PHP 5.1, calling call_user_func_array is slower than actually
       * counting params, then calling the function itself with the given parameters.
       * 
       * @param object $object The object to call a method on
       * @param string $method The method to call on the object
       * @param array $params An array of parameters to pass
       * @return mixed The return value of the called method
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      static function callObjectMethod( $object, $method, $params = array() ) {
         
         switch( count( $params ) ) {
            case 0:
               return $object->{$method}();
            case 1:
               return $object->{$method}( $params[0] );
            case 2:
               return $object->{$method}( $params[0], $params[1] );
            case 3:
               return $object->{$method}( $params[0], $params[1], $params[2] );
            case 4:
               return $object->{$method}( $params[0], $params[1], $params[2], $params[3] );
            case 5:
               return $object->{$method}( $params[0], $params[1], $params[2], $params[3], $params[4] );
            default:
               return call_user_func_array( array( $object, $method ), $params);
            break;
         }
         
      }
      
      /**
       * IO-validation engine based on ruleset matching.
       *
       * @param array $variable The input variable to filter
       * @param array $ruleset The ruleset, key'ed by rulename
       * @param string $function The base ruleset to filter
       * @param string $rulename The rulename matching the variable (GET, POST or FIELDS etc.)
       * @return array A filtered array of data based on the given ruleset at function, rulename.
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      private function ValidateInput( $variable, $ruleset, $function, $rulename = 'fields' ) {
         
         // we can only validate input arrays
         if( !is_array( $variable ) ) return array();
         
         // do we have a rule for this array?
         if( isset( $ruleset[strtolower($function)][strtolower($rulename)] ) ) {
            
            // start with an empty initial ruleset
            $result = array();
            
            // check the rulename
            switch( $rulename ) {
               
               // different handling for field lists
               case 'fields':
                  
                  // iterate over the variable and extract the value
                  foreach( $variable as $value ) {
                     
                     // did we run out of validation rules?
                     if( !count( $ruleset[$function][$rulename] ) ) return $result;
                     
                     // append the validated field output to the result-array
                     $result = $this->ValidateFieldByType( $result, array_shift( $ruleset[$function][$rulename] ), null, $value );
                     
                  }
                  break;
                  
               default:
                  
                  if( isset( $ruleset[$function][$rulename] ) ) {
                     
                     $result = $this->ValidateFieldArray( $result, $variable, $ruleset[$function][$rulename] );
                     
                  }
                  
                  break;
               
            }
            
            // return the result
            return $result;
            
         // if we have no ruleset...
         } else {
            
            // return an empty array
            return array();
            
         }
         
      }
      
      /**
       * Part of the validation framewor for input data.
       * Handles validation of an array of validation rules/data.
       *
       * @param string $results The results so far for this level
       * @param array $variable The list of variable data to filter
       * @param array $ruleset The list of rules to filter by
       * @return array A filtered list of arrays/values.
       */
      private function ValidateFieldArray( $results, $variable, $ruleset ) {
         
         if( is_array( $ruleset ) && !is_array( $variable ) ||
            !is_array( $ruleset ) &&  is_array( $variable ) ) return $results;
         
         foreach( $ruleset as $key => $type ) {
               
            if( is_array( $type ) ) {
               
               if( isset( $variable[$key] ) && isset( $ruleset[$key] ) ) {
                  
                  $results[$key] = $this->ValidateFieldArray( $results, $variable[$key], $ruleset[$key] );
                  
               }
               
            } else {
               
               if( isset( $variable[$key] ) ) {
                  
                  $results = $this->ValidateFieldByType( $results, $type, $key, $variable[$key] );
                  
               }
               
            }
            
         }
         
         return $results;
         
      }
      
      private function ValidateFieldValue( $type, $value ) {
         
         // shift an item off the beginning of the rule-stack
         switch( $type ) {
            
            // check the type of rule, and apply it
            default:
            case VALIDATE_STRING:
            case VALIDATE_DEFAULT:
               $result = String::saferHTML( stripslashes( $value ) );
               break;
            case VALIDATE_INTEGER:
               $result = (int) $value;
               break;
            case VALIDATE_ESCAPE:
               $result = DB::escape( stripslashes( $value ) );
               break;
            case VALIDATE_FLOAT:
               $result = (float) str_replace( ',', '.', $value );
               break;
            case VALIDATE_ARRAY:
                $result = $value;
                break;
                
               
         }
         
         return $result;
            
      }
      
      /**
       * Part of the validation framework for input data.
       * Handles validation of data values based on a given ruleset.
       *
       * @param array $results The results so far for this level
       * @param integer $type The variable type (constant set)
       * @param string $field The field to filter
       * @param mixed $value The value to filter
       * @return mixed The filtered value as specified by each data type
       */
      private function ValidateFieldByType( $results, $type, $field, $value ) {
         
         if( is_array( $type ) ) {
            
            $result = $this->ValidateFieldValue( $type['type'], $value );
            
            switch( $type['type'] ) {
               
               case VALIDATE_FLOAT:
               case VALIDATE_INTEGER:
                  if( isset( $type['min'] ) ) {
                     $result = max( array( $type['min'], $result ) );
                  }
                  if( isset( $type['max'] ) ) {
                     $result = min( array( $type['max'], $result ) );
                  }
                  break;
               
               case VALIDATE_STRING:
                  if( isset( $type['min'] ) ) {
                     $result = str_pad( $result, $type['min'] );
                  }
                  if( isset( $type['max'] ) ) {
                     $result = substr( $result, 0, $type['max'] );
                  }
                  break;
               
            }
            
         } else {
            
            $result = $this->ValidateFieldValue( $type, $value );
            
         }
         
         // do we have a field?
         if( empty( $field ) ) {
            
            // no? add with no key
            $results[] = $result;
            
         } else {
            
            // use the field as the key
            $results[$field] = $result;
            
         }
         
         // return the result-set
         return $results;
         
      }
      
      static function drawXMLDocument( $fields, $roottag = 'fields' ) {
         
         header( "content-type: text/xml" );
         echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
         echo "<$roottag>\n";
         
         Dispatcher::drawXMLNode( $fields );
         
         echo "</$roottag>\n";
         die();
         
      }
      
      static function drawXMLNode( $fields, $level = 1 ) {
         
         foreach( $fields as $field => $value ) {
            
            /*
            if( $field == 'password' ) {
               echo "<skipped itemid=\"$field\" />\n";
               continue;
            }
            */
            
            if( is_numeric( $field ) ) {
               $field = 'iterateableitem'.$field;
            }
            
            if( $field == '' ) {
               $field = 'unknown';
            }
            
            echo str_repeat( "   ", $level );
            
            echo "<$field>";
            
            if( is_array( $value ) || is_object( $value ) ) {
               
               echo "\n";
               
               Dispatcher::drawXMLNode( $value, $level + 1 );
               
               echo str_repeat( "   ", $level );
               
            } else {
               
               if( !is_null( $value ) ) {
                  
                  echo print_r( str_replace( array( '&', '<', '>' ), array( '&amp;', '&lt;', '&gt;' ), $value ), true );
                  
               }
               
            }
            
            echo "</$field>\n";
            
         }
         
      }
      
      /**
       * Draws a template using the specified engine, expanding the
       * template name automatically using Dispatcher's path-system
       *
       * @param string $engine The engine to use
       * @param string $template The template to draw
       * @param array $fields The fields to include
       * @return string The rendered template
       */
      static function drawTemplate( $engine, $template, $fields = array() ) {
         
         // create a TemplateController instance and draw it
         $controller = new TemplatingController( $engine );
         
         // expand the template name
         $template = $controller->findTemplate( Dispatcher::$tmplPath, $template );
         
         // return the rendered template
         return $controller->drawTemplate( $template, $fields );
         
      }
      
   }
   
?>