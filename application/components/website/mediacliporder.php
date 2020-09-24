<?php

   /**
    * Wrapper component to hide ugly old EF stuff
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    *
    */

   class MediaclipOrder {
      
      
      /**
       * Get the current mediaclip order
       *
       * @return array
       * 
       */
      static function get() {
         
         return array(
            "userid" => (int) MediaclipOrder::userId(),
            "projectid" => (int)$_SESSION["mediaclip"]["project_id"],
            "realprojectid" => (int)$_SESSION["mediaclip"]["realprojectid"],
            "productid" => (int)$_SESSION["mediaclip"]["product_id"],
            "refid" => (int)$_SESSION["mediaclip"]["article_id"],
            "quantity" => (int)$_SESSION["mediaclip"]["quantity"],
            "numextrapages" => (int)$_SESSION["mediaclip"]["additionalSheetCount"],
            "sheetcount" => $_SESSION["mediaclip"]["sheetcount"],
            "title" => $_SESSION["mediaclip"]["title"],
            
         );
         
      }
      
      /**
       * Clear all mediaclip data in session
       *
       */
      static function clear() {
         
         unset( $_SESSION["mediaclip"] );
         
      }
      
      
      
      /**
       * Check if necessary properties are set
       *
       * @return boolean
       * 
       */
      static function valid() {
         
         $orderData = MediaclipOrder::get();
         if( $orderData["userid"] > 0 && $orderData["projectid"] > 0 && $orderData["refid"] > 0 && $orderData["quantity"] > 0 ) {
            return true;
         }
         //debugging remove when done
         mail("tor.inge@eurofoto.no", "not valid mediaclip ordre" , 
         "orderData_userid= " .$orderData["userid"].
         "\nprojectid= ".$orderData["projectid"].
         "\nrefid= " . $orderData["refid"].
         "\nquantity= " .$orderData["quantity"]
         );
         return false;
         
      }
      
      
      /**
       * Get the title of project
       *
       * @return string
       */
      static function title() {
         
         return $_SESSION["mediaclip"]["title"];
         
      }
      
      
      /**
       * Get onwer user id of project
       *
       * @return integer
       */
      static function userId() {
      	if(!isset($_SESSION["mediaclip"]["userid"])){
      		if( !Login::isLoggedIn() ) {
   				$_SESSION["mediaclip"]["userid"] = 639866;
   			}else{
   				$_SESSION["mediaclip"]["userid"] = Login::userid();
   			}
      	}
         
         return (int)$_SESSION["mediaclip"]["userid"];
         
      }
      
      
      /**
       * Get the project id
       *
       * @return integer
       */
      static function projectId() {
         
         return (int)$_SESSION["mediaclip"]["project_id"];
         
      }
      
      
      /**
       * Get the real project id and not
       * the order project id
       *
       * @return integer
       */
      static function realProjectId() {
         
         return (int)$_SESSION["mediaclip"]["realprojectid"];
         
      }
      
      
      /**
       * Get the product id
       *
       * @return integer
       */
      static function productId() {
         
         return (int)$_SESSION["mediaclip"]["product_id"];
         
      }
      
      
      /**
       * Get the ref id referencing old EF artnr
       *
       * @return integer
       */
      static function refId() {
         
         return (int)$_SESSION["mediaclip"]["article_id"];
         
      }
      
      
      /**
       * Get the quantity of order
       *
       * @return integer
       */
      static function quantity() {
      
         return (int)$_SESSION["mediaclip"]["quantity"];
         
      }
      
      
      /**
       * Get number of extra pages
       *
       * @return integer
       */
      static function extraPages() {
         
         return (int)$_SESSION["mediaclip"]["additionalSheetCount"];
         
      }
      
      
      
      /**
       * Get the sheet count
       *
       * @return unknown
       */
      static function sheetCount() {
         
         return $_SESSION["mediaclip"]["sheetcount"];
         
      }
      
      
      /**
       * The refid used for extra pages
       *
       * @return integer
       */
      static function refIdExtraPages() {
         
         config( 'website.cart' );
         $ref = Settings::get( 'cart', 'mediaclipextrapages' );
         $refId = $ref["refid"];
         
         return $refId;
         
      }
      
      
      static function productOptionId() {
         
         $project = new Project( MediaclipOrder::realProjectId() );
         if( $project instanceof Project && $project->isLoaded() ) {
            return $project->productoptionid;
         }
         
         return 0;
         
      }
      
   }


?>