<?PHP

   /******************************************
    *TEST SCRIPT FOR ADDING CUTMARKS
    ***************************************/
   chdir( dirname( __FILE__ ) );
   include '../../bootstrap.php';

   config( 'website.config' );
   config( 'website.countries' );
   config( 'production.settings');
   import( 'system.cli' );
   import( 'website.order' );
   import( 'website.order.merkelapporder');
   model( 'order.merkelapp' );

   class MerkelappImportScript extends Script {
      
      public $webspoolFolder = '/home/produksjon/merkelapp/test/';
      
      Public function Main(){
            
            $outputFile = $this->webspoolFolder . "test1223.pdf"; 
            
            //create a doc first. This refers to the output doc. 
            $pdf = PDF_new(); 
            //$pdf->set_option("license=0");

            // begin the document 
            if (PDF_begin_document($pdf, $outputFile, "") == 0) { 
                die("Error: " . PDF_get_errmsg($pdf)); 
            }
            
            
            
            $fullFilename = $this->webspoolFolder . "kake.pdf" ; 
            
            $src_doc      = pdf_open_pdi_document($pdf,$fullFilename,''); 
            $src_page      = pdf_open_pdi_page($pdf,$src_doc,1,''); 
            
            // I want the width and height of the pdf I saved (and now opened) above. 
            $src_width     =  283; //PDF_pcos_get_number($pdf, $src_doc, 'width'); 
            $src_height    =  524; //PDF_pcos_get_number($pdf, $src_doc, 'height' ); 
            
            $slika = pdf_load_image($pdf,"jpeg", $this->webspoolFolder . "aprintfile.jpg" ,"");
            
            
            // begin a page 
            pdf_begin_page_ext($pdf, $src_width, $src_height, "");
            
            pdf_fit_image($pdf,$slika, 0, 0,"");
            
            // place the src/doc page. Note that $pdf refers to the final large doc and $src_page is the handle to the pdf we saved above 
            pdf_fit_pdi_page($pdf,$src_page,0,0,''); 
            pdf_close_pdi_page($pdf,$src_page); 
            PDF_end_page_ext($pdf,''); 
            
            
            PDF_end_document($pdf,''); 
            PDF_delete($pdf);
                
            
            
            
            /*
            $pdfdoc = pdf_new();
            pdf_begin_document($pdfdoc, $this->webspoolFolder . "test.pdf", "");
            pdf_begin_page_ext($pdfdoc, 1179, 2185, "");
            
            $slika = pdf_load_image($pdfdoc,"jpeg", $this->webspoolFolder . "aprintfile.jpg" ,"");
            pdf_fit_image($pdfdoc,$slika, 0, 0,"scale 4.16");
            
            /*$eps = pdf_load_image($pdfdoc, "png", $this->webspoolFolder . "test.png" ,"");
            pdf_fit_image($pdfdoc,$eps, 0, 0,"scale 4.16");
            
            $stroke = pdf_open_pdi_document( $pdfdoc,  $this->webspoolFolder . "kake.pdf", "" );
            pdf_fit_pdi_page( $pdfdoc, $stroke, 0 , 0 , "");
            
            pdf_end_page_ext($pdfdoc, "");
            pdf_end_document($pdfdoc, "");
            fclose($fd);
                
                
                $outputFile         = "bigoutputfile.pdf"; 
                     
                //create a doc first. This refers to the output doc. 
                $pdf = PDF_new(); 
                     
                // begin the document 
                if (PDF_begin_document($pdf, $outputFile, "") == 0) { 
                       die("Error: " . PDF_get_errmsg($pdf)); 
                } 
                     
                // This line is required to avoid problems on Japanese systems...whatever 
                    PDF_set_parameter($pdf, "hypertextencoding", "winansi"); 
                 
                //foreach ( $aryItemParams as $key => $value ) { // Don't worry about the values in the foreach...I'm not going to mention them 
                        // Okay, here I curled a script which brings me back a pdf. 
                        // I then save the pdf to the filesystem. With each loop, 
                        // I get another pdf. No sense putting this code here as it will 
                        // just make things less clear. 
                         
                         
                     // we want to open the pdf (called $fullFilename) we just 
                        // saved using cURL (again, didn't show that code to keep 
                        // things clear) 
                    $src_doc      = pdf_open_pdi($pdf,$fullFilename,'', 0); 
                    $src_page      = pdf_open_pdi_page($pdf,$src_doc,1,''); 
                         
                        // I want the width and height of the pdf I saved (and now opened) above. 
                    $src_width     = pdf_get_pdi_value($pdf,'width' ,$src_doc,$src_page,0); 
                    $src_height    = pdf_get_pdi_value($pdf,'height',$src_doc,$src_page,0); 
                
                    // begin a page 
                    PDF_begin_page_ext($pdf, $src_width, $src_height, ""); 
                         
                    // place the src/doc page. Note that $pdf refers to the final large doc and $src_page is the handle to the pdf we saved above 
                    pdf_fit_pdi_page($pdf,$src_page,0,0,''); 
                    pdf_close_pdi_page($pdf,$src_page); 
                    PDF_end_page_ext($pdf,''); 
                //} 
                PDF_end_document($pdf,''); 
                PDF_delete($pdf); 
            } */
            
      }
   
   
   }
   

   CLI::Execute();

?>