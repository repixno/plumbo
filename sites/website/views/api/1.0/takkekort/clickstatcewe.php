<?php
   import( 'pages.json' );

   model( 'site.cardcategorycewe' );
   
   class APICardClickstatCEWE extends JSONPage implements NoAuthRequired, IView {
      
        public function Execute() {
         
            $id = $id ? $id : $_POST['id'];
            
            
            $card = new DBCardcatergorycewe( $id );
            $card->hit = $card->hit + 1;
            $card->save();
         
            $this->hit = $card->hit;
            $this->result = true;
            $this->message = "OK";
         
      }
      
   }


?>