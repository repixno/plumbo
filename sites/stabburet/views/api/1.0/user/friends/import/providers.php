<?

   import( 'pages.json' );
   
   class ApiFriendsImportProviders extends JSONPage implements IView {

      function Execute() {
         
         $this->message = 'OK';
         $this->result = true;
         $this->providers = array( 'gmail', 'yahoo', 'msn' );
      }
      
   }

?>