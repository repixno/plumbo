<?PHP
   
   class BasePage implements IBaseView {
      
      private $fields = array();
      private $dispatcher = null;
      protected $template = 'default';
      protected $templateengine = 'phptal';
      
      public function quickRoute( $viewpath, $classname, $params = array(), $method = 'Execute' ) {
         
         Dispatcher::extendView( $viewpath );
         
         if( class_exists( $classname ) ) {
            
            $object = new $classname();
            
            $return = Dispatcher::callObjectMethod( $object, $method, $params );
            
            $this->setTemplate( $object->getTemplate() );
            
            // does the page implement postalize?
            if( $object instanceof IPostalize ) {
               
               // run the postilization routine if available.
               if( !$object->postalize() ) {
                  
                  // throw a security exception if we don't have the correct access to the page
                  throw new SecurityException( 'You do not have access to this page', 403 );
                  
               }
               
            }
            
            foreach( $object->getFields() as $field => $value ) {
               $this->$field = $value;
            }
            
            return $return;
            
         } else {
            
            return null;
            
         }
         
      }
      
      public function __set( $key, $val ) {
         
         return $this->fields[$key] = $val;
         
      }
      
      public function __get( $key ) {
         
         return isset( $this->fields[$key] ) ? $this->fields[$key] : null;
         
      }
      
      public function Initialize() {
         
         $this->request = array( 
            'execpath' => Dispatcher::$execPath 
         );
         
         return true;
         
      }
      
      public function getDispatcher() {
         
         return $this->dispatcher;
         
      }
      
      public function setDispatcher( Dispatcher $dispatcher ) {
         
         return $this->dispatcher = $dispatcher;
         
      }
      
      public function getFields() {
         
         return $this->fields;
         
      }
      
      public function resetFields() {
         
         $this->fields = array();
         return true;
         
      }
      
      public function getTemplate() {
         
         return $this->template;
         
      }
      
      public function setTemplate( $template = false ) {
         
         return $this->template = $template;
         
      }
      
      public function getTemplateEngine() {
         
         return $this->templateengine;
         
      }
      
      public function setTemplateEngine( $engine ) {
         
         return $this->templateengine = $engine;
         
      }
      
   }
   
?>