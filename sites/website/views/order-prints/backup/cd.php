<?php

   /**
    * View for ordering backup DVDs
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    *
    */

   import( 'website.cart' );
   config( 'website.cart' );

   class OrderPrintsBackupCD extends UserPage implements IView {
      
      protected $template = 'order-prints.backup.cd';
      
      // Max size for a DVD in Mb
      private $maxmbsize = 700;
      
      
      /**
       * Calculate the different sizes and
       * quantities for images and discs
       * 
       * @author Andreas Färnstrand <andreas.farnstrandæinterweb.no>
       *
       */
      public function Execute() {
      
         // Setup productoption
         $settings = Settings::Get( 'cart', 'set' );
         $settings = $settings['options'];
         $productoptionid = $settings['CD'];

         // Get product data
         $productoption = new ProductOption( $productoptionid );
         $product = new Product( $productoption->productid );

         $this->product = $product->asArray();
         

         // Get and calculate data for user's images
         $res = DB::query( "
               SELECT 
                  count(*) as antall, 
                  sum(size) as totalsize 
               FROM 
                  bildeinfo 
               WHERE 
                  owner_uid=? AND 
                  deleted_at is null AND
                  quarantined_at IS NULL
               ", Login::userid() );

         list( $qty, $totalsize ) = $res->fetchRow();

         // Setup sizes and quantities for template
         $this->imagequantity = $qty;
         $this->size = $totalsize;
         $this->mbsize = sprintf( "%.02f",( $this->size/1024/1024) );
         $this->discquantity = (int) ceil( $this->mbsize / $this->maxmbsize );
         $this->price = ProductOption::priceFromProdNo( $this->product['options'][0]['prodno'], $this->discquantity ) * $this->discquantity;
         $this->productoptionid = $productoptionid;
         
      }
      
      
      /**
       * Adds discs to cart. Need own function
       * to be able to add extra attributes to
       * order row.
       *
       * @param string $productoptionid
       * @param integer $quantity
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       * 
       */
      public function addToCart( $productoptionid = 0, $quantity = 1 ) {
      
         $this->setTemplate( '' );
         if( !$quantity > 0 ) {
            $quantity = 1;
         }
         
         if( is_array( $_POST ) ) {
            
            if( isset( $_POST['id'] ) ) {
               $productoptionid = $_POST['id'];
            }
            
            if( isset( $_POST['quantity'] ) ) {
               $quantity = (int) $_POST['quantity'];
            }
            
         }
         
         // Get file info
         $res = DB::query( "
               SELECT 
                  count(*) as antall, 
                  sum(size) as totalsize 
               FROM 
                  bildeinfo 
               WHERE 
                  owner_uid=? AND 
                  deleted_at is null AND
                  quarantined_at IS NULL
               ", Login::userid() 
         );
            
         list( $qty, $totalsize ) = $res->fetchRow();

         // Calculate all sizes and quantities
         $this->imagequantity = $qty;
         $this->size = $totalsize;
         $this->mbsize = sprintf( "%.02f",( $this->size/1024/1024) );
         $this->discquantity = ceil( $this->mbsize / $this->maxmbsize );
         
         // Get the total disc quantity
         $totaldiscquantity = ( $quantity * $this->discquantity );
         
         // Set all attributes needed by cart
         $attributes = array(
            'set' => true,
            'setitemquantity' => $this->discquantity,
            'totalitemquantity' => $totaldiscquantity,
            'type' => 'CD',
         );
         
         
         // Add to cart
         $cart = new Cart();
         $cart->addItemByProductOptionId( $productoptionid, $quantity, $attributes );
         
         $cart->save();
         
         relocate( '/cart' );
         
      }
      
   }

?>