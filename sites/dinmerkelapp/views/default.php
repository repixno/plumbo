<?PHP
   
   class NotFound extends WebPage implements IView {
      
      public function Execute() {
         
         if( !count( Dispatcher::getExecPath() ) ) {
            
            if( Dispatcher::getPortal() == 'DM-002' ){
     // relocate('/bestilling/gratismerkelapp');
             relocate('/merkelapp');
            }
          /*  else if( Dispatcher::getPortal() == 'FE-001' ){
               relocate('/skapa');
            }
            */
            else{
               
               
               
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
