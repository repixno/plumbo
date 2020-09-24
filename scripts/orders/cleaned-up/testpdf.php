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
   
   library( 'pdf.fpdf' );
   library( 'pdf.fpdi' );
   

   class MerkelappImportScript extends Script {
      
      public $webspoolFolder = '/home/produksjon/merkelapp/test/';
      
      Public function Main(){
            
            $outputFile = $this->webspoolFolder . "nytest.pdf";
            $fullFilename = $this->webspoolFolder . "cut.pdf" ; 
            
            $pdf = new FPDI( 'P','mm',array(100,185) );
            $pdf->AddPage();
            $pdf->Image( $this->webspoolFolder . "aprintfile.jpg" , 0, 0, -300 );
                
            $pdf->setSourceFile( $fullFilename );
            $tplIdx = $pdf->importPage(1);
            $pdf->useTemplate($tplIdx, 0, 0, 0);
            
            $pdf->Output( $outputFile , 'F'); 
      }
   
   
   }
   

   CLI::Execute();

?>