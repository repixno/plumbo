<?PHP
import( 'pages.json' );
import( 'website.user' );
import( 'session.usersessionarray' );

class APIGetCropCoordinates extends JSONPage implements IValidatedView{
   
   
   public function Validate(){
      
        return array(
            'execute' => array(
               'post'      => array(
                  'imageid'=> VALIDATE_INTEGER,
                  'prodno' => VALIDATE_STRING,
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

         } else {
            
            $imageid = $_POST['imageid'];
            $prodno = $_POST['prodno'];
            $coordinates = $printOrder[0]["imageobjects"][$imageid]["cropratio"][$prodno]['cropcoordinates'];
            $this->result = true;
            if( is_array ( $coordinates ) ){
               $this->message = $coordinates;
            }else{
               $this->message = null;
            }
         
         }
         
      } catch( Exception $e ) {
         
         $this->message = 'An unknown error occured when creating your post. Please try again later!';
         $this->result = false;
         
      }
      
      return false;
      
   }
   
}

?>