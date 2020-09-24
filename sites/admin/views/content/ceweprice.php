<?PHP
   
   import( 'pages.admin' );
   import( 'website.product' );
   import( 'website.article' );
   
   class AdminCewePrice extends AdminPage implements IView {
      
      protected $template = 'content.ceweprices';
      
      public function Execute( $lang = 'NO', $regen = null) {
        
        
        
            if( $regen == 'regenerate' ){
                $save = serialize( $this->ceweprices( $lang ) );
                $serialized =  file_put_contents( '/data/pd/ef28/takkekort/ceweprice-' . $lang . '.txt', $save  );
            }
            
            $serialized =  file_get_contents( '/data/pd/ef28/takkekort/ceweprice-' . $lang . '.txt', $serialized  );
            $unserialized =  unserialize( $serialized );
            
            $ceweupdate = $this->ceweprices( $lang );
            
            foreach(  $unserialized as  $key=>$product ){
                $unserialized[$key]['price'] = $ceweupdate[$key]['price'];
            }
            

            //$serialized =  file_put_contents( '/data/pd/ef28/takkekort/ceweprice-' . $lang . '.txt', serialize($unserialized)  );
            
            $this->cewepricelist = $unserialized;
            $this->lang = $lang;
      }
      
      
      
      public function save ($lang){
            $this->template = null;
            
            $serialized =  file_get_contents( '/data/pd/ef28/takkekort/ceweprice-'. $lang .'.txt', $serialized  );
        
            $unserialized =  unserialize( $serialized );
            
            $lang = $_POST['lang'];
            
            $id = explode( '-' , $_POST['id'] );
            $text = $_POST['text'];
            
            $productid = $id[1];
            $addonid = $id[2];

            if( $addonid > 0 ){
               $unserialized[$productid]['addons'][$addonid]['name'] = $text;
            }else{
               $unserialized[$id[1]]['name'] = $text;
            }
            
            
            
            $save = serialize( $unserialized );
            $serialized =  file_put_contents( '/data/pd/ef28/takkekort/ceweprice-' . $lang . '.txt', $save  );
            
            echo "OK";
        
      }
      
      
      public function toggle (){
            $this->template = null;
            //$lang = 'NO';
            
            $lang = $_POST['lang'];

            $serialized =  file_get_contents( '/data/pd/ef28/takkekort/ceweprice-'. $lang .'.txt', $serialized  );
            $unserialized =  unserialize( $serialized );
            $id = explode( '-' , $_POST['id'] );
            $hide = $_POST['hide'];
            
            echo $hide;
            
            if( $hide == 'false'){
                $hide = 1;
            }else{
                $hide = 0;
            }

            $productid = $id[1];
            $addonid = $id[2];
            
            if( $addonid > 0 ){
               $unserialized[$productid]['addons'][$addonid]['hide'] = $hide;
            }else{
               $unserialized[$productid]['hide'] = $hide;
            }
            
            
            
            $save = serialize( $unserialized );
            $serialized =  file_put_contents( '/data/pd/ef28/takkekort/ceweprice-' . $lang . '.txt', $save  );
            
            echo "OK";
        
      }
      
      
      private function ceweprices( $lang ){
         
         
         
         if( $lang == 'SV' ){
            import( 'xtci.config-se');
         }else{
            import( 'xtci.config');
         }
         
         
         import( 'xtci.xtci');
         import( 'xtci.xtci_sso');
         
         $XTCiForSSO = new XTCiForSSO();
         $pricelist = $XTCiForSSO->pricelist();
         $xml = new SimpleXMLElement($pricelist[2]);
         
         
         foreach( $xml->content->add_ons->add_on as $addon ){

            $addonname = (string)$addon['name'];
            $addonprice = (int)$addon->base_prices->price[price] / 100;
            
            
            if( (int)$addon['id'] > 8863 ){
               foreach( $addon->linked_articles->linked_article  as $link ){
                       
                   $cardaddon[(int)$link['id']][(int)$addon['id']] = array(
                                           'id' => (int)$addon['id'],
                                           'name' => $addonname,
                                           'price' => $addonprice,
                                           'hide'  => 0
                                           );
                   
               }
            }
        }
         
            
        foreach( $xml->content->articles  as $article ){
               
               foreach( $article as $res ){
                  //Util::Debug( (string)$res->base_prices->price[price] );
                  $id = (string)$res['id'];
                  $price = (int)$res->base_prices->price[price] / 100;
                  
                  $price = str_replace( '.', ',' ,(string)$price );
                  
                  $ariclearray[$id] = array(
                     'id' => $id,
                     'name' => (string)$res['name'],
                     'price' => $price,
                     'addons' => $cardaddon[$id],
                     'hide'   => 0
                  );
                   //Util::Debug( $res );
               }
            }

         return  $ariclearray;
         

      }
      
   }
   
?>