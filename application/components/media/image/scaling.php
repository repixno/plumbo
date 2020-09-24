<?php
   
   class ImageScaling {
      
      public $newleft = 0;
      public $newtop = 0;
      public $srcleft = 0;
      public $srctop = 0;
      public $newwidth = 0;
      public $newheight = 0;
      public $srcwidth = 0;
      public $srcheight = 0;
      public $imgwidth = 0;
      public $imgheight = 0;
      
      public $width = 0;
      public $height = 0;
      public $size = 0;
      
      public function __construct( $height, $width, $size ) {
         
         $this->width = $width;
         $this->height = $height;
         $this->size = $size;
         
      }
      
      public function scaleSquare() {
         
         // limit the maximum scale to size of image 
         if( $this->width < $this->size ) {
            $this->size = $this->width;
         }
         
         if( $this->height < $this->size ) {
            $this->size = $this->height;
         }
         
         // define new coordinates
         $this->newtop = 0;
         $this->newleft = 0;
         $this->newwidth = $this->size;
         $this->newheight = $this->size;
         
         if( $this->width > $this->height ) {
            
            $this->srctop = 0;
            $this->srcleft = ($this->width / 2)-($this->height / 2);
            $this->srcwidth = $this->height;
            $this->srcheight = $this->height;
            
         } else {
            
            $this->srctop = ($this->height / 2)-($this->width / 2);
            $this->srcleft = 0;
            $this->srcwidth = $this->width;
            $this->srcheight = $this->width;
            
         }
         
         $this->imgwidth = $this->newwidth;
         $this->imgheight = $this->newheight;
                     
      }
      
      public function scaleAspect() {
         
         // define new coordinates
         $this->srctop = 0;
         $this->srcleft = 0;
         $this->srcwidth = $this->width;
         $this->srcheight = $this->height;
         
         if( $this->width > $this->height ) {
            
            $this->newwidth = $this->size;
            $this->newheight = ( $this->height / $this->width ) * $this->size;
            
         } else {
         
            $this->newheight = $this->size;
            $this->newwidth = ( $this->width / $this->height ) * $this->size;
            
         }
         
         $this->newtop = 0;
         $this->newleft = 0;
         $this->imgwidth = $this->newwidth;
         $this->imgheight = $this->newheight;
         
      }
      
      public function scaleSquareAspect() {
         
         // define new coordinates
         $this->imgwidth = $this->size;
         $this->imgheight = $this->size;
         
         $this->srctop = 0;
         $this->srcleft = 0;
         $this->srcwidth = $this->width;
         $this->srcheight = $this->height;
         
         if( $this->width > $this->height ) {
         
            $this->newwidth = $this->size;
            $this->newheight = ( $this->height / $this->width ) * $this->size;
         
         } else {
         
            $this->newheight = $this->size;
            $this->newwidth = ( $this->width / $this->height ) * $this->size;
         
         }
         
         $this->newtop = ($this->imgheight / 2) - ($this->newheight / 2);
         $this->newleft = ($this->imgwidth / 2) - ($this->newwidth / 2);

      }
      
      public function scaleBox() {
         
         list( $boxwidth, $boxheight ) = explode( 'x', $this->size );
         
         // define new coordinates
         $this->srctop = 0;
         $this->srcleft = 0;
         $this->srcwidth = $this->width;
         $this->srcheight = $this->height;
         
         if( $this->width > $this->height ) {
            
            $this->newwidth = $boxwidth;
            $this->newheight = ( $this->height / $this->width ) * $boxwidth;
            
            if( $this->newheight > $boxheight ) {
               
               $this->newheight = $boxheight;
               $this->newwidth = ( $this->width / $this->height ) * $boxheight;
               
            }
            
         } else {
            
            $this->newheight = $boxheight;
            $this->newwidth = ( $this->width / $this->height ) * $boxheight;
            
            if( $this->newwidth > $boxwidth ) {
               
               $this->newwidth = $boxwidth;
               $this->newheight = ( $this->height / $this->width ) * $boxwidth;
               
            }
            
         }
         
         $this->newtop = 0;
         $this->newleft = 0;
         $this->imgwidth = $this->newwidth;
         $this->imgheight = $this->newheight;
         
      }
      
      public function scaleWidth() {
         
         // limit the maximum scale to size of image 
         if( $this->width < $this->size ) {
            $this->size = $this->width;
         }
         
         // define new coordinates
         $this->newtop = 0;
         $this->newleft = 0;
         $this->newwidth = $this->size;
         $this->newheight = round( $this->size * ( $this->height / $this->width ) );
         
         $this->srctop = 0;
         $this->srcleft = 0;
         $this->srcwidth = $this->width;
         $this->srcheight = $this->height;
         
         $this->imgwidth = $this->newwidth;
         $this->imgheight = $this->newheight;
         
      }
      
      public function scaleHeight() {
         
         // limit the maximum scale to size of image 
         if( $this->height < $this->size ) {
            $this->size = $this->height;
         }
         
         // define new coordinates
         $this->newtop = 0;
         $this->newleft = 0;
         $this->newwidth = round( $this->size * ( $this->width / $this->height ) );
         $this->newheight = $this->size;
         
         $this->srctop = 0;
         $this->srcleft = 0;
         $this->srcwidth = $this->width;
         $this->srcheight = $this->height;
         
         $this->imgwidth = $this->newwidth;
         $this->imgheight = $this->newheight;
         
      }
      
      public function scale( $image ) {
         
         // create the output image
         $target = imagecreatetruecolor( $this->imgwidth, $this->imgheight );
         
         $white = imagecolorallocate( $target, 0xFF, 0xFF, 0xFF );
         imagefilledrectangle( $target, 0, 0, $this->imgwidth, $this->imgheight, $white );
         
         // set the alphablending bit
         imagealphablending( $target, $this->alphascaling );
         
         // resample the image to the target
         imagecopyresampled( $target, 
                             $image, 
                             $this->newleft, 
                             $this->newtop, 
                             $this->srcleft, 
                             $this->srctop, 
                             $this->newwidth, 
                             $this->newheight, 
                             $this->srcwidth, 
                             $this->srcheight );
         
         // make sure to save alpha inscaleion
         imagesavealpha( $target, true );
         
         return $target;
         
      }
   }
?>