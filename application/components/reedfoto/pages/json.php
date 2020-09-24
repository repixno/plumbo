<?PHP
   
   import( 'reedfoto.pages.user' );
   import('reedfoto.page');

   class JSONPage extends UserPage implements ICustomRenderView {
      
      protected $template = false;
      private $singleValue = null;
      
      public function Render( $content, $result ) {
         
         $values = isset( $this->singleValue ) ? $this->singleValue : $this->getFields();
         
         if( isset( $_GET['xml'] ) ) {
            
            return Dispatcher::drawXMLDocument( $values, 'apiresult' );
            
         } else {
            
            return json_encode( $values );
            
         }
         
      }
      
      public function initialize() {
         
         // support basic auth for JSON pages
         if( isset( $_SERVER['PHP_AUTH_USER'] ) && isset( $_SERVER['PHP_AUTH_PW'] ) ) {
            if( !Login::byPortalUsernameAndPassword( Dispatcher::getPortal(), $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'] ) ) {
               return false;
            }
         }
         
         // fetch apache headers
         $headers = apache_request_headers();
         if( isset( $headers['X-AuthToken'] ) ) {
            if( !Login::bySecureToken( $headers['X-AuthToken'] ) ) {
               return false;
            }
         }
         
         // check for auth in GET
         if( isset( $_GET['X-AuthToken'] ) ) {
            if( !Login::bySecureToken( $_GET['X-AuthToken'] ) ) {
               return false;
            }
         }
         
         // check for auth in POST
         if( isset( $_POST['X-AuthToken'] ) ) {
            if( !Login::bySecureToken( $_POST['X-AuthToken'] ) ) {
               return false;
            }
         }
         
         // does this page require authorization?
         if( $this instanceof NoAuthRequired ) {
            
            $initialize = WebPage::Initialize( true );
            
         } else {
            
            $initialize = parent::Initialize( true );
            
            // make sure we're logged in
            if( !Login::isLoggedIn() ) {
               
               // reset all fields and return
               $this->resetFields();
               
               // setup defaults
               $this->result = false;
               $this->message = 'Not logged in';
               
               // render and die
               echo $this->Render( '', false );
               die();
               
            }
            
         }
         
         // forward the parent request
         if( !$initialize ) return false;
         
         // reset all fields and return
         $this->resetFields();
         
         // setup defaults
         $this->result = false;
         $this->message = 'API function not implemented';
         
         // return success
         return true;
         
      }
      
      public function returnSingleValue( $value ) {
         
         $this->singleValue = $value;
         
      }
      
   }
   
   interface NoAuthRequired {
      
      
   }
   
?>