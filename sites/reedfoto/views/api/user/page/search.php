<?PHP

   import( 'reedfoto.pages.json' );
   
   class ReedFotoApiUserPageSearch extends JSONPage implements IValidatedView  {
      
      /**
       * Validates the data going into this API
       *
       * @return array of validation rules.
       */
      public function Validate() {
      
         return array(
            'execute' => array(
               'post' => array(
                  'correctionid' => VALIDATE_INTEGER,
                  'text' => VALIDATE_STRING,
               ),
               'fields' => array(
                  'correctionid' => VALIDATE_INTEGER,
                  'text' => VALIDATE_STRING,
               ),
            )
         );
      
      }
      
      /*
      * Search for pages in PDF file
      *
      * @api-name user.pagesearch
      * @api-javascript yes
      * @api-post-optional correctionid Integer ID of the correction
      * @api-param-optional correctionid Integer ID of the correction
      * @api-post-optional text String The text to search for
      * @api-param-optional text String The text to search for
      * @api-result result Boolean true/false
      * @api-result pagecount Integer Count of pages
      * @api-result pages Array List of pages
      * @api-result message String Describes the result of the operation in US English
      */           
      public function Execute( $correctionid = 0, $text = '' ) {
         
         $correctionid = $_POST['correctionid'] ? $_POST['correctionid'] : $correctionid;
         $text = $_POST['text'] ? $_POST['text'] : $text;
         
         $pages = array();
         
         foreach ( RFPage::enum( $correctionid ) as $page ) {
         
            $retpage = array();
            $matches = array();
            
            if ( preg_match( "/$text/i", $page->pagetext, $match ) ) {
               
               $retpage['id'] = $page->id;
               
               if ( count($matches) <= 0 ) preg_match( "/(\S+) (\S+) $text (.*)/i", $page->pagetext, $matches );
               if ( count($matches) <= 0 ) preg_match( "/(\S+) $text (.*)/i", $page->pagetext, $matches );
               if ( count($matches) <= 0 ) preg_match( "/(\S+) (\S+)$text (.*)/i", $page->pagetext, $matches );
               if ( count($matches) <= 0 ) preg_match( "/(\S+)$text (.*)/i", $page->pagetext, $matches );
               if ( count($matches) <= 0 ) preg_match( "/(\S+) (\S+)$text/i", $page->pagetext, $matches );
               if ( count($matches) <= 0 ) preg_match( "/(\S+)$text/i", $page->pagetext, $matches );
               if ( count($matches) <= 0 ) preg_match( "/$text (.*)/i", $page->pagetext, $matches );
               if ( count($matches) <= 0 ) preg_match( "/(.*) $text/i", $page->pagetext, $matches );
               if ( count($matches) <= 0 ) preg_match( "/$text(.*)/i", $page->pagetext, $matches );
               if ( count($matches) <= 0 ) preg_match( "/(.*)$text/i", $page->pagetext, $matches );
               
               $retpage['title'] = $page->title;
               $retpage['text'] = substr($matches[0],0,50);
               $retpage['search'] = $match[0];
               
               $pages[] = $retpage;
            }
         }
         
         if ( count($pages) > 0 ) {
            
            $this->pagecount = count($pages);
            $this->pages = $pages;
         
            $this->message = 'OK';
            $this->result = true;
         
         } else {
         
            $this->message = 'No result';
            $this->result = false;
         }
         
      }
   }

?>