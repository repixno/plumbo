<?php

   /**
    * User article credits
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    *
    */
   
   model( 'user.credit' );
   import( 'website.product' );
   
   class Credit extends DBCredit {
      
      /**
       * Enumerate the given user's credited articles
       *
       * @param integer $userid
       * @return array
       */
      static function enum( $userid = 0 ) {
         
         try {
            
            $creditlist = array();
			   $credits = new Credit();
			
   			foreach( $credits->collection( 'id', array( 'uid' => Login::userid() ) )->fetchAllAs('Credit') as $credit ) {
   				
   			   $productoption = ProductOption::fromRefId( $credit->artikkelnr );
   			   
   			   if( $productoption instanceof ProductOption && $productoption->isLoaded() ) {
      			   
   			      $product = new Product( $productoption->productid );
      			   
   			      if( $product->isLoaded() ) {
         				$creditlist []= array(
         				  "id" => $credit->id,
         				  "refid" => $credit->artikkelnr,
         				  "usertitle" => $credit->tekst,
         				  "quantity" => $credit->quantity,
         				  "time"     => $credit->time,
         				  "product"  => $product->asArray()
         				);
      			   }
      			   
   			   }
               
   			}
   			
   			return $creditlist;
            
         } catch( Exception $e ) {
            
            return false;
            die();
            
         }
         
      }
      

      
      /**
       * Remove credit from product
       *
       * @param integer $qty
       * @return array
       */
      public function useCredit( $qty = 0 ) {

         // Check boundaries
         if( $qty > 0 && ( $this->quantity >= $qty ) ) {
            
            // Withdraw
            $this->quantity -= $qty;
            $this->save();
            return array( 'result' => true, 'quantity' => $this->quantity );
            
         }
         
         return array( 'result' => false, 'quantity' => $this->quantity );
         
      }
      
      

      /**
       * Add credits to product
       *
       * @param integer $qty
       * @return array
       */
      public function addCredit( $qty = 0 ) {
         
         if( $qty > 0 ) {
            
            $this->quantity += $qty;
            $this->save();
            return array( 'result' => true, 'quantity' => $this->quantity );
            
         }
         
         return array( 'result' => false, 'quantity' => $this->quantity );
         
      }
      
      
      /**
       * Get any credit based on user id and refid
       *
       * @param integer $userid
       * @param integer $refid
       */
      static function getCreditByUserIdAndRefId( $userid, $refid ) {
         
         $response = array();
         
         try {
            
            $credits = new Credit();
            foreach( $credits->collection( 'id', array( 'userid' => $userid, 'refid' => $refid ) )->fetchAllAs( 'Credit' ) as $credit ) {
               $response[] = $credit;
            }
            
         } catch( Exception $e ) {}
         
         return $response[0];
         
      }
      
      
      public function asArray() {
         
         return array(
            'id' => $this->id,
            'userid' => $this->userid,
            'refid' => $this->refid,
            'quantity' => $this->quantity,
            'text' => $this->text,
            'time' => $this->time,
         );
         
      }
      
   }
   
?>