<?PHP
   
   // make sure we have loaded the common config
   config( 'common.config' );
   
   // quite a few things require internatialization to work
   import( 'website.login' );
   
   // import PHPUnit testing framework
   library( 'PHPUnit.Framework' );
   
   /**
    * OO-wrapper for PHPUnit
    */
   class Testing extends PHPUnit_Framework_TestCase {
      
      private $timingStarted = 0;
      
      static function execute() {
         
         foreach( get_declared_classes() as $classname ) {
            
            // make sure we're a decendant of ourself
            if( get_parent_class( $classname ) != 'Testing' ) continue;
            
            // fake argv
            $_SERVER['argv'] = array( __FILE__, $classname );
            
            // execute the command-line interface
            library( 'PHPUnit.TextUI.Command' );
            
         }
         
      }
      
      protected function timingStart() {
         
         $this->timingStarted = microtime( true );
         
      }
      
      protected function timingEnd() {
         
         return microtime( true ) - $this->timingStarted;
         
      }
      
   }
   
?>