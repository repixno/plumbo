<?PHP
   
   class NotFound extends WebPage implements IView {
      
      public function Execute() {
         
         if( !count( Dispatcher::getExecPath() ) ) {
            
            if( Dispatcher::getPortal() == 'RF-001' ){
               relocate("/frontpage" );
            }
            
            else{
               
               if( !$_COOKIE['NewsletterEF'] || $_GET['newsletterpopup']){
                  //setcookie("TestCoo", date('Y-m-d') );
                  setcookie("NewsletterEF", date('Y-m-d'), 2147483647);
                  $this->newsletter = "test";
               }
               
               if( $_COOKIE['Chooseef'] || Login::isLoggedIn() ){
                   $this->template = "frontpage.index";
               }
               else{
                  if( Dispatcher::getPortal()  == '' ){
                     $this->template = "frontpage.index";
                  }else{
                     $this->template = "frontpage.index";
                  }
               }
              
               //relocate( '/frontpage' );
            }

         } else if( !$this->quickRoute( 'cms.default', 'cms', Dispatcher::getExecPath() ) ) {
            
            header( 'HTTP/1.0 404 Not Found' );
            
            $this->setTemplate( 'errors.notfound' );
            
            WebPage::$canonical = '';
            $this->canonical = '';
            
         }
         
      }
      
   }
   
?>
