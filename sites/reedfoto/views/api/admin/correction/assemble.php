<?PHP

   import( 'reedfoto.pages.json' );
   import( 'reedfoto.page' );
   
   class ReedFotoApiAdminCorrectionAssemble extends JSONPage implements IValidatedView  {
      
      /**
       * Validates the data going into this API
       *
       * @return array of validation rules.
       */
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
                  'filehash' => VALIDATE_STRING,
                  'pages' => VALIDATE_INTEGER,
                  'correctionid' => VALIDATE_INTEGER,
               ),
               'fields' => array(
                  'filehash' => VALIDATE_STRING,
                  'pages' => VALIDATE_INTEGER,
                  'correctionid' => VALIDATE_INTEGER,
               ),
            )
         );
         
      }
      
      /**
       * Assemble pages for a correction
       *
       * @api-name admin.correction.assemble
       * @api-javascript yes
       * @api-post-optional filehash String Hash of the pages to assemble in archive/database
       * @api-post-optional pages Integer number of pages to assemble
       * @api-post-optional correctionid Integer ID of the correction to add the pages to
       * @api-param-optional filehash String Hash of the pages to assemble in archive/database
       * @api-param-optional pages Integer number of pages to assemble
       * @api-param-optional correctionid Integer ID of the correction to add the pages to
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */  
      public function Execute( $filehash = '', $pages = 0, $correctionid = 0 ) {
         
         $filehash = $_POST['filehash'] ? basename( $_POST['filehash'] ) : basename( $filehash );
         $pages = $_POST['pages'] ? $_POST['pages'] : $pages;
         $correctionid = $_POST['correctionid'] ? $_POST['correctionid'] : $correctionid;
         
         $storagepath = sprintf( '%s/%s', getRootPath(), Settings::Get( 'reedfoto', 'storageroot', 'data/reedfoto/corrections' ) );
         $stagingfolder = Settings::Get( 'reedfoto', 'stagingfolder', 'staging' );
         $archivefolder = Settings::Get( 'reedfoto', 'archivefolder', 'archive' );
         $uploadfolder = Settings::Get( 'reedfoto', 'uploadfolder', 'upload' );
         
         $i = 0;
         $m = 0;
         
         try {
            
            for ( $i = 1; $i <= $pages; $i+=2 ) {
               
               $page = new RFPage();
               $page->correctionid = $correctionid;
               $page->orderkey = $m;
               
               $pagetext = '';
               
               try {
               
                  $firsttextfile = sprintf ( '%s/%s/%s-%d.txt', $storagepath, $stagingfolder, $filehash, $i );
                  $secondtextfile = sprintf ( '%s/%s/%s-%d.txt', $storagepath, $stagingfolder, $filehash, $i+1 );
                  
                  $pagetext = implode( '', file( $firsttextfile ) );
                  $pagetext .= implode( '', file( $secondtextfile ) );
                  
                  unlink( $firsttextfile );
                  unlink( $secondtextfile );
               
               } catch (Exception $f) {
                  
               }
               
               $page->pagetext = $pagetext;

               $page->save();
               
               $guid = $page->imageguid;
               
               $firstimagefile = sprintf ( '%s/%s/%s-%d.png', $storagepath, $stagingfolder, $filehash, $i );
               $secondimagefile = sprintf ( '%s/%s/%s-%d.png', $storagepath, $stagingfolder, $filehash, $i+1 );
               
               $firstfolder = substr($guid, 0, 2);
               $secondfolder = substr($guid, 2, 2);
               
               $archivepath = sprintf( '%s/%s', $storagepath, $archivefolder );

               if ( !is_dir( sprintf( '%s/%s', $archivepath, $firstfolder ) ) ) mkdir( sprintf( '%s/%s', $archivepath, $firstfolder ) );
               if ( !is_dir( sprintf( '%s/%s/%s', $archivepath, $firstfolder, $secondfolder ) ) ) mkdir( sprintf( '%s/%s/%s', $archivepath, $firstfolder, $secondfolder ) );
               
               $largefile = sprintf ( '%s/%s/%s/%s.large.jpg', $archivepath, $firstfolder, $secondfolder, $guid );
               $mediumfile = sprintf ( '%s/%s/%s/%s.medium.jpg', $archivepath, $firstfolder, $secondfolder, $guid );
               $smallfile = sprintf ( '%s/%s/%s/%s.small.jpg', $archivepath, $firstfolder, $secondfolder, $guid );
               
               if ( file_exists( $firstimagefile ) ) {
                  if ( file_exists( $secondimagefile ) && ( $i > 1 ) ) {
                     
                     $page->title = sprintf ( 'Side %d og %d', $i, $i+1 );
                     
                     passthru( sprintf( '%s %s %s %s %s %s','convert', $firstimagefile, $secondimagefile, '+append', '-scale 2000x -quality 95', $largefile ) );

                     passthru( sprintf( '%s %s %s %s','convert', $largefile, '-scale 1000x -quality 95', $mediumfile ) );
                     
                     passthru( sprintf( '%s %s %s %s','convert', $mediumfile, '-scale x90  -quality 95', $smallfile ) );
                     
                     unlink( $firstimagefile );
                     unlink( $secondimagefile );

                  } else {
                     
                     $page->title = sprintf ( 'Side %d', $i );
                     
                     passthru( sprintf( '%s %s %s %s','convert', $firstimagefile, '-scale 1000x -quality 95', $largefile ) );
                     
                     list($width, $height, $type, $attr) = getimagesize($largefile);
                     
                     if ( $i == 1 ) {
                     
                        passthru( sprintf( '%s %s %s %s','convert', $largefile, sprintf('-background white -gravity east -extent 2000x%s -quality 95', $height), $largefile ) );
                        $i--;
                        
                     } else {
                     
                        passthru( sprintf( '%s %s %s %s','convert', $largefile, sprintf('-background white -extent 2000x%s -quality 95', $height), $largefile ) );
                     
                     }
                     
                     passthru( sprintf( '%s %s %s %s','convert', $largefile, '-scale 1000x -quality 95', $mediumfile ) );
                     
                     passthru( sprintf( '%s %s %s %s','convert', $mediumfile, '-scale x90 -quality 95', $smallfile ) );
                     
                     unlink( $firstimagefile );
                      
                  }
               }
               
               $m++;
               
               $page->save();
               
            }
            
            unlink( sprintf ( '%s/%s/%s.pdf', $storagepath, $uploadfolder, $filehash ) );
            
            $this->result = true;
            $this->message = "OK";
            
         } catch (Exception $e) {
            
            $this->result = false;
            $this->message = sprintf( 'Exception occurred: %s', $e );
            
         }
         
      }
   }
   
?>