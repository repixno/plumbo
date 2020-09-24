<?php
   import( 'pages.json' );
   import( 'website.product' );
   
   class APICardEnum extends JSONPage implements NoAuthRequired, IView {

          
      public function Execute() {
         
         $group = $group ? $group : $_POST['group'];
         $catid = $catid ? $catid : $_POST['catid'];
         
         $cards = DB::query( "SELECT * FROM site_card_category WHERE catid = ?", $catid )->fetchAll( DB::FETCH_ASSOC );
                  
         $cardarray = array();
         foreach( $cards as $card ){
            
            $product = ProductOption::fromProdNo( $card['articleid'] );
            
            $cardarray[] = array(
               
               
               
            )
            Util::Debug( $product );
            die();

         }
         
         //$this->cards = $cards;
         //$this->result = true;
         //$this->message = "OK";
         
      }
      
   }


?>