<?PHP
   
   import( 'website.image' );
   import( 'website.imageeffects' );
   
   class ImageEffectSample extends WebPage implements IValidatedView {
      
      protected $template = false;
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'fields' => array(
                  'effectid' => VALIDATE_INTEGER,
                  'imageid' => VALIDATE_INTEGER,
               )
            ),
         );
         
      }
      
      public function Execute( $effectid = 0, $imageid = 0 ) {
         
         try {
            
            if( !$imageid ) {
               
               throw new Exception( 'Image does not exist!' );
               
            }
            
            if( !$effectid ) {
               
               throw new Exception( 'Invalid effect number!' );
               
            }
            
            // make sure this is ours.
            $image = new Image( $imageid );
            $effect = new ImageEffects();
            
            // create a preview effect.
            $newimagefile = $effect->processImage( $imageid, $effectid, 0, ImageEffects::RETURN_TEMPFILENAME, true );
            
            // return this image to stdout
            header( 'Content-Type: image/jpeg' );
            header( 'Content-Length: '.filesize( $newimagefile ) );
            readfile( $newimagefile );
            unlink( $newimagefile );
            
         } catch( Exception $e ) {
            
            echo $e->getMessage();
            
         }
         
      }
      
   }
   
   
?>