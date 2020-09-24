<?PHP
   
   import( 'website.mediacliporder' );

   class CreateAssemblyWizard extends WebPage implements IView {
      
      public function Execute( $projectkey = '' ) {
         
         $assembly = sprintf( '%s/data/projects/assemblies/%s/assembly.xml', getRootPath(), $projectkey );
         $basedir = dirname( $assembly );
         $enabled = sprintf( '%s/enabled', $basedir );
         
         try {
            
            if( file_exists( $assembly ) && file_exists( $enabled ) ) {
               
               $project = simplexml_load_file( $assembly );
               if( !$project instanceof SimpleXMLElement
                 || $project->getName() != 'assembly' ) {
                  throw new Exception( 'Unable to read XML' );
               }
               
               $this->setTemplate( (string) $project->attributes()->template );
               $this->projectkey = $projectkey;
               $this->type = (string) $project->attributes()->type;
               $this->title = (string) $project->attributes()->title;
               $this->header = (string) $project->attributes()->header;
               $this->description = (string) $project->attributes()->description;
               $this->icon = (string) $project->attributes()->icon;
               $this->productid = (string) $project->attributes()->productid;
               $this->productoptionid = (int) $project->attributes()->productoptionid;
               $this->minpages = (int) $project->attributes()->minpages;
               
               $this->headerxml = (string) $project->header->attributes()->xml;
               $this->headerpages = (int) $project->header->attributes()->pages;
               $this->footerxml = (string) $project->footer->attributes()->xml;
               $this->footerpages = (int) $project->footer->attributes()->pages;
               
               if( !file_exists( sprintf( '%s/parts/%s', $basedir, $this->headerxml ) ) ) {
                  throw new Exception( 'Header XML not found!' );
               }
               
               if( !file_exists( sprintf( '%s/parts/%s', $basedir, $this->footerxml ) ) ) {
                  throw new Exception( 'Footer XML not found!' );
               }
               
               $sections = array();
               
               foreach( $project->parts->part as $part ) {
                  
                  $options = array();
                  foreach( $part->option as $option ) {
                     
                     if( !file_exists( sprintf( '%s/parts/%s', $basedir, $option->attributes()->xml ) ) ) {
                        throw new Exception( 'XML part not found: '.$option->attributes()->xml );
                     }
                     
                     $options[] = array(
                        'id' => md5( 'option'.$optionid++ ),
                        'title' => (string) $option->attributes()->title,
                        'xml' => (string) $option->attributes()->xml,
                        'icon' => (string) $option->attributes()->icon,
                        'selected' => (integer) $option->attributes()->selected,
                        'pages' => (integer) $option->attributes()->pages,
                     );
                  }
                  
                  $fillers = array();
                  foreach( $part->filler as $filler ) {
                     
                     if( !file_exists( sprintf( '%s/parts/%s', $basedir, $filler->attributes()->xml ) ) ) {
                        throw new Exception( 'XML filler not found: '.$filler->attributes()->xml );
                     }
                     
                     $fillers[] = array(
                        'id' => md5( 'filler'.$fillerid++ ),
                        'title' => (string) $filler->attributes()->title,
                        'pages' => (integer) $filler->attributes()->pages,
                        'xml' => (string) $filler->attributes()->xml,
                     );
                     
                  }
                  
                  $sections[] = array(
                     'id' => md5( 'section'.$sectionid++ ),
                     'title' => (string) $part->attributes()->title,
                     'selected' => (integer) $part->attributes()->selected,
                     'options' => $options,
                     'fillers' => $fillers,
                  );
                  
               }
               
               $this->sections = $sections;
               
            } else {
               
               throw new Exception( 'Not found', 404 );
               
            }
            
         } catch ( Exception $e ) {
            
            $this->notfound = true;
            
         }
         
      }
      
      public function Setup( $projectkey = '' ) {
         
         header( 'content-type: text/xml' );
         
         $productid = (string) $_POST['productid'];
         $productoptionid = (int) $_POST['productoptionid'];
         $projecttitle = (string) $_POST['projecttitle'];
         $projectkey = (string) $_POST['projectkey'];
         $minpages = (int) $_POST['minpages'];
         $type = (string) $_POST['type'];
         $userid = MediaclipOrder::userId();
         
         $assembly = sprintf( '%s/data/projects/assemblies/%s/assembly.xml', getRootPath(), $projectkey );
         $basedir = dirname( $assembly );
         $enabled = sprintf( '%s/enabled', $basedir );
         
         if( $enabled ) {
            
            try {
               
               list( $headerpages, $header ) = explode( ';', $_POST['headerxml'] );
               list( $footerpages, $footer ) = explode( ';', $_POST['footerxml'] );
               
               $header = file_get_contents( sprintf( '%s/parts/%s', $basedir, $header ) );
               $footer = file_get_contents( sprintf( '%s/parts/%s', $basedir, $footer ) );
               
               $sections = array();
               $numpages = $headerpages + $footerpages;
               $fillersegments = 0;
               
               $buffer = '';
               foreach( $_POST['sections'] as $section ) {
                  
                  if( $section['selected'] && $section['option'] ) {
                     
                     $sections[] = $section;
                     
                     list( $pages ) = explode( ';', $section['option'] );
                     
                     $numpages += $pages;
                     
                     if( count( $section['fillers'] ) > 0 ) {
                        $fillersegments++;
                     }
                     
                  }
                  
               }
               
               $pagesleft = $minpages - $numpages;
               $numfillerspersegment = ceil( $pagesleft / $fillersegments / 2 );
               $sections = array_reverse( $sections );
               
               foreach( $sections as $section ) {
                  
                  reset( $section['fillers'] );
                  for( $i = 0; $i < $numfillerspersegment; $i++ ) {
                     $filler = current( $section['fillers'] );
                     if( next( $section['fillers'] ) === false ) {
                        reset( $section['fillers'] );
                     }
                     list( $pages, $source ) = explode( ';', $filler );
                     if( $pagesleft - $pages >= 0 ) {
                        
                        $pagesleft -= $pages;
                        $numpages += $pages;
                        $buffer = file_get_contents( sprintf( '%s/parts/%s', $basedir, $source ) ) . $buffer;
                        // echo "F:$source:$pages<br />\n";
                        
                     }
                     
                  }
                  
                  list( $pages, $source ) = explode( ';', $section['option'] );
                  // echo "P:$source:$pages<br />\n";
                  $buffer = file_get_contents( sprintf( '%s/parts/%s', $basedir, $source ) ) . $buffer;
                  
               }
               
               $buffer = $header.$buffer.$footer;
               
               $projectid = DB::query( 'INSERT INTO
                                          mediaclip_projects ( id, user_id, title, description, created, saved, type ,predefinert, predefined_project_id, productid, productoptionid, project_xml )
                                       VALUES
                                          ( DEFAULT, ?, ?, ?, NOW(), NOW(), ?, NULL, NULL, ?, ?, ? )
                                       RETURNING id;',
                                          $userid, $projecttitle, '', $type, $productid, $productoptionid, utf8_decode( trim( $buffer ) )
                                    )->fetchSingle();
               
               relocate( '/create/%s/edit/%d', $type, $projectid );
               
               die();
               
            } catch( Exception $e ) {
               
               echo $e->getMessage();
               die();
               
            }
            
         }
         
      }
      
   }
   
?>