<?PHP
   
   class IUEmbed extends WebPage implements IView {
      
      protected $template = 'streams.i18n.iuembed';
      
      public function Execute() {
         
         header( 'content-type: text/javascript' );
         
      }
      
   }
   
?>