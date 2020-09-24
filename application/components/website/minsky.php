<?php

model('user.minsky');
library( 'xml.photobookbeta' );
import( 'mail.send');


class minSky extends DBminSkyProject{
    
    public function Create( $host, $job_uuid, $password, $user = null ) {
    
        $productid = '891000';
        $themeUrl = '{library}Photobook\Themes\A4 Stående\02_Classic\02_White\theme.xml';

        
        $exists = $this->projectFromProjectid( $job_uuid );
        
        
        if( $exists->id ){
            return false;
        }else{
            $this->productid =  $productid;
            $this->projectid =  $job_uuid;
            $this->created = date('Y-m-d H:i:s');
            $this->name = $user['name'];
            $this->email = $user['email'];
            $this->phone = $user['phone'];
            $this->save();
        }
        
        
        $string = file_get_contents('/var/www/repix/sites/website/views/create/prefill/text.xml');
        $string  = str_replace(':', '' , $string );
        $xml = simplexml_load_string($string);
     
        $cover = $xml->apiproject->photobookphotobook->photobookprintableCover;
        
        
        $width = (int)$cover['width'];
        $height = (int)$cover['height'];
        $bleed = (string)$cover['bleed'];
        $project = new ProjectXML('<?xml version="1.0" encoding="utf-8"?><orderRequest/>');
    
        $project->productid = $productid;
        $project->width = $width;
        $project->height = $height;
     
        $project->addProjectAttr();
            
        $projects = $project->addChild('projects');
        $photobook = $projects->addChild('hack:photobook:photobook');
        $photobook['productId'] = $productid;
        $photobook['plu'] = $productid;
    
        $photobook->AddState( $themeUrl);
     
        $printableCover = $photobook->addChild('hack:photobook:printableCover');
        $printableCover['bleed'] = 225;
        $printableCoverFront = $printableCover->addChild('hack:photobook:front');
        
        $printableCoverFront->addAttribute('width', (int)$cover->photobookfront['width']);
        $printableCoverFront->addAttribute( 'height', (int)$cover->photobookfront['height']);
        $printableCoverFront->addBackground( $this->background, $cover->photobookfront->collagebackgroundElement );
        //$printableCoverFront->AddPhoto();
        $printableCoverSide = $printableCover->addChild('hack:photobook:side' );
        $printableCoverSide->addAttribute('width', (int)$cover->photobookside['width']);
        $printableCoverSide->addAttribute('height',(int)$cover->photobookside['height']);
        $printableCoverSide->addBackground( $this->background , $cover->photobookside->collagebackgroundElement);
        $printableCoverBack = $printableCover->addChild('hack:photobook:back' );
        $printableCoverBack->addAttribute( 'width', (int)$cover->photobookback['width']);
        $printableCoverBack->addAttribute('height', (int)$cover->photobookback['height']);
        $printableCoverBack->addBackground( $this->background, $cover->photobookback->collagebackgroundElement );
        $pages = $photobook->addChild('hack:photobook:pages');
    
        $pagecount = 10;
    
        $id = "60f250eb-6716-4f90-bd2e-6ee7682b50fe";
        $id = $albumid;
        //$albumjson = file_get_contents( 'https://'. $host . '/st/4/jobs/' . $id . '/changes?serial=1&key=KVEz-WDMsj' );
        $albumjson = file_get_contents( sprintf( 'https://%s/st/4/jobs/%s/photobook?auth=%s&key=oQEpKf06Ue',  $host, $job_uuid, $password  ) );
        $imagesjson = json_decode( $albumjson );
        
       
        $images = array();
        foreach($imagesjson->files as $ret ){
            $id = $ret->file_uuid;
            
            
            $fileurl  = sprintf( "https://%s/st/4/jobs/%s/files_by_path/%s?auth=%s&key=oQEpKf06Ue", $host, $job_uuid, $ret->path,  $password );
            
            $filepath = $job_uuid . "/" . $id . ".jpg";
            
            $this->downloadFile( $fileurl, $filepath );
            
            
            $images[] = array(
                'id' => $id,
                'path' => $ret->path,
                'height' => $ret->height,
                'width' => $ret->width,
                'date' => $ret->ctime,
                'comments' => $ret->comments,
            );
            /*$image = new stdClass();
            $image->id = $id;
            $image->path = $ret->comments;
            $image->date = $ret->comments;
            $image->comments = $ret->comments;
            $image->width = $ret->width,;
            $image->height = $ret->height,;*/
   
        }
        
        
    
        /*$tmpimages = $images;
        foreach(  $tmpimages as $image ){
            $images[] = $image;
        }*/
    
        $imagelist = json_encode($images);
        
  
        $photobookpages = $xml->apiproject->photobookphotobook->photobookpages;
        $bleed = (int)$photobookpages['bleed'];
        $pages->addAttribute('bleed', $bleed );
        $pagecount = 30;
        $tempplatesites = count($photobookpages->photobookpage);
        $tp = 0;
        $imagecount = 0;
        
        for ($i = 1; $i <= $pagecount; $i++) {
            
            if( $tp >= $tempplatesites ){
                $tp = 0;
            }
            $photobookpage = $photobookpages->photobookpage[$imgPrPage[$i] - 1];
            $element = $photobookpage->photos;
            $backgroundElement = $photobookpage->collagebackgroundElement;
            $textElement = $photobookpage->collagetextElement;
            
            $width = (int)$photobookpage['width'];
            $height = (int)$photobookpage['height'];
            
            $page = $pages->addChild('hack:photobook:page');
            $page->addAttribute('width', $width );
            $page->addAttribute('height', $height );
            $page->addAttribute('style', 'a' );
         
            $page->addBackground( $this->background, $backgroundElement );
            
            $countelement =  count( $element );
            
            for ($c = 0; $c < $countelement; $c++) {
                if( $images[$imagecount] ){
                    $image = $images[$imagecount];
                }else{
                  
                     /*$image = new stdClass();
                     $image->placeholder = "{resources}";
                     $image->x = "1396";
                     $image->y = "1047";
                     $image->width = "2000";
                     $image->height = "2000";*/
                     
                     $image = array(
                        'placeholder' => "{resources}",
                        'x' => "1396",
                        'y' => "1047",
                        'width' => "2000",
                        'height' => "2000"
                    );
                    
                }
                $page->addPhotoMinSky($image, $job_uuid, $element[$c]);

                $imagecount++;
            }
            //for ($t = 0; $t < $countelement; $t++) {   
                //$page->AddText( $textElement[$t] );
            //}
            $tp++;
                
        }
        
        
            

        $xml =  $project->asXML();
        
        $this->projectxml = $xml;
        $this->imagelist = $imagelist;

        $this->save();

        //echo $xml;
        
        //header("Content-type: text/xml");
        //echo $xml;
        //exit;
        
        /*
         $productid = $productid;
         $productoptionid = $productid;
         
         
         
         if( Login::isLoggedIn() ){
            $userid = Login::userid();
         }else{
            $userid = 639866;
         }
         
         $type = 'Photobook';*/
        
        return $job_uuid;
        
    }
    
    
    static function projectFromProjectid($projectid){
        
        return ProductOption::fromFieldValue(
                  array(
                    'projectid' => $projectid
                  ),
                  'minSky'
               );
    
    }
    
    private function downloadFile($url, $path){
        $imgfolder = "/data/pd/minsky/";
        $newfname = $imgfolder . $path;
        $dirname = dirname( $newfname );
        
        if( !file_exists( $dirname ) ){
            mkdir($dirname);
        }
        
        if( file_exists( $newfname )  ){
            
            return true;
        }
        
        $file = fopen ($url, 'rb');
        if ($file) {
            $newf = fopen ($newfname, 'wb');
            if ($newf) {
                while(!feof($file)) {
                    fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
                }
            }
        }
        if ($file) {
            fclose($file);
        }
        if ($newf) {
            fclose($newf);
        }
        
        $img = new Imagick($newfname);
        $img->thumbnailImage ( 1024 , 1024, true );
        $img->writeImage( str_replace( '.jpg', '_screen.jpg', $newfname ) );
        $img->thumbnailImage ( 150 , 150, true );
        $img->writeImage( str_replace( '.jpg', '_thumb.jpg', $newfname ) );
        
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
}