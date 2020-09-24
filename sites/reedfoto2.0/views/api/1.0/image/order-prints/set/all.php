<?php

   /**
    * Set the quantity for a product on all images in order process
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'pages.json' );
   import( 'session.usersessionarray' );
   import( 'website.image' );
   
   class APIImagesProductQuantity extends JSONPage implements IValidatedView{
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
                  'prodno' => VALIDATE_STRING,
                  'quantity' => VALIDATE_INTEGER,
               ),
               'fields' => array(
                  VALIDATE_STRING,
                  VALIDATE_INTEGER,
               ),
            ),
         );
         
      }
      
      /**
       * Set the quantity for a product on all images in order process
       * 
       * @api-name image.order-prints.set.all
       * @api-auth required
       * @api-post-optional prodno String Product number
       * @api-param-optional prodno String Product number
       * @api-post-optional reference String Item reference id
       * @api-param-optional reference String Item reference id
       * @api-post-optional quantity Integer Number of items
       * @api-param-optional quantity Integer Number of items
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */  
      
      public function Execute( $prodno, $quantity ) {
         
         $printOrder = UserSessionArray::getItems( "printorder" );
         $printOrder = reset( $printOrder );
         
         if( count( $printOrder["imageobjects"] ) > 0 ) {
            
            foreach( $printOrder["imageobjects"] as $imageid => $imagedata ) {            
               
               $this->result = false;
               $this->message = "No access to this image";
               
               $image = new Image( $imageid );
               if( !$image->isLoaded() || !$image instanceof Image ) return false;
               
               $this->result = false;
               $this->message = "No such image";
               
               if( !isset( $printOrder["imageobjects"][$imageid] ) ) return false;
               
               if( isset( $printOrder["imageobjects"][$imageid]["quantity"] ) ) {
                  
                  $printOrder["imageobjects"][$imageid]["quantity"][$prodno] = $quantity;
                  
               }
               
               UserSessionArray::clearItems( "printorder" );
               UserSessionArray::addItem( "printorder", $printOrder );
            }
            
         }
         
         
         $this->result = "OK";
         $this->message = "Quantity changed";
         
         
         
      }
      
   }


?>