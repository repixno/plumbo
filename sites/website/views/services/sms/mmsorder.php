<?PHP
   
   import( 'sms.send' );
   import( 'math.zbase32' );
   import( 'website.album.identifier' );

   import( 'sms.send' );
   import( 'mms.send' );

   import( 'storage.util' );   
   
   class AlbumIdentifierException extends Exception {}
   
   class ServicesSMSMMSOrder extends WebPage implements IView {
      
      protected $template = false;
      
      public function Execute( $mobile = '', $code = '' ) {
         
         try {
            
            $id = (int) zbase32::decode( strtolower( substr( $code, 4 ) ) );
            $checksum = strtoupper( substr( $code, 0, 4 ) );
            
            if( $id > 0 ) {
               
               $identifier = new AlbumIdentifier( $id );
               
               if( !$identifier->isLoaded() || substr( $identifier->identifier, 0, 4 ) != $checksum ) {
                  
                  throw new AlbumIdentifierException( __( 'The given mobile activation code is invalid!' ) );
                  
               }  else {
                  
                  if( $identifier->mobile && $identifier->mobile != $mobile ) {
                  
                     throw new AlbumIdentifierException( __( 'This code has already been assigned to a number.' ) );
                  
                  } else {
                     
                     $identifier->mobile = $mobile;

                     list( $albumid, $userid ) = DB::query( 'SELECT aid, uid FROM bildealbum WHERE identifier = ?', $identifier->identifier )->fetchRow();
                     
                     if ( $albumid > 0 ) {
                     
                        Login::byUserId( $userid );
   
                        Session::id( md5( rand( 0, 999999999 ) ) );
                        
                        $imageid = DB::query( 'SELECT bid FROM bildeinfo WHERE aid = ? ORDER BY dato ASC, bid ASC LIMIT 1', $albumid )->fetchSingle();
                        
                        if ( $imageid > 0 ) {
            
                           $mms = new SFMMS( Settings::Get( 'sms', 'customerid' ), Settings::Get( 'sms', 'password' ) );
                           
                           $im = new Imagick();
                           $im->readImageFile( fopen( sprintf( 'storage://eurofoto/%s', $imageid ), rb ) );
                           $im->scaleImage( 640, 0 );
                           
                           $mms->AddFileData( $im->getImageBlob(), sprintf( '%s.jpg', $imageid ) );
                           
                           $mms->send( $mobile, __( 'Picture from eurofoto' ) );
                           
                           $identifier->mmssent = true;
                           
                        }
                        
                     }
                     
                     $identifier->save();
                     
                     echo "OK";

                  }
                  
               }
               
            } else {
               
               throw new AlbumIdentifierException( __( 'The given mobile activation code is invalid!' ) );
               
            }
            
         } catch( AlbumIdentifierException $e ) {
            
            $sms = new SMS();
            $sms->send( $mobile, utf8_decode( $e->getMessage() ) );
            
            echo "OK";
            
         } catch ( Exception $e ) {
            
            $sms = new SMS();
            $sms->send( $mobile, utf8_decode( __( 'An error occured. Please contact post@eurofoto.no'.$e->getMessage() ) ) );
            
            echo "OK";
            
         }
         
      }
      
   }
   
?>