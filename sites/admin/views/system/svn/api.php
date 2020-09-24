<?PHP
   
   import( 'pages.json' );
   
   import( 'network.ssh2' );
   import( 'website.server' );
   
   class SVNAPIs extends JSONPage implements IView {
      
      public function Update() {
         
         if( !isset( $_POST['serverid'] ) ) {
            
            relocate( '/system/svn/' );
            return true;
            
         }
         
         $server = new Server( $_POST['serverid'] );
         
         $shellscript = array( 
            'cd /var/www/repix',
            'fixperms',
            'svn up',// --accept postpone
            'cd /home/httpd/www.eurofoto.no/webside/',
            'sudo svn up',// --accept postpone
            'exit',
         );

         $output = '';
         
         $ssh = new SSH2( $server->hostname, $server->port );
         if( $ssh->pubkey( $server->username ) ) {
            
            $newserver = array( 'header' => $server->hostname );
            
            $shell = $ssh->shell();
            stream_set_blocking( $shell, true );
            
            foreach( $shellscript as $line ) {
               fwrite( $shell, $line.PHP_EOL );
            }
            
            $dooutput = false;
            while( $line = fgets( $shell ) ) {
               if( substr( $line, 0, 5 ) == 'Linux' ) {
                  $header = $line;
               } else
               if( strpos( $line, '$ svn up' ) !== false ) {
                  $output .= "EF 3.0\n";
                  $dooutput = true;
               } else
               if( strpos( $line, '$ sudo svn up' ) !== false ) {
                  $output .= "EF 2.5\n";
                  $dooutput = true;
               } else
               if( strpos( $line, 'cd /home/httpd/www.eurofoto.no/webside/' ) !== false ) {
                  $output .= "\n";
               } else
               if( strpos( $line, '$ exit' ) !== false ) {
                  $dooutput = false;
               } else
               if( $dooutput ) $output .= trim( $line )."\n";
            }
            
            $server->updated = date( 'Y-m-d H:i:s' );
            $server->save();
            
         }
         
         $this->result = true;
         $this->output = $output;
         $this->message = trim( $header );
         $this->updated = $server->updated;
         
      }
      
      function Status() {
         
         if( !isset( $_POST['serverid'] ) ) {
            
            relocate( '/system/svn/' );
            return true;
            
         }
         
         $server = new Server( $_POST['serverid'] );
         
         $shellscript = array( 
            'cd /var/www/repix',
            'svn st -u',
            'cd /home/httpd/www.eurofoto.no/webside/',
            'sudo svn st -u',
            'exit',
         );
         
         $output = '';
         
         $ssh = new SSH2( $server->hostname, $server->port );
         if( $ssh->pubkey( $server->username ) ) {
            
            $newserver = array( 'header' => $server->hostname );
            
            $shell = $ssh->shell();
            stream_set_blocking( $shell, true );
            
            foreach( $shellscript as $line ) {
               fwrite( $shell, $line.PHP_EOL );
            }
            
            $dooutput = false;
            while( $line = fgets( $shell ) ) {
               if( substr( $line, 0, 5 ) == 'Linux' ) {
                  $header = $line;
               } else
               if( strpos( $line, '$ svn st -u' ) !== false ) {
                  $output .= "EF 3.0\n";
                  $dooutput = true;
               } else
               if( strpos( $line, '$ sudo svn st -u' ) !== false ) {
                  $output .= "EF 2.5\n";
                  $dooutput = true;
               } else
               if( strpos( $line, 'cd /home/httpd/www.eurofoto.no/webside/' ) !== false ) {
                  $output .= "\n";
               } else
               if( strpos( $line, '$ exit' ) !== false ) {
                  $dooutput = false;
               } else
               if( $dooutput ) $output .= trim( $line )."\n";
            }
            
            $servers[] = $newserver;
            
         }
         
         $this->result = true;
         $this->output = $output;
         $this->message = trim( $header );
         
      }
      
   }
   
?>