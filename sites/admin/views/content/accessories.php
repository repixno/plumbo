<?PHP
   
   import( 'pages.admin' );
   import( 'website.product' );
   import( 'website.product.accessory' );
   
   class AccessoriesList extends AdminPage implements IView {
      
      protected $template = 'content.accessories';
      
      public function Execute( $accessoryid = null ) {
         
         if( isset( $accessoryid ) ) return $this->edit( $accessoryid );
         
         if( isset( $_POST['save'] ) ) {
            $this->save( $_POST['save'] );
            relocate( '/content/accessories/0' );
            die();
         }
         
         $accessories = array(
            'products' => array(),
            'common' => array(),
         );
         
         $collection = new ProductAccessory();
         foreach( $collection->collection(array('accessoryid'))->fetchAllAs('ProductAccessory') as $accessory ) {
            
            if( $accessory->productid > 0 ) {
               
               if( !isset( $accessories['products'][$accessory->productid]['product'] ) ) {
                  
                  $product = new Product( $accessory->productid );
                  $accessories['products'][$accessory->productid]['product'] = $product->asArray();
                  
               }
               
               $accessories['products'][$accessory->productid]['options'][] = $accessory->asArray();
               
            } else {
               
               $accessories['common'][] = $accessory->asArray();
               
            }
            
         }
         
         $this->accessories = $accessories;
         
      }
      
      private function Save( $data ) {
         
         if( isset( $data['accessoryid'] ) ) {
            
            if( $data['accessoryid'] > 0 ) {
               try {
                  $accessory = new ProductAccessory( $data['accessoryid'] );
               } catch( Exception $e ) {
                  $accessory = new ProductAccessory();
               }
            } else {
               $accessory = new ProductAccessory();
            }
            
            unset( $data['accessoryid'] );
            
            foreach( $data as $key => $val ) {
               $accessory->$key = $val;
            }
            
            $accessory->save();
            
         }
         
      }
      
      public function delete( $accessoryid = 0 ) {
         
         if( $accessoryid > 0 ) {
            
            try {
               $accessory = new ProductAccessory( $accessoryid );
               if( $accessory->isLoaded() ) {
                  $accessory->delete();
               }
            } catch( Exception $e ) {}
            
         }
         
         relocate( '/content/accessories/' );
         
      }
      
      public function edit( $accessoryid = 0 ) {
         
         $this->setTemplate( 'content.accessoryeditor' );
         
         if( $accessoryid > 0 ) {
            
            try {
               $accessory = new ProductAccessory( $accessoryid );
            } catch ( Exception $e ) {
               $accessory = new ProductAccessory();
            }
            
         } else {
            
            $accessory = new ProductAccessory();
            
         }
         
         // fetch all products
         $products = array();
         $collection = new Product();
         foreach( $collection->collection( array( 'id' ), array( 'deleted' => null ) )->fetchAllAs('Product') as $product ) {
            
            $id = $product->id;
            $products[$id] = array(
               'id' => $id,
               'title' => $product->title,
               'ingress' => $product->ingress,
               'selectedproduct' => $accessory->productid == $id ? true : false,
               'selectedaccessory' => $accessory->accessoryproductid == $id ? true : false,
            );
            
         }
         
         
         /*
         // fetch all productoptions
         $allproductoptions = $productoptions = array();
         $collection = new ProductOption();
         foreach( $collection->collection( array( 'id' ), array( 'deleted' => null ) )->fetchAllAs('ProductOption') as $productoption ) {
            $allproductoptions[$productoption->productid]['options'][''] = array(
               'id' => $id,
               'title' => $productoption->title,
               'productid' => $productoption->productid,
               'selectedonlyoption' => $accessory->onlyoptionid == $id ? true : false,
            );
         }
         
         foreach( $allproductoptions as $productid => $protuctoption ) {
            foreach( $productoption as $id => $title ) {
               $productoptions[$id] = array(
               );
            }
         }
         */
         
         $this->connection = array(
            'id' => $accessory->accessoryid,
            'minquantity' => $accessory->minquantity,
            'maxquantity' => $accessory->maxquantity,
            'product' => array(
               'id' => $accessory->productid,
               'title' => $products[$accessory->productid]['title'],
               'ingress' => $products[$accessory->productid]['ingress'],
            ),
            'accessory' => array(
               'id' => $accessory->accessoryproductid,
               'title' => $products[$accessory->accessoryproductid]['title'],
               'ingress' => $products[$accessory->accessoryproductid]['ingress'],
            ),
         );
         
         $this->products = array_values( $products );
         // $this->productoptions = array_values( $productoptions );
         
         
      }
      
   }
   
?>