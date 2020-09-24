<?PHP


   /**
    *
    */

   import( 'pages.json' );


   class APIPhonelookup extends JSONPage implements NoAuthRequired,IView {

      public function Execute(){
        
        $phonenumber = str_replace(' ', '', $_GET['phonenumber']);
         
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://secure.bringcrm.no/api/address/v1/Lookup/Telephone/" . $phonenumber );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json' , 'Authorization:BB9B38C5D8359AAEE040007F01000D02' ) );
        $content = curl_exec($ch);
        
        $this->message = 'ok';
        $this->result = $content;
         
      }

   }
   
   
   
   
?>