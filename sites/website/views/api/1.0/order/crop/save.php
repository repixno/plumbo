<?PHP
import( 'pages.json' );
import( 'website.user' );
import( 'session.usersessionarray' );

class APISaveCropCoordinates extends JSONPage implements IValidatedView{
   
   
   public function Validate(){
      
        return array(
            'execute' => array(
               'post'      => array(
                  'width'   => VALIDATE_INTEGER,
                  'height'  => VALIDATE_INTEGER,
                  'x1'      => VALIDATE_INTEGER,
                  'y1'      => VALIDATE_INTEGER,
                  'x2'      => VALIDATE_INTEGER,
                  'y2'      => VALIDATE_INTEGER,
                  'dx'      =>VALIDATE_INTEGER,
                  'dy'      =>VALIDATE_INTEGER,
                  'imageid' => VALIDATE_INTEGER,
                  'prodno'  => VALIDATE_STRING,
                  'fitin'    => VALIDATE_STRING,
               ),
            )
         ); 
   }
   
   
   public function Execute() {
      
      try {
         $printOrder = UserSessionArray::getItems( "printorder" );
         
         

         if( !$printOrder && $_POST ) {
            
            $this->result = false;
            $this->message = 'No printorder';
            file_put_contents( '/tmp/noprintorder.txt',  '' );
            
         } else {
            
            $width = $_POST['width'];
            $height = $_POST['height'];            
            $x1 = $_POST['x1'];
            $y1 = $_POST['y1'];
            $x2 = $_POST['x2'];
            $y2 = $_POST['y2'];
            $dx = $_POST['dx'];
            $dy = $_POST['dy'];
            $fitin = $_POST['fitin'];
            $imageid = $_POST['imageid'];
            $prodno = $_POST['prodno'];
            
            if( $fitin  ){
               UserSessionArray::clearItems( $printOrder[0]["imageobjects"][$imageid]["cropratio"][$prodno]['cropcoordinates'] );
               
               if( $fitin == 'set' ){
                  $printOrder[0]["imageobjects"][$imageid]["cropratio"][$prodno]['cropcoordinates'] = array( 'fitin' => 1 );
               }else{
                  $printOrder[0]["imageobjects"][$imageid]["cropratio"][$prodno]['cropcoordinates'] = '';
               }
            }
            else if( $dx < 10 || $dy < 10 ){
               $this->result = false;
               $this->message = 'Size to small';
               return false;
            }
            else{
              $printOrder[0]["imageobjects"][$imageid]["cropratio"][$prodno]['cropcoordinates'] = array(
                                                                                  'width' => $width,
                                                                                  'height'=> $height,
                                                                                  'x1' => $x1,
                                                                                  'y1' => $y1,
                                                                                  'x2' => $x2,
                                                                                  'y2' => $y2,
                                                                                  'dx'=> $dx,
                                                                                  'dy'=>$dy,
                                                                                  'fitin' => $fitin, 
                                                                                   );
                                                                                
            }

            UserSessionArray::clearItems( "printorder" );
            UserSessionArray::addItem( "printorder", $printOrder[0]);
            
            $this->result = true;
            $this->message = 'OK';
            
         }
         
      } catch( Exception $e ) {
         
         $this->message = 'An unknown error occured when creating your post. Please try again later!';
         $this->result = false;
         
      }
      
      return false;
      
   }
   
}

?>