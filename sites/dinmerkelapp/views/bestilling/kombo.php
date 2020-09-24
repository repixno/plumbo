<?php
Dispatcher::extendView( 'bestilling.default' );

   class BestillingMerkelapp extends BestillingMerkelappIndex implements IView {
      
      protected $template = 'bestilling.kombo';
      private $orderfolder = '/data/global/merkelapp/';
      
      public function Execute() {
         
         
         $project = new UserMerkelappOrder();
         $project->date = date( 'Y-m-d H:i:s');
         
         if( Login::isLoggedIn() ){
            
            $project->userid = Login::userid();
            
         }
         
         $project->save();
         $selected = array();
         
        // tatt vekk strykelapp 02.08.2018 : 2734, 2732 
           $productoptionids = array(  2734 );
         
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
