<?PHP

   class downloadImage extends Webpage implements IView {
      
      protected $template = '';
      
      public function execute( $bid ){
         
         try{
            $image = new Image( $bid );
            $image->dl_image();
         }catch( Exception $e){
            throw new SecurityException( 'You dont have permission to download this image!' );
         }
            
      }
       
   }