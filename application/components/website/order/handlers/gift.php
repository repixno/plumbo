<?php


   /**
    * 
    * Base handler class for products
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */


   
   import( 'website.order.handlers.base' );
   import( 'website.order.template' );
   import( 'website.giftordertemplate' );
   import( 'website.giftpagetemplate' );
   import( 'website.giftordertext' );
   import( 'website.giftorderclipart' );
   
   model( 'order.option' );
   
   
   class OrderHandlerGifts extends OrderHandlerBase {

      private $clipartpath;
      private $fontpath;
      private $originaltemplatespath;
      
      public function Process( UserOrder $order, $type, $item ) {
         
         parent::Process( $order, $type, $item );
         
         $this->clipartpath            = Settings::Get( 'paths', 'clipart' );
         $this->fontpath               = Settings::Get( 'paths', 'fonts' );
         $this->originaltemplatespath  = Settings::Get( 'paths', 'originaltemplates' );
         
         $credit = $this->checkCredit( $item );
         
         $this->createFilePaths( $item );
         $this->parseItem( $item );
		 $this->checkLicense( $item );
         $this->Finalize();
         
         if( $item['redeyeremoval'] ){
            $this->finalizeRedeyeremoval($item['redeyeremoval']);
         }
         if( $item['varnish'] ){
            $this->finalizeXtra($item['varnish']);
         }
	 if( $item['upgrade'] ){
            $this->finalizeXtra($item['upgrade']);
         }
         // Did product have credits?
         if( is_array( $credit ) ) {
            $this->finalizeCredits( $credit, $item);
         }
      }
      
      
      /**
       * Creates the necessary file paths
       * for later production export.
       *
       * @param string $type
       * @param array $item
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      private function createFilePaths( $item ) {
         
         $orderdirectory = "$this->downloadpath/".$this->today;
         
         if( !is_dir( $orderdirectory ) ) {
            mkdir( $orderdirectory, 0750 );
	      }
	
	      $orderdirectory.= "/$this->orderid";
	      if( !is_dir( $orderdirectory ) ) {
	         mkdir( $orderdirectory, 0750 );
	      }
	      
	      if( !is_dir( "$orderdirectory/autoedit" ) ) {
	         mkdir( "$orderdirectory/autoedit", 0750 );
	      }

	      if( is_dir( "$orderdirectory/autoedit" ) ) {

	         $fp = fopen( "$orderdirectory/README.txt", "w" );
	         fputs( $fp, "This order is not complete until a file named COMPLETED is in this directory" );
	         fclose($fp);

	         $tmpfile = "autoedit/xfiles.txt ";
	         $fp = fopen( "$orderdirectory/autoedit/xfiles.txt","a" );
	         fputs( $fp, "$tmpfile\n" );
	         fclose($fp);

	      }

	      exec( "touch $orderdirectory/WAITINGFORFILES" );
	      
      }
      
      
      
      /**
       * Parse a gift template item and 
       * insert into necessary tables.
       * 
       *
       * @param array $item
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      private function parseItem( $item ) {
         
         $templatereferenceid = $item['referenceid'];
         $quantity            = $item['quantity'];
         
         $templateorders = new GiftOrderTemplate();
		 
		 $templatcollection = $templateorders->collection( array( 'id' ), array( 'malorderref' => $templatereferenceid ), 'page' )->fetchAllAs( 'GiftOrderTemplate' );
		 
		 if( count( $templatcollection ) == 0  ){
			$templatcollection = $templateorders->collection( array( 'id' ), array( 'id' => $templatereferenceid ), 'page' )->fetchAllAs( 'GiftOrderTemplate' );
		 }
		 
         foreach(  $templatcollection as $giftordertemplate ){
            
            $unique        = sprintf( "%03d", $this->order->getStartLotNr() );
            $sartnr        = sprintf( "%03d", $item['refid'] );
            $page          = sprintf( "%03d", $giftordertemplate->page );
            $fquantity     = sprintf( "%03d", $quantity );
            $filename      = $fquantity."-".$this->orderid."-".$unique."-".$sartnr."-".$page."."."jpg";
            $usermod       = $giftordertemplate->user_mod;
            $productoption = $item['currentproductoption'];
            $refsubid      = $productoption['refsubid'];
            
            $this->artnr = $sartnr; 
            
            // Dont know if this is necessary.
            // It seems always to be true in old code so
            // I'll set it as true here for now.
            if( !$usermod ) {
               $usermod = true;
            } else {
              $usermod = false; 
            }
            $usermod = true;
            
            // User wish for redeye correction?
            if( count( $item['redeyeremoval'] ) > 0 ) {
               $redeye = true;
            } else {
               $redeye = false;
            }
            
            if( count( $item['varnish'] ) > 0 ) {
               $varnish = true;
            } else {
               $varnish = false;
            }
	    
	    if( count( $item['upgrade'] ) > 0 ) {
               $upgrade = true;
            } else {
               $upgrade = false;
            }

            // Update object and save it
            $orderobject               = new OrderTemplate();
            $orderobject->orderid	= $this->orderid;
            $orderobject->artnr		= $giftordertemplate->refid;
            $orderobject->templateid	= $giftordertemplate->templateid;
            $orderobject->lot		= $giftordertemplate->id;
            $orderobject->page		= $giftordertemplate->page;
            $orderobject->imageid	= $giftordertemplate->imageid;
            $orderobject->quantity	= $quantity;
            $orderobject->filename	= $filename;
            $orderobject->text		= $giftordertemplate->text;
            $orderobject->user_mod	= $usermod;
            $orderobject->redeye	= $redeye;
            $orderobject->varnish	= $varnish;
			$orderobject->upgrade	= $upgrade;
            $orderobject->save();
            
            // Is there an option for this gift?
            if( strlen( $refsubid ) ) {
               $options    = explode( '-', $refsubid );
               $mainoption = reset( $options );
               $suboption  = end( $options );
               
               $option              = new DBOrderOption();
               $option->orderid     = $this->orderid;
               $option->templateid  = $giftordertemplate->id;
               $option->option      = $mainoption;
               $option->suboption   = $suboption;
               $option->quantity    = $quantity;
               $option->save();
               
            }
            
            $this->produceTemplate( $orderobject );
         }
         
      }
      
      
      
      /**
       * Produce compilation scripts for making template order
       * and put them in the necessary directories.
       *
       * @param DBOrderTemplate $ordertemplate
       * @return boolean
       * 
       * @todo move sRGB.icm to ef3 folder
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      private function produceTemplate( DBOrderTemplate $ordertemplate ) {
         
         if( $ordertemplate instanceof DBOrderTemplate && $ordertemplate->isLoaded() ) {
            
            //$image               = new Image( $ordertemplate->imageid );
            $image               = new DBObject( $ordertemplate->imageid );
            $filename            = $image->filename;
            $filetype            = $image->filetype;
            $title               = $image->title;
            $lot                 = $ordertemplate->lot;
            $unique              = sprintf( "%03d", $lot );
            $targetfilename      = $ordertemplate->filename;
            $originalfilename    = "org_$unique.$filetype";
            $templateorderfiles  = '';
            $oldroot = Settings::Get( 'paths', 'oldroot' );
            
            try {
               
               $torder                 = new GiftOrderTemplate( $ordertemplate->lot );
               $partition              = explode( '/', $image->filename );
               $partition              = reset( $partition );
               $orderdirectory         = "$this->imagepath/$partition/"."print_download";
               
               
               // Create production directories to put files in
               if( !is_dir( $orderdirectory ) ) {
                  mkdir( $orderdirectory, 0750 );
               }
               $orderdirectory.= "/".$this->today;
               if( !is_dir( $orderdirectory ) ) {
                  mkdir( $orderdirectory, 0750 );
               }
               $orderdirectory.= "/$this->orderid";
               if( !is_dir( $orderdirectory ) ) {
                  mkdir( $orderdirectory, 0750 );
               }
               $orderdirectory.= "/$ordertemplate->artnr";
               if( !is_dir( $orderdirectory ) ) {
                  mkdir( $orderdirectory, 0750 );
               }
               if( !is_dir( $orderdirectory."/autoedit" ) ) {
                  mkdir( $orderdirectory."/autoedit", 0750 );
               }
               
               
               if( !file_exists( $orderdirectory."/autoedit/".$originalfilename ) ) {
                  link( $this->imagepath."/".$filename, $orderdirectory."/autoedit/".$originalfilename );
               }
               chmod( $orderdirectory."/autoedit/".$originalfilename, 0664 );
               
               
               if($torder->malid == 0){
                  
                  $commands = array();
                  
                  $editor_x = $torder->editor_x;
                  $editor_y = $torder->editor_y;
                  
                  $fullsize_x = $image->x;
                  $fullsize_y = $image->y;
                  
                  $editRatio_x = $fullsize_x / $editor_x;
                  $editRatio_y = $fullsize_y / $editor_y;
                  
                  $command_x = round( $torder->x * $editRatio_x);
                  $command_y = round( $torder->y * $editRatio_y );
                  $command_dx = round( $torder->dx * $editRatio_x);
                  $command_dy = round( $torder->dy * $editRatio_y);
                  
                  $cropRatio = $command_dx / $command_dy;
                  
                  $printsize_x = round($torder->printsize_x * 94.5);
                  $printsize_y = round($torder->printsize_y * 94.5);
                  
                  
                  $commands[] = sprintf("convert -size %sx%s -units PixelsPerInch -density 240x240 xc:white org.png", $command_dx, $command_dy);
                  $commands[] = sprintf("composite -geometry -%s-%s %s org.png -profile /home/httpd/www.repix.no/webside/grafikk/sRGB.icm org.png", $command_x, $command_y, $originalfilename);
                  
                 $printtype = $torder->printtype;
                 
                 $gallery = explode("x", $printtype);
                  
                  if(count($gallery) == 2){       
                     $gallery_x = $gallery[1];
                     $gallery_y = $gallery[0];
                                     
                     $fullimage = 'org.jpg';
                     
                     $framewidth = round(94.5  * 4);
                     
                     $each_printsize_x = (($printsize_x - (($gallery_x+1) * $framewidth)) / $gallery_x) + ($framewidth * 2);
                     $each_printsize_y = (($printsize_y - (($gallery_y+1) * $framewidth)) / $gallery_y) + ($framewidth * 2);

                     $commands[] = sprintf("convert org.png -resize %sx%s -quality 100 %s", $printsize_x, $printsize_y, $fullimage );
                     $commands[] = sprintf("convert -size %sx%s -units PixelsPerInch -density 240x240 xc:white -quality 100 orgeach.jpg", $each_printsize_x, $each_printsize_y);
                     $commands[] = sprintf("convert -size %sx%s -units PixelsPerInch -density 240x240 -background white -bordercolor black -border 1 -pointsize 16 -gravity South caption:'%s' canvas.tif",$each_printsize_x + 280, $each_printsize_y + 280, $this->orderid );
                     
                     for($x1 = 0; $x1 < $gallery_x; $x1++){
                        
                        $crop_x =  ($each_printsize_x - $framewidth)  *  $x1;  
                        for($y = 0; $y < $gallery_y; $y++ ){
                           $crop_y = ($each_printsize_y - $framewidth)  *  $y;
                           $commands[] = sprintf("composite -geometry -%s-%s %s orgeach.jpg org%s.tif", $crop_x, $crop_y, $fullimage, $x1.$y);
                           $commands[] = sprintf("convert org%s.tif -bordercolor black -border 1 -quality 97 org%s.jpg", $x1.$y, $x1.$y);
                           $commands[] = sprintf("composite -geometry +141+141 org%s.jpg canvas.tif -profile /home/httpd/www.repix.no/webside/grafikk/sRGB.icm -units PixelsPerInch -density 240x240 -quality 97 ../%s_%s", $x1.$y, $x1.$y , $targetfilename);
                           
                        }
                     }   
                  }
                  else{
                     $commands[] = sprintf("convert org.png -resize %sx%s -bordercolor black -border 1 -quality 100 org.jpg", $printsize_x, $printsize_y);
                     $commands[] = sprintf("convert -size %sx%s -units PixelsPerInch -density 240x240 -background white -bordercolor black -border 1 -pointsize 16 -gravity South caption:'%s' canvas.tif",$printsize_x + 280, $printsize_y + 280, $this->orderid  );
                     $commands[] = sprintf("composite -geometry +141+141 org.jpg canvas.tif -profile /home/httpd/www.repix.no/webside/grafikk/sRGB.icm -units PixelsPerInch -density 240x240 -quality 97 ../%s", $targetfilename);
                     $commands[] = "rm canvas.tif";
                  }

                  file_put_contents( "$orderdirectory/autoedit/$unique"."_command_list.sh", implode( "\n", $commands ) );                  
                  
               }
               else{
               
                  $tpagedata              = GiftPageTemplate::fromTemplateIdAndPageId( $torder->malid, $torder->page );
                  
                  $templatefile           = "$this->originaltemplatespath/$tpagedata->fullsize_src";
                  $templatetarget         = $tpagedata->fullsize_src;
   
                  
                  $templateorderfiles .= "$ordertemplate->artnr/autoedit/$orginalfilename ";
                  if( !file_exists( $orderdirectory."/autoedit/$templatetarget" ) ) {
                     copy( $templatefile, $orderdirectory."/autoedit/".$templatetarget );
                     chmod( $orderdirectory."/autoedit/".$templatetarget, 0664 );
                     $templateorderfiles .= "$artnr/autoedit/$templatetarget ";
                  }
                  
                  
                  // Handle uploaded files here? Original code
                  /*$client_info["uploaded_images"] = convertToUploadedImages( $client_info );
                  if($fildata[0]["owner_uid"] == 61224 && $client_info["uploaded_images"][$bid] == 1){
                     //Ok situation
                  }
                  else if($fildata[0]["owner_uid"]){
                     //Ok situation, but want to move the image please
                     sql_simpleExec("UPDATE bildeinfo SET owner_uid=$shopuid WHERE bid=$bid;");
                  }
                  else{
                     must_pictureaccess($bid);
                  }*/
                  
                  
                  
                  // Ok let's start calculating stuff
                  
                  // Rotate image
                  if( $torder->rotate == 90 || $torder->rotate == 270 ) {
                     $tmp = $image->x;
                     $image->x = $image->y;
                     $image->y = $tmp;
                  }
                  
                  $ratio_image = $image->x / $torder->dx;
                  
                  $ratio = $tpagedata->fullsize_x / $torder->editor_x;
                  
                  $clip_dx = ceil( $tpagedata->fullsize_pos_dx * $ratio_image );
                  $clip_dy = ceil( $tpagedata->fullsize_pos_dy * $ratio_image );
                  
                  if( $clip_dx > $clip_dy ) {
                     $clip_dy = $clip_dx;
                  } else{
                     $clip_dx = $clip_dy;
                  }
                  
                  
                  // Calculate offsets
                  $offset_clip_x = ( $torder->x - $tpagedata->fullsize_pos_x  ) * $ratio_image;
                  $offset_clip_x = ceil( $offset_clip_x );
                  if( $offset_clip_x >= 0 ) {
                     $offset_clip_x = "+$offset_clip_x";
                  }
                  $offset_clip_y = ( $torder->y - $tpagedata->fullsize_pos_y ) * $ratio_image;
                  $offset_clip_y = ceil( $offset_clip_y );
                  if( $offset_clip_y >= 0 ) {
                     $offset_clip_y = "+$offset_clip_y";
                  }
                  
                  
                  $offset_x = $tpagedata->fullsize_pos_x;
                  $offset_x = ceil( $offset_x );
                  if( $offset_x >= 0 ) {
                     $offset_x = "+$offset_x";
                  }
                  $offset_y = $tpagedata->fullsize_pos_y;
                  $offset_y = ceil( $offset_y );
                  if( $offset_y >= 0 ) {
                     $offset_y = "+$offset_y";
                  }
                  $fullclip_x = $tpagedata->fullsize_pos_dx;
                  $fullclip_y = $tpagedata->fullsize_pos_dy;
                  if( $fullclip_x > $fullclip_y ) {
                     $fullclip_y = $fullclip_x;
                  } else{
                     $fullclip_x = $fullclip_y;
                  }
                  
                  
                  // Start creating the compilation script
                  if( file_exists( $this->imagepath."/".$filename ) ) {
                     
                     $textcommands = array();
                     $tempfiles = array();
                     $private_kode = $unique;
                     $final = $private_kode.".tif";
                     
                     $textcommands[] = "convert -size $clip_dx" . "x" . "$clip_dy xc:white $private_kode.jpg";
                     $textcommands[] = "composite -geometry $offset_clip_x$offset_clip_y  $originalfilename $private_kode.jpg -profile $oldroot". "webside/grafikk/sRGB.icm $final";
                     
                     $textcommands[] = "convert -size " . $tpagedata->fullsize_pos_dx . "x" . $tpagedata->fullsize_pos_dy . " xc:white background1.jpg";
                     $textcommands[] = "convert background1.jpg $final -geometry $fullclip_x" . "x" . "$fullclip_y -composite $final";
   
                     $textcommands[] = "convert -size " . $tpagedata->fullsize_x . "x" . $tpagedata->fullsize_y . " xc:white background2.jpg";
                     $convertcommand = "convert background2.jpg $final -geometry $offset_x$offset_y -composite -quality 100";
                     if( $tpagedata->fullsize_src ) {
                        $convertcommand .= " " . $tpagedata->fullsize_src . " -geometry +0+0 -composite";
                     }
   
                     $final = $private_kode.".jpg";
                     $convertcommand .= " $final";
                     
                     $textcommands []= $convertcommand;
                     $textcommands []= "convert $final -quality 97 $private_kode" . "_image_and_template.jpg";
   
                     $texts     = GiftOrderText::enumTextsFromTemplateIdAndPageId( $torder->malorderid, $torder->page );
                     $clipart   = GiftOrderClipart::enumClipartFromTemplateIdAndPageId( $torder->malorderid, $torder->page );
                     
                     // Cliparts
                     $n = count( $clipart );
                     $prevfile = $tmp7;
                     $clipcommands = array();
                     $counter = 0;
                     for( $i=0; $i<$n; $i++ ) {
                        
                        $fullx   = $clipart[$i]['x'];
                        $fully   = $clipart[$i]['y'];
                        $fulldx  = $clipart[$i]['dx'];
                        $fulldy  = $clipart[$i]['dy'];
                        if( $fulldx < 0 || $fulldy < 0 ) {
                           continue;
                        }
                        
                        $clipid = $clipart[$i]['clipid'];
                        $outfile = $private_kode . "t$i" . ".png";
                        $tempfiles []= $outfile;
						
						
						
						
   
                        if( $fullx < 0 ) {
                           $fullx = $fullx;
                        }else{
						   $fullx = "+" . $fullx;
						}
                        if( $fully < 0 ) {
						   $fully = $fully;
                        }else{
						   $fully = "+" . $fully;
						}
   
                        
                        $textcommands []= "composite -geometry !" . $fulldx . "x!" . $fulldy . $fullx . $fully . " " . $clipid ."_" . $clipart[$i]['clipnr'] . ".png $final $final";
                        copy( "$this->clipartpath/$clipid".".png", $orderdirectory."/autoedit/$clipid"."_".$clipart[$i]["clipnr"].".png" );
						
                        $prevfile = $outfile;
                        $counter++;
                        
                     }
   
                     // Texts
                     $n = count( $texts );
                     $counter = 0;
                     for( $i=0;$i<$n;$i++ ){
                        
                        $fullx   = $texts[$i]["x"];
                        $fully   = $texts[$i]["y"];
                        $fulldx  = $texts[$i]["dx"];
                        $fulldy  = $texts[$i]["dy"];
                        if( $fulldx < 0 || $fulldy < 0 ) {
                           continue;
                        }
                        
                        $text    = $texts[$i]["text"];
                        $color   = $texts[$i]["color"];
                        $font    = $texts[$i]["font"];
                        $gravity = $texts[$i]["gravity"];
                        
                        switch( $gravity ) {
                           case "West":
                              break;
                           case "Center":
                              break;
                           case "East":
                              break;
                           default:
                              $gravity = "West";
                        }
   
                        if( $text == "" ) {
                           continue;
                        }
                        
                        $text = str_replace( "XXNYLINJEXX", "\\n", $text );
                        $text = str_replace( "XXOGXX", "&", $text );
                        $text = str_replace( "\r", "", $text );
						
						if( $this->artnr == 7454 || $this->artnr == 7455 ){
						   $textcommands []= "convert -background transparent -pointsize 300 -fill \"#$color\" -font $this->fontpath/$font.ttf -gravity $gravity label:\"$text\" -repage +0+0 -resize !$fulldx" . "x!$fulldy text$counter.png";
						}else{
						   $textcommands []= "convert -background transparent -pointsize 300 -fill \"#$color\" -font $this->fontpath/$font.ttf -gravity $gravity label:\"$text\" -trim -repage +0+0 -resize !$fulldx" . "x!$fulldy text$counter.png";
						}
                         if( $fullx < 0 ) {
                           $fullx = "-" . $fullx;
                        }else{
						   $fullx = "+" . $fullx;
						}
                        if( $fully < 0 ) {
						   $fully = "-" . $fully;
                        }else{
						   $fully = "+" . $fully;
						}
                        
                        $textcommands []= "composite -compose over -geometry $fullx"."$fully text$counter".".png $final $final";
                        $counter++;
                        
                     }
                     
                     if( $this->artnr == 7196 ){
                        $textcommands [] = "composite -pointsize 18 label:$this->orderid  $final $final";
                     }
                     
                     $textcommands []= "convert $final -profile $oldroot" . "webside/grafikk/sRGB.icm -units PixelsPerInch -density " . $tpagedata->fullsize_dpi . "x" . $tpagedata->fullsize_dpi. " -quality 99 ../$targetfilename";
                     $textcommands []= "rm -f " . "$private_kode.tif $private_kode.jpg";
                     $n = count( $tempfiles );
                  }
                  
                  // Save the production script to disc
                  file_put_contents( "$orderdirectory/autoedit/$unique"."_command_list.sh", implode( "\n", $textcommands ) );
                  
                  #util::debug( "$orderdirectory/autoedit" );
                  #die();
               }
            } catch( Exception $e ) {
               
               // Do some error handling here
               //util::debug( $e->getMessage() );
               
            }
           
            
         }
         
         return true;
         
      }
      
   }
   
   
?>