<?php



   import( 'website.cart' );
   model( 'order.ukeplan' );
   import( 'pages.json' );

   class SaveUkeplan extends JSONPage implements NoAuthRequired, IView {
  
      public function Execute() {
          try{
         $data = $_POST;

         $ukeplanXML = new SimpleXMLElement("<ukeplan></ukeplan>");
         
         $template = $ukeplanXML->addChild( 'template ');
         $template->addAttribute( 'malid' , $data['template']['malid'] );
         $template->addAttribute( 'mal_width' , $data['template']['mal_width'] );
         $template->addAttribute( 'mal_height' , $data['template']['mal_height'] );
         $template->addAttribute( 'imagefield_height' , $data['template']['imagefield_height'] );
         $template->addAttribute( 'imagefield_width' , $data['template']['imagefield_width'] );
         $template->addAttribute( 'imagefield_x' , $data['template']['imagefield_x'] );
         $template->addAttribute( 'imagefield_y' , $data['template']['imagefield_y'] );
         $template->addAttribute( 'editorcontainer_width' , $data['template']['editorcontainer_width'] );
         $template->addAttribute( 'gridcolor' ,  $data['template']['gridcolor'] );
         $template->addAttribute( 'numberofcolumns', $data['template']['numberofcolumns'] );
         $template->addAttribute( 'background', $data['template']['background'] );
         $template->addAttribute( 'maskit', $data['template']['maskit'] );
         $template->addAttribute( 'clipart', $data['template']['clipart'] );
         $template->addAttribute( 'imagelayout', $data['template']['imagelayout'] );
         $template->addAttribute( 'plantype', $data['template']['plantype'] );
         $template->addAttribute( 'notatoption', $data['template']['notatoption'] );
         
         
         $weekdays = $ukeplanXML->addChild( 'weekdays' );
         $weekdays->addAttribute( 'color' , $data['weekdays']['color'] );
         $weekdays->addAttribute( 'font' , $data['weekdays']['font'] );
         
         $ratio = number_format( $data['template']['ratio'] , 2 ); 
         
         if( is_array( $data['images'] ) ){
            foreach ( $data['images'] as $image ){
               
               $i++;
               $imagenode = $ukeplanXML->addChild('image');
               $imagenode->addAttribute( 'sorting' , $image['sorting'] );
               $imagenode->addAttribute( 'imageid', $image['imageid'] );
               $imagenode->addAttribute( 'imagefield_height', $image['imagefield_height']  *  $ratio);
               $imagenode->addAttribute( 'imagefield_width', $image['imagefield_width']  *  $ratio);
               $imagenode->addAttribute( 'width',  str_replace( 'px', '', $image['width']) *  $ratio);
               $imagenode->addAttribute( 'height', str_replace( 'px', '',  $image['height']) * $ratio );
               $imagenode->addAttribute( 'margin-left',  str_replace( 'px', '', $image['margin-left'] ) * $ratio );
               $imagenode->addAttribute( 'margin-top',  str_replace( 'px', '',  $image['margin-top']) * $ratio);
               $imagenode->addAttribute( 'blackandwhite', $image['blackandwhite'] );
               $imagenode->addAttribute('rotate',  $image['rotate']);
               $imagenode->addAttribute('filename',  $image['src']);
               $imagenode->addAttribute('toptext',  $image['toptext']);
               $imagenode->addAttribute('toptextcolor',  $image['toptextcolor']);
               $imagenode->addAttribute('toptextfont',  $image['toptextfont']);
   
            }
         }
         
         if( count( $data['names'] ) ){
            foreach ( $data['names'] as $name ){
               
               $namenode = $ukeplanXML->addChild( 'name' );
               $namenode->addAttribute( 'sorting' , $name['sorting'] );
               $namenode->addAttribute( 'font' , $name['font'] );
               $namenode->addAttribute( 'color' , $name['color'] );
               $namenode->addAttribute( 'height' , $name['height'] * $ratio );
               $namenode->addAttribute( 'width' , $name['width'] * $ratio );
               $namenode->addAttribute( 'text', $name['text'] );
               
            }
         }
         if( count( $data['grid'] ) ){
               $gridnode = $ukeplanXML->addChild( 'grid' );
               $gridnode->addAttribute( 'color' , $data['grid']['color'] );
               $gridnode->addAttribute( 'quantity' , $data['grid']['quantity'] );
               $gridnode->addAttribute( 'maingridrows' , $data['grid']['maingridrows'] );
               
               foreach ( $data['grid']['gridvalues'] as $gridvalues  ){
                  $gridvalue = $gridnode->addChild( 'gridvalue', $gridvalues );
               }
         }
         
         
         if( count( $data['headergrid']) ){
            $headergridnode = $ukeplanXML->addChild( 'headergridnode' );
            
            foreach ( $data['headergrid'] as $headergrid ){
               $gridvalue = $headergridnode->addChild( 'headergrid', $headergrid['title'] );
            }
         }
         
         
        if( count( $data['bottomgrid']) ){
            $bottomgridnode = $ukeplanXML->addChild( 'bottomgridnode' );
            
            foreach ( $data['bottomgrid'] as $bottomgrid ){
               $gridvalue = $bottomgridnode->addChild( 'bottomgrid', $bottomgrid['title'] );
            }
         }
         
         //file_put_contents( '/var/www/ukeplan/debugging.txt', "ok" );
        
         $ukeplanorder = new DBUkeplanOrder();
         $ukeplanorder->userid = Login::userid();
         $ukeplanorder->articleid = $data['template']['prodno'];
         $ukeplanorder->quantity = $data['product']['quantity'];
         $ukeplanorder->project_xml = $ukeplanXML->asXML();
         $ukeplanorder->date = date( 'Y-m-d H:i:s' );
         $ukeplanorder->save();
         }catch (Exception $e){
            mail( 'tor.inge@eurofoto.no', "ukeplan add to cart bug", $e->getMessage() );
            //file_put_contents( '/var/www/ukeplan/debugging.txt', $e->getMessage() );
         }
         
         //$ukeplanXML->asXML( '/var/www/ukeplan/ukeplan2.txt' );

         //file_put_contents( '/var/www/ukeplan/testorder.txt', $data['template']['maskit'] );
         
         //file_put_contents( '/var/www/ukeplan/debugging1.txt', serialize( $data['product']['quantity'] )  );
         
         try{
            $cart = new Cart();
   	      $cart->addItemByProductOptionId( $data['product']['productoptionid'], 
   	          $data['product']['quantity'], 
   	          array(
   	             "projectid"  => $ukeplanorder->id,
   	             "templateorderid" => $data['template']['malid'],
   	             "redeyeremoval" => $redeye ? true : false,
   	             "maskit"        => $data['template']['maskit'] ? true : false,
   	          )
   	      );
         }catch ( Exception $e ){
             
            mail( 'tor.inge@eurofoto.no', "ukeplan add to cart bug", $e->getMessage() );
            //file_put_contents( '/var/www/ukeplan/order.txt', $e->getMessage() );
         }
	      
         $cart->save();
         die();

      }

   }



?>