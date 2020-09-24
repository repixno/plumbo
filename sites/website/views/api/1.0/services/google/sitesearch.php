<?php


   /**
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    *
    */

   import( 'services.google.sitesearch' );
   import( 'pages.json' );
   
   class APIServicesGoogleSiteSearch extends JSONPage implements NoAuthRequired, IValidatedView {
      
      /**
       * Validate the incoming data
       *
       */
      public function Validate() {
         
         return array(
            'execute' => array(
               'fields' => array(
                  VALIDATE_STRING,
               ),
            ),
         );
         
      }
      /**
       * Google site-search
       * 
       * @api-name services.google.sitesearch
       * @api-auth required
       * @api-fields query String Search query
       * @api-result response String Query response
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */    
      public function Execute( $query = '' ) {
         
         $gss = new GoogleSiteSearch();
         
         $this->result = false;
         $this->message = "Failed to get site search";
         
         if( $res = $gss->get( $query ) ) {
            
            $this->response = $res;
            $this->result = true;
            $this->message = "OK";
            
            return true;
            
         }
         
         return false;;
         
      }
      
   }



?>