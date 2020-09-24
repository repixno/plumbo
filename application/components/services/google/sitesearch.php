<?php

   /**
    * Get the result of a google site search
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   library( 'pear.http.request' );

   class GoogleSiteSearch {
      
      public $url = 'http://www.google.com/cse?cx=016471786999794692400%3Avwv1h0ulsse&client=google-csbe&output=xml_no_dtd&q=';

      public function get( $query = '' ) {

         try {
            
            $request = new HTTP_Request( sprintf('%s%s', $this->url, $query) , array(
               'method' => HTTP_REQUEST_METHOD_GET,
               'timeout' => 30,
            ) );
            
            $request->sendRequest();
            $res = $request->getResponseBody();
            
            return $res;
         
         } catch( Exception $e ) {
            
            return false;
            
         }
         
      }
      
   }

?>