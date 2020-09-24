<?PHP
   
   import( 'sms.send' );
   import( 'math.zbase32' );
   import( 'website.album.identifier' );
   
   class AlbumIdentifierException extends Exception {}
   
   class AlbumIdentifierSMSService extends WebPage implements IView {
      
      protected $template = false;
      
      public function Execute( $mobile = '', $code = '' ) {
         
         try {
            
            $id = (int) zbase32::decode( strtolower( substr( $code, 4 ) ) );
            $checksum = strtoupper( substr( $code, 0, 4 ) );
            if( $id > 0 ) {
               
               $identifier = new AlbumIdentifier( $id );
               if( !$identifier->isLoaded() || substr( $identifier->identifier, 0, 4 ) != $checksum ) {
                  
                  throw new AlbumIdentifierException( __( 'The given mobile activation code is invalid!' ) );
                  
               }
               
               if( $identifier->mobile && $identifier->mobile != $mobile ) {
                  
                  throw new AlbumIdentifierException( __( 'This code has already been assigned to a number.' ) );
                  
               } else {
                  
                  $identifier->mobile = $mobile;
                  $identifier->save();
                  
               }
               
               switch( $identifier->project ) {
                  
                  case 'dyreparken':
                     $activationurl = 'http://eurofoto.no/dyreparken';
                     break;
                  default:
                     $activationurl = 'http://eurofoto.no/hentbilder';
                     break;
                     
               }
               
               throw new AlbumIdentifierException( __( 'Your pincode is %s. Visit %s to view your pictures. The pictures will be available no later than 24 hours after the shooting.', $identifier->identifier, $activationurl ) );
               
            } else {
               
               throw new AlbumIdentifierException( __( 'The given mobile activation code is invalid!' ) );
               
            }

         } catch( AlbumIdentifierException $e ) {
            
            $sms = new SMS();
            $sms->send( $mobile, utf8_decode( $e->getMessage() ) );
            
            echo "OK";
            
         } catch( Exception $e ) {
            
            $sms = new SMS();
            $sms->send( $mobile, utf8_decode( __( 'An error occured when trying to activate your barcode. Please contact post@eurofoto.no' ) ) );
            
            echo "OK";
            
         }
         
      }
      
   }
   
?>