<?PHP
   
   config( 'database.export' );
   
   import( 'pages.admin' );
   import( 'core.version' );
   
   class DatabaseExport extends AdminPage implements IView {
      
      protected $template = 'database.queries.overview';
      
      public function Execute() {
         
         $this->queries = DB::query( "SELECT procpid as pid, 
                                             current_query as query, 
                                             to_char(query_start, 'YYYY-MM-DD HH24:MI:SS') as started
                                        FROM pg_stat_activity 
                                       WHERE current_query NOT LIKE '%<IDLE>%'" 
                                    )->fetchAll( DB::FETCH_ASSOC );
         
      }
      
   }
   
?>