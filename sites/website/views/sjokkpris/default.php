<?PHP
   import('website.article');   

   class SjokkprisIndex extends WebPage implements IView {
      
      protected $template = 'sjokkpris.default';
      
      public function Execute() {
         
        $article = new Article(2387);
        
        $this->article = $article->asArray();
      }
      
      public function SendCode(){
         $this->template = '';
         import( 'math.signer' );
         import( 'mail.send' );
         
   
         $code = "SP4243dfk";
         
         MailSend::Simple(
               $_POST['email'],
               __( 'Din SjokkPris-kode' ),
               'sjokkpris.get-code',
               array(
                  'code' => $code,
               )
         );
         
         relocate( 'sjokkpris');
                 
      }
      
      public function SendCodeFriend(){
         
         $name =  $_POST['name']; 
         
         $this->template = '';
         import( 'math.signer' );
         import( 'mail.send' );
         
   
         $code = "sjokkpris1";
         
         MailSend::Simple(
               $_POST['email'],
               __( 'Du har blitt tipset om SjokkPris!' ),
               'sjokkpris.friend-code',
               array(
                  'code' => $code,
                  'yourname' => $name,
                  
               )
         );
         
         relocate( 'sjokkpris');
                 
      }
      
   }
   
?>