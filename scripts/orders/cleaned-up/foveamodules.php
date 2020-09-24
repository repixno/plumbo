<?PHP

/******************************************
* Script for handling Fovea modules
***************************************/

chdir( dirname( __FILE__ ) );
include '../../bootstrap.php';

config( 'website.config' );
import( 'production.foveatemplate' );
import( 'system.cli' );


class Fovemodulescript extends Script {


    Public function Main(){
    
        $modul = new foveatemplate();
        
        $modul->convertimage( '/home/produksjon/man_ord/foveatest/test.jpg', '/home/produksjon/man_ord/foveatest/module13.jpg' , 5013  );
    
    }



}


CLI::Execute();

?>
