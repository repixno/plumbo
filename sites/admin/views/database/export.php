<?PHP
   
   config( 'database.export' );
   
   import( 'pages.admin' );
   import( 'core.version' );
   
   class DatabaseExport extends AdminPage implements IView {
      
      protected $template = 'database.export.overview';
      
      public function Execute() {
         
         $settings = Settings::Get( 'database', 'export', array() );
         
         $this->enabled = $settings['enabled'];
         
         if( count( $settings['tables'] ) ) {
            $this->tables = $settings['tables'];
         }
         
         $this->dbversion = Version::GetDatabaseVersion();
         $this->filename = sprintf( 'dbexport-%s-%s.ef', trim(`hostname`), date( 'Ymd-his' ) );
         
      }
      
      public function ArchiveProductImages() {
         
         
         
      }
      
      public function Download( $filename ) {
         
         $this->setTemplate( false );
         
         header( 'Content-type: text/plain' );
         header( 'Content-disposition: attachment' );
         
         if( !isset( $_POST['tables'] ) ) {
            
            relocate( '/database/export' );
            die();
            
         }
         
         $export = array(
            'version' => Version::GetDatabaseVersion(),
            'tables' => array(),
         );
         
         foreach( $_POST['tables'] as $table => $enabled ) {
         	
            $export['tables'][$table] = DB::query( sprintf( 'SELECT * FROM %s', $table ) )->fetchAll( DB::FETCH_ASSOC );
            
         }
         
         echo serialize( $export );
         
      }
      
   }
   
?>