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
   import( 'website.reedfoto.reedfotoalbum');

   class ReedfotoBarcodeNC extends WebPage implements IValidatedView {
      
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
               
              
               
               $identifier = Reedfotoalbum::fromIdentifier( $identifiervalue );
                
               if ( $identifier ) {
            
                  $albumid = $identifier->aid;
                  
                  
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
                        
                     } catch( Exception $e ) {
                        util::debug( $e->getMessage() );
                        die();
                        
                     }

                     relocate( sprintf( '/fetchalbum/order/Norwaycup/%s', $imageid ) );
                
                  } else {
                     
                     $this->setTemplate( 'fetchalbum.success' );
                     
                  }
                  
               } else {
                  
                  $this->setTemplate( 'fetchalbum.wrong-codenc' );
                  
               }
               
            } else {
               
               $this->setTemplate( 'fetchalbum.wrong-codenc' );
               
            }
            
         } else {
            
            $identifier = $_POST['identifier'] ? $_POST['identifier'] : $identifier;
            
            
            
            $test = Reedfotoalbum::fromIdentifier( $identifier );
            
            if( $test ){
               Session::pipe( 'identifier', $identifier, null, true );
               Session::set( 'identifiervalue', $identifier );
               Session::pipe( 'loginredirecturl', sprintf( '/fetchalbum/barcodenc/%s', $identifier ) );
               Session::set( 'loginredirecturl', sprintf( '/fetchalbum/barcodenc/%s', $identifier ) );
               
               relocate( '/fetchalbum/login' );
            }else{
               $this->setTemplate( 'fetchalbum.wrong-code' );
            }

         }
         
      }
      
      

      
   }
?>