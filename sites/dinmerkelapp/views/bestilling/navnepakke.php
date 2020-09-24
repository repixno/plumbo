<?php
   import( 'session.usersessionarray' );
   import( 'website.cart' );
   import( 'website.order.merkelapporder' );

   class BestillingMerkelapp extends WebPage implements IView {
      
      protected $template = 'bestilling.navnepakke';
      private $orderfolder = '/data/global/merkelapp/';
      
      public function Execute() {
         
         $project = new UserMerkelappOrder();
         $project->date = date( 'Y-m-d H:i:s');
         
         if( Login::isLoggedIn() ){
            $project->userid = Login::userid();
         }
         
         $project->save();
         $selected = array();
         
         $productoptionids = array( 6045 );
          //$productoptionids = array( 5593, 5595, 5597, 5599, 5601, 5603, 5605, 5607,5609 );
        
         foreach ( $productoptionids as $ret ){
            
            $productoption = new ProductOption( $ret ); 
            $product = new Product( $productoption->productid );
            $selected[] = array(
               'productoptionid' => $ret,
               'productoption' => $productoption->asArray(),
               'productid' => $productoption->productid,
               'product' => $product->asArray()
            );  
         } 
         $this->projectid = $project->id;
         $this->selected = $selected;
        
      }
      
      public function addtocart(){
         
         //Util::debug( $_POST );
         
         
         $this->template = null;
         
         $line1    = isset( $_POST['line1'] ) ? $_POST['line1'] : '';
         $line2   = isset( $_POST['line2'] ) ? $_POST['line2'] : '';
    //     $line3    = isset( $_POST['line3'] ) ? $_POST['line3'] : '';
         $mal = isset( $_POST['mal'] ) ? $_POST['mal'] : '';
         if( isset( $_POST['productoptionid'] ) ) $productoptionid = (int)$_POST['productoptionid'];
         if( isset( $_POST['quantity'] ) ) $quantity = (int)$_POST['quantity'];
         if( isset( $_POST['projectid'] ) ) $attributes = (int)$_POST['projectid'];
         
         
         try{
            $project = new UserMerkelappOrder();
            $project->line1 = $line1;
            $project->line2 = $line2;
            $project->line3 = $line3;
            $project->articleid = $productoptionid;
            $project->backgroundfile = $mal;
            $project->save();
         }catch (Exception $e ){
            
         }
         
         $attributes = array(
               'projectid' => $project['id']
         );
         
         $cart = new Cart();
         $cart->addItemByProductOptionId( $productoptionid, $quantity, $attributes );
         $cart->save();
         
          relocate( '/cart' );
         
      }

   }
   
   
?>
