<?php

   /**
    * 
    * Create and show a giftcard pdf for download
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */


   import( 'website.order.giftcard' );
   import( 'core.util' );


   class CheckoutGiftcardCreate extends WebPage implements IView {
      
      private $folder = '/tmp/';
      protected $template = false;
      
      public function Execute( $orderid = null ) {
         
         if( !isset( $orderid ) ) $orderid = isset( $_POST['orderid'] ) ? $_POST['orderid'] : null;
         
         $outfile = "/tmp/giftcard_$orderid.pdf";
         $jobname = date( 'Ymd'.$orderid );

         $portal = Dispatcher::getPortal();
         switch( $portal ) {
            case 'VG-997':
               $bgfilename = 'background.jpg';
               break;
            default:
               $bgfilename = 'background.jpg';
               break;
         }
         
         $background = sprintf( '%s/sites/static/webroot/gfx/giftcard/%s', getRootPath(), $bgfilename );
         
         $numjobs = DB::query( "SELECT count(giftcardid) FROM giftcard WHERE orderid = ? AND buyerid = ?", $orderid, Login::userid() )->fetchSingle();
         
         if( $numjobs > 0 ) {
            
            // Create a new pdf document
            $p = PDF_new();
            PDF_begin_document( $p, '', 'destination={page=1 type=fixed zoom=1 top=0 left=0 right=0 bottom=0}' );
            
            PDF_set_info( $p, "Creator", 'Eurofoto' );
            PDF_set_info( $p, "Author", 'Eurofoto' );
            PDF_set_info( $p, "Title", $jobname );
            
            // Append image to page
            $image = PDF_load_image( $p, 'jpeg', $background, '' );
            
            $giftcards = new Giftcard();
            foreach( $giftcards->collection( array( 'giftcardid' ), array( 'buyerid' => Login::userid(), 'orderid' => $orderid ) )->fetchAllAs( 'giftcard' ) as $giftcard ) {
               
                  // Start the current page
                  PDF_begin_page( $p, 525, 301 );
                  
                  PDF_fit_image( $p, $image, 0, 0, 'scale 0.25' );
                  
                  // Setup the text
                  $color = $this->rgb2cmyk( $this->hex2rgb( '#010101' ) ); 
                  $font = 'trebuc.ttf';
                  $fontfile = sprintf( '%s/data/fonts/%s', getRootPath(), util::urlize( $font ) );
                  $expires = date( 'Y-m-d', strtotime( $giftcard->expires ) );
                  
                  if( !isset( $fontfiles[$font] ) ) {
                     if( file_exists( $fontfile ) ) {
                        $fontfiles[$font] = PDF_set_parameter( $p, 'FontOutline', sprintf( '%s=%s', $font, $fontfile ) );
                     }
                  }
                  
                  // Write the text
                  $fontsize = 22;
                  $output = PDF_load_font( $p, $font, 'iso8859-1', '' );
                  PDF_setfont( $p, $output, $fontsize );
                  PDF_setcolor( $p, 'both', 'cmyk', $color['c'], $color['m'], $color['y'], $color['k'] );

                  PDF_set_text_pos( $p, 200, 160 );
                  PDF_show( $p, 'Verdi: '.$giftcard->orgvalue.'kr' );
                  
                  PDF_set_text_pos( $p, 110, 130 );
                  PDF_show( $p, 'Din kode: '.$giftcard->code );
                  
                  $fontsize = 12;
                  PDF_setfont( $p, $output, $fontsize );
                  PDF_set_text_pos( $p, 200, 50 );
                  PDF_show( $p, 'Gyldig til: '.$expires );
                  
                  // End the current page
                  PDF_end_page( $p );
               
            }
            
            PDF_close_image( $p, $image );
            
            // End the document
            PDF_end_document( $p, '' );
            
            // Get the buffer and write to file
            $pdf = PDF_get_buffer( $p );
            header("Content-Type: application/pdf");
            header("Content-Disposition: attachment; filename=giftcard_$orderid.pdf");
            header("Content-length: " . strlen( $pdf ) );
            
            echo $pdf; 
            
         }
         
      }
      
      private function rgb2cmyk( $var1, $g=0, $b=0 ) {
         if( is_array( $var1 ) ) {
            $r = $var1['r'];
            $g = $var1['g'];
            $b = $var1['b'];
         }
         else $r=$var1;
         $cyan    = 255 - $r;
         $magenta = 255 - $g;
         $yellow  = 255 - $b;
         $black   = min($cyan, $magenta, $yellow);
         $cyan    = @(($cyan    - $black) / (255 - $black)) * 255;
         $magenta = @(($magenta - $black) / (255 - $black)) * 255;
         $yellow  = @(($yellow  - $black) / (255 - $black)) * 255;
         return array('c' => $cyan / 255,
                      'm' => $magenta / 255,
                      'y' => $yellow / 255,
                      'k' => $black / 255);
      }
      
      
      private function hex2rgb( $hex ) {
        $color = str_replace('#','',$hex);
        $rgb = array('r' => hexdec(substr($color,0,2)),
                     'g' => hexdec(substr($color,2,2)),
                     'b' => hexdec(substr($color,4,2)));
        return $rgb;
      }
      
   }


?>