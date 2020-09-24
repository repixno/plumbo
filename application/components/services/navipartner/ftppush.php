<?PHP
   
   config( 'finance.ftpexport' );
   import( 'network.ftp' );
   
   class FTPPush {
      
      static function Send( $localfile, $remotefilename ) {
         
         $configuration = Settings::Get( 'finance', 'ftpexport', array() );
         
         try {
            
            $ftp = new FTP( $configuration['hostname'] );
            
            $ftp->login( $configuration['username'], $configuration['password'] );
            
            $success = $ftp->put( $localfile, $remotefilename );
            
            $ftp->disconnect();
            
            return $success ? true : false;
            
         } catch( Exception $e ) {
            
            return false;
            
         }
         
      }
      
   }
   
   
?>