<?php

class ProjectXML extends SimpleXMLElement {
    
    public $productid = '891000'; 
    public $width = 0; 
    public $height = 0;
    
    
    public function getName() {
        return dom_import_simplexml($this)->nodeName;
    }
 
    public function getType() {
        return dom_import_simplexml($this)->nodeType;
    }
    
    public function domtest( $filename ){
        
        $node = dom_import_simplexml($this); 

        $no   = $node->ownerDocument;
        
        $frontCover = $no->createElementNS('collage', 'collage:backgroundElement');
        $frontCover->setAttribute('width', '4041');
        $frontCover->setAttribute('height', '3119');
        $frontCover->setAttribute('color', '#000000');
        $frontCover->setAttribute('fileName', $filename);
        
        $this->addChild('ladllasl');
        
    }
    
    public function addProjectAttr(){
        $this['xmlns:model'] ="Mediaclip.Modules.Model";
        $this['xmlns:photobook']="Mediaclip.Photobook.Model";
        $this['xmlns:collage']="Mediaclip.Modules.Model.Collage";
        $this['creationDate']="2015-11-02";
        $this['xmlns'] = "Mediaclip.ClipProducer.Factories.Contract.OrderRequest";
        $this->customer = "3bc3151757b86dc87992e0875d8c5484";
    }
    
    
    public function AddState( $themeUrl ){
        $state = $this->addChild('hack:photobook:state');
        $state['step'] = "EDITION";
        $state['themeUrl'] = $themeUrl;
        $state['creationVersion'] = "4.2.0.225";
        $state['lastEditVersion'] = "4.2.0.225";
        
    }
    
    public function addBackground( $filename, $back ){
        
        //$node = dom_import_simplexml($this); 

        //$no   = $node->ownerDocument;
        
        $frontCover = $this->addChild('hack:collage:backgroundElement');
        $frontCover->addAttribute('x', '0');
        $frontCover->addAttribute('y', '0');
        $frontCover->addAttribute('width', '0');
        $frontCover->addAttribute('height', '0');
        $frontCover->addAttribute('cropX', (int)$back['cropX']);
        $frontCover->addAttribute('cropY', (int)$back['cropY']);
        $frontCover->addAttribute('cropWidth', (int)$back['cropWidth']);
        $frontCover->addAttribute('cropHeight', (int)$back['cropHeight']);
        $frontCover->addAttribute('color', (string)$back['color']);
        //$frontCover->addAttribute('fileName', (string)$back['fileName']);
        $frontCover->addAttribute('fileName', (string)$back['fileName']  );
        
    }
    
    
    public function AddText( $textelement, $textstring="{insertYourTextHere}" ){
    
        $text = $this->addChild('hack:collage:textElement');
        $text->addAttribute('x', (int)$textelement['x']);
        $text->addAttribute('y', (int)$textelement['y']);
        $text->addAttribute('width', (int)$textelement['width']);
        $text->addAttribute('height', (int)$textelement['height']);
        $text->addAttribute('wordWrap', 'false');
        $text->addAttribute('scaleToFit', 'true');
        $text->addAttribute('text', (string)$textstring);
        
    
        
        $textelementstyle =  $textelement->collagetextStyle;
        
        
        $textstyle = $text->addChild('hack:collage:textStyle');
        $textstyle->addAttribute('fontSize', (int)$textelementstyle['fontSize']);
        if( $textelementstyle['color'] ){
            $textstyle->addAttribute('color', (string)$textelementstyle['color']);
        }
        
        $textstyle->addAttribute('fontFamily', (string)$textelementstyle['fontFamily']);

        $background = $textstyle->addChild('hack:collage:straightBackground ');
        
        $background->addAttribute('opacity', '0');     
        
        
    }
    
    
    
    public function addPhoto( $image,  $element ){

        $userid = Login::userid();
        
        if( $image['placeholder'] ){
            $filename = $image['placeholder'];
        }else{
            $filename = sprintf( '{userFiles}%d\%d.jpg' , $userid, $image['id'] );
        }
        
        $x = (int)$element['x'];
        $y = (int)$element['y'];
        $width = (int)$element['width'];
        $height = (int)$element['height'];
        $pinPosition = (string)['pinPosition'];
        

        $placeholderratio = $width / $height;
        $imageratio = $image['x'] / $image['y'];
        
        if( $placeholderratio >  $imageratio ){
            $ratio = $image['y'] / $image['x'];
            $cropWidth = $width;
            $cropHeight = $width * $ratio;
            $cropY = -( $cropHeight - $height ) / 2;
        }else{
            $ratio = $image['x'] / $image['y'];
            $cropHeight = $height;
            $cropWidth = $height * $ratio;
            $cropX = -( $cropWidth - $width ) / 2;
        }
        
        $photo = $this->addChild('hack:collage:photoElement');
        $photo->addAttribute('x', $x );
        $photo->addAttribute('y', $y );
        $photo->addAttribute('width',  $width );
        $photo->addAttribute('height', $height );
        $photo->addAttribute('cropY', (int)$cropY);
        $photo->addAttribute('cropX', (int)$cropX);
        $photo->addAttribute('cropWidth', (int)$cropWidth );
        $photo->addAttribute('cropHeight', (int)$cropHeight );
        $photo->addAttribute('fileName', $filename);
        
        $photoframe = $photo->addChild('hack:collage:frameBorder');
        $photoframe->addAttribute('filename', '{library}Borders\Fancy\07_Stamp_Charcoal\FrameBorder.xml');
        
        
    }
    
    
    public function addPhotoMinSky( $image, $id , $element ){
        
        $userid = $image->user_uuid;
        $imagewidth = $image[width];
        $imageheight = $image[height];
        $filename = "{resources}";
        if( $image['placeholder'] ){
            $filename = "{resources}";
        }else{ 
            if( $image['id'] ){
                $filename = sprintf( '{userFiles}minSky\%s_%s.jpg' , $id, $image['id'] );
                //$size = getimagesize(sprintf( "https://t0.cptr.no/th/3/%s?share=%s&area=1280&clip=1&pri=1&auth=&key=KVEz-WDMsj", $image->id, $id  ));
                //$imagewidth = $size[0];
                //$imageheight = $size[1];
            }
            
        }
         
        if( $imagewidth >= $imageheight){
            $textElement = $element->landscape->collagetextElement;
            $element = $element->landscape->collagephotoElement;
            
        }else{
            $textElement = $element->portrait->collagetextElement ;
            $element = $element->portrait->collagephotoElement;
           
        }
        
        $x = (int)$element['x'];
        $y = (int)$element['y'];
        $width = (int)$element['width'];
        $height = (int)$element['height'];
        $pinPosition = (string)['pinPosition'];
        
        $placeholderratio = $width / $height;
        $imageratio = $imagewidth/ $imageheight;
        
        if( $placeholderratio >  $imageratio ){
            $ratio = $imageheight / $imagewidth;
            $cropWidth = $width;
            $cropHeight = $width * $ratio;
            $cropY = -( $cropHeight - $height ) / 2;
        }else{
            $ratio = $imagewidth / $imageheight;
            $cropHeight = $height;
            $cropWidth = $height * $ratio;
            $cropX = -( $cropWidth - $width ) / 2;
        }
        
        $photo = $this->addChild('hack:collage:photoElement');
        $photo->addAttribute('x', $x );
        $photo->addAttribute('y', $y );
        $photo->addAttribute('width',  $width );
        $photo->addAttribute('height', $height );
        $photo->addAttribute('cropY', (int)$cropY);
        $photo->addAttribute('cropX', (int)$cropX);
        $photo->addAttribute('cropWidth', (int)$cropWidth );
        $photo->addAttribute('cropHeight', (int)$cropHeight );
        $photo->addAttribute('fileName', $filename);
        
        $photoframe = $photo->addChild('hack:collage:straightBorder');
        $photoframe->addAttribute('color', '#000000');
        $photoframe->addAttribute('size', '20');
        
        $text ="";
                  
                  
        //Util::debug($textElement['text']);
        //Util::debug($image[comments]);
                    
        try{
            if( $image[comments] && $textElement['text'] ){
                foreach( $image[comments] as $textline ){
                    $text .= $textline->{"comment.text"} . "\n";
                }
            }else{
                $text ="{insertYourTextHere}";
            }
        }catch(Exception $e){
            
        }
        
        //Util::Debug($text);
        
        if( $textElement ){
            $this->AddText( $textElement, $text );
        }
        
        /*
        if( $image->comment ){
            
            $textelement = $this->addChild('hack:collage:textElement');
            $textelement->addAttribute('x', $text['x'] );
            $textelement->addAttribute('y', $text['y'] );
            $textelement->addAttribute('width', $text['width'] );
            $textelement->addAttribute('height', $text['height'] );
            $textelement->addAttribute('text', $image->comment );
            $textelement->addAttribute('wordWrap', 'false' );
            $textelement->addAttribute('scaleToFit', 'true' );
            
            $textstyle = $textelement->addChild('hack:collage:textStyle');
            $textstyle->addAttribute('fontFamily', "Linden Hill" );
            $textstyle->addAttribute('fontSize', "20" );
            
            $textbackground = $textstyle->addChild('hack:collage:straightBackground');
            
            $textbackground->addAttribute( "opacity" , "0");
            
            
        }*/
        
        
        
    }
    
}


?>