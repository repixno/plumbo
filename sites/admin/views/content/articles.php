<?PHP
   
   import( 'website.article' );
   
   Dispatcher::extendView( 'content.textentity' );
   
   class ArticleEditor extends TextEntityEditor {
      
      protected $objectclass = 'Article';
      
   }
   
?>