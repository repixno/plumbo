<?PHP
   
   Dispatcher::extendView( 'images.products' );
   
   class AttachmentsViewer extends ProductImageViewer implements IView {
      
      protected $resourcepath = '%s/data/attachments/%s';
      
      public function Thumbs( $type = 'square', $size = 32, $image = '', $force = false ) {
         
         if( func_num_args() > 3 ) {
            
            $force = false;
            $pathelems = array();
            
            foreach( func_get_args() as $argument ) {
               if( $skiptwofirst++ < 2 ) continue;
               $pathelems[] = $argument;
            }
            
            if( end( $pathelems ) == '1' ) {
               array_pop( $pathelems );
               $force = true;
            }
            
            $image = implode( '/', $pathelems );
            
         }
         
         return parent::Thumbs( $type, $size, $image, $force );
         
      }
      
      public function Execute( $image = '' , $language = '' ) {
         
         $pathelems = array();
         
         foreach( func_get_args() as $argument ) {
            $pathelems[] = $argument;
         }
         
         $image = implode( '/', $pathelems );
         
         return parent::Execute( $image );
         
      }
   }
   
?>