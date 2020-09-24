<?php

   import( 'pages.admin' );
   import( 'gui.toolkit' );
   
   import( 'cache.memcache' );
   import( 'cache.diskcache' );
   
   class ClearCache extends AdminPage implements IView {
      
      public function Execute() {
         
         $this->header = __( 'Clear System Caches' );
         
         new text( __( 'In the unlikely event of a "stuck" cached element, you can use this tool to clear one of, or all system caches in one simple operation.' ) );
         
         if( isset( $_POST['clear'] ) ) {
            
            if( isset( $_POST['clear']['templatecache'] ) ) {
               
               $tmppath = realpath( Settings::get( 'templating', 'tmp', '/tmp' ) );
               foreach( new DirectoryIterator( $tmppath ) as $entry ) {
                  $filename = $entry->getFilename();
                  if( substr( $filename, 0, 1 ) == '.' ) continue;
                  if( substr( $filename, 0, 3 ) != 'tpl' ) continue;
                  unlink( sprintf( '%s/%s', $tmppath, $filename ) );
               }
               
               new note( __( 'Template cache cleared!' ) );
               
            }
            
            if( isset( $_POST['clear']['diskcache'] ) ) {
               
               $cache = new DiskCacheEngine();
               $cache->clear();
               
               new note( __( 'Disk cache cleared!' ) );
               
            }
            
            
            if( isset( $_POST['clear']['memorycache'] ) ) {
               
               if( MemCacheEngine::available() ) {
                  $cache = new MemCacheEngine();
                  $cache->clear();
               }
               
               new note( __( 'Memory cache cleared!' ) );
               
            }
            
            if( isset( $_POST['clear']['varnishcache'] ) ) {
               
               $this->varnish( true );
               
            }
            
         }
         
         $form = new form( 'clear' );
         $form->addField( __( 'Clear Disk Cache' ), 'diskcache', isset( $_POST['clear']['diskcache'] ), null, 'check' );
         $form->addField( __( 'Clear Template Cache' ), 'templatecache', isset( $_POST['clear']['templatecache'] ), null, 'check' );
         $form->addField( __( 'Clear Memory Cache' ), 'memorycache', isset( $_POST['clear']['memorycache'] ), null, 'check' );
         $form->addField( __( 'Clear Varnish Cache' ), 'varnishcache', isset( $_POST['clear']['varnishcache'] ), null, 'check' );
         $form->addSubmit( __( 'Clear' ) );
         $form->render();
         
      }
      
      public function Varnish( $noheader = false ) {
         
         //$hostkey = 'E262B975DD616BB58791318ED31DC7B3';
         $hostname = 'static.eurofoto.no';
         $username = 'svn';
         //$purgecmd = '/usr/bin/varnishadm -T localhost:6082 purge.url ".*"';
         $purgecmd = 'sudo service varnish restart';
         
         if( !$noheader ) $this->header = 'Varnish Cache Purge';
         
         import( 'network.ssh2' );
         
         $connection = new SSH2( $hostname );
         
         if( $connection->pubkey( $username ) ) {
            
            new note( __( 'Connected to %s.', $hostname ) );
            
         } else {
            
            new note( __( 'Connection to %s failed', $hostname ) );
            
         }
         
         $stream = $connection->execute( $purgecmd );
         stream_set_blocking( $stream, true );
         $output = trim( stream_get_contents( $stream ) );
         
         if( empty( $output ) ) {
            
            new note( __( 'Purged.' ) );
            
         } else {
            
            new note( $output );
            
         }
         
      }
      
   }
   
?>