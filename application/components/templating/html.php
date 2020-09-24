<?PHP
   
   import( 'templating.interface' );
   
   class TemplateEngine_HTML implements ITemplateEngine {
      
      public function Execute( $filename, $variables ) {
         
         $template = file_get_contents( $filename );
         
         if( is_array( $variables ) ) {
            foreach( $variables as $key => $val ) {
               $template = str_replace( sprintf( '<!-- %s /-->', $key ), $val, $template );
            }
         } else {
            $template = str_replace( '<!-- content /-->', $variables, $template );
         }
         
         return $template;
         
      }
      
   }

?>