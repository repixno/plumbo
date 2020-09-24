<?PHP
   
   import( 'pages.base' );
   
   class XMLPage extends BasePage implements ICustomRenderView {
      
      protected $template = false;
      private $rootnode = 'response';
      
      public function Render( $content, $result ) {
         
         $buffer = Dispatcher::drawXMLDocument( $this->getfields(), $this->rootnode );
         
         header( 'Content-Type: text/xml' );
         header( sprintf( 'Content-Length: %d', strlen( $buffer ) ) );
         
         echo $buffer;
         
      }
      
      public function setRootNode( $nodename ) {
         
         $this->rootnode = $nodename;
         
      }
      
   }
   
?>