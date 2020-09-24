<?PHP

   import( 'reedfoto.pages.json' );
   import('math.uuid');
   
   class ReedFotoApiAdminPdfExtract extends JSONPage implements IValidatedView  {
      
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
                  'page' => VALIDATE_INTEGER,
                  'type' => VALIDATE_STRING,
               ),
               'fields' => array(
                  'filehash' => VALIDATE_STRING,
                  'page' => VALIDATE_INTEGER,
                  'type' => VALIDATE_STRING,
               ),
            )
         );
         
      }
      
      /**
       * Extract image/text from a PDF-page
       *
       * @api-name admin.pdf.extract
       * @api-javascript yes
       * @api-post-optional filehash String Hash of the PDF-file to extract page from
       * @api-post-optional page Integer The pagenumber to extract
       * @api-post-optional type String Extract 'text', 'images' or 'both'?
       * @api-param-optional filehash String Hash of the PDF-file to extract page from
       * @api-param-optional page Integer The pagenumber to extract
       * @api-param-optional type String Extract 'text', 'images' or 'both'?
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */      
      public function Execute( $filehash = '', $page = 0, $type = 'text' ) {
         
         $filehash = $_POST['filehash'] ? $_POST['filehash'] : $filehash;
         $page = $_POST['page'] ? $_POST['page'] : $page;
         $type = $_POST['type'] ? $_POST['type'] : $type;
         
         $ghostscriptpath = Settings::Get( 'reedfoto', 'ghostscript', '/usr/bin/gs' );
         $pdftotextpath = Settings::Get( 'reedfoto', 'pdftotext', '/usr/bin/pdftotext' );
         $storagepath = sprintf( '%s/%s', getRootPath(), Settings::Get( 'reedfoto', 'storageroot', 'data/reedfoto/corrections' ) );
         $uploadfolder = Settings::Get( 'reedfoto', 'uploadfolder', 'upload' );
         $stagingfolder = Settings::Get( 'reedfoto', 'stagingfolder', 'staging' );
         
         $pdfguid = $filehash;

            // '-r72x72 '.         
         $extractpng = '-q '.
            '-sDEVICE=pngalpha '.
            '-sOutputFile=%s-%d.png '.
            '-dQUIET '.
            '-r300x300 '.
            '-dPARANOIDSAFER '.
            '-dBATCH '.
            '-dNOPAUSE '.
            '-dNOPROMPT '.
            '-dFirstPage=%d '.
            '-dLastPage=%d '.
            '-dTextAlphaBits=4 '.
            '-dGraphicsAlphaBits=4 '.
            '%s';  
            //     
         /*$extracttext = '-q '.
            '-dNODISPLAY '.
            '-dPARANOIDSAFER '.
            '-dNOPAUSE '.
            '-dNOPROMPT '.
            '-dQUIET '.
            '-dDELAYBIND '.
            '-dWRITESYSTEMDICT '.
            '-dFirstPage=%d '.
            '-dLastPage=%d '.
            '-dSIMPLE '.
            '-c save '.
            '-f ps2ascii.ps %s '.
            '-c quit';*/
            
         $systemreturn = '';
         
         try {
            
            if ($type == 'image') {
               
               $execstring =  sprintf( 
                  '%s %s', 
                  $ghostscriptpath, 
                  escapeshellcmd ( 
                     sprintf( 
                        $extractpng, 
                        sprintf ( 
                           '%s/%s/%s', 
                           $storagepath,
                           $stagingfolder,
                           $pdfguid 
                        ),
                        $page,
                        $page, 
                        $page, 
                        sprintf( 
                           '%s/%s/%s.pdf', 
                           $storagepath,
                           $uploadfolder,
                           basename( $filehash )
                        ) 
                     )  
                  )   
               );
               
               passthru ( $execstring, $systemreturn );
               
               echo $execstring;
               
               $this->message = $systemreturn;
               $this->result = true;
               
            } else if (type == 'text') {
               
               /*$execstring =  sprintf( 
                  '%s %s >> %s', 
                  $ghostscriptpath, 
                  escapeshellcmd ( 
                     sprintf( 
                        $extracttext, 
                        $page, 
                        $page, 
                        sprintf ( 
                           '%s/%s/%s.pdf', 
                           $storagepath, 
                           $uploadfolder,
                           basename( $filehash )
                        )
                     ) 
                  ),
                  sprintf( 
                     '%s/%s/%s-%s.txt',
                     $storagepath,
                     $stagingfolder,
                     $pdfguid,
                     $page
                  )
               );*/
               
               $execstring = sprintf( 
                  '%s %s -f %s -l %s -enc UTF-8',
                  $pdftotextpath,
                  sprintf ( 
                     '%s/%s/%s.pdf', 
                     $storagepath, 
                     $uploadfolder,
                     basename( $filehash )
                  ),
                  $page,
                  $page
               );
               
               passthru ( $execstring, $systemreturn );
               
               copy(
                  sprintf ( 
                     '%s/%s/%s.txt', 
                     $storagepath, 
                     $uploadfolder,
                     basename( $filehash )
                  ),
                  sprintf( 
                     '%s/%s/%s-%s.txt',
                     $storagepath,
                     $stagingfolder,
                     $pdfguid,
                     $page
                  )
               );
               
               $this->message = "OK";
               $this->result = true;

            } else {
               
               $execstring =  sprintf( 
                  '%s %s', 
                  $ghostscriptpath, 
                  escapeshellcmd ( 
                     sprintf( 
                        $extractpng, 
                        sprintf ( 
                           '%s/%s/%s', 
                           $storagepath,
                           $stagingfolder,
                           $pdfguid 
                        ),
                        $page,
                        $page, 
                        $page, 
                        sprintf( 
                           '%s/%s/%s.pdf', 
                           $storagepath,
                           $uploadfolder,
                           basename( $filehash )
                        ) 
                     )  
                  )   
               );
               
               passthru ( $execstring, $systemreturn );
               
               //--
               
               $execstring = sprintf( 
                  '%s %s -f %s -l %s -enc UTF-8',
                  $pdftotextpath,
                  sprintf ( 
                     '%s/%s/%s.pdf', 
                     $storagepath, 
                     $uploadfolder,
                     basename( $filehash )
                  ),
                  $page,
                  $page
               );
               
               
               passthru ( $execstring, $systemreturn );
               
               copy(
                  sprintf ( 
                     '%s/%s/%s.txt', 
                     $storagepath, 
                     $uploadfolder,
                     basename( $filehash )
                  ),
                  sprintf( 
                     '%s/%s/%s-%s.txt',
                     $storagepath,
                     $stagingfolder,
                     $pdfguid,
                     $page
                  )
               );
               
               passthru ( $execstring, $systemreturn );
                      
               $this->message = "OK";
               $this->result = true;
                            
            }
         } catch (Exception $e) {
            $this->message = $e;
            $this->result = false;
         }
      }
   }
   
?>