<?PHP
   import('website.article');   

   class SjokkprisIndex extends WebPage implements IView {
      
      protected $template = 'dnt.default';
      
      public function Execute() {
         

        $article = new Article(2534);
        
        if( isset( $_COOKIE['CeweCodeCookie'] )){
           $this->cewecodecookie = 1;
        }
        
        $this->article = $article->asArray();
        
        

      }
      
      
      
      public  function SendEFcode(){
        $this->template = '';
        if( isset( $_POST ) ){
         import( 'mail.send' );
         $code = '';
         MailSend::Simple(
               $_POST['email'],
               __( 'Din Rabatt-kode' ),
               'dnt.get-code',
               array(
                  'code' => $code,
               )
         );
         relocate('/dnt');
         
        }
      }
      
      public  function SendCEWEcode(){
        $this->template = '';

        if( isset( $_POST ) ){
         import( 'mail.send' );
         $code = '';
         MailSend::Simple(
               'post@eurofoto.no',
               __( 'CEWE Rabatt-kupong' ),
               'dnt.cewe-code',
               array(
                  'post' => $_POST,
               )
         );
         
         MailSend::Simple(
               $_POST['email'],
               __( 'CEWE Rabatt-kupong' ),
               'dnt.cewe-code-kunde',
               array(
                  'post' => $_POST,
               )
         );
         setcookie("CeweCodeCookie", 1);
         relocate('/dnt');
         
         
        }
      }

      
   }
   
?>