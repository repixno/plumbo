<?PHP

   chdir( dirname( __FILE__ ) );
   include '../../bootstrap.php';

   config( 'website.config' );

   import( 'system.cli' );

   /**
    * CEWE Article mapping
    * @copyright Eivind A. Moland <eivind@eivind.biz>
    */

   class CeweArticleMapping extends Script {

      public function Main() {
      
         $row = 0;

         if ( ( $handle = fopen( '2011-04-14-cewe_article_mapping.csv', 'r' ) ) !== FALSE ) {
 
            while ( ( $data = fgetcsv( $handle, 1000, ',' ) ) !== FALSE ) {

               if ( $row > 0 ) {

                  $ceweid = $data[0];
                  $eurofotoid = $data[1];

                  $artnr = DB::query( 'SELECT artnr from article where artnr = ?', $eurofotoid )->fetchSingle(); 
                  $oldceweid = DB::query( 'SELECT cewe_id from article where artnr = ?', $eurofotoid )->fetchSingle(); 

                  if ( !$artnr ) {

                     echo "Produktet finnes ikke: ". $eurofotoid. " " .$artnr."\n";

                  } else {

                     echo "oppdaterer: ". $eurofotoid. " med " .$ceweid."\n";

                     if ( $oldceweid ) {
                        echo "Gammel cewe id: ". $oldceweid ."\n";
                     }

                     DB::query( 'update article set cewe_id = ? where artnr = ?', $ceweid, $eurofotoid );

                  }
               }

               $row++;
            }
            fclose($handle);
         }
         
      }
   }

   CLI::execute();

?>

