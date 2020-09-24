<?PHP
   
   import( 'pages.admin' );
   import( 'website.user' );
   
   class CampaignMonitorNewsletterImporter extends AdminPage implements IView {
      
      protected $template = 'import.newsletter.campaignmonitor';
      
      public function Execute() {
         
         
      }
      
      public function CSV() {
         
         set_time_limit( 0 );
         ignore_user_abort( true );
         
         $blacklist = $_POST['blacklist'] ? true : false;
         
         $notfound = array();
         $blacklisted = array();
         $unsubscribed = array();
         $alreadyunsub = array();
         
         $handle = fopen( $_FILES['csv']['tmp_name'], 'r' );
         while( ( list( $email ) = fgetcsv( $handle, 1000, ',' ) ) !== false ) {
            
            if( strpos( $email, '@' ) !== false ) {
               
               $user = User::fromFieldValue( array( 'brukarnamn' => $email ), 'User' );
               if( !$user instanceof User || !$user->isLoaded() ) {
                   $user = User::fromUsernameAndPortal( $email );
               }
               if( $user instanceof User && $user->isLoaded() ) {
                  
                  if( $user->newsletter ) {
                     
                     $user->newsletter = false;
                     $unsubscribed[$email] = true;
                     
                     if( $blacklist ) {
                        
                        $user->newsletter_blacklist = true;
                        $blacklisted[$email] = true;
                        
                     }
                     
                     $user->save();
                     
                  } else {
                     
                     $alreadyunsub[$email] = true;
                     
                  }
                  
               } else {
                  
                  $notfound[$email] = true;
                  
               }
               
            }
            
         }
         
         $this->unsubscribed = array(
            'notfound' => count( $notfound ),
            'notfoundlist' => array_keys( $notfound ),
            'blacklisted' => count( $blacklisted ),
            'unsubscribed' => count( $unsubscribed ),
            'alreadyunsub' => count( $alreadyunsub ),
         );
         
      }
      
   }
   
?>