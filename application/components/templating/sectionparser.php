<?PHP

   import( 'templating.controller' );

   class TemplatingSectionParser {

      static function ParseTemplate( $template = 'cms.segment.templatename' ) {

         $siteid = Session::get( 'adminsiteid', 0 );
         if( !$siteid ) {
            $siteid = Session::get( 'siteid', 1 );
         }
         $sites = Settings::get( 'application', 'sites', array() );
         
         $website = 'website';
         $templates = 'eurofoto';
         if( isset( $sites[$siteid]['short'] ) ) {
            $website = $sites[$siteid]['short'];
         }
         if( isset( $sites[$siteid]['templates'] ) ) {
            $templates = $sites[$siteid]['templates'];
         }
         
         $controller = new TemplatingController('phptal');
         $template = sprintf( '%s/sites/%s/templates/%s/%s.html', getRootPath(), $website, $templates, str_replace( '.', '/', $template ) );
         
         $results = array();

         if( file_exists( $template ) ) {

            $buffer = file_get_contents( $template );
            if( preg_match_all( "/define\:section=\"(.*),(.*)\"/", $buffer, $matches ) ) {

               if( count( $matches[0] ) ) {
                  foreach( $matches[0] as $key => $segment ) {
                     $results[trim($matches[1][$key])] = trim($matches[2][$key]);
                  }
               }

            }

         }

         return $results;

      }

   }

?>