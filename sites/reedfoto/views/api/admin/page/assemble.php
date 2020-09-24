<?PHP

   import( 'reedfoto.pages.json' );
   import( 'reedfoto.page' );
   
   class ReedFotoApiAdminCorrectionAssemblePage extends JSONPage implements IValidatedView  {
      
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
                  'correctionid' => VALIDATE_INTEGER,
                  'pagenumber' => VALIDATE_INTEGER,
               ),
               'fields' => array(
                  'filehash' => VALIDATE_STRING,
                  'correctionid' => VALIDATE_INTEGER,
                  'pagenumber' => VALIDATE_INTEGER,
               ),
            )
         );
         
      }
      
      /**
       * Assemble a page for a correction
       *
       * @api-name admin.correction.assemblepage
       * @api-javascript yes
       * @api-post-optional filehash String Hash of the pages to assemble in archive/database
       * @api-post-optional pagenumber Integer page number
       * @api-post-optional correctionid Integer ID of the correction to add the pages to
       * @api-param-optional filehash String Hash of the pages to assemble in archive/database
       * @api-param-optional pagenumber Integer page number
       * @api-param-optional correctionid Integer ID of the correction to add the page to
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */  
      public function Execute( $filehash = '', $correctionid = 0, $pagenumber = 0 ) {
         
         $filehash = $_POST['filehash'] ? basename( $_POST['filehash'] ) : basename( $filehash );
         $correctionid = $_POST['correctionid'] ? $_POST['correctionid'] : $correctionid;
         $pagenumber = $_POST['pagenumber'] ? $_POST['pagenumber'] : $pagenumber;
         
         $storagepath = sprintf( '%s/%s', getRootPath(), Settings::Get( 'reedfoto', 'storageroot', 'data/reedfoto/corrections' ) );
         
         $stagingfolder = Settings::Get( 'reedfoto', 'stagingfolder', 'staging' );
         $archivefolder = Settings::Get( 'reedfoto', 'archivefolder', 'archive' );

         $stagingpath = sprintf( '%s/%s', $storagepath, $stagingfolder );
         $archivepath = sprintf( '%s/%s', $storagepath, $archivefolder );
         
         $watermarkfolder = Settings::Get( 'reedfoto', 'watermarkfolder', 'system/watermark' );
         $watermarkpath = sprintf( '%s/%s', $storagepath, $watermarkfolder );
         
         $systemfolder = Settings::Get( 'reedfoto', 'systemfolder', 'system' );
         $systempath = sprintf( '%s/%s', $storagepath, $systemfolder );
         
         $convert = Settings::Get( 'reedfoto', 'convert', '/usr/bin/convert' );
         $composite = Settings::Get( 'reedfoto', 'composite', '/usr/bin/composite' );
         
         try {
            
            $page = new RFPage();
            $page->correctionid = $correctionid;
            $page->orderkey = $pagenumber;
            
            $pagetext = '';
            
            try {
            
               $firsttextfile = sprintf ( '%s/%s-%d.txt', $stagingpath, $filehash, $pagenumber );
               
               $pagetext = implode( '', file( $firsttextfile ) );
               
               unlink( $firsttextfile );

            } catch (Exception $f) {
               
            }
            
            try {
               $secondtextfile = sprintf ( '%s/%s-%d.txt', $stagingpath, $filehash, $pagenumber + 1 );
               
               $pagetext .= implode( '', file( $secondtextfile ) );
               
               unlink( $secondtextfile );
               
            } catch (Exception $f) {
               
            }
            
            $page->pagetext = $pagetext;

            $page->save();
            
            $guid = $page->imageguid;
            
            $firstimagefile = sprintf ( '%s/%s-%d.png', $stagingpath, $filehash, $pagenumber );
            $secondimagefile = sprintf ( '%s/%s-%d.png', $stagingpath, $filehash, $pagenumber + 1 );
            
            $firstfolder = substr($guid, 0, 2);
            $secondfolder = substr($guid, 2, 2);

            if ( !is_dir( sprintf( '%s/%s', $archivepath, $firstfolder ) ) ) mkdir( sprintf( '%s/%s', $archivepath, $firstfolder ) );
            if ( !is_dir( sprintf( '%s/%s/%s', $archivepath, $firstfolder, $secondfolder ) ) ) mkdir( sprintf( '%s/%s/%s', $archivepath, $firstfolder, $secondfolder ) );
            
            $largefile = sprintf ( '%s/%s/%s/%s.large.jpg', $archivepath, $firstfolder, $secondfolder, $guid );
            $mediumfile = sprintf ( '%s/%s/%s/%s.medium.jpg', $archivepath, $firstfolder, $secondfolder, $guid );
            $smallfile = sprintf ( '%s/%s/%s/%s.small.jpg', $archivepath, $firstfolder, $secondfolder, $guid );

            if ( file_exists( $firstimagefile ) ) {
               if ( file_exists( $secondimagefile ) && ( $pagenumber > 1 ) ) {
                  
                  $page->title = sprintf ( 'Side %d og %d', $pagenumber, $pagenumber + 1 );
                  
                  passthru( sprintf( '%s %s %s %s %s %s',$convert, $firstimagefile, $secondimagefile, '+append', '-scale 2000x -quality 95', $largefile ) );
                  
                  list($width, $height, $type, $attr) = getimagesize($largefile);
                  
                  $page->width = $width;
                  $page->height = $height;
                  
                  $watermarkheightfile = sprintf( '%s/%s', $watermarkpath, sprintf( 'watermark_%s.png', $height ) );
                  $watermarkfile = sprintf( '%s/watermark.png', $systempath );
                  
                  if ( !file_exists( $watermarkheightfile ) ) {
                      passthru( sprintf( '%s %s %s %s', $convert, $watermarkfile, sprintf( '-scale x%s', $height ), $watermarkheightfile ) );  
                  }
                  
                  passthru( sprintf( '%s %s %s %s %s', $composite, $watermarkheightfile, '-dissolve 10 -gravity center', $largefile, $largefile ) );  

                  passthru( sprintf( '%s %s %s %s',$convert, $largefile, '-scale 948x -quality 95', $mediumfile ) );
                  
                  passthru( sprintf( '%s %s %s %s',$convert, $mediumfile, '-scale x90  -quality 95', $smallfile ) );
                  
                  unlink( $firstimagefile );
                  unlink( $secondimagefile );

               } else {
                  
                  $page->title = sprintf ( 'Side %d', $pagenumber );
                  
                  passthru( sprintf( '%s %s %s %s',$convert, $firstimagefile, '-scale 1000x -quality 95', $largefile ) );
                  
                  list($width, $height, $type, $attr) = getimagesize($largefile);
                  
                  $page->width = $width;
                  $page->height = $height;
                  
                  if ( $pagenumber == 1 ) {
                  
                     passthru( sprintf( '%s %s %s %s',$convert, $largefile, sprintf('-background white -gravity east -extent 2000x%s -quality 95', $height), $largefile ) );
                     
                  } else {
                  
                     passthru( sprintf( '%s %s %s %s',$convert, $largefile, sprintf('-background white -extent 2000x%s -quality 95', $height), $largefile ) );
                  
                  }
                                   
                  $watermarkheightfile = sprintf( '%s/%s', $watermarkpath, sprintf( 'watermark_%s.png', $height ) );
                  $watermarkfile = sprintf( '%s/watermark.png', $systempath );
                  
                  if ( !file_exists( $watermarkheightfile ) ) {
                      passthru( sprintf( '%s %s %s %s', $convert, $watermarkfile, sprintf( '-scale x%s', $height ), $watermarkheightfile ) );  
                  }
                  
                  passthru( sprintf( '%s %s %s %s %s', $composite, $watermarkheightfile, '-dissolve 10 -gravity center', $largefile, $largefile ) );  
                  
                  passthru( sprintf( '%s %s %s %s',$convert, $largefile, '-scale 948x -quality 95', $mediumfile ) );
                  
                  passthru( sprintf( '%s %s %s %s',$convert, $mediumfile, '-scale x90 -quality 95', $smallfile ) );
                  
                  unlink( $firstimagefile );
                   
               }
               
               $page->save();
               
            }
            
            $this->result = true;
            $this->message = "OK";
            
         } catch (Exception $e) {
            
            $this->result = false;
            $this->message = sprintf( 'Exception occurred: %s', $e );
            
         }
         
      }
   }
   
?>