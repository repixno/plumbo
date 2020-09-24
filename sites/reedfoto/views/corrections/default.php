<?PHP

   import( 'reedfoto.user' );
   import( 'reedfoto.correction' );
   
   class ReedFotoCorrectionList extends WebPage implements IView {
      
      protected $template = 'corrections';
      
      public function Execute( ) {
      
         $userid = Login::userid();
         
         if ($userid <= 0) relocate('/');

         $user = new RFUser( $userid );
         $this->userdata = $user->asArray();

         $corrections = array();
         
         foreach ( RFCorrection::enum( $userid ) as $correction ) {

            if ( ($correction->state != 'setup') && ($correction->state != 'approved')) {
               
               $correction = array(
               
                  'id' => $correction->id,
                  'state' => $correction->state,
                  'title' => $correction->title
                  
               );
                
               $corrections[] = $correction;
            }

         }

         $this->corrections = $corrections;   
         
      }
      
   }
   
?>