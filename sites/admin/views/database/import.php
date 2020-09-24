<?PHP
   
   config( 'database.import' );
   
   import( 'pages.admin' );
   import( 'core.version' );
   
   class DatabaseImport extends AdminPage implements IView {
      
      protected $template = 'database.import.overview';
      
      public function Execute() {
         
         $settings = Settings::Get( 'database', 'import', array() );
         
         $this->enabled = $settings['enabled'];
         
         if( count( $settings['tables'] ) ) {
            $this->tables = $settings['tables'];
         }
         
         $this->dbversion = Version::GetDatabaseVersion();
         
      }
      
      public function Upload() {
         
         $this->setTemplate( 'database.import.upload' );
         
         $settings = Settings::Get( 'database', 'import', array() );
         $allowedtables = array_flip( $settings['tables'] );
         
         $datapacket = file_get_contents( $_FILES['import']['tmp_name'] );
         
         $this->filename = $_FILES['import']['name'];
         
         $data = @unserialize( $datapacket );
         
         if( !isset( $data['tables'] ) ) {
            
            $this->error = 'Unknown format for datafile';
            
         } elseif( $data['version'] != Version::GetDatabaseVersion() ) {
            
            $this->error = sprintf( 'Datafile model version mismatch (%d vs %d)', $data['version'], Version::GetDatabaseVersion() );
            
         } else {
            
            $cache = new DiskCacheEngine();
            $cache->write( 'lastupload['.Session::id().']', $datapacket );
            
            $tables = array();
            foreach( $data['tables'] as $table => $rows ) {
               
               $tables[] = array(
                  'name' => $table,
                  'allowed' => isset( $allowedtables[$table] ) ? true : false,
                  'records' => count( $rows ),
               );
               
            }
            
            $this->tables = $tables;
            
         }
         
      }
      
      public function Complete() {
         
         $this->setTemplate( 'database.import.complete' );
         
         $settings = Settings::Get( 'database', 'import', array() );
         $allowedtables = array_flip( $settings['tables'] );
         
         $cache = new DiskCacheEngine();
         $datapacket = $cache->read( 'lastupload['.Session::id().']' );
         
         $data = @unserialize( $datapacket );
         
         if( !isset( $data['tables'] ) ) {
            
            $this->error = 'Datafile expired. Please start over.';
            
         } elseif( $data['version'] != Version::GetDatabaseVersion() ) {
            
            $this->error = sprintf( 'Datafile model version mismatch (%d vs %d)', $data['version'], Version::GetDatabaseVersion() );
            
         } else {
            
            $results = array();
            
            foreach( $_POST['tables'] as $table => $enabled ) {
               
               if( isset( $allowedtables[$table] ) ) {
                  
                  if( isset( $data['tables'][$table] ) ) {
                     
                     DB::beginTransaction();
                     
                     $query = sprintf( 'TRUNCATE TABLE "%s"', $table );
                     DB::query( $query );
                     $recordcount = 0;
                     
                     foreach( $data['tables'][$table] as $record ) {
                        
                        $keys = array_keys( $record );
                        $vals = array_values( $record );
                        $qs = array_fill( 0, count( $vals ), '?' );
                        
                        $query = sprintf( 'INSERT INTO "%s" (%s) VALUES (%s)', $table, implode( ', ', $keys ), implode( ', ', $qs ) );
                        
                        DB::query( $query, $vals );
                        $recordcount++;
                        
                     }
                     
                     DB::commit();
                     
                     $results[] = array(
                        'name' => $table,
                        'records' => $recordcount,
                     );
                     
                  }
                  
               }
               
            }
            
            $this->results = $results;
            
            $cache = new DiskCacheEngine();
            $cache->erase( 'lastupload['.Session::id().']' );
            
         }
         
      }
      
   }
   
?>