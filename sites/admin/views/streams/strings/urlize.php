<?PHP
   
   import( 'pages.json' );
   import( 'core.util' );
   
   class URLIzeJSON extends JSONPage implements IView {
      
      public function Execute( $string = '' ) {
         
         $this->string = Util::urlize( $string ? $string : $_REQUEST['string'] );
         $this->message = 'OK';
         $this->result = true;
         
      }
      
   }
   
?>