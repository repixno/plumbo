<?PHP
   
   class Laglokk extends WebPage implements IView {
      
      protected $template = "stabburet.editor";
      
      public function Execute() {
         
         $serverdata = $_SERVER['HTTP_USER_AGENT'];
         if( strpos( $serverdata, 'FBID' )){
            $this->facebookapp = true;
         }
         else{
            $this->facebookapp = false;
         }
         
         if( Login::isLoggedIn() ){
               $cart = new Cart();
               $cart->clear();
               Login::logout();
         }

         
         $this->uploadedimages = UploadedImagesArray::get();
         $datestr="2020-12-31 00:00:00";
         $date=date( 'Y-m-d H:i:s' , strtotime($datestr) );
         if( date( 'Y-m-d H:i:s')  > $date ){
            $this->template = "stabburet.preindex";
         }
         
      }
      
      public function Kampanjeregler() {
         
        $this->template = "stabburet.regler";
         
      }
      
      
      
      public function Informasjon() {
         
        $this->template = "stabburet.informasjon";
         
      }
      
       public function Informasjon2() {
         
        $this->template = "stabburet.informasjon2";
         
      }
      
      
      public function Informasjon3() {
         
        $this->template = "stabburet.informasjon3";
         
      }
      
      
       public function orklainfoside() {
         
        $this->template = "stabburet.orklainfoside";
         
      }
      

      public function SetMobiledevice(){

         setcookie("isMobiledevice", 'true', time()+3600);
         
         relocate('/lag-lokk');
         
      }
   }
   
?>
