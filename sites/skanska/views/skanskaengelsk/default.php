<?PHP
   
   class Laglokk extends WebPage implements IView {
      
      protected $template = "skanska.editor2";
      
      public function Execute() {
         
         $serverdata = $_SERVER['HTTP_USER_AGENT'];

         if( strpos( $serverdata, 'FBID' )){
            $this->facebookapp = true;
         }
         else{
            $this->facebookapp = false;
         }

         Login::logout();
         $this->uploadedimages = UploadedImagesArray::get();
         
         $datestr="2021-02-01 00:00:00";
         $date=date( 'Y-m-d H:i:s' , strtotime($datestr) );
         if( date( 'Y-m-d H:i:s')  > $date ){
            $this->template = "stabburet.preindex";
         }
         
      }
      
     
      
      
      
     
   
      
      
      
      public function SetMobiledevice(){

         setcookie("isMobiledevice", 'true', time()+3600);
         
         relocate('/skanskamobile.editor2');
         
      }
   }
   
?>
