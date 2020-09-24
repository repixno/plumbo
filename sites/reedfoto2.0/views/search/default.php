<?php


   /**
    * 
    * Get the result of a google site search
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'services.google.sitesearch' );

   class Search extends WebPage implements IValidatedView  {
      
      protected $template = 'search.index';
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'request' => array(
                  'q'         => VALIDATE_STRING,
                  'offset'    => VALIDATE_INTEGER,
               ),
               'fields' => array(
                  'q'         => VALIDATE_STRING,
                  'offset'    => VALIDATE_INTEGER
               ),
            ),
         );
      }
      
      public function Execute( $q = '', $offset = 0 ) {
         
         if( isset( $_REQUEST["q"] ) ) $q = $_REQUEST["q"];
         if( isset( $_REQUEST["offset"] ) ) $q = $_REQUEST["offset"];
         
         $this->searchstring = $q;
         $this->searchresult = array();
         $rows = array();
         /*
         $gss = new GoogleSiteSearch();
		 
         if( $res = $gss->get( rawurlencode( $q ) ) ) {
			
			
			$res = str_replace( '<em>', '' , $res );
			$res = str_replace( '</em>', '' , $res );
			
            $xmlreader = simplexml_load_string( $res );
			
            if( empty( $xmlreader ) ) return false;
            
            if( isset( $xmlreader->Context ) && count( $xmlreader->Context->Facet ) )
            
            
            foreach( $xmlreader->Context->Facet as $facetitem ) {
               
               $searchresult['facets'][] = array(
                  'label' => (string) $facetitem->FacetItem->label,
                  'anchor_text' => (string) $facetitem->FacetItem->anchor_text,
               );
               
            }
            
            if( isset( $xmlreader->RES ) && count( $xmlreader->RES->R ) )
            foreach( $xmlreader->RES->R as $rItem ) {
			   
               $searchresult['RES'][] = array(
                  'T' => (string) $rItem->T,
                  'D' => (string) $rItem->SL_RESULTS->SL_MAIN->BODY_LINE[0]->BLOCK->T,
                  'S' => (string) $rItem->S,
                  'U' => (string) $rItem->U,
               );
               
            }
            $this->searchresult = $searchresult;
            
         }*/
         
      }
      
   }

?>