<?PHP
   
   model( 'user.admin' );
   config( 'admin.settings' );
   
   class Admin extends DBAdmin {
      
      public function isAdmin() {
         
         return $this->level >= 1 ? true : false;
         
      }
      
      public function isSupport() {
         
         return $this->level == 1 ? true : false;
         
      }
      
      public function isSysAdmin() {
         
         return $this->level == 2 ? true : false;
         
      }
      
   }
   
   class limitedAdmin{
      
      public function isLimitedAdmin(){
         $limitedadmins  =  Settings::get( 'admin', 'portaladmins' );
         
         $limitedallow = Dispatcher::getCustomAttr( 'limitedadmin' );
         
          if( $limitedadmins[Login::userid()] && $limitedallow ){
            return true;
          }else{
            return false;
          }
      }
      
      
   }
   
?>