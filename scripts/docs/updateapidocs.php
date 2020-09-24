#!/usr/bin/php -q
<?PHP

   $_SERVER['HTTP_HOST'] = 'eurofoto.no';
   $_SERVER['HTTP_USER_AGENT'] = '';
   $_SERVER['ALL_HTTP'] = '';
   $_SERVER['HTTP_ACCEPT'] = '*/*';
      
   // set up the environment
   chdir( dirname( __FILE__ ) );
   include '../../bootstrap.php';
   config( 'website.config' );
   
   import( 'system.cli' );
   import( 'templating.controller' );
   
   /**
    * API Documentation Updater
    * 
    * Parses the API codebase and generates documentation
    *
    * @author Oyvind Selbek <oyvind@selbek.com>
    */
   class APIDocUpdater extends Script {
      
      static $rootpath = '';
      static $outputto = '';
      public $methods = array();
      
      public function Main() {
         
         echo "Parsing API for documentation...";
         $this->DocumentFolder( sprintf( '%s/%s', getRootPath(), APIDocUpdater::$rootpath ) );
         echo "OK\n";
         
         // util::Debug( $this->methods );
         $this->DumpMethodsToXHTML();
         echo "API Documentation Updated.\n";
         
      }
      
      private function DocumentFolder( $folder ) {
         
         foreach( new DirectoryIterator( $folder ) as $item ) {
            
            if( substr( $item, 0, 1 ) == '.' ) continue;
            
            if( $item->isDir() ) {
               
               $this->DocumentFolder( sprintf( '%s/%s', $folder, $item ) );
               
            } elseif( $item->isFile() ) {
               
               $this->DocumentFile( sprintf( '%s/%s', $folder, $item ) );
               
            }
            
         }
         
      }
      
      private function DumpMethodsToXHTML() {
         
         ksort( $this->methods, SORT_STRING );
         
         $outputpath = sprintf( '%s/%s', getRootPath(), APIDocUpdater::$outputto );
         $apitemplate = sprintf( '%s/%s/template/apipage.html', getRootPath(), APIDocUpdater::$outputto );
         
         $controller = new TemplatingController( 'phptal' );
         $groupedmethods = array();
         foreach( $this->methods as $methodname => $method ) {
            
            echo " * Processing $methodname...";
            
            $method['baseurl'] = WebsiteHelper::rootBaseUrl(false);
            
            file_put_contents( 
               sprintf( '%s/%s.html', $outputpath, util::urlize( $methodname, '.' ) ),
               $controller->drawTemplate( $apitemplate, $method )
            );
            
            list( $groupsection ) = explode( '.', $methodname );
            $groupedmethods[$groupsection]['name'] = $groupsection;
            $groupedmethods[$groupsection]['methods'][] = $method;
            
            echo "OK\n";
            
         }
         
         $data = array( 
            'baseurl' => WebsiteHelper::rootBaseUrl(false),
            'methods' => $groupedmethods,
         );
         $apitemplate = sprintf( '%s/%s/template/index.html', getRootPath(), APIDocUpdater::$outputto );
         file_put_contents( 
            sprintf( '%s/index.html', $outputpath ),
            $controller->drawTemplate( $apitemplate, $data )
         );
         
      }
      
      private function DocumentFile( $file ) {
         
         /*
         $oldclasses = get_declared_classes(); include $file;
         $newclasses = array_diff( get_declared_classes(), $oldclasses );
         
         if( count( $newclasses ) ) foreach( $newclasses as $class ) {
            
            if( is_subclass_of( $class, JSONPage ) ) {
               
               $rc = new ReflectionClass( $class );
               print_r( $rc->getDocComment() );
               print_r( $rc->getMethods( ReflectionMethod::IS_PUBLIC ) );
               
            }
            
         }
         */
         
         $expr = "/((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\/\/.*))/"; 

         $comments = file_get_contents( $file );
         
         preg_match_all($expr, $comments, $matches ); //capture the comments 
         
         if( isset( $matches[0] ) ) {
            foreach( $matches[0] as $comment ) {
               
               $comment = trim( $comment );
               
               if( substr( $comment, 0, 3 ) != '/**' ) continue;
               $commentlines = explode( "\n", $comment );
               array_shift( $commentlines ); array_pop( $commentlines );
               
               $method = array();
               $attribute = 'description';
               
               foreach( $commentlines as $commentline ) {
                  
                  $commentline = trim( $commentline );
                  
                  if( substr( $commentline, 0, 2 ) != '* ' ) continue;
                  $commentline = substr( $commentline, 2 );
                  
                  // is this a doc-instruction?
                  if( $commentline[0] == '@' ) {
                     
                     // skip non-API-directive doc-instructions
                     if( substr( $commentline, 0, 5 ) == '@api-' ) {
                        
                        list( $attribute, $key, $data ) = explode( ' ', substr( $commentline, 5 ), 3 );
                        $attribute = str_replace( '-', '_', $attribute );
                        
                        if( $data ) {
                           $method[$attribute][] = array( 'key' => $key, 'data' => $data );
                        } else {
                           $method[$attribute] = $key;
                        }
                        
                     }
                     
                  // if not, just append it to the trimmed buffer.
                  } else {
                  
                     $method[$attribute] = trim( $method[$attribute] ."\n".$commentline );
                     
                  }
                  
               }
               
               // finally, was this a valid method?
               if( isset( $method['name'] ) ) {
                  
                  // highlight the example string
                  $method['example'] = $method['example'] ? highlight_string( trim( (string) $method['example'] ), true ) : '';
                  
                  // calculate missing method paths
                  $method['path'] = isset( $method['path'] ) ? $method['path'] : str_replace( '.', '/', $method['name'] );
                  
                  if (!$this->methods[$method['name']]) {

                     // add the method to the method list
                     $this->methods[$method['name']] = $method;

                  } else {
                     
                     $duplicate = sprintf( ' Duplicate API names stupid! : %s ', $method['name'] );

                     $stars = str_repeat( '*', strlen( $duplicate ) );

                     die( sprintf( "\n\033[31;46;5m%s\n%s\n%s\033[0m\n", $stars, $duplicate, $stars ) );
                  
                  }

               }
               
            }
            
         }
         
      }
      
   }

   // some minor configuration
   APIDocUpdater::$rootpath = 'sites/website/views/api/1.0';
   APIDocUpdater::$outputto = 'docs/api/';
   
   // Execute CLI
   CLI::Execute();
   
?>
