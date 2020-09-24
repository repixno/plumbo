<?PHP

import( 'cewe.default');

class CeweImage{
    
    
    public $api = object;
    public $image = object;
    
    public function __construct( $id = null ) {
        
        $this->api = new ceweApi();
        
        $id = $this->api->decodeBid( $id );
        
        if( $id ){
            $this->image = $this->api->getApi( '/photos/' . $id  );
            
        }
    }
    
    public function execute(){
        return $this->image;
    }
    
    public function asArray() {
        
        $aid = reset( $this->image->albumIds );
        return $this->api->ceweImageArray( $this->image, $aid );
        
    }
    
    static function select( $imageid = 0 ) {

        Session::set( 'selectedimageid', $imageid );

    }
    
    static function selected() {

         return Session::get( 'selectedimageid', 0 );

    }



}