<?PHP
   
   import( 'pages.admin' );
   
   class StatisticsBarcodes extends AdminPage implements IView {
      
      protected $template = 'statistics.barcodes';
      
      public function Execute() {
         
         // store the number of albums with an identifier
         $this->totalalbums = DB::query( 'SELECT COUNT(b.aid) 
                                            FROM bildealbum b 
                                       LEFT JOIN album_identifiers ai 
                                              ON ai.identifier=b.identifier
                                           WHERE b.identifier IS NOT NULL' )->fetchSingle();
         
         // store the number of activated albums
         $this->activations = DB::query( 'SELECT COUNT(b.aid) 
                                            FROM bildealbum b 
                                       LEFT JOIN album_identifiers ai 
                                              ON ai.identifier=b.identifier
                                       LEFT JOIN tilgangtilalbum_dedikert ttad 
                                              ON ttad.aid=b.aid
                                           WHERE b.identifier IS NOT NULL 
                                             AND ttad.aid IS NOT NULL;' )->fetchSingle();
         
         // create some stats on downloadsales.
         list( $this->downloadorders, $this->downloadsum ) = DB::query( "SELECT COUNT(ordrenr), 
                                                                SUM(pris) 
                                                           FROM historie_ordre 
                                                          WHERE ordrenr IN (
                                                                SELECT DISTINCT(ordrenr) 
                                                                  FROM historie_ordrelinje 
                                                                 WHERE artikkelnr = '7012'
                                                                );" )->fetchRow();
         
         // create some stats on productsales.
         list( $this->productorders, $this->productsum ) = DB::query( "SELECT COUNT(ordrenr), 
                                                                SUM(pris) 
                                                           FROM historie_ordre 
                                                          WHERE ordrenr IN (
                                                                SELECT DISTINCT(ordrenr) 
                                                                  FROM historie_ordrelinje 
                                                                 WHERE artikkelnr = '7008'
                                                                );" )->fetchRow();
         
      }

   }
   
?>