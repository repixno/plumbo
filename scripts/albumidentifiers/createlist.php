#!/usr/bin/php -q
<?PHP
   
   // bootstrap the platform
   chdir( dirname( __FILE__ ) ); include '../../bootstrap.php';
   
   // configure the platform for CLI
   config( 'website.config' );
   import( 'system.cli' );
   
   // import required classes
   import( 'website.album.identifier' );
   
   class AlbumIdentifierListGenerator extends Script {
      
      public function Main( $argc = 0, $argv = array() ) {
         
         if( $argc < 3 ) {
            
            echo "Usage: createlist.php <project> <numberofitems>\n";
            
         } else {
            
            $project = $argv[1];
            $objects = (int) $argv[2];
            $batchid = AlbumIdentifier::nextBatchId();
            
            echo sprintf( "Creating batch #%d of %d items for project '%s'...\n", $batchid, $objects, $project );
            
            for( $i = 0; $i < $objects; $i++ ) {
               
               $object = AlbumIdentifier::Create();
               $object->project = $project;
               $object->batchid = $batchid;
               $object->save();
               
               if( $i % 100 == 0 ) echo '.';
               
            }
            
            echo "\nOK.\n";
            
         }
         
      }
      
   }
   
   CLI::Execute();
   
?>