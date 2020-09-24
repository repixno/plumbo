<?PHP
   
   // Import qualifying interface for template engines
   import( 'templating.interface' );
   
   // Load any available configuration
   config( 'common.templating' );
   
   class TemplateException extends ImportException {}
   
   /**
    * Handles drawing of templates and selecting the right template class
    * 
    * @author Oyvind Selbek <oyvind@selbek.com>
    */
   class TemplatingController {
      
      /**
       * @var ITemplateEngine Holds the current engine instance
       */
      private $engine;
      
      /**
       * Constructs the class and makes an instance of the selected engine
       *
       * @param string $type An available templating class
       */
      public function __construct( $type = 'html' ) {
         
         // import the selected template engine
         import( 'templating.%s', $type );
         
         // format the expected classname to load
         $engine = sprintf( 'TemplateEngine_%s', $type );
         
         // make sure we have loaded the engine
         if( class_exists( $engine ) ) {
            
            // attempt to make an instance
            $this->engine = new $engine();
            
         } else {
            
            // throw an exception about the missing template file
            throw new TemplateException( sprintf( 'Could not create template engine!', 1200 ) );
            
         }
         
      }
      
      /**
       * Locates a template filename on disk "import-style"
       *
       * @param string $templatename dot-separated template name
       */
      public function findTemplate( $templatepath, $templatename, $extension = 'html' ) {
         
         // expand the templatepath
         $templatename = str_replace( '.', '/', $templatename );
         
         // import the current host name
         $hostname = isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : Settings::Get( 'default', 'hostname', 'eurofoto.no' );
         
         // read the domain map
         $sites = Settings::getSection( 'domainMap' );
         
         // make sure our hostname is configured
         if( !isset( $sites[$hostname] ) ) {
            
            // throw an exception if not
            throw new DispatcherException( sprintf( 'Domain name not found in domainMap: %s', $hostname ), 1001 );
            
         }
         
         // read the hostname-config
         $site = $sites[$hostname];
         
         // find the full templatepath
         $fulltemplatepath = sprintf( '%s/%s/%s.%s', $templatepath, $site['template'], $templatename, $extension );
         
         // verify that the template file exists on disk, fallign back if required
         if( !file_exists( $fulltemplatepath ) && isset( $site['fallback'] ) ) {
            
            // find the full templatepath
            $fulltemplatepath = sprintf( '%s/%s/%s.%s', $templatepath, $site['fallback'], $templatename, $extension );
            
         }
         
         // return the name of the template file
         return $fulltemplatepath;
         
      }
      
      /**
       * Locates and draws a template on disk using the selected template engine
       *
       * @param string $filename The filename of the template to draw
       * @param array $variables An array of arguments to pass to the templates
       * @return string The rendered templates output
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      public function drawTemplate( $filename, $variables ) {
         
         // add some system variables to the template
         if( isset( $_SERVER['HTTP_REFERER'] ) ) $variables['httpReferer'] = $_SERVER['HTTP_REFERER'];
         if( isset( $_SERVER['HTTPS'] ) ) $variables['https'] = $_SERVER['HTTPS'];
         
         // make sure our template engine is valid
         if( $this->engine instanceof ITemplateEngine ) {
            
            // make sure the template exists
            if( file_exists( $filename ) ) {
               
               // execute the template engine and return the content
               return $this->engine->execute( $filename, $variables );
               
            } else {
               
               // throw an exception about the missing template file
               throw new TemplateException( sprintf( 'Template was not found on disk: %s', $filename ), 1201 );
               
            }
            
         } else {
            
            // throw an exception about the missing template file
            throw new TemplateException( sprintf( 'Could not create template engine!', 1200 ) );
            
         }
         
      }
      
   }
   
?>