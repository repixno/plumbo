<?php

   import( 'website.subscription' );
   import( 'website.album' );
   
   import( 'pages.protected' );
   
   class AccountOverview extends ProtectedPage implements IView {

      protected $template = 'account.overview.index';
      
      /**
       * Execute
       *
       */

      public function Execute() {
         
         // album variables
         
         $this->albumslatest = $this->getLatestAlbums();
         $this->albumssharedtome = $this->getAlbumsSharedToMe();
         $this->albumssharedbyme = $this->getAlbumsSharedByMe();   
         
         // project variables
         
         $this->projectslatest = $this->getLatestProjects();
         
         // user variables
         
         $user = new User( Login::userid() );
         
         $this->userlastlogin = $this->getLastLogin();
         $this->useremail = $user->email;
         $this->userfullname = $user->fullname;
         $this->usercellphone = $user->cellphone;
         
         $this->usersubscription = $this->getSubscription();
         $this->usersubscriptionlastdate = $this->getSubscriptionLastDate();
         
         $this->usercredits = $this->getCredits();
      
         // user item counts
      
         $this->userimagecount = $this->getImageCount();
         $this->useralbumcount = $this->getAlbumCount();
         
         $this->useralbumssharedcount = $this->getAlbumsSharedToMeCount();
         $this->userimagessharedcount = $this->getImagesSharedToMeCount();
         
         $this->inboximagecount = $this->getInboxImageCount();
         
      }
      
      /**
       * Get inbox image count
       *
       * @return Integer
       */
      
      private function getInboxImageCount() {
         
         $images = new Image();
         
         return count( $images->collection( array( 'bid' ), array( 'owner_uid' => Login::userid(), 'aid' => NULL, 'deleted_at' => NULL ) )->fetchAll() );
         
      }
      
      /**
       * Get possible credits for this user
       *
       * @return array
       */
      
      private function getCredits() {

         return Credit::enum( Login::userid() );

      }

      /**
       * Get latest albums
       *
       * @return array
       */
      
      private function getLatestAlbums( $limit = 7 ) {
         
         $albums = array();
         
         $lastalbums = new Album();
         
         foreach( $lastalbums->collection( 'aid', $where = array( 'uid' => Login::userid(), 'deleted_at' => NULL ), 'created_time DESC NULLS LAST', $limit )->fetchAllAs('Album') as $album ) {
            $albums[] = $album->asArray();
         }
      
         return $albums;
      
      }
      
      /**
       * Count albums
       * 
       * @return array
       */
      
      private function getAlbumCount() {
         
         return (int)DB::query( "SELECT count(aid) FROM bildealbum WHERE uid = ? AND deleted_at IS NULL", Login::userid() )->fetchSingle();
         
      }
      
      /**
       * Count images
       * 
       * @return array
       */
      
      private function getImageCount() {
         
         return (int)DB::query( "SELECT count(aid) FROM bildeinfo WHERE owner_uid = ? AND deleted_at IS NULL", Login::userid() )->fetchSingle();


      }
      
      /**
       * Count albums shared to you
       * 
       * @return array
       */
      
      private function getAlbumsSharedToMeCount() {
         
         return (int)DB::query( "SELECT count(aid) FROM tilgangtilalbum_dedikert WHERE uid = ?", Login::userid() )->fetchSingle();
         
      }
      
      /**
       * Count images shares to you
       * 
       * @return array
       */
      
      private function getImagesSharedToMeCount() {
         
         $sharedimages = DB::query( "SELECT aid FROM tilgangtilalbum_dedikert WHERE aid IN ( SELECT aid FROM bildealbum WHERE uid = ? AND deleted_at IS NULL )", Login::userid() )->fetchAll();
         
         return count( $sharedimages );

      }
      
      /**
       * Get last login
       * 
       * @return array
       */
      
      private function getLastLogin() {
         
         $sessionlog = new DBSessionLog();
         
         $session = $sessionlog->collection( 'sessionid', array( 'uid' => Login::userid() ), 'tidspunkt DESC NULLS LAST', 1 )->fetchAllAs('DBSessionLog');
         
         return $session[ 0 ]->timestamp;
      }

      /**
       * Get albums shared to me
       *
       * @return array
       */
      
      private function getAlbumsSharedToMe( $limit = 7 ) {
      
         $res = array_reverse( DB::query( "SELECT aid FROM tilgangtilalbum_dedikert WHERE uid = ?", Login::userid() )->fetchAll() );
      
         $sharedList = array();
         
         while( count( $res ) > 0 && count( $sharedList ) < $limit ) {
            
            list( $albumid ) = array_pop( $res );
            
            if( !isset( $sharedList[ $albumid ] ) ) {
               
               try {
                  
                  $album = new Album( $albumid );
                  $sharedList[ $albumid ]= $album->asArray();
                  
               } catch( Exception $e ) {}
               
            }
         }
      
         return array_values( $sharedList );
      
      }
      
      /**
       * Get latest projects
       *
       * @return array
       */ 
           
      private function getLatestProjects( $limit = 7 ) {

         $user = new User( Login::userid() );
		   $projects = array();
		   foreach ( $user->listProjects( $limit ) as $project ) {

		      $type = $project->type == null ? 'photobook' : $project->type;

		      $projects[] = array(
		         'id'            => $project->id,
		         'title'         => $project->title,
		         'description'   => $project->description,
		         'url'           => '',
		         'isShared'      => $project->share ? true : false,
		         'date'          => $project->created,
		         'product'       => $project->product->asArray(),
		         'type'          => $type,
		         'editurl'       => sprintf( '%s%s%s%s', '/create/', $type, '/edit/', $project->id )
	         );

		   }

		   return $projects;

      }
      
      /**
       * Get albums shared by me
       *
       * @return array
       */
      
      private function getAlbumsSharedByMe( $limit = 7 ) {
      
         $res = array_reverse( DB::query( "SELECT aid FROM tilgangtilalbum_dedikert WHERE aid IN ( SELECT aid FROM bildealbum WHERE uid = ? AND deleted_at IS NULL )", Login::userid() )->fetchAll() );
      
         $sharedList = array();
         
         while( count( $res ) > 0 && count( $sharedList ) < $limit ) {
            
            list( $albumid ) = array_pop( $res );
            
            if( !isset( $sharedList[ $albumid ] ) ) {
               
               try {
                  $album = new Album( $albumid );
                  
                  $sharedList[ $albumid ]= $album->asArray();
                  
               } catch( Exception $e ) {}
               
            }
         }
      
         return array_values( $sharedList );
      
      }
      
      /**
       * Get subscription for user
       *
       * @return array
       */
      
      private function getSubscription() {

         return Subscription::staticAsArray( Login::userid() );
         
      }
      
      /**
       * Get last subscriptiondate for user
       *
       * @return array
       */
      
      private function getSubscriptionLastDate() {
      
         return Subscription::latestOrderValidToByUserId( login::userid() );
            
      }
      
   }
   
?>