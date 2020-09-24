<?PHP
   
   $_SERVER['HTTP_HOST'] = 'eurofoto.no';
   $_SERVER['HTTP_USER_AGENT'] = '';
   
   import( 'core.dispatcher' );
   Dispatcher::setupRequest();
   
   interface IScript {
      
      // public function Main( $argc = 0, $argv = array() );
      
   }
   
   abstract class Script {
      
      // abstract public function Main( $argc = 0, $argv = array() );
      
   }
   
   class CLI {
      
      static function Execute() {
         
         set_time_limit( 0 );
         ignore_user_abort( true );
         
         $argc = (int) $_SERVER['argc'];
         $argv = $_SERVER['argv'];
         
         foreach( get_declared_classes() as $classname ) {
            
            if( is_subclass_of( $classname, Script ) ) {
               
               $object = new $classname();
               $object->main( $argc, $argv );
               
            }
            
         }
         
      }
      
   }
   
?>