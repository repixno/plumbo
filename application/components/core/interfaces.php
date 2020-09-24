<?PHP
   
   define( 'VALIDATE_DEFAULT', 0x01 );
   define( 'VALIDATE_INTEGER', 0x02 );
   define( 'VALIDATE_ESCAPE',  0x03 );
   define( 'VALIDATE_FLOAT',   0x04 );
   define( 'VALIDATE_STRING',  0x05 );
   
   interface IView {
      
      #public function Execute();
      
   }
   
   interface IBaseView {
      
      public function Initialize();
      public function getFields();
      public function getTemplate();
      public function setTemplate( $template = false );
      public function getTemplateEngine();
      public function setTemplateEngine( $engine );
      
   }
   
   interface IPostalize {
      
      public function Postalize();
      
   }
   
   interface IValidatedView extends IView {
      
      public function Validate();
      
   }
   
   interface ICustomRenderView {
      
      public function Render( $content, $result );
      
   }
   
?>