<?php

   /**
    * 
    * Add one or more image ID's to the chosen images-array
    *
    * 
    */

   import( 'pages.json' );
   import( 'website.image' );
   import( 'session.usersessionarray' );
   
   class APIOrderPrintsImage extends JSONPage implements IValidatedView {
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'fields' => array(
                  VALIDATE_STRING,
                  VALIDATE_STRING,
                  VALIDATE_STRING
               ),
               'post' => array(
                  'imageid' => VALIDATE_STRING,
                  'prodno' => VALIDATE_STRING,
                  'quantity' => VALIDATE_STRING
               )
            ),
            'delete' => array(
               'fields' => array(
                  VALIDATE_STRING  
               ),
               'post' => array(
                  'imageid' => VALIDATE_STRING
               )
            )
         );
         
      }
      
      
      /**
       * Add one or more image ID's to the chosen images-array
       * 
       * @api-name order.prints.image
       * @api-param imageid String ID or comma separated list of the image(s) to add
       * @api-param prodno String prodno or comma separated list of the prodno's.
       * @api-param quantity String Quantaty or comma separated list of quantaties
       * @api-post imageid String ID or comma separated list of the image(s) to add
       * @api-post prodno String prodno or comma separated list of the prodno's.
       * @api-post quantity String Quantaty or comma separated list of quantaties
       * @api-result result Boolean true/false
       * @api-result images Array List of chosen images
       * @api-result message String Describes the result of the operation in US English
       */ 
      public function Execute( $imageid = '', $prodno = '', $quantity = '' ) {
         
         $imageid = $_POST['imageid'] ? $_POST['imageid'] : $imageid;
         $prodno = $_POST['prodno'] ? $_POST['prodno'] : $prodno;
         $quantaty = $_POST['quantaty'] ? $_POST['quantaty'] : $quantaty;
         
         if ( strpos( $imageid, ',' ) > -1 ) {

            $images[] = explode(',', $imageid );
            
         } else {
            
            $images[0] = $imageid;
         
         }
         
         if ( strpos( $quantaty, ',' ) > -1 ) {

            $quantaties[] = explode(',', $quantaty );
            
         } else {
            
            $quantaties[0] = $quantaty;
         
         }

         if ( strpos( $prodno, ',' ) > -1 ) {

            $prodnumbers[] = explode(',', $prodno );
            
         } else {
            
            $prodnumbers[0] = $prodno;
         
         }
         
         $i = 0;
         
         $printorder = UserSessionArray::getItems( "printorder" );
         $printorder = reset( $printorder );
         
         foreach( $images as $image ) {
            
            
            // Try loading image
            try {
               $image = new Image( $image );
            } catch( Exception $e ) {
               $this->result = false;
               $this->message = 'Failed to add image' .$e->getMessage();
               return false;
            }
   
            // Get the user session array containing ticked images
            $checkedimages = UserSessionArray::getItems( 'tickedimages' );
            $checkedimages = $checkedimages[0];
   
            // Check if previously exists
            if( count( $checkedimages ) > 0 ) {
            
               // Check so this image isn't already in array
               $exists = false;
               foreach( $checkedimages as $key => $checkedimage ) {
                  if( $imageid == $checkedimage['id'] ) {
                     $exists = true;
                  }
                  
               }
               
               if( !$exists ) {
                  
                  UserSessionArray::clearItems( 'tickedimages' );
                  
                  $checkedimages []= $image->asArray();
                  UserSessionArray::addItem( 'tickedimages', $checkedimages );
                  
               }
            
            } else {
               
               $checkedimages []= $image->asArray();
               UserSessionArray::addItem( 'tickedimages', $checkedimages );
               
            }
            
            if ( $quantities[$i] > 0 && ( !empty( $prodnumbers[$i] ) ) ) {
               
               if( !isset( $printorder["imageobjects"][$imageid] ) ) return false;
               
               if( isset( $printorder["imageobjects"][$imageid]["quantity"] ) ) {
               
                  $printorder["imageobjects"][$imageid]["quantity"][$prodno] = $quantities[$i];
               
               }
               
            }
            
            $i++;
            
         }
         
         UserSessionArray::clearItems( "printorder" );
         UserSessionArray::addItem( "printorder", $printorder );
         
         // Everything is fine and dandy
         $this->result = true;
         $this->message = 'OK';
         $this->images = $checkedimages;
         
         return true;
         
      }
      
      /**
       * Remove a image ID from the chosen images-array
       * 
       * @api-nameorder.prints.image.delete
       * @api-param imageid String ID or a comma separated list of ID's ff the images to remove
       * @api-result result Boolean true/false
       * @api-result images Array List of chosen images
       * @api-result message String Describes the result of the operation in US English
       */       
      public function delete( $imageid = '' ) {
         
         $imageid = $_POST['imageid'];
         
         if ( strpos( $imageid, ',' ) > -1 ) {

            $images[] = explode(',', $imageid );
            
         } else {
            
            $images[0] = (int)$imageid;
         
         }
         
         foreach( $images as $image ) {
            
            // Get the user session array containing ticked images
            $checkedimages = UserSessionArray::getItems( 'tickedimages' );
            $checkedimages = $checkedimages[0];
            
            // Check if previously exists
            if( count( $checkedimages ) > 0 ) {
            
               // Check so this image isn't already in array
               foreach( $checkedimages as $key => $checkedimage ) {
                  if( $imageid == $checkedimage['id'] ) {
                     
                     UserSessionArray::clearItems( 'tickedimages' );
                     unset( $checkedimages[$key] );
                     UserSessionArray::addItem( 'tickedimages', $checkedimages );
                     
                  }
                  
               }
            
            }
            
         }
            
         $this->result = true;
         $this->message = 'OK';
         $this->images = $checkedimages;
         
         return true;
         
      }

      
   }


?>