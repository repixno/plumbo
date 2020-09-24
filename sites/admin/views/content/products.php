<?PHP
   
   import( 'website.product' );
   import( 'website.textentity' );
   
   Dispatcher::extendView( 'content.textentity' );
   
   class ProductEditor extends TextEntityEditor {
      
      protected $objectclass = 'Product';
      
      protected function editForms( DBTextEntity $object ) {
         
         $this->historicalprices = $object->getHistoricalPrices();
         
         $this->addlink = sprintf( '%s/editoption/%d/%d', $this->getEditorRoot(), $object->id, Model::CREATE );

         $collection = new ProductOption();

         foreach( $collection->collection( array( 'id' ), array( 'productid' => $object->id, 'languageid' => $languageid, 'deleted' => null ) )->fetchAllAs( 'ProductOption' ) as $productoption ) {
            
            $expired = strtotime( $productoption->validto ) < time() && isset( $productoption->validto ) ? true : false;

            $optionarray = $productoption->asArray();
            
            $options[] = array(
               'id'  => $productoption->id,
               'editlink'  => sprintf( '%s/editoption/%d/%d', $this->getEditorRoot(), $object->id, $productoption->id ),
               'deletelink'  => sprintf( '%s/deleteoption/%d/%d', $this->getEditorRoot(), $object->id, $productoption->id ),
               'items'     => $productoption,
               'expired'   => $expired,
               'orderkey'  => $productoption->orderkey,
               'price'     => $optionarray['price']
            );
            
         }
         if(is_array($options)){
            uasort( $options, 'orderProductOptionsByKeyPriceAsc');
         }

         $this->productoptions = $options;
         
      }

      protected function saveEntity( $object, $post, $files ) {

         if( isset( $files['save']['name']['common']['image'] ) && is_uploaded_file( $files['save']['tmp_name']['common']['image'] ) ) {

            if( strlen( $object->images ) > 0 ) {
               $images = explode( ',', $object->images );
            } 
            
            $file = $files['save'];
            
            $extension = explode( '.', $file['name']['common']['image'] );
            $extension = end( $extension );
            
            $image = sprintf( '%s.%s', UUID::create(), $extension );
            $images[] = $image;
            
            $filename = sprintf( '%s/data/images/products/%s', getRootPath(), $image );
            
            move_uploaded_file( $file['tmp_name']['common']['image'], $filename );
            
            $object->images = implode( ',', $images );
            
         }
         
         
         // Price values
         
         if( $object['siteid'] == 3 ){
            foreach( $post['save'] as $language => $languagevalues ) {
               foreach( $languagevalues as $key => $value ) {
                  switch( $key ) {
                     case 'price':
                        $this->savePrice( $object->id , $language , $value ); 
                        break;
                     default:
                        break;
                  }
                  
               }
            }
         }
         
         //die();
         return parent::saveEntity( $object, $post, $files );
         
      }
      
      public function savePrice( $productid , $language , $price ){
         
         
         $languagearray = array( 'nb_NO' => 160, 'sv_SE' => 203  );
         
         $countryid = $languagearray[$language];
         
         if( $countryid ){
            $product = new Product( $productid );         
            $product->savePrice( $price, $countryid  );
         }
         
         
      }
      
      public function deleteOption( $productid, $productoptionid ) {
         
         $productoption = new ProductOption( $productoptionid );
         $productoption->delete();
         
         relocate( '%s/%d', $this->getEditorRoot(), $productid );
         die();
         
      }
      
      public function editOption( $productid, $productoptionid ) {
         
         $this->setTemplate( 'content.productoptioneditor' );
         
         $productoption = new ProductOption( $productoptionid );
         if( !$productoption->isLoaded() ) {
            
            $productoption->productid = $productid;
            
         }
         
         if( isset( $_POST['save'] ) ) $this->saveOption( $productoption, $_POST, $_FILES );
         
         $legacyarticles = array();
         foreach( $productoption->getLegacyArticles() as $refid => $title ) {
            $legacyarticles[] = array(
               'refid' => $refid, 
               'title' => $title,
               'selected' => $refid == $productoption->refid ? true : false,
            );
         }
         
         $legacyoptionsdata = array();
         foreach( $productoption->getLegacyArticleOptions( $productoption->refid ) as $refid => $options ) {
            list( $title, $section ) = $options;
            $legacyoptionsdata[$section][] = array(
               'refid' => $refid, 
               'title' => $title,
               'selected' => $refid == $productoption->refsubid ? true : false,
            );
         }
         
         $legacyoptions = array();
         foreach( $legacyoptionsdata as $section => $options ) {
            
            $legacyoptions[] = array(
               'title' => $section,
               'items' => $options,
            );
            
         }
         
         $collection = new Language();
         foreach( $collection->collection( array( 'languageid' ), null, 'languageid' )->fetchAllAs( 'Language' ) as $language ) {
            $countrycode = explode( '_', $language->code );
            $countrycode = strtolower( end( $countrycode ) );
            $languages[] = array(
               'name'      => $language->elementname,
               'segment'   => $language->code,
               'country'   => $countrycode,
               'title'     => $productoption->getTitle( $language->code ),
               'urlname'   => $productoption->getUrlName( $language->code ),
               'ingress'   => $productoption->getIngress( $language->code ),
               'body'      => $productoption->getBody( $language->code ),
            );
            
         }
         
         $tags = array();
         foreach( explode( ' ', $productoption->tags ) as $tag ) {
            $tags[$tag] = true;
         }
         
         $product = new Product( $productid );
         $this->header = $product->title;
         
         
         if( $productoption->prodno > 100000 ){
            $useef = true;
         }else{
            $useef = false;
         }
         
         
         if( $productoption->thumb ){
            $this->productoptiontumb = "http://static.eurofoto.no/images/products/thumbs/square/90/". $productoption->thumb;
         }
         else{
            if( file_exists("/data/pd/ef28/products/" . $productid . "_" . $productoptionid . ".jpg" ) ){
               $this->productoptiontumb = "http://static.eurofoto.no/images/products/thumbs/square/90/". $productid . "_" . $productoptionid . ".jpg";
               
            }
         }
         
         $this->common  = array(
            'tags'      => $tags,
            'prodno'    => $productoption->prodno,
            'articles'  => $legacyarticles,
            'options'   => $legacyoptions,
            'validfrom' => $productoption->validfrom,
            'validto'   => $productoption->validto,
            'purchaseurl'=>$productoption->purchaseurl,
            'isproduct' => true,
            'backlink'  => sprintf( '%s/%d', $this->getEditorRoot(), $productid ),
            'useef' => $useef
         );
         
         $this->languages = $languages;
         
      }
      
      protected function editFields( Array $record, DBTextEntity $object ) {
         
         $images = explode( ',', $object->images );
         if( is_array( $images ) && count( $images ) > 0 ) {
            
            foreach( $images as $image ) if( !empty( $image ) ) {
               
               $record['images'][] = strlen( $image ) > 0 ? sprintf( 'images/products/thumbs/squareaspect/50/%s', $image ) : '';
               
            }
            
         }
         
         $record['isproduct'] = $object->type == 'product' ? true : false;
         
         return $record;
         
      }
      
      protected function saveOption( $object, $post, $files ) {
         
         $imagefolder = "/data/pd/ef28/products/";
         
         if( isset( $post['tags'] ) ) {
            
            $object->tags = implode( ' ', array_keys( $post['tags'] ) );
            
         }
         
         // make sure the object has an id before we attempt to save the title
         if( $object->id == Model::CREATE ) {
            $object->save();
         }

         if( $files['productoptionimage']['error'] == 0 ){
            
            if( $object->thumb ){
               $imagefile = $imagefolder . '/' . $object->thumb . '.jpg';
            }else{
               $imagefile = $imagefolder . '/' . $object->productid . '_' . $object->id . '.jpg';
            }
            
            if( file_exists( $imagefile ) ){
               unlink( $imagefile );
            }
            
            $thumbname = md5( $object->productid . '_' . $object->id . time() );
            
            $imagefile = $imagefolder . '/' . $thumbname . '.jpg';
            
            $object->thumb  = $thumbname . '.jpg';
            
            move_uploaded_file( $files['productoptionimage']['tmp_name'], $imagefile );
            
         }
         
         // Normal values
         foreach( $post['save'] as $language => $languagevalues ) {
            
            foreach( $languagevalues as $key => $value ) {
               
               switch( $key ) {
                  case 'title':
                     $object->setTitle( $value, $language );
                     break;
                  case 'urlname':
                     $object->setUrlName( ( $value ? $value : $languagevalues['title'] ), $language );
                     break;
                  case 'body':
                     $object->setBody( $value, $language );
                     break;
                  case 'ingress':
                     $object->setIngress( $value, $language );
                     break;
                  case 'useef3':
                     $object->refid = sprintf( '1%04d', $object->id );
                     $object->prodno = sprintf( '1%04d', $object->id );
                     break;
                  default:
                     $object->$key = $value;
                     break;
                  
               }
               
            }
            
         }
         
         $object->save();
         
         relocate( '%s/editoption/%d/%d', $this->getEditorRoot(), $object->productid, $object->id );
            
      }
      
   }
   
?>