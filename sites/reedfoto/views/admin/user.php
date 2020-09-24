<?PHP

   import( 'reedfoto.user' );
   import( 'reedfoto.correction' );

   class ReedFotoAdminUser extends AdminPage implements IView {

      protected $template = 'admin.user';

      public function Execute( $userid ) {

         $user = new RFUser( $userid );
         $this->userdata = $user->asArray();

         $ret = array();
         foreach ( RFCorrection::enum( $userid ) as $correction ) {

            $ret[] = array(
               'id' => $correction->id,
               'state' => $correction->state,
               'title' => $correction->title
            );

         }

         $this->corrections = $ret;
         $this->userid = $user->id;

      }

   }

?>
