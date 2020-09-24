<?
/*
 *
 */

 model( 'user.mediaclipsession' );
 
class MediaclipLogin extends WebPage implements IView{
    
    protected $template = null;
    
    public function Execute (){
        if( Login::isLoggedIn() ){
            $this->template = 'create.mediacliplogin';
            util::debug( "logged in " );
        }else{
            $this->template = 'create.mediacliplogin';
            util::Debug( "not loged in" );
        }
    }
    
    public function Dologin() {
         
        // check if we're logged in
        if( Login::isLoggedIn() ){
            
            $logindata =  Login::data();
            $mcsession = new DBmediaclipSession();
            
            $mcsession->sessionid = $logindata['sessionid'];
            $mcsession->userid = (int)$logindata['userid'];
            $mcsession->loggedin = true;
            $mcsession->browser = $_SERVER['HTTP_USER_AGENT'];
            $mcsession->save();
            
            relocate( 'http://mia.eurofoto.no/mobile/success.html?sessionId=' . $logindata['sessionid']  );
            die();
        }
         
         // login failure?
         if( Login::LoginFailed() ) {
            $this->error = true;
            $this->errorreason = Login::$LoginFailureReason;
         }
         
      }
}











?>