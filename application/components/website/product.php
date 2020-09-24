<?PHP

   model( 'site.product' );
   model( 'site.product.pricehistory' );
   model( 'site.product.price');
   
   import( 'cache.engine' );

   class Product extends DBProduct {

      public function getOptions() {

         $collection = new ProductOption();

         return $collection->collection(
            array( 'id' ),
            array( 'productid' => $this->id, 'deleted' => null )
         )->fetchAllAs( 'ProductOption' );

      }

      static function listProductsBySales( $limit = 10, $lastxdays = 30 ) {

         $identifier = sprintf( 'product-list-by-sales-%s-%d-%d-%s', Dispatcher::getPortal(), $limit, $lastxdays, i18n::$language );

         $products = CacheEngine::read( $identifier );
         if( is_array( $products ) ) return $products;

         $products = array();

         $datequalifier = date( 'Y-m-d', strtotime( sprintf( '-%d DAYS', (INT) $lastxdays ) ) );

         foreach( DB::query( "SELECT spo.productid,
                                     SUM(data.count) AS sum
                                FROM (
                                      SELECT COUNT(ol.id),
                                             ol.artikkelnr
                                        FROM historie_ordre o
                                   LEFT JOIN historie_ordrelinje ol
                                          ON o.ordrenr = ol.ordrenr
                                       WHERE tidspunkt >= ?
                                       GROUP BY ol.artikkelnr
                                     ) as data
                                JOIN site_product_option spo
                                  ON spo.refid = data.artikkelnr
                                JOIN site_textentity ste
                                  ON ste.id = spo.id
                               WHERE ste.deleted IS NULL
                                 AND spo.tags NOT LIKE '%productionmethod%'
                                 AND spo.tags NOT LIKE '%correctionmethod%'
                                 AND spo.tags NOT LIKE '%paperquality%'
                                 AND spo.tags NOT LIKE '%subscription%'
                                 AND spo.tags NOT LIKE '%notoplists%'
                                 AND spo.tags NOT LIKE '%print%'
                                 AND spo.refid != 127
                            GROUP BY spo.productid
                            ORDER BY sum DESC", $datequalifier )->fetchAll() as $row ) {
            
            list( $productid, $count ) = $row;
            $product = new Product( $productid );
            if( $product instanceof Product ) {
               
               if( $product->getUrl() && $product->getTitle() ) {
                  
                  // util::Debug( $product->getTitle() ); die();
                  
                  $products[] = $product->asArray();
                  if( ++$productcount == $limit ) break;
                  
               }
               
            }
            
         }
         
         CacheEngine::write( $identifier, $products );
         
         return $products;

      }
      
      static function listProductsByGroup( $tag ) {

         $identifier = sprintf( 'product-list-by-tag-%s-%d-%s-%s', Dispatcher::getPortal(), $limit, i18n::$language, $tag );

         $products = CacheEngine::read( $identifier );
         if( is_array( $products ) ) return $products;

         $products = array();
         $collection = new Product();

         foreach( DB::query( "SELECT DISTINCT(spo.productid), 
                                     ste.created
                                FROM site_product_option spo
                                JOIN site_product sp
                                  ON spo.productid = sp.id
                                JOIN site_textentity ste
                                  ON ste.id = sp.id
                               WHERE ste.deleted IS NULL
                                 AND spo.refid != 127 AND ste.grouping LIKE ?", "%$tag%" )->fetchAll() as $row ) {
            list( $productid ) = $row;
            $product = new Product( $productid );
            if( $product instanceof Product ) {

               if( $product->getUrl() && $product->getTitle() ) {
                  
                  $products[] = $product->asArray();
                  if( ++$productcount == $limit ) break;
                  
               }
               
            }
            
         }
         
         CacheEngine::write( $identifier, $products );

         return $products;

      }

      static function listProductsByCreated( $limit = 10 ) {

         $identifier = sprintf( 'product-list-by-created-%s-%d-%s', Dispatcher::getPortal(), $limit, i18n::$language );

         $products = CacheEngine::read( $identifier );
         if( is_array( $products ) ) return $products;

         $products = array();
         $collection = new Product();

         foreach( DB::query( "SELECT DISTINCT(spo.productid), 
                                     ste.created
                                FROM site_product_option spo
                                JOIN site_product sp
                                  ON spo.productid = sp.id
                                JOIN site_textentity ste
                                  ON ste.id = sp.id
                               WHERE ste.deleted IS NULL
                                 AND spo.tags NOT LIKE '%productionmethod%'
                                 AND spo.tags NOT LIKE '%correctionmethod%'
                                 AND spo.tags NOT LIKE '%paperquality%'
                                 AND spo.tags NOT LIKE '%subscription%'
                                 AND spo.tags NOT LIKE '%notoplists%'
                                 AND spo.tags NOT LIKE '%print%'
                                 AND spo.refid != 127
                            ORDER BY ste.created DESC" )->fetchAll() as $row ) {

            list( $productid ) = $row;
            $product = new Product( $productid );
            if( $product instanceof Product ) {

               if( $product->getUrl() && $product->getTitle() ) {
                  
                  $products[] = $product->asArray();
                  if( ++$productcount == $limit ) break;
                  
               }
               
            }
            
         }
         
         CacheEngine::write( $identifier, $products );

         return $products;

      }

      public function getUrl() {

         return $this->getTranslatedURL( i18n::languageCode() );

      }

      public function getTranslatedURL( $langcode = '' ) {
         
         $siteid = Dispatcher::getSiteId()?Dispatcher::getSiteId():1;
         
         try {
            if( $this->defaultmenuid ) {
               
               $menuid = $this->defaultmenuid;
               
            } else {
            
               $urlname = $this->getUrlName( $langcode );
               $menuid = (int) DB::query( "SELECT smc.menuid
                                             FROM site_menu_contents smc
                                        LEFT JOIN site_menu_portals smp
                                               ON smp.menuid = smc.menuid
                                        LEFT JOIN site_menu sm
                                               ON sm.id = smc.menuid
                                            WHERE sm.deleted IS NULL
                                              AND sm.siteid = ?
                                              AND sm.url LIKE '/products/%'
                                              AND (smp.portal = 'all' OR smp.portal = ?)
                                              AND smc.textentityid = ?
                                         ORDER BY smc.created DESC LIMIT 1", $siteid, Dispatcher::getPortal(), $this->id )->fetchSingle();
               
               if( !$menuid )
               $menuid = (int) DB::query( "SELECT smc.menuid
                                             FROM site_menu_contents smc
                                        LEFT JOIN site_menu_portals smp
                                               ON smp.menuid = smc.menuid
                                        LEFT JOIN site_menu sm
                                               ON sm.id = smc.menuid
                                            WHERE sm.deleted IS NULL
                                             AND sm.siteid = ?
                                              AND (smp.portal = 'all' OR smp.portal = ?)
                                              AND smc.textentityid = ?
                                         ORDER BY smc.created DESC LIMIT 1", $siteid, Dispatcher::getPortal(), $this->id )->fetchSingle();
               
            }
            
            if( $menuid ) {
               
               $menu = new MenuItem( $menuid );
               $menuurl = $menu->getTranslatedUrl( $langcode );
               
            } else {
               
               return '';
               
            }
            
            return sprintf( '%s/%s', rtrim( $menuurl, '/' ), $urlname );

         } catch( Exception $e ) {

            return '';

         }

      }

      public function getMenuItems() {

         $queryString = "
            SELECT
               menuid
            FROM
               site_menu_contents AS a
            WHERE
               a.textentityid=?
            ";

         $ret = array();
         foreach ( DB::query( $queryString, $this->id )->fetchAllAs( 'MenuItem' ) as $menuItem ) {

            $ret[] = array(
               'id' => $menuItem->id,
               'url' => $menuItem->url,
               'title' => $menuItem->title
               );

         }

         return $ret;

      }

      public function asArray() {
         
         if( file_exists( '/data/pd/ef28/cms/3dimages/' . $this->id ) ){ 
            $images360 = 1;
         }
         else{
            $images360 = null;
         }

         $images = array();
         foreach( explode( ',', $this->images ) as $image ) {
            $images[] = array( 'url' => $image );
         }

         $options = array();
         foreach( $this->options as $option ) {
            $option =  $option->asArray();
            
            
            if( $option['price'] == 0 ){
               //return false;
            }

            if( $option['valid'] ){
               $options[] = $option;
            }
         }

         uasort( $options, 'orderProductOptionsByKeyPriceAsc' );

         $array = array(
            'id' => $this->id,
            'title' => $this->title,
            'shorttitle' => $this->shorttitle,
            'urlname' => $this->urlname,
            'url' => $this->url,
            'ingress' => $this->ingress,
            'body' => $this->body,
            'images' => $images,
            'images360' => $images360,
            'options' => $options,
            'type' => $this->type,
         );

         if( count( $options ) > 0 ) {

            $array['option'] = current( $options );

         }
         
         if( strlen( $this->customjs ) > 0 ) $array['customjs'] = $this->customjs;
         if( strlen( $this->customcss ) > 0 ) $array['customcss'] = $this->customcss;
         if( strlen( $this->comment ) > 0 ) $array['comment'] = $this->comment;

         return $array;

      }

      public function delete() {

         // find all product-options and delete them too
         $collection = new ProductOption();
         foreach( $collection->collection( array( 'id' ), array( 'productid' => $this->id ) )->fetchAllAs( 'ProductOption' ) as $option ) {
            $option->delete();
         }

      }
      
      public function addHistoricalPrice( $portal, $price ) {
         
         $historicalprice = new DBProductPriceHistory();
         $historicalprice->productid = $this->id;
         $historicalprice->portal = $portal;
         $historicalprice->price = $price;
         $historicalprice->save();
         
         return $historicalprice;
         
      }
      
      public function getHistoricalPrices() {
         
         $result = array();
         $collection = new DBProductPriceHistory();
         foreach( $collection->collection( null,
            array( 
               'productid' => $this->id, 
            )
         )->fetchAllAs('DBProductPriceHistory') as $historicalprice ) {
            $result[] = $historicalprice->asArray();
         }
         
         return $result;
         
      }
      
      public function deleteHistoricalPrice( $id ) {
         
         $historicalprice = new DBProductPriceHistory( $id );
         $historicalprice->delete();
         
      }
      public function activateHistoricalPrice( $id, $active ) {
         
         $historicalprice = new DBProductPriceHistory( $id );
         
         if( $active == 'true' ){
            $historicalprice->active = true;
         }else{
            $historicalprice->active = false;
         }
         
         
         $historicalprice->save();
         
      }
      
      public function savePrice( $price , $countryid = 160  ){
         //check if exist
         $priceobject = new DBPrice( $this->getPriceID( $countryid ) );
         if( !$priceobject->id ){
            $priceobject->productid = $this->id;
            $priceobject->countryid = $countryid;
         }
         $priceobject->price = $price;
         $priceobject->save();
         
      }
      
      
      public function getPriceID( $countryid =  160 ){
         $priceid =  DB::query( "SELECT id FROM site_product_price WHERE productid = ? AND countryid = ? ", $this->id, $countryid )->fetchSingle();
         return $priceid;
      }
      
      static function getPrice( $id, $country =  'no' ){
         
         $languagearray = array( 'no' => 160, 'se' => 203  );
         $countryid = $languagearray[$country];
         
         if( $countryid ){
            $price = DB::query( "SELECT price FROM site_product_price WHERE productid = ? AND countryid = ? ", $id, $countryid )->fetchSingle();
            
            return $price ? $price : 0.00;
         }
         else{
            return 0.00; 
         }
         
      
      }
      
   }

   model( 'site.productoption' );
   import( 'website.helper' );

   class ProductOption extends DBProductOption {

      public function asArray() {

         if( $this->hasTag( 'mediaclip' ) ) {
            $type = 'mediaclip';
         } 
         else if( $this->hasTag( 'gift' ) ) {
            $type = 'gift';
         } 
         else if( $this->hasTag( 'ukeplan' ) ) {
            $type = 'ukeplan';
         }
         else if( $this->hasTag( 'smilesontiles' ) ) {
            $type = 'smilesontiles';
         }
         else if( $this->hasTag( 'textgift' ) ) {
            $type = 'textgift';
         }
         else if( $this->hasTag( 'merkelapp' ) ) {
            $type = 'merkelapp';
         }
         else if( $this->hasTag( 'stempel' ) ) {
            $type = 'stempel';
         }
         else {
            $type = 'goods';
         }

         $historicalprice = $this->getHistoricalPrice( Dispatcher::getPortal() );
         
         if( $historicalprice ){
            $showhistorical = true;
         }else{
            $showhistorical = false;
         }
         
         $lowestprice = (float) $this->getPrice( 9999 );
         
         if( count( $this->getPrices() ) == 0 ){
            $valid = false;
         }else{
            $valid = true;
         }
         
         $ret =  array(
            'id' => $this->id,
            'title' => $this->title,
            'urlname' => $this->urlname,
            'prodno' => $this->prodno,
            'price' => $lowestprice,
            'historical' => array(
               'price' => $historicalprice,
               'rebate' => $historicalprice ? round( ( 100 - round( 100 * $lowestprice / $historicalprice ) ) / 5 ) * 5 : 0,
               'showhistorical' => $showhistorical,
               'showrebate' => $this->hasTag( 'showrebate' ),
               'newproduct' => $this->hasTag( 'newproduct' ),
            ),
            'type' => $type,
            'prices' => $this->getPrices(),
            'refsubid' => $this->refsubid,
            'purchaseurl' => $this->purchaseurl,
            'weigth' => $this->getUnitWeight(),
            'orderkey' => $this->orderkey,
            'valid'    => $valid,
            'thumb'    => $this->thumb,
         );
         
         
        return $ret;

      }

      public function getHistoricalPrice( $portal ) {
         
         $historicalprice = Model::fromFieldValue( 
            array( 
               'productid' => $this->productid, 
               'portal' => $portal,
               'active' => true
            ), 
            'DBProductPriceHistory',
            false
         );
         
         if( !$historicalprice instanceof DBProductPriceHistory ) {
            
            $historicalprice = Model::fromFieldValue( 
               array( 
                  'productid' => $this->productid, 
                  'portal' => 'all',
                  'active' => 'true'
               ), 
               'DBProductPriceHistory'
            );
            
         }
         
         if( $historicalprice instanceof DBProductPriceHistory ) {
            
            return $historicalprice->price;
            
         } else {
            
            return false;
            
         }
         
      }
      
      /**
       * Get the unit weight for this product
       *
       * @return float
       */
      public function getUnitWeight() {

         $identifier = sprintf( 'productoption-unitweight-%d', $this->refid );
         if( $weight = CacheEngine::read( $identifier ) ) return $weight;
         
         $weight = DB::query( "
            SELECT weight
            FROM article
            WHERE artnr = ?
         ", $this->refid )->fetchSingle();
         
         CacheEngine::write( $identifier, $weight, 120 );
         return $weight;
         
      }

      static function fromProdNo( $prodno ) {

         try {
            
            if( $prodno > 100000 ){
               return new ProductOption($prodno);
            }
            else{      
               return ProductOption::fromFieldValue(
                  array(
                     'prodno' => $prodno,
                     'deleted' => null,
                  ),
                  'ProductOption'
               );
            }


         } catch ( Exception $e ) {

            return false;

         }

      }


      /**
       * Create a productoption from refid
       *
       * @param integer $refid
       * @return object
       */
      static function fromRefId( $refid ) {

         try {
            
            if( $prodno > 10000 ){
               return new ProductOption($prodno);
            }
            else{    
               return ProductOption::fromFieldValue(
                  array(
                     'refid' => $refid,
                     'deleted' => null,
                  ),
                  'ProductOption'
               );
            }

         } catch( Exception $e ) {

            return false;

         }

      }
      
      
      /**
       * Get the price for this product
       * at the given quantity
       *
       * @param integer $prodno
       * @param integer $quantity
       *
       */
      public function getPrice( $quantity = 1, $refid = null ) {
         
         $regionid = WebsiteHelper::region();
         
         $countryid = Dispatcher::getCustomAttr( 'countryid' );

         $productgroup = ProductOption::getProductParent( $this->refid, $regionid );

         $identifier = sprintf( 'productoption-price-%s-%s-%d-%d', $regionid, $productgroup, $quantity, $this->refid );
         if( $price = CacheEngine::read( $identifier ) ) return $price;
         
         $price = DB::query( "SELECT price FROM site_product_price WHERE productid = ? AND countryid = ?" , $this->productid, $countryid )->fetchSingle();
         
         if( $price  > 0 ){
            if( $refid && $this->id == 3529 ){
               model( 'smilesontiles.project' );
               $project = new DBSmilesProject( $refid );
               $clipsize = json_decode(  $project['clipsize'] );
               $price +=  round(  $clipsize->tilesize->dx * $clipsize->tilesize->dy * 0.002 );
            }
            /*else if( $this->id == 3529 ){
               //if( $price < 89){
                  $price += 20;
               //}
            }*/
            
            CacheEngine::write( $identifier, $price, 120 );
            return $price;
         }
         else{
         
         $price = DB::query( "
            SELECT
               price
            FROM prices
            WHERE artnr=?
               AND regionid=?
               AND pricegroup
               IN( SELECT quantumid
                   FROM group_quantum
                   WHERE arttype=?
                   AND min_antall<=?
                   ORDER BY min_antall
                   DESC LIMIT 1
               )
         ", $this->refid, $regionid, $productgroup, $quantity )->fetchSingle();
         }
         
         CacheEngine::write( $identifier, $price, 120 );
         
         return $price;
         
      }



      /**
       * Get all prices and priceranges
       * for this product
       *
       * @return array
       *
       */
      public function getPrices() {
         
         $res = array();
         
         $regionid = WebsiteHelper::region();
         $productgroup = ProductOption::getProductParent( $this->refid, $regionid );
   
         $identifier = sprintf( 'productoption-prices-%s-%s-%d', $regionid, $productgroup, $this->refid );
         
         if( $res = CacheEngine::read( $identifier ) ) return $res;
         
         if( $this->refid > 10000 ){
            
            $res []= array(
                "min"    => 0,
                "price"  => Product::getPrice( $this->productid ),
             );
            
         }
         else{
            
           $prices = DB::query( "
               SELECT
                  price,
                  min_antall
               FROM
                  prices,
                  group_quantum
               WHERE
                  quantumid=pricegroup AND
                  artnr=? AND
                  regionid=?
               ORDER BY min_antall;
            ", $this->refid, $regionid );
   
           
   
           while( list( $price, $minimumQuantity ) = $prices->fetchRow() ) {
   
              if( $minimumQuantity == 0 ) $minimumQuantity = 1;
   
             $res []= array(
                "min"    => $minimumQuantity,
                "price"  => $price,
             );
   
           }
   
           // Loop through and set max amount for each quantum
           if( count( $res ) > 0 ) {
   
              for( $i=0; $i<count( $res ); $i++ ) {
   
                 if( isset( $res[$i+1]["min"] ) ) {
                    $res[$i]["max"] = ( $res[$i+1]["min"] - 1 );
                 }
   
              }
   
           }
         }

      	CacheEngine::write( $identifier, $res, 120 );
         
      	return $res;

      }


      static function priceFromProdNo( $prodno = 0, $quantity = 1 ) {

         $product = ProductOption::fromProdNo( $prodno );
         if( !$product->isLoaded() || !$product instanceof ProductOption ) return false;

         return $product->getPrice( $quantity );

      }

      public function hasTag( $tag ) {

         //return ( strpos( $this->tags, $tag ) !== false ) ? true : false;
         $producttags = explode( ' ', $this->tags );
         if( is_array( $producttags ) ) {
            if( in_array( $tag, $producttags ) ) return true;
         }
         //return ( strpos( $this->tags, ' '.$tag.' ' ) !== false ) ? true : false; 

         return false;

      }


      /**
       * Get the weight of a product times the quantity of it
       *
       * @param string $prodno
       * @param integer $quantity
       * @return float
       *
       */
      static function getWeight( $prodno = 0, $quantity = 0 ) {

         $identifier = sprintf( 'productoption-weightbyprodno-%s', $prodno );
         if( $weight = CacheEngine::read( $identifier ) ) return ( $weight * $quantity );
         
         $product = ProductOption::fromProdNo( $prodno );
         if( !$product->isLoaded() || !$product instanceof ProductOption  ) return false;

         $quantity = (int) $quantity;

         $weight = DB::query( "
            SELECT weight
              FROM article
             WHERE artnr = ?
         ", $product->refid )->fetchSingle();
         
         CacheEngine::write( $identifier, $weight, 120 );
         
         return ( $weight * $quantity );

      }



      /**
       * Get the grouptype of given product
       *
       * @param integer $productid
       * @param integer $regionid
       * @return integer
       *
       */
      static function getProductParent( $productid = 0, $regionid = 0 ) {

         $identifier = sprintf( 'productoption-productparent-%s-%s', $productid, $regionid );
         if( $grouptype = CacheEngine::read( $identifier ) ) return $grouptype;
         
         if( $productid > 0 ) {

            $grouptype = DB::query( "
               SELECT grouptype
                 FROM article, prices
                WHERE article.artnr = prices.artnr
                  AND article.artnr = ? 
                  AND regionid = ?
            ", $productid, $regionid )->fetchSingle();

         }
         
         CacheEngine::write( $identifier, $grouptype, 120 );
         
         return $grouptype;
         
      }

      static function getLegacyArticleOptions( $legacyrefid ) {

         $options = array( 0 => array( __( 'Not set' ), __( 'Default' ) ) );

         foreach( DB::query( "SELECT a.alternativeid,
                                     ao.sorting,
                                     name.message as name,
                                     descr.message as description,
                                     oname.message as title
                                FROM article_alternative_opt ao
                           LEFT JOIN article_alternative a
                                  ON ao.alternativeid = a.alternativeid
                           LEFT JOIN language_resource name
                                  ON name.lang_res_id = a.name
                                 AND ( name.language = 1 OR name.language = 2 OR name.language = 3 )
                           LEFT JOIN language_resource descr
                                  ON descr.lang_res_id = a.descr
                                 AND ( descr.language = 1 OR descr.language = 2 OR descr.language = 3 )
                           LEFT JOIN language_resource oname
                                  ON oname.lang_res_id = ao.name
                                 AND ( oname.language = 1 OR oname.language = 2 OR oname.language = 3 )
                               WHERE a.artnr = ?
                            ORDER BY a.alternativeid, ao.sorting, name.language, descr.language, oname.language",
                                     $legacyrefid )->fetchAll() as $row ) {

            list( $optionid, $suboptionid, $name, $description, $title ) = $row;
            if( $name || $title || $description ) {
               $options[sprintf( '%d-%d', $optionid, $suboptionid )] = array( $title, sprintf( '%s (%s)', $name, $description ) );
            }

         }

         return $options;

      }

      static function getLegacyArticles() {

         $articles = array( 0 => __( 'Not set' ) );

         foreach( DB::query( "
            SELECT a.artnr, lr.message
            FROM article AS a
            LEFT JOIN language_resource AS lr ON lr.lang_res_id = a.name AND (lr.language = 1 OR lr.language = 2 OR lr.language = 3)
            WHERE a.artnr IS NOT NULL
            ORDER BY a.artnr ASC, lr.language
         " )->fetchAll() as $row ) {

         list( $artnr, $message ) = $row;

            $articles[$artnr] = sprintf( '%04d - %s', $artnr, $message );

         }

         return $articles;

      }
      
   }
   
   if( !function_exists('orderProductOptionsByKeyPriceAsc')) {
      
      function orderProductOptionsByKeyPriceAsc( $a, $b ) {
         
         if ( $a['orderkey'] == 0 ) {
            
            if ($a['price'] == $b['price']) {
               return 0;
            }
            
            return ($a['price'] < $b['price']) ? -1 : 1;
            
         } else {
            
            if ($a['orderkey'] == $b['orderkey']) {
               return 0;
            }
            
            return ($a['orderkey'] < $b['orderkey']) ? -1 : 1;
            
         }
         
      }
      
   }
   
?>
