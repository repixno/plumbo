<?
/**
 * ******************************************************
 * script to display the thumbnails of the projects to the 
 * customer in my projects and shopping cart
 * @author Tor Inge Lovland <tor.inge@eurofoto.no>
 *********************************************************/
import('website.project');
import('website.projectorder');
import('website.product');
config( 'website.mediaclip' );

class Thumb extends WebPage implements IView {

   	protected $template = '';
   	protected $anonymous = 639866;
   	protected $example_images = 2302423;
	
      public function Execute(){
      
         relocate(WebsiteHelper::rootBaseUrl());
      
      }
      
      public function projectThumb($id){
         
         $project = new Project($id);
         header( "Content-Type: image/jpeg" );
         
         if(Login::userid() == $project->user_id || $project->share == 1){
            
            $thumb_path = Settings::Get( 'mediaclip', 'thumbpath' );
            
            $filename = md5($project->id);
            
            $thumb = $thumb_path . date("Y-m-d" ,strtotime($project->created)) . "/" . $filename . ".jpg";
            $cachefilename = "/home/www/tmpbilder/" . $filename . ".jpg";
            
            if( file_exists($cachefilename )){
               session_write_close();
                  // grab all request headers
               $headers = getallheaders();
               
               // if browser sent id, we check if they match
               if( isset( $headers['If-None-Match'] ) && ereg( $filename, $headers['If-None-Match'] ) ) {
                  // Output a 304 Not Modified header
                  header( 'HTTP/1.1 304 Not Modified' );
                  header( 'Content-Length: 0' );
                  // simply exit
                  exit;
               
               } else {
                  header( 'content-length: '.filesize( $cachefilename ) );
                  // setup caching headers
                  header( "ETag: \"".$res[0]["hashcode"]."\"");
                  header( "Accept-Ranges: bytes");
                  header( sprintf( 'Expires: %s GMT', gmdate( 'D, d M Y H:i:s', time() + ( 86400 * 60 ) ) ) );
                  header( 'Cache-Control: public' );
                  header( 'Pragma: public' );
                  readfile ($cachefilename);
               }
            } 
            
            else if(file_exists($thumb)){
               if(!file_exists($cachefilename)){
                  symlink($thumb , $cachefilename);
               }
               // setup caching headers
               header( "ETag: \"" . $filename . "\"");
               header( "Accept-Ranges: bytes");
               header( sprintf( 'Expires: %s GMT', gmdate( 'D, d M Y H:i:s', time() + ( 86400 * 60 ) ) ) );
               header( 'Cache-Control: public' );
               header( 'Pragma: public' );
               
               readfile ($cachefilename);
            }
            else{
               $images = explode(',' ,$project->getProduct()->images);
               $imageurl = WebsiteHelper::staticBaseUrl() . "/images/products/thumbs/square/70/" . $images[0];
               try {
                  echo file_get_contents( $imageurl );
               } catch( Exception $e ) {
                  die();
               }

            }
         }
         else{
            $images = explode(',' ,$project->getProduct()->images);
            $imageurl = WebsiteHelper::staticBaseUrl() . "/images/products/thumbs/square/70/" . $images[0];
            try {
               echo file_get_contents( $imageurl );
            } catch( Exception $e ) {
               die();
            }


         }
         die();
         
      }
     
     
      public function orderThumb($id){
         
         $project = new ProjectOrder($id);
         header( "Content-Type: image/jpeg" );
         
         if(Login::userid() == $project->user_id){
            
            $thumb_path = Settings::Get( 'mediaclip', 'thumbpathcart' );
            
            $filename = md5($project->production_id);
            
            $thumb = $thumb_path . $project->product_id . "/" . $filename . ".jpg";
            $cachefilename = "/home/www/tmpbilder/" . $filename . ".jpg";
            
            if( file_exists($cachefilename )){
               session_write_close();
                  // grab all request headers
               $headers = getallheaders();
               
               // if browser sent id, we check if they match
               if( isset( $headers['If-None-Match'] ) && ereg( $filename, $headers['If-None-Match'] ) ) {
                  // Output a 304 Not Modified header
                  header( 'HTTP/1.1 304 Not Modified' );
                  header( 'Content-Length: 0' );
                  // simply exit
                  exit;
               
               } else {
                  header( 'content-length: '.filesize( $cachefilename ) );
                  // setup caching headers
                  header( "ETag: \"".$res[0]["hashcode"]."\"");
                  header( "Accept-Ranges: bytes");
                  header( sprintf( 'Expires: %s GMT', gmdate( 'D, d M Y H:i:s', time() + ( 86400 * 60 ) ) ) );
                  header( 'Cache-Control: public' );
                  header( 'Pragma: public' );
                  readfile ($cachefilename);
               }
            } 
            
            else if(file_exists($thumb)){
               if(!file_exists($cachefilename)){
                  symlink($thumb , $cachefilename);
               }
               // setup caching headers
               header( "ETag: \"" . $filename . "\"");
               header( "Accept-Ranges: bytes");
               header( sprintf( 'Expires: %s GMT', gmdate( 'D, d M Y H:i:s', time() + ( 86400 * 60 ) ) ) );
               header( 'Cache-Control: public' );
               header( 'Pragma: public' );
               readfile ($cachefilename);
            }
            else{
               $project_id = $project->project_id;
               $original_project = new Project( $project_id );
               $images = explode( ',' ,$original_project->getProduct()->images);
               $imageurl = WebsiteHelper::staticBaseUrl() . "/images/products/thumbs/square/70/" . $images[0];
               try {
                  echo file_get_contents( $imageurl );
               } catch( Exception $e ) {
                  die();
               }

             
           

            }
         }
         else{
            $project_id = $project->project_id;
            $original_project = new Project( $project_id );
            $images = explode(',' ,$original_project->getProduct()->images);
            $imageurl = WebsiteHelper::staticBaseUrl() . "/images/products/thumbs/square/70/" . $images[0];
            try {
               echo file_get_contents( $imageurl );
            } catch( Exception $e ) {
               die();
            }


         }
         die();
         
      }
      

}