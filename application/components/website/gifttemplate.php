<?PHP
   
   model( 'site.gifttemplate' );
   
   import( 'cache.engine' );
   
   class GiftTemplate extends DBGiftTemplate {
      
      public function asArray() {
         
         if( Dispatcher::getPortal() == "UP-DK" ){
            $this->image_medium = "DK_" . $this->image_medium;
         }
         
         $jpgthumb = sprintf( '/imageresource/1/%d.jpeg', $this->image_medium );
         $gifthumb = sprintf( '/imageresource/1/%d.gif', $this->image_medium );
         
         $filename = sprintf( '%s/data%s', getRootPath(), $jpgthumb );
         if( file_exists( $filename ) ) {
            $thumbnail = $jpgthumb;
         } else {
            $filename = sprintf( '%s/data%s', getRootPath(), $gifthumb );
            if( file_exists( $filename ) ) {
               $thumbnail = $gifthumb;
            } else {
               $thumbnail = '';
            }
         }
         
       /*
        * Denne er endra 09.03. og lagt til nederst  'message' => $this->message,*/
         $pages = DB::query( 'SELECT malpageid, 
                        pagenr, 
                        630 as websize_x,
                        round(CAST((CAST(fullsize_y AS numeric)/CAST(fullsize_x AS numeric))*630 AS numeric)) AS websize_y,
                        fullsize_x, 
                        fullsize_y, 
                        fullsize_pos_x, 
                        fullsize_pos_y, 
                        fullsize_pos_dx, 
                        fullsize_pos_dy,
                        fullsize_dpi,
                        fullsize_src,
                        edit_mal_x,
                        edit_mal_y,
                        edit_mal_dx,
                        edit_mal_dy,
                        edit_image_x,
                        edit_image_y,
                        edit_image_dx,
                        edit_image_dy,
                        edit_src,
                        bgcolor,
                        small_mal_x,
                        small_mal_y,
                        small_mal_dx,
                        small_mal_dy,
                        small_image_x,
                        small_image_y,
                        small_image_dx,
                        small_image_dy,
                        small_src,
                        preview_src 
                   FROM malpage 
                  WHERE malid = ? ORDER BY pagenr', $this->id )->fetchAll( DB::FETCH_ASSOC );
         
         
          /*
           $pages = DB::query( 'SELECT 
                              malpageid, 
                              pagenr, 
                              630 as websize_x,
                              round(CAST((CAST(fullsize_y AS numeric)/CAST(fullsize_x AS numeric))*630 AS numeric)) AS websize_y,
                              fullsize_x, 
                              fullsize_y, 
                              fullsize_pos_x, 
                              fullsize_pos_y, 
                              fullsize_pos_dx, 
                              fullsize_pos_dy,
                              fullsize_dpi,
                              fullsize_src,
                              edit_mal_x,
                              edit_mal_y,
                              edit_mal_dx,
                              edit_mal_dy,
                              edit_image_x,
                              edit_image_y,
                              edit_image_dx,
                              edit_image_dy,
                              edit_src,
                              bgcolor,
                              small_mal_x,
                              small_mal_y,
                              small_mal_dx,
                              small_mal_dy,
                              small_image_x,
                              small_image_y,
                              small_image_dx,
                              small_image_dy,
                              small_src,
                              preview_src,
                              message
                              FROM malpage mp
                              INNER JOIN mal m ON m.malid = mp.malid
                              INNER JOIN language_resource lr ON lr.lang_res_id=m.name
                              WHERE mp.malid = ? ORDER BY mp.pagenr', $this->id )->fetchAll( DB::FETCH_ASSOC );*/

           
           

          
         
         $returnarray = array();
         
         foreach( $pages as $page ){
            if( Dispatcher::getPortal() == "UP-DK" ){
               $page['fullsize_src'] = 'DK_' . $page['fullsize_src'];
            }elseif( Dispatcher::getPortal() == "VP-001"  ){
               $page['fullsize_src'] = 'SV_' . $page['fullsize_src'];
            }
            $returnarray[] = $page;
         }
         
         return array(
            'id' => $this->id,
            'title' => $this->name,
            'description' => $this->description,
            'message' => $this->message,
            'images' => array(
               'medium' => $thumbnail,
            ),
            'pages' => $returnarray
         );
         
      }
      
   }
   
?>