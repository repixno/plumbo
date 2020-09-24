<?php
Dispatcher::extendView( 'bestilling.default' );

   class BestillingMerkelapp extends BestillingMerkelappIndex implements IView {
      
      protected $template = 'bestilling.index';
      private $orderfolder = '/data/global/merkelapp/';
      
      public function Execute() {
         
         
         $project = new UserMerkelappOrder();
         $project->date = date( 'Y-m-d H:i:s');
         
         if( Login::isLoggedIn() ){
            
            $project->userid = Login::userid();
            
         }
         
         $project->save();
         $selected = array();
         
         $productoptionids = array(  2730 );
         
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
