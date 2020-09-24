<?php

   library( '/minify/min/lib/JSMin');
   import( 'cache.memcache');

   class MyScript extends Webpage implements IValidatedView  {
      
      protected $template = false;
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'fields' => array(
                  VALIDATE_STRING,
                  VALIDATE_INTEGER
               ),
               'get' => array(
                  'debug' => VALIDATE_STRING
               )
               
            )
         );
      }
      
      public function Execute( $script = 'account' ) {
  
         $script = explode( '.', $script );
         $script = $script[0];
         
         $debug = $_GET['debug'];
         
         $cachekey = 'scriptcache' . $script;
         
         header('Content-type: application/javascript');
         
         if ( $debug ) echo "// in debug mode, cache is disabled\n\n";
         
         if( !$debug && $buffer = CacheEngine::read( $cachekey ) ) {
            
            die( $debug ? $buffer : JSMin::minify($buffer) );
         
         } else {
         
            $basefolder = sprintf( '%s/sites/static/webroot/js/%s', getRootPath(), strtolower( $script ) );
            
            foreach( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $basefolder ) ) as $name => $file ) {
               
               if ( $this->hasJsExt( $file ) ) {
                  
                  $buffer .= ( $debug ? '// ' . $file->getPath()  . ' - ' . $file->getFileName() . "\n" : '' ) . file_get_contents( sprintf( '%s/%s', $file->getPath(), $file->getFileName() ) );
                  
               }
               
            }
            
            CacheEngine::write( $cachekey, $buffer, 86400 );
            
            die( $debug ? $buffer : JSMin::minify($buffer) );
            
         }
         
      }

      private function hasJsExt( $file = '' ) {
         
         return strtolower( substr( $file, -3 ) ) == '.js' ? true : false;
         
      }
   }

?>