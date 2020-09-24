<?PHP
   
   class MyAccountUploadRedirectAlbum extends UserPage implements IValidatedView {
      
      public function Validate() { }
      
      public function Execute( ) {
 
         relocate("/myaccount/album/%s", $_SESSION['upload_aid']);
         
      }

   }
?>