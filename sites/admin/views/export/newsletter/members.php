<?PHP
   
   import( 'pages.admin' );
   
   class NewsletterExport extends AdminPage implements IView {
      
      static $rootpath = '%s/data/mailinglists/';
      protected $template = 'export.newsletter.members';
      
      public function Execute( $date = '' ) {
         
         if( $date ) {
            
            $items = array();
            
            foreach( new DirectoryIterator( sprintf( NewsletterExport::$rootpath.'%s', getRootPath(), $date ) ) as $item ) {
               
               if( $item->isFile() ) {
                  
                  $items[$item->getBaseName()] = array(
                     'file' => $item->getBaseName(),
                     'size' => round( $item->getSize() / 1024, 1 ),
                     'folder' => $date,
                     'modified' => date( 'Y-m-d H:i:s', $item->getMTime() ),
                  );
                  
               }
               
            }
            
            krsort( $items );
            
            $this->folder = $date;
            $this->items = $items;
            
         }
         
         $folders = array();
         
         foreach( new DirectoryIterator( sprintf( NewsletterExport::$rootpath, getRootPath() ) ) as $folder ) {
            
            if( $folder->isDir() && substr( $folder->getBaseName(), 0, 1 ) != '.' ) {
               
               $folders[$folder->getBaseName()] = array(
                  'folder' => $folder->getBaseName(),
                  'modified' => date( 'Y-m-d H:i:s', $folder->getMTime() ),
               );
               
            }
            
         }
         
         krsort( $folders );
         
         $this->folders = $folders;
         
      }
      
      public function Stream( $date = '', $file = '' ) {
         
         $localfile = sprintf( NewsletterExport::$rootpath.'%s/%s', getRootPath(), $date, $file );
         
         if( file_exists( $localfile ) ) {
            
            header( 'Content-type: application/octet-stream' );
            header( 'Content-length: '.filesize( $localfile ) );
            readfile( $localfile );
            die();
            
         } else {
            
            header( 'HTTP/1.0 404 Not Found' );
            echo "404 - Not found!";
            die();
            
         }
         
      }
      
      public function Create() {
         
         set_time_limit( 0 );
         ignore_user_abort( true );
         
         $allmembers = isset( $_POST['allmembers'] );
         $onlyemail = isset( $_POST['onlyemail'] );
         
         if( $allmembers ) {
         
            $res = DB::query( "SELECT b.brukarnamn, 
                                      b.kode,
                                      k.fnavn,
                                      k.mnavn,
                                      k.enavn
                                 FROM brukar b
                            LEFT JOIN kunde k 
                                   ON k.uid = b.uid 
                                WHERE k.newsletter_blacklist = false
                                  AND b.passord !='nopass'
                                  AND b.deleted IS NULL
                                  AND aliasfor IS NULL" );
         } else {
            
            $res = DB::query( "SELECT b.brukarnamn, 
                                      b.kode,
                                      k.fnavn,
                                      k.mnavn,
                                      k.enavn
                                 FROM brukar b
                            LEFT JOIN kunde k 
                                   ON k.uid = b.uid 
                                WHERE k.newsletter = true
                                  AND k.newsletter_blacklist = false
                                  AND b.passord !='nopass'
                                  AND b.deleted IS NULL
                                  AND aliasfor IS NULL" );
         }
         
         while( list( $email, $portal, $firstname, $middlename, $lastname ) = $res->fetchRow() ) {
            
            $lists[$portal][$email] = array( $firstname, $middlename, $lastname );
            
         }
         
         $skipped = array();
         $written = array();
         
         foreach( $lists as $portal => $members ) {
            
            if( !$portal ) $portal = 'default';
            if( count( $members ) < 1000 ) {
               $skipped[] = array(
                  'portal' => $portal,
                  'records' => count( $members ),
               );
               continue;
            }
            
            $typename = $allmembers ? 'systemlist-all-members' : 'newsletter';
            $typename = $onlyemail ? $typename.'-emailonly' : $typename;
            $filename = sprintf( NewsletterExport::$rootpath.'%s/%s-%s.csv', getRootPath(), date( 'Y-m-d' ), $typename, $portal );
            $outputdir = dirname( $filename );
            if( !file_exists( $outputdir ) ) {
               mkdir( $outputdir, 0700, true );
            }
            
            $list = fopen( $filename, 'w' );
            foreach( $members as $email => $name ) {
               list( $firstname, $middlename, $lastname ) = $name;
               $name = utf8_encode( trim( $firstname.' '.trim( $middlename.' '.$lastname ) ) );
               $firstname = utf8_encode( trim( $firstname ) );
               $middlename = utf8_encode( trim( $middlename ) );
               $lastname = utf8_encode( trim( $lastname ) );
               if( $onlyemail ) {
                  fwrite( $list, sprintf( "%s\n", $email ) );
               } else {
                  fwrite( $list, sprintf( "\"%s\";\"%s\";\"%s\";\"%s\";\"%s\"\n", $email, $name, $firstname, $middlename, $lastname ) );
               }
            }
            fclose( $list );
            
            $written[] = Array(
               'portal' => $portal,
               'records' => count( $members ),
               'folder' => date( 'Y-m-d' ),
               'filename' => basename( $filename ),
            );
            
         }
         
         $this->skipped = $skipped;
         $this->written = $written;
         
         relocate( '/export/newsletter/members/%s', date( 'Y-m-d' ) );
         
      }
      
   }
   
?>