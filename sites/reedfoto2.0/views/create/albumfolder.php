<?

class Xml extends WebPage implements IView {

	protected $template = '';
	
	public function Execute($id=0){
		header( 'content-type: text/plain' );
		//$allalbums = new Image( $albumid );

		$dirlist = array();
	   
		$albuminfos = DB::query("SELECT filnamn FROM nomore_bildeinfo WHERE owner_uid = ? order by filnamn" ,$id )->fetchAll( );
	   
	   
		//Util::Debug( $albuminfos);
	   
		//foreach( $allalbums->collection( array( 'filnamn' ), array( 'owner_uid' => $id,  'filnamn' ) )->fetchAll() as $albuminfo ) {
		foreach( $albuminfos as $albuminfo ) {
         
			//list( $aid, $namn, $uid ) = $albuminfo;
			
			list( $filnamn ) = $albuminfo;
         
			$dirlist[] = dirname($filnamn);

		}
		
		$dirlist = array_unique($dirlist);
		
		sort($dirlist);
		
		foreach ($dirlist as $dir) {
		   
		   echo "add "  . $dir . "\n";
			
		}
	}
	
	
}




?>
