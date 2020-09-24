<?php
   
   // the license fee to charge extra
   DEFINE( 'IMAGE_LICENSE_FEE', 80.00 );
   
   /**
    * Share album to user from Barcode
    * 
    */
   
   import( 'website.album' );
   import( 'website.image' );
   import( 'website.album.identifier');

   class DyreparkenBarcode extends WebPage implements IValidatedView {
      
      /**
       * Validates the data going into this view
       *
       * @return array of validation rules.
       */
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
                  'identifier' => VALIDATE_STRING,
               ),
               'fields' => array(
                  'identifier' => VALIDATE_STRING,
               ),
            )
         );
         
      }
               
      public function Execute( $identifier = '' ) {
         
         if ( Login::isLoggedIn() ) {
            
            $identifiervalue = Session::pipe( 'identifier' );
            
            if ( empty( $identifiervalue ) ) {
               
               $identifiervalue = $_POST['identifier'] ? $_POST['identifier'] : $identifier;

            }
            
            if ( !empty( $identifiervalue ) ) {
               
               $identifier = AlbumIdentifier::fromIdentifier( $identifiervalue );
               
               if ( $identifier ) {
            
                  $albumid = Album::idFromIdentifier( $identifiervalue );
                  
                  if ( $albumid > 0 ) {
                     
                     if( DB::query( 'SELECT COUNT(aid) FROM tilgangtilalbum_dedikert WHERE uid = ? AND aid = ?', Login::userid(), $albumid )->fetchSingle() <= 0 ) {
                     
                        DB::query( 'INSERT INTO tilgangtilalbum_dedikert (uid, aid) VALUES (?,?)', Login::userid(), $albumid );
                     
                     }
                     
                     try {
                        
                        $album = new Album( $albumid );
                        foreach( $album->listImageIDs() as $imageid ) {
                           
                           try {
                              
                              $image = new Image( $imageid );
                              $image->licensefee = IMAGE_LICENSE_FEE;
                              $image->save();
                              
                           } catch( Exception $e ) {}
                           
                        }
                        
                        // TODO: make sure album has extra images.
                        
                     } catch( Exception $e ) {}
                       
                     relocate( sprintf( '/dyreparken/album/%s', $albumid ) );
                
                  } else {
                     
                     $this->setTemplate( 'dyreparken.success' );
                     
                  }
                  
               } else {
                  
                  $this->setTemplate( 'dyreparken.wrong-code' );
                  
               }
               
            } else {
               
               $this->setTemplate( 'dyreparken.wrong-code' );
               
            }
            
         } else {
            
            $identifier = $_POST['identifier'] ? $_POST['identifier'] : $identifier;
            
            Session::pipe( 'identifier', $identifier, null, true );
            Session::pipe( 'loginredirecturl', sprintf( '/dyreparken/barcode/%s', $identifier ) );
            
            relocate( '/dyreparken/login' );

         }
         
      }
   }
?>