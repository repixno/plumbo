<?php
Dispatcher::extendView( 'bestilling.default' );

   class BestillingMerkelapp extends BestillingMerkelappIndex implements IView {
      
      protected $template = 'bestilling.utelappen_stempel';
      private $orderfolder = '/data/global/merkelapp/';
      
      public function Execute() {
         
         
         $project = new UserMerkelappOrder();
         $project->date = date( 'Y-m-d H:i:s');
         
         if( Login::isLoggedIn() ){
            
            $project->userid = Login::userid();
            
         }
         
         $project->save();
         $selected = array();
         
         
         // content/products/editoption/5905/5906
         $productoptionids = array( 5900, 5906  );
         
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

   }
   
   
?>
