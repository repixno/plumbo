<?

import( 'website.album' );
import( 'website.image' );
import( 'website.album.identifier');
import( 'website.reedfoto.reedfotoalbum');

class FetchalbumLogin extends WebPage implements IView {
      
      protected $template = 'fetchalbum/login';
      
      function Execute() {
             
            $identifiervalue = Session::get( 'identifiervalue' );
            
            $imagethumbbs = array();
            
            if( !empty( $identifiervalue ) ){
                  $identifier = Reedfotoalbum::fromIdentifier( $identifiervalue );
                  $images = DB::query( "SELECT bid FROM bildeinfo WHERE aid = ? AND deleted_at IS NULL" , $identifier->aid )->fetchAll( DB::FETCH_ASSOC );
                  
                  foreach( $images as $image ){
                        $imagethumbbs[]  = sprintf('%s/fetchalbum/login/previewthumbnail/%d',
                                    WebsiteHelper::rootBaseUrl(),
                                    $image['bid']
                              );
                  }
            }
            $this->identifiervalue = $identifiervalue;
            $this->thumbnails = $imagethumbbs;
         
      }
      
      public function previewthumbnail($bid=0){
            
         $this->template = null;
         config( 'website.storage' );

         $identifiervalue = Session::get( 'identifiervalue' );

         $thumb = DB::query("SELECT * FROM bildeinfo WHERE bid = ? AND deleted_at IS NULL", $bid )->fetchAll( DB::FETCH_ASSOC );
         $thumb = $thumb[0];

         $rfalbum = DB::query( 'SELECT identifier FROM reedfoto_album WHERE aid = ?' , $thumb['aid'] )->fetchSingle();

         
         if( $rfalbum != $identifiervalue ){
            return false;
         }

         $imagespath = Settings::Get( 'storage', 'path');
         $cachefilename = "/home/www/tmpbilder/" . $thumb['hashcode'] . ".mc_preview.jpg";
         try{
            $cachefilename = $imagespath . $thumb['filnamn']  . ".preview.jpg";        
            header( "Content-Type: image/jpeg" );
            readfile ($cachefilename);
          
         }catch (Exception $e){
            $cachefilename = '/var/www/repix/sites/static/webroot/gfx/404/not_found_100px.jpg';
            header( "Content-Type: image/jpeg" );
            readfile( $cachefilename );
         } 
      }
}
      
?>