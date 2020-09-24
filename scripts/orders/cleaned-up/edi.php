<?PHP

   /******************************************
    * Script for handling EDI Files
    ***************************************/
   
  //sudo mount -t cifs //10.64.1.176/edi /mnt/edi -o username=produksjon,password=produksjon,gid=1001,uid=1001

   chdir( dirname( __FILE__ ) );
   include '../../bootstrap.php';

   config( 'website.config' );
   import( 'system.cli' );

   class EdiImportScript extends Script {
      
      private $ediFolder = '/home/edi';
      private $doneFolder = '/mnt/edi/Done';
      private $readyFolder = '/mnt/edi/Ready';
      
      Public function Main(){
         
         system( 'rsync -avh remus.eurofoto.no::edi /home/edi/ --delete '  );
         
         $imporfiles = glob($this->ediFolder . '/*.txt');
         $donefiles = glob($this->doneFolder . '/*.txt');
         
         Util::Debug(count($donefiles));
         
         $doneArray = array();
         foreach ($donefiles as $donefile){
            $interval = strtotime('-90 days');
           
            if (filemtime($donefile) <= $interval) {
               unlink($donefile);
            } else {
               $donefile = explode('_' , basename($donefile));
               $doneArray[] = $donefile[0] . '.txt';
            }
         }
         
         foreach ($imporfiles as $file) {
            if (!in_array(basename($file), $doneArray)) {
               //copy($file , $this->readyFolder . '/' . basename($file));
               $dest = $this->readyFolder . '/' . basename($file);
               $cmd = "iconv -sc -f UTF-8 -t WINDOWS-1252 $file > $dest";
               exec($cmd);
               util::Debug("finns ikkje--->" . basename($file));
            } else {
               util::Debug("Er i Done-mappen --->" . basename($file));
            }
         }
      }
   }

   CLI::Execute();

?>
