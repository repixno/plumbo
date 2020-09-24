#!/usr/bin/php -q
<?PHP
   
   // bootstrap the platform
   chdir( dirname( __FILE__ ) ); include '../../bootstrap.php';
   
   // configure the platform for CLI
   config( 'website.config' );
   import( 'system.cli' );
   import( 'core.session' );
   
   // import required classes
   import( 'website.album.identifier' );

   import( 'sms.send' );
   import( 'mms.send' );

   import( 'storage.util' );   

   class AlbumIdentifierNotifyMobile extends Script {
      
      public function Main( ) {
         
         foreach( DB::query( 'SELECT identifier, mobile FROM album_identifiers WHERE mobile IS NOT NULL AND ( notified = FALSE OR notified IS NULL )' )->fetchAll() as $row ) {

            $identifier = AlbumIdentifier::fromIdentifier( $row[0] );

            list( $albumid, $userid ) = DB::query( 'SELECT aid, uid FROM bildealbum WHERE identifier = ?', $identifier->identifier )->fetchRow();
            
            if ( $albumid > 0 ) {
               
               switch( $identifier->project ) {
                  
                  case 'dyreparken':
                     $activationurl = 'http://eurofoto.no/dyreparken';
                     break;
                  default:
                     $activationurl = 'http://eurofoto.no/hentbilder';
                     break;
                     
               }

               $mobilecode = strtoupper( substr( $identifier->identifier, 0, 4 ).str_pad( zBase32::encode( $identifier->id ), 3, '0', STR_PAD_LEFT ) );

               $sms = new SMS();
               $sms->send( $row[1], utf8_decode( __( 'Your photos from the zoo is now at %s. Use the PIN %s. For photos on MMS (25,-), send DPMMS %s to 1933.', $activationurl, $identifier->identifier, $mobilecode ) ) );
               
               DB::query( 'UPDATE album_identifiers SET notified = TRUE WHERE identifier = ?', $identifier->identifier );

            }

         }
         
      }
      
   }
   
   CLI::Execute();
   
?>
