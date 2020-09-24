<?php
Dispatcher::extendView( 'bestilling.default' );

   class BestillingMerkelapp extends BestillingMerkelappIndex implements IView {
      
      protected $template = 'bestilling.senior_stempel';
      private $orderfolder = '/data/global/merkelapp/';
      
      public function Execute() {
         
         
         $project = new UserMerkelappOrder();
         $project->date = date( 'Y-m-d H:i:s');
         
         if( Login::isLoggedIn() ){
            
            $project->userid = Login::userid();
            
         }
         
         $project->save();
         $selected = array();
         
         
         // 5776 = Navnestempel for klær, 5887= Navnestempel for klær med ekstra blekkpute
         $productoptionids = array( 5776, 5887  );
         
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
