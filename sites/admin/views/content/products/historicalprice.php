<?PHP
   
   import( 'pages.json' );
   import( 'website.product' );
   
   class HistoricalPriceAPI extends JSONPage implements IValidatedView, AdminRequired {
      
      public function Validate() {
         return array(
            'add' => array(
               'post' => array(
                  'productid' => VALIDATE_INTEGER,
                  'price'     => VALIDATE_FLOAT,
                  'portal'    => VALIDATE_STRING,
               ),
            ),
            'delete' => array(
               'post' => array(
                  'productid' => VALIDATE_INTEGER,
                  'priceid'   => VALIDATE_INTEGER,
               ),
            ),
            'activate' => array(
               'post' => array(
                  'productid' => VALIDATE_INTEGER,
                  'priceid'   => VALIDATE_INTEGER,
                  'active'   => VALIDATE_STRING,
               ),
            )
         );
      
      }
      
      public function Add() {
         
         try {
            
            $productid = $_POST['productid'];
            $price = $_POST['price'];
            $portal = $_POST['portal'];
            
            $product = new Product( $productid );
            $price = $product->addHistoricalPrice( $portal, $price );
            
            $this->price = $price->asArray();
            $this->result = true;
            $this->message = 'OK';
            
         } catch( Exception $e ) {
            
            $this->result = false;
            $this->message = $e->getMessage();
            
         }
         
      }
      
      public function Delete() {
         
         try {
            
            $productid = $_POST['productid'];
            $priceid = $_POST['priceid'];
            
            $product = new Product( $productid );
            $price = $product->deleteHistoricalPrice( $priceid );
            
            $this->result = true;
            $this->message = 'OK';
            
         } catch( Exception $e ) {
            
            $this->result = false;
            $this->message = $e->getMessage();
            
         }
         
      }
      
      public function Activate() {
         
         try {
            
            $productid = $_POST['productid'];
            $priceid = $_POST['priceid'];
            $active = $_POST['active'];
            
            $product = new Product( $productid );
            $price = $product->activateHistoricalPrice( $priceid, $active );
            
            $this->result = true;
            $this->message = 'OK';
            
         } catch( Exception $e ) {
            
            $this->result = false;
            $this->message = $e->getMessage();
            
         }
         
      }
      
   }


?>