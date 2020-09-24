<?PHP
   
   library( 'getsatisfaction.fastpass.fastpass' );
   #model( 'user.fastpass' );
   
   class GetSatisfactionFastpass /*extends DBUserFastpass*/ {
      
      public function __construct() {
         
         /*
         try {
            parent::__construct( Login::userid() );
         } catch( Exception $e ) {
            parent::__construct();
            $this->uid = Login::userid();
         }*/
         
      }
      
      static function Fetch() {
         
         $userid = Login::userid();
         $email = Login::data('username');
         $fullname = Login::data('fullname');
         
         FastPass::$domain = 'feedback.eurofoto.no';
         return FastPass::script( 'pu29la2pld6t', 'onjwuoc7szom6m88xsls1vb316zfn36p', $email, $fullname, $userid, false, array() );
         
      }
      
   }
   
?>