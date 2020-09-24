<?PHP

   /******************************************
    * Script for createing productionfiles 
    * 
    ***************************************/
    chdir( dirname( __FILE__ ) );
    include '../../../bootstrap.php';
 
    config( 'website.config' );
    config( 'website.countries' );
    config( 'production.settings');
    import( 'system.cli' );
    import( 'website.order' );
 
    class giftProductionScript extends Script {
       
        public $webspoolFolder = '/home/produksjon/webspool/';
        public $gavefolder = '/home/produksjon/Gave/';
       
        Public function Main(){
            
            //$to = date('Y-m-d', strtotime('2015-12-09'));
            $to = date('Y-m-d');
            $from = date('Y-m-d', strtotime( $to . '-1 day'));
            
            $articles = DB::query('SELECT artnr from article where prodid = 25')->fetchALL(DB::FETCH_ASSOC);
            
            
            Util::Debug($to);
            
            $arkwidth = 3602;
            $arkheight = 5906;
            $arkcount = 1;
                
            $image = new Imagick();
            $image->newImage($arkwidth, 5906, new ImagickPixel('white'));
            $image->setImageFormat('jpeg');
            $image->setImageCompressionQuality (99);
            $image->setImageResolution(150, 150);
            
            $step = 50;
            $stepy = 0;
            
            $x = $step;
            $y = $step;
            
            $utfolder = $this->gavefolder . date('Y') . '/'. $to;
            
            if( !file_exists($utfolder) ){
                mkdir( $utfolder , 0775, true );
            }
            
            $draw = new ImagickDraw();
            $draw->setFillColor('black');
            $draw->setFontSize( 20 );
            
            foreach( $articles as $article ){
                
                $kopper = DB::query("SELECT * FROM
                                        historie_mal hol,
                                        historie_ordre ho
                                    WHERE
                                        ho.ordrenr = hol.ordrenr
                                    AND
                                        hol.artikkelnr = ?
                                    AND
                                        ho.tidspunkt BETWEEN ? AND ?
                                        ", $article['artnr'], $from, $to )->fetchAll(DB::FETCH_ASSOC);
                
                
                
                foreach( $kopper as $kopp ){
                    
                    $date = date( 'Y-m-d', strtotime(  $kopp['tidspunkt'] ) );
                    
                    $filename =  sprintf( "%s%s/%s/%s/%s" ,
                                        $this->webspoolFolder,
                                        $date,
                                        $kopp['ordrenr'],
                                        $kopp['artikkelnr'],
                                        $kopp['filnamn']
                                         ); 
                    
                    
                    if( !file_exists($filename) ){
                        continue;
                    }
                    
                    $malbilde = new Imagick($filename);
                    //$malbilde->flopImage();
                    
                    $xd = $malbilde->getImageWidth();
                    $yd = $malbilde->getImageHeight();
                    
                    $i = 1;
                    while( $i <= $kopp['antall'] ){
                        if( ( $x + $xd )  >  $arkwidth ){
                            $x = $step;
                            $y = $y + $stepy + $step;
                            $stepy = 0;
                            //$firstimage = true;
                        }
                        $stepy = $yd > $stepy?$yd:$stepy;
                        
                        if( ( $y + $stepy ) > $arkheight ){
                            $image->writeImage( $utfolder . '/kopp' . $arkcount . '.jpg');
                            $arkcount++;
                            $x = $step;
                            $y = $step;
                            $image = new Imagick();
                            $image->newImage($arkwidth, $arkheight, new ImagickPixel('white'));
                            $image->setImageFormat('jpeg');
                            $image->setImageCompressionQuality (99);
                            $image->setImageResolution(150, 150);
                        }
                        
                        $image->annotateImage($draw, $x, $y -13 , 0, $kopp['ordrenr'] . ' - ' . $kopp['artikkelnr'] );
                        
                        $image->compositeImage($malbilde, $malbilde->getImageCompose(), $x,  $y);
                        $x = $x + $malbilde->getImageWidth() + $step; 
                        $i++;
                    }
                }
            }
            
            //Util::Debug($kopper);
            
            $image->writeImage( $utfolder . '/kopp' . $arkcount . '.jpg');
            
            exit;
        }
    }
   
    CLI::Execute();

?>