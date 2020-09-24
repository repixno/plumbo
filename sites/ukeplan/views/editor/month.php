<?php
   import( 'website.product' );
   import( 'website.gifttemplate' );

   class CreateUkeplan extends WebPage implements IView {
      
      protected $template = 'editor.month';
      
      public function execute( $productid = 0, $productoptionid = 0, $gifttemplateid = 0 ) {

         $product = new Product( $productid );
         $productoption = new ProductOption( $productoptionid );

         $template = new GiftTemplate( $gifttemplateid );
         $templateArray = $template->asArray();
         $pages = $templateArray['pages'][0];
         
         $backgroundarray = array( 2779, 2781, 2783,5097,5099,5101 );

         
         $templates = array(
            1 => array(
               'id'   => $templateArray['id'],
               'mal_width'       => $pages['fullsize_x'],
               'mal_height'      => $pages['fullsize_y'],
               'imagefield_height'=> $pages['fullsize_pos_dy'],
               'imagefield_width'=> $pages['fullsize_pos_dx'],
               'imagefield_x'    => $pages['fullsize_pos_x'],
               'imagefield_y'    => $pages['fullsize_pos_y'],
               'imagefield_space'=> 35,
               'backgroundcolor' => "#" . $pages['bgcolor'],
            )
         
         );
         
         if( in_array( $productid , $backgroundarray  ) ){
            
            $this->choosebackground = 'true';
            
         }
         
         $portal = Dispatcher::getPortal();
         
         if( $portal == 'VP-001' ){
            $ext = "SV_";
         }
         else if( $portal == 'UP-DK'){
            $ext = 'DK_';
         }
         else{
            $ext = '';
         }

         $this->selected = array(
            'templateid' => $gifttemplateid,
            'template' => $templateArray,
            'productoptionid' => $productoptionid,
            'productoption' => $productoption->asArray(),
            'productid' => $productid,
            'product' => $product->asArray(),
            'templatefile' => $templateArray['pages'][0]['fullsize_src']
         );

         $this->ukeplantemplates = $templates;
         
      }
      
   }
   
?>