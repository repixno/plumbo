<?PHP
   
   import( 'website.photocompetition.index' );
   import( 'website.photocompetition.image' );
   import( 'storage.util' );
   
   /**
    * PhotoCompetition Viewer
    * 
    * Takes in a given URLname, looks it up in the photocompetiton table
    * and renders its given template to the enduser. Also gatheres any
    * submitted data and images and stores them for further use.
    *
    * @author Oyvind Selbek <oyvind@selbek.com>
    * @author Tor Inge Løvland <tor.inge@eurofoto.no>
    */
   class PhotoCompetitionViewer extends WebPage implements IView {
      
      protected $template = false;
      
      /**
       * Main method
       *
       * @param string $urlname The URLName to look up the competition on
       * @return boolean true on success, false on failure.
       */
      public function Execute( $urlname = '' ) {
         
         $competition = PhotoCompetition::fromUrlName( $urlname );
         
         if( !$competition->isLoaded() ) {
            return relocate( WebsiteHelper::rootBaseUrl() );
         }
         
         if( isset( $_POST['fields'] ) && isset( $_FILES['image'] ) ) {
            
            // import the email address field
            $email = isset( $_POST['fields']['email'] ) ? $_POST['fields']['email'] : false;
            
            // create a new competition image, and fill its data
            $entry = new PhotoCompetitionImage();
            $entry->photocompetitionid = $competition->id;
            $entry->ownerid = $album->uid;
            $entry->fielddata = serialize( $_POST['fields'] );
            $entry->save();
            
            // grant permission to the imageid we just uploaded, and to the competitions upload folder
            PermissionManager::current()->grantAccessTo( $entry->imageid, 'image', PERMISSION_OWNER );
            PermissionManager::current()->grantAccessTo( $competition->uploadaid, 'album', PERMISSION_SHARED );
            
            // load the album from disk
            $album = new Album( $competition->uploadaid );
            
            // store the uploaded image
            StorageUtil::uploadImage(
               $album->uid,
               $competition->uploadaid,
               $_FILES['image']['tmp_name'],
               $_FILES['image']['type'],
               $_POST['fields']['title'],
               $_POST['message'],
               $entry->imageid
            );
            
            if( $email ) {
               
               // first, attempt to load the userid from the brukar-table.
               $userid = User::UserIDfromUsernameAndPortal( $email, Dispatcher::getPortal() );
               
               // did we succeed?
               if( $userid > 0 ) {
                  // is there an entry in the kunde table for this customer?
                  $res = DB::query( "SELECT uid FROM kunde WHERE uid = ?", $userid );
                  // if not...
                  if( $res->count() == 0 ) {
                     // ...create a kunde-table entry
                     DB::query( "INSERT INTO kunde( uid ) VALUES( ? )", $userid );
                     // did we get a fullname field?
                     if( isset( $_POST['fields']['fullname'] ) ) {
                        // load the user, update the fullname field...
                        $user = new User( $userid );
                        $user->fullname = $_POST['fields']['fullname'];
                        $user->mobile = $_POST['fields']['phone'];
                        // ...and save! :)
                        $user->save();
                     }
                  }
               }
               
               // finally, attempt to load the user from the customer table.
               $user = User::fromUsernameAndPortal( $email, Dispatcher::getPortal() );

               // if none is found...
               if( !$user instanceof User || !$user->isLoaded() ) {
                  // create a new nopass user.
                  $user = new User();
                  $user->portal = Dispatcher::getPortal();
                  $user->username = $email;
                  $user->password = 'nopass';
                  $user->fullname = isset( $_POST['fields']['fullname'] ) ? $_POST['fields']['fullname'] : $email;
                  $user->mobile = $_POST['fields']['phone'];
                  $user->save();
                  
               }
               
               // is newsletter checked?
               if( isset( $_POST['newsletter'] ) && $_POST['newsletter'] ) {
                  $user->newsletter = true;
                  $user->html = true;
                  $user->save();
               }
               
            }
            
            // store the oncompleted string in the template
            $this->oncompleted = $competition->oncompleted;
            
         }
         else{
            if($urlname == "sommerens-fotokonkurranse"){
               
               include("dailyoffers.php");
               
               $this->theme = $theme;
               $this->product = $product;
               $this->producturl = $producturl;
               $this->productimage = $productimage;
               $this->productprice = $productprice;
               

            }
         }
         
         $this->competition = $competition->asArray();
         $this->setTemplate( $competition->template );
         
         // success!
         return true;
         
      }
      
   }
   
?>