<?php
   import( 'website.product' );
   import( 'website.gifttemplate' );

   class CreateUkeplan extends WebPage implements IView {
      
      protected $template = 'editor.garderobeplan';
      
      public function execute( $productid = 0, $productoptionid = 0, $gifttemplateid = 0, $orientation = 'portrait' ) {
         
         $product = new Product( $productid );
         $productoption = new ProductOption( $productoptionid );

         $template = new GiftTemplate( $gifttemplateid );
         $templateArray = $template->asArray();
         $pages = $templateArray['pages'][0];
         
         $backgroundarray = array( 2762, 2760, 2764, 2766, 2768 );
         
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

         $this->selected = array(
            'templateid' => $gifttemplateid,
            'template' => $template->asArray(),
            'productoptionid' => $productoptionid,
            'productoption' => $productoption->asArray(),
            'productid' => $productid,
            'product' => $product->asArray()
         );

         $this->ukeplantemplates = $templates;
         
      }
      
      
      
   }
   
?>