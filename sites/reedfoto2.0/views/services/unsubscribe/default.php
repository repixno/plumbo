<?PHP
   
   import( 'website.user' );
   
   class UnsubscribeService extends WebPage implements IView {
      
      static $securitycode = '79ff286f-6d57-43a3-ba51-9821e18e06ce';
      
      public function Execute( $email = '', $hash = '' ) {
         
         $this->setTemplate( 'services.unsubscribe.default' );
         
         $this->success = false;
         
         if( md5( self::$securitycode.$email ) == $hash ) {
            
            $user = User::fromUsernameAndPortal( $email, Dispatcher::getPortal() );
            if( $user instanceof User && $user->isLoaded() ) {
               
               try {
                  
                  $user->newsletter = false;
                  $user->save();
                  
                  $this->success = true;
                  $this->email = $email;
                  $this->user = $user->asArray();
                  $this->hash = $hash;
                  
               } catch ( Exception $e ) {}
               
            }
            
         }
         
      }
      
      public function ReSubscribe( $email = '', $hash = '' ) {
         
         $this->setTemplate( 'services.unsubscribe.resubscribe' );
         
         $this->success = false;
         
         if( md5( self::$securitycode.$email ) == $hash ) {
            
            $user = User::fromUsernameAndPortal( $email, Dispatcher::getPortal() );
            if( $user instanceof User && $user->isLoaded() ) {
               
               try {
                  
                  $user->newsletter = true;
                  $user->save();
                  
                  $this->success = true;
                  $this->email = $email;
                  $this->user = $user->asArray();
                  $this->hash = $hash;
                  
               } catch ( Exception $e ) {}
               
            }
            
         }
         
      }
      
   }
   
?>