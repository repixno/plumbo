<?php
   import( 'pages.json' );
   
   class APICardCeweEnum extends JSONPage implements NoAuthRequired, IView {
      
      private $cachename = null;

      public function Execute() {
         
         $group = $group ? $group : $_POST['group'];
         $catid = $catid ? $catid : $_POST['catid'];
         $count = $count ? $count : $_POST['count'];
         $articleid = $articleid ? $articleid : $_POST['articleid'];
         
         if( Dispatcher::getPortal() == "TK-SV" ){
            $this->cachename = 'cewe-pricelist_sv';
         }else{
            $this->cachename = 'cewe-pricelist';
         }
         
         
         if( strlen( $articleid )  > 5 ){
            $selection = "AND grouping in ( '$articleid' ) ";
         }else{
            switch( $group ){
               case 'takkekort':
                  $selection = "AND grouping in ( 'takkekort' ) ";
               break;
               case 'foldekort':
                  $selection = "AND grouping in ( 'foldekort', 'foldekort_13x18_cm', 'foldekort_14x14_cm' ) ";
               break;
               case 'foldekort_xl':
                  $selection = "AND grouping in( 'foldekort_xl_kort_fals' , 'foldekort_xl_lang_fals')";
               break;
            }
         }
         
         if( $catid == 0 ){
            $cards = DB::query( "SELECT * FROM site_card_category_cewe WHERE grouping = ? order by sort, hit desc, id", $group )->fetchAll( DB::FETCH_ASSOC );
         }else{
            $cards = DB::query( "SELECT * FROM site_card_category_cewe WHERE catid = ? $selection AND visible = 't' order by sort, hit desc, id", $catid )->fetchAll( DB::FETCH_ASSOC );
         }    
          
         $pricearray = CacheEngine::read( $this->cachename );
        
         if( empty( $pricearray[8311]['price'] ) ){
            $this->fetchPrice();
            $pricearray = CacheEngine::read( $this->cachename );
         }
         
         $productarray = array();
         
         $available = count( $cards );
              
         $productarray = array();
         
         if( $count > 1 ){
            $max = $available;
            $last = "end";
         }
         else if( $available <= $count + 5 ){
            $max = $available;
            $last = "end";
         }
         else{
            $max = $count + 5 ;
            $last = "continue";
         }
         
         if( $available == 5 ){
            $last = "stop";
         }

         for( $i = $count; $i < $max; $i++ ){
            
            $purchaseurl = $cards[$i]['link'];
            if( Dispatcher::getPortal() == "TK-SV" ){
               $purchaseurl = str_replace( '85021473', '85021449', $purchaseurl );
               $title = $cards[$i]['title_sv'];
            }else{
               $title = $cards[$i]['title'];
            }
            
            
            $var  = parse_url($purchaseurl, PHP_URL_QUERY);
            $var  = html_entity_decode($var);
            $var  = explode('&', $var);
            $arr  = array();
          
            foreach($var as $val)
             {
              $x = explode('=', $val);
              $arr[$x[0]] = $x[1];
             }

            
            $productarray[] = array(
                                    'price' => $pricearray[$arr['productId']],
                                    'articleid' => $cards[$i]['articleid'],
                                    'thumbnail' => "/images/cewetakkekort/" . $cards[$i]['thumbnail'],
                                    'purchaseurl' => $purchaseurl,
                                    'title' => $title,
                                    'id' => $cards[$i]['id']
                                   );

         }
         
         $this->cards = $productarray;
         $this->result = true;
         $this->message = $last;
         
      }
      
      
      
      
      public function fetchPrice(){
         
         import( 'xtci.config');
         import( 'xtci.xtci');
         import( 'xtci.xtci_sso');
         
         $cachename = $this->cachename;
         
         try {
            
            $XTCiForSSO = new XTCiForSSO();
            $pricelist = $XTCiForSSO->pricelist('13');
            $ariclearray = array();
            
            $xml = new SimpleXMLElement($pricelist[2]);
            
            foreach( $xml->content->articles  as $article ){
               
               foreach( $article as $res ){
                  //Util::Debug( (string)$res->base_prices->price[price] );
                  $id = (string)$res['id'];
                  $price = (int)$res->base_prices->price[price] / 1000;
                  
                  $price = str_replace( '.', ',' ,(string)$price );
                  
                  $ariclearray[$id] = array(
                     'id' => $id,
                     'name' => (string)$res['name'],
                     'price' => $price     
                  );
                   //Util::Debug( $res );
               }
            }
            
            $pricearray = array(
                8311 => $ariclearray[8311]['price'],
                8584 => $ariclearray[8585]['price'],
                6051 => $ariclearray[9405]['price'],
                8569 => "3,99",
                8573 => "3,99",
                8563 => "3,99",
                6050 => "3,99",
                6424 => "3,99",
                6426 => $ariclearray[9102]['price'],
                8161 => "3,99",
                8110 => "3,99",
                6414 => $ariclearray[9403]['price'],
                6413 => $ariclearray[9402]['price'],
            );
            
            CacheEngine::write( $cachename, $pricearray, 86400 );
         
         }catch( Exception $e ){
            Util::Debug( $e );
         }
         
      }
      
   }


?>