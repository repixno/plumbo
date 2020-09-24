<?PHP
   chdir( dirname( __FILE__ ) );
   include '../../bootstrap.php';

   config( 'website.config' );
   config( 'website.countries' );
   config( 'production.settings');
   import( 'system.cli' );
   import( 'storage.util' );
   import( 'website.userfriend' );
   




   class OrderImportScript extends Script {
      
      private $rfuser =  926508;
      
      
      Public function Main(){

         Login::byUserId( $this->rfuser );

         Session::id( md5( rand( 0, 999999999 ) ) );


         //$albuminfo = DB::query( "SELECT * FROM bildealbum WHERE uid = 926508")->fetchAll( DB::FETCH_ASSOC ) ;
         $albuminfo = DB::query( "SELECT * FROM bildealbum WHERE aid in(3348795, 3348750) ")->fetchAll( DB::FETCH_ASSOC ) ;

         foreach ( $albuminfo as $albumet ){

           $album = new Album( $albumet['aid'] );
           $album->access = 2;
           $album->for_download = true;
           
           $album->save(); 
           
           $user = new User();
           $friend = $user->addFriend( $album->title, '', '' );

           $userid = User::UserIDfromUsernameAndPortal( $album->title );
           
           if( !DB::query( 'SELECT COUNT(aid) FROM tilgangtilalbum_dedikert WHERE aid = ? AND uid = ? AND groupid=0', $album->aid, $userid )->fetchSingle() > 0 ) {

               DB::query( 'INSERT INTO tilgangtilalbum_dedikert (aid, uid) VALUES (?,?)', $album->aid, $userid );
               
               
           
           }

           $subject = "Dine Dyrebar bilder fra Dogs4all-messen";
           $albumdata = array(
               'album' => $album->asArray(),
               'publiclink' => $album->getSharingURL(),
               'link' => $album->getAlbumURL(),
               'message' => $message,
               'email'   => $album->title,
            );

            
            MailSend::Simple( $album->title, $subject, 'share.dyrebar', $albumdata, 'phptal' );
            
            
            util::Debug( $album->title );
            util::Debug( $album->getSharingURL() );
         }
         

         
         //util::Debug( $albumdata );
         
         $i= 0;

         

         
         
         echo $i;
         
         //util::Debug( readfile( $this->imagepath . "kobling_epost_bilder_norwaycup_til_japanphoto.csv") );
         
         //Login::byUserId( $this->rfuser );
   
         //Session::id( md5( rand( 0, 999999999 ) ) );

         
         
         
         
      }
      

  
   }
   
   CLI::Execute();

?>