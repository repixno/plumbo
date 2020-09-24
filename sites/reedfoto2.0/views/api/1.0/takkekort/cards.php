<?php
   import( 'pages.json' );
   import( 'website.product' );
   
   class APICardEnum extends JSONPage implements NoAuthRequired, IView {

      
      private $mcserver = "http://sandra.eurofoto.no/ECommerceBridge/Library/GreetingCard/Themes/";
      
      public function Execute() {
         
         //$group = $group ? $group : $_POST['group'];
         $catid = $catid ? $catid : $_POST['catid'];
         $count = $count ? $count : $_POST['count'];
         $group = $group ? $group : $_POST['group'];
         $articleid = $articleid ? $articleid : $_POST['articleid'];
        
         if( $articleid  > 0 ){
            $selection = "AND articleid =" . $articleid;
         }else{
          switch( $group ){
             case  'collage':
                $selection = "AND articleid in ( 939, 940, 7237, 7238 )";
             break;
             case 'folded':
                $selection = "AND articleid in ( 7039, 7233, 7136, 7105, 7234 )";
             break;
             case 'postkort':
                $selection = "AND articleid in ( 7239 )";
             break;
             default:
                $selection = " ";
             break;
          }
         }
        
         
         if( $catid == 0 ){
            $cards = DB::query( "SELECT * FROM site_card_category WHERE catid > 0 AND visible = 't' $selection order by sort, hit desc, id" )->fetchAll( DB::FETCH_ASSOC );
         }else{
            $cards = DB::query( "SELECT * FROM site_card_category WHERE catid = ?  AND visible = 't' $selection order by sort, hit desc, id", $catid )->fetchAll( DB::FETCH_ASSOC );
         }    
         //Util::Debug( $cards );
         
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
         
         for( $i = $count; $i < $max; $i++ ){
            
            $productoption = ProductOption::fromProdNo( sprintf( "%04d", $cards[$i]['articleid']  ) );
            
            $product = new Product( $productoption->productid );
            
            $productoptionarray =  $productoption->asArray();
            
            $productarr = $product->asArray();

            $thumbnail = base64_encode( $cards[$i]['thumbnail']  );
            
            $productarray[] = array(
                                    'price' => $productoptionarray['price'],
                                    //'thumbnail' => $this->mcserver . $cards[$i]['thumbnail'],
                                    'thumbnail' => $thumbnail,
                                    'purchaseurl' => $productoptionarray['purchaseurl'] . "/0/" . base64_encode( $cards[$i]['template'] ),
                                    'title' => $productarr['title'],
                                    'id' => $cards[$i]['id']
                                    
                                   );
            
            //Util::Debug( $productarray );
            
            //die();

         }
         
         $this->cards = $productarray;
         $this->result = true;
         $this->message = $last;
         
      }
      
   }


?>