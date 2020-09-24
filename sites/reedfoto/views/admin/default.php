<?PHP

   class ReedFotoAdmin extends AdminPage implements IView {

      protected $template = 'admin.default';

      public function Execute() {

         $ret = array();
         foreach ( RFUser::enum() as $user ) {

            $ret[] = array(
               'id' => $user->id,
               'fullname' => $user->fullname,
               'username' => $user->username
            );

         }

         $this->users = $ret;
         
         $ret = array();
         foreach ( RFUser::enum(true) as $user ) {

            $ret[] = array(
               'id' => $user->id,
               'fullname' => $user->fullname,
               'username' => $user->username
            );

         }

         $this->admins = $ret;

      }

   }

?>