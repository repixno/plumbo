<?php
   
   import( 'pages.admin' );
   import( 'network.ssh2' );
   import( 'website.server' );
   
   class DiskFreeManager extends AdminPage implements IView {
      
      protected $template = 'system.diskfree';
      
      public function Execute() {
         
         $this->header = 'Server Disk Status';
         
         $servers = array();
         $allkeys = array();
         $partitions = array();
         $totals = array();
         foreach( $this->getServers() as $server ) {
            
            // skip freebsd hell for now
            if( strpos( $server->hostname, 'bente' ) !== false ) continue;
            
            $shellscript = array( 
               'df',
               'exit',
            );
   
            $ssh = new SSH2( $server->hostname, $server->port );
            if( $ssh->pubkey( $server->username ) ) {
               
               $newserver = array( 'header' => $server->hostname );
               
               $shell = $ssh->shell();
               stream_set_blocking( $shell, true );
               
               foreach( $shellscript as $line ) {
                  fwrite( $shell, $line.PHP_EOL );
               }
               
               $partitionkey = false;
               $nextline = false;
               while( $line = fgets( $shell ) ) {
                  
                  if( $nextline ) {
                     
                     $parts = preg_split( '/ /', $line, 0, PREG_SPLIT_NO_EMPTY );
                     $match = trim( end( $parts ) );
                     $match = explode( '/', $match );
                     $match = trim( end( $match ) );
                     if( $match == $partitionkey ) {
                        list( $partitionsize, $partitionused, $partitionfree, $partitionfill ) = $parts;
                        
                        $partitions[$server->hostname][$partitionkey] = array(
                           'size' => round( $partitionsize / 1024 / 1024 ).'G',
                           'used' => round( $partitionused / 1024 / 1024 ).'G',
                           'free' => round( $partitionfree / 1024 / 1024 ).'G',
                           'fill' => $partitionfill,
                        );
                        
                        $totals[$server->hostname]['size'] += $partitionsize;
                        $totals[$server->hostname]['used'] += $partitionused;
                        $totals[$server->hostname]['free'] += $partitionfree;
                        
                     }
                     
                     $nextline = false;
                     
                  } elseif( substr( $line, 0, 6 ) == 'monica' ) {
                     
                     $parts = explode('/', $line );
                     $partitionkey = trim( end( $parts ) );
                     $allkeys[$partitionkey] = true;
                     $nextline = true;
                     
                  } else continue;
                  
               }
               
            }
            
         }
         
         foreach( $totals as $server => $total ) {
            $totals[$server]['size'] = round( $total['size'] / 1024 / 1024 ).'G';
            $totals[$server]['free'] = round( $total['free'] / 1024 / 1024 ).'G';
            $totals[$server]['used'] = round( $total['used'] / 1024 / 1024 ).'G';
         }
         
         ksort( $allkeys );
         
         $partitionmap = array();
         foreach( $partitions as $server => $partitiondata ) {
            
            foreach( $allkeys as $partitionkey => $d ) {
               
               if( isset( $partitiondata[$partitionkey] ) ) {
                  
                  $partitionmap[$partitionkey][$server] = $partitiondata[$partitionkey];
                  
               } else {
                  
                  $partitionmap[$partitionkey][$server] = array(
                     'size' => 'N/M',
                     'used' => 'N/M',
                     'free' => 'N/M',
                     'fill' => 'N/M',
                  );
                  
               }
               
            }
            
         }
         
         $serverpartitionmap = array();
         foreach( $partitionmap as $server => $partitions ) {
            $serverpartitionmap[] = array(
               'name' => $server,
               'partitions' => array_values( $partitions ),
            );
         }
         
         $this->partitions = array_keys( $partitions );//array_keys( $allkeys );
         $this->servers = $serverpartitionmap;
         $this->totals = $totals;
         
      }
      
      private function getServers() {
         
         $servers = array();
         $collection = new Server();
         foreach( $collection->collection( array( 'serverid' ), array( 'active' => true ), 'serverid' )->fetchAllAs('Server') as $server ) {
            $servers[] = $server;
         }
         return $servers;
         
      }
      
   }
   
?>