<?

import( 'website.album' );
import( 'website.image' );
import( 'website.album.identifier');
import( 'website.reedfoto.reedfotoalbum');
import( 'session.usersessionarray' );
import( 'website.cart' );

class OrderReedfotoDefault extends WebPage implements IView {
      
      protected $template = 'order/default';
      
      function prev( $code ) {
             
            $identifiervalue = $code;
            
            $imagethumbbs = array();
            $gruppethumbs = array();
            
            if( !empty( $identifiervalue ) ){
                  $identifier = Reedfotoalbum::fromIdentifier( $identifiervalue );
                  $images = DB::query( "SELECT bid, hashcode FROM bildeinfo WHERE aid = ? AND deleted_at IS NULL and identifier is null" , $identifier->aid )->fetchAll( DB::FETCH_ASSOC );
                  
                  $gruppebilder = DB::query( "SELECT bid, hashcode FROM bildeinfo WHERE aid = ? AND deleted_at IS NULL and identifier = 'gruppe'" , $identifier->aid )->fetchAll( DB::FETCH_ASSOC );
                  
                  foreach( $images as $image ){
                        $imagethumbbs[]  = array(
                                'thumb' => sprintf('%s/order/previewthumbnail/%s/%s/%s',
                                    WebsiteHelper::rootBaseUrl(),
                                    $image['bid'],
                                    $image['hashcode'],
                                    $code),
                                 'link' => '/order/add/' . $image['bid'],
                                 'bid' => $image['bid']
                                 ); 
                  }
                  foreach( $gruppebilder as $image ){
                        $gruppethumbs[]  = sprintf('%s/order/previewthumbnail/%s/%s/%s',
                                    WebsiteHelper::rootBaseUrl(),
                                    $image['bid'],
                                    $image['hashcode'],
                                    $code
                              );
                  }
            }
            $this->identifiervalue = $identifiervalue;
            $this->thumbnails = $imagethumbbs;
            $this->gruppebilde = $gruppethumbs;
         
      }
      
      
      public function thumbnail($bid='', $code  ){
            
         $this->template = null;
         config( 'website.storage' );

         //$identifiervalue = Session::get( 'identifiervalue' );
         $identifiervalue = $code;

         $thumb = DB::query("SELECT * FROM bildeinfo WHERE bid = ? AND deleted_at IS NULL", $bid )->fetchAll( DB::FETCH_ASSOC );
         $thumb = $thumb[0];
         
         $rfalbum = DB::query( 'SELECT identifier FROM reedfoto_album WHERE aid = ?' , $thumb['aid'] )->fetchSingle();

         
         if( $rfalbum != $identifiervalue ){
            return false;
         }

         $imagespath = Settings::Get( 'storage', 'path');
         $cachefilename = "/home/www/tmpbilder/" . $thumb['hashcode'] . ".thumb_preview.jpg";
         
         
         
         if ( !file_exists($cachefilename) ) {
            $filsrc = $imagespath . $thumb['filnamn'] . '.preview.jpg';
            symlink($filsrc,$cachefilename);	
         }
               
            header( "Content-Type: image/jpeg" );
            header( 'content-length: ' . filesize( $cachefilename ) );
            // setup caching headers
            header( "ETag: \"" . $thumb->hashcode . "\"");
            header( "Accept-Ranges: bytes");
            header( sprintf( 'Expires: %s GMT', gmdate( 'D, d M Y H:i:s', time() + ( 86400 * 60 ) ) ) );
            header( 'Cache-Control: public' );
            header( 'Pragma: public' );
            
            readfile ($cachefilename);
         
         try{
            //$cachefilename = $imagespath . $thumb['filnamn'];        
            header( "Content-Type: image/jpeg" );
            readfile ($cachefilename);
         }catch (Exception $e){
            $cachefilename = '/var/www/repix/sites/static/webroot/gfx/404/not_found_100px.jpg';
            header( "Content-Type: image/jpeg" );
            readfile( $cachefilename );
         } 
      }
      
      public function previewthumbnail($bid='', $hashcode, $code  ){
            
         $this->template = null;
         config( 'website.storage' );

         //$identifiervalue = Session::get( 'identifiervalue' );
         $identifiervalue = $code;

         $thumb = DB::query("SELECT * FROM bildeinfo WHERE bid = ? AND  hashcode = ? AND deleted_at IS NULL", $bid,  $hashcode )->fetchAll( DB::FETCH_ASSOC );
         $thumb = $thumb[0];
         
         $rfalbum = DB::query( 'SELECT identifier FROM reedfoto_album WHERE aid = ?' , $thumb['aid'] )->fetchSingle();

         
         if( $rfalbum != $identifiervalue ){
            return false;
         }

         $imagespath = Settings::Get( 'storage', 'path');
         $cachefilename = "/home/www/tmpbilder/" . $thumb['hashcode'] . ".orf_preview.jpg";
         
         
         if( !file_exists($cachefilename) ){
            $imagemagick = new Imagick( $imagespath . $thumb['filnamn'] );
            $imagemagick->thumbnailImage(300, 300, true );
            $imagemagick->writeImage($cachefilename);  
         }
         
         
         //Util::debug($cachefilename);
         //exit;
         
         try{
            //$cachefilename = $imagespath . $thumb['filnamn'];        
            header( "Content-Type: image/jpeg" );
            readfile ($cachefilename);
          
         }catch (Exception $e){
            $cachefilename = '/var/www/repix/sites/static/webroot/gfx/404/not_found_100px.jpg';
            header( "Content-Type: image/jpeg" );
            readfile( $cachefilename );
         } 
      }
      
      
      public function fetch(){
      
            $kode = $_POST['kode'];
            
            relocate( '/order/' . $kode );
            
            exit;
      }
      
      public function Execute( $identifiervalue ){
            
        $_SESSION["identifiervalue"] = $identifiervalue;
        
        $cart = new Cart();
        $cart->removeDeliveryType();
        $cart->removePaymentType();
        $cart->recalculateTotals();
        $cart->save();
        
        $this->template  = 'order/add';
        $identifier = Reedfotoalbum::fromIdentifier( $identifiervalue );
        if( !empty( $identifiervalue ) ){
            $identifier = Reedfotoalbum::fromIdentifier( $identifiervalue );
            $images = DB::query( "SELECT bid, hashcode FROM bildeinfo WHERE aid = ? AND deleted_at IS NULL and ( identifier is null OR identifier = '' ) order by tittel" , $identifier->aid )->fetchAll( DB::FETCH_ASSOC );
            
            $gruppebilder = DB::query( "SELECT bid, hashcode FROM bildeinfo WHERE aid = ? AND deleted_at IS NULL and identifier ilike 'gruppe%'" , $identifier->aid )->fetchAll( DB::FETCH_ASSOC );
            
            foreach( $images as $image ){
                  $imagethumbbs[]  = array(
                          'thumb' => sprintf('%s/order/previewthumbnail/%s/%s/%s',
                              WebsiteHelper::rootBaseUrl(),
                              $image['bid'],
                              $image['hashcode'],
                              $identifiervalue),
                           'bid' => $image['bid']
                           ); 
            }
            
            foreach( $gruppebilder as $image ){
                  $gruppethumbs[]  = array(
                          'thumb' => sprintf('%s/order/previewthumbnail/%s/%s/%s',
                              WebsiteHelper::rootBaseUrl(),
                              $image['bid'],
                              $image['hashcode'],
                              $identifiervalue),
                           'bid' => $image['bid']
                           ); 
            }
        }
        
        $productarray = array( 3291, 3817, 3799, 3815, 3801, 3966 );
        
        foreach( $productarray as $productoptionid ){
            
            if( $productoptionid == 3815 ){
                   $type = 'group';
            }
            else{
                  $type = 'portrait';
            }
            $productoption = new ProductOption($productoptionid);
            $productid = $productoption->productid;
            $product = new Product($productid);
            $product = $product->asArray();
            
            $products[$productoptionid] = array(
                        'productoptionid' => $productoptionid,
                        'option' => $productoption->asArray(),
                        'productid' => $productid,
                        'product' => $product,
                        'type' => $type
                     );
        }
        
        $this->klassebilde = null;
        $this->products = $products;
        $this->identifiervalue = $identifiervalue;
        $this->thumbnails = $imagethumbbs;
        $this->gruppebilde = $gruppethumbs;
        
      }
}
      
?>