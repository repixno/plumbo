<?php
/**
 * ***************************************************
 * Script for å flytte mediaclip til produksjon
 *****************************************************/
require_once("class.get.files.php");

$mediaclipfolder = "/mnt/clipproducer2/produksjon/WorkingFolder/";

// Gaver
find_files( $mediaclipfolder . 'gift/', '/13x18_Alu.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/front-cover.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/stainless-steel-travel-mug.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/alukopp-liten.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/alu-kopp-liten.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/alukopp-stor.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/alu-kopp-stor.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/bear-kopp.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/bordbrikke.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/drikkekant-kopp.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/fargeskifte-kopp.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/latte-kopp.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/lattekopp-lokk.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/latte-kopp-lokk.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/love-kopp.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/shotteglass.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/slank-kopp.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/sportsflaske.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/sports flaske.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/storlatte-kopp.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/termokrus.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/termokrus-stort.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/termos-mini.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/termosmini.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/vannflaske-liten-silver.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/vannflaskelitenwhite.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/vannflaske-liten-white.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/vannflaske-stor-silver.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/vannflaske-stor-white.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/vannflaskestorsilver.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/vannflaskelitensilver.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/notebook.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/vannflaskestorwhite.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/matboks.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/matboks-pink.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/matboks-blue.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/3041.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/notebook_mini.jpg$/', 'move_mediaclip');

find_files( $mediaclipfolder .  'gift/', '/0.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder .  'gift/', '/00.jpg$/', 'move_mediaclip'); //la til denne 15.09 for å få inn dørskilt
find_files( $mediaclipfolder .  'gift/', '/01.jpg$/', 'move_mediaclip'); //la til denne 21.09.2016 for å få inn lerret på mediaclip 5.0
find_files( $mediaclipfolder .  'gift/', '/02.jpg$/', 'move_mediaclip'); //la til denne 21.09.2016 for å få inn lerret på mediaclip 5.0
find_files( $mediaclipfolder .  'gift/', '/03.jpg$/', 'move_mediaclip'); //la til denne 21.09.2016 for å få inn lerret på mediaclip 5.0
find_files( $mediaclipfolder .  'gift/', '/04.jpg$/', 'move_mediaclip'); //la til denne 21.09.2016 for å få inn lerret på mediaclip 5.0
find_files( $mediaclipfolder .  'gift/', '/05.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder .  'gift/', '/06.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder .  'gift/', '/07.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder .  'gift/', '/08.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder .  'gift/', '/09.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder .  'gift/', '/10.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder .  'gift/', '/11.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder .  'gift/', '/12.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder .  'gift/', '/13.jpg$/', 'move_mediaclip');
//find_files( $mediaclipfolder .  'desktopcalendar/', '/12.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder .  'postercalendar/', '/poster.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder .  'poster/', '/Poster.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder .  'greetingcard/', '/FrontPage.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder .  'gift/', '/doorsign_15x20_plexi.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder .  'gift/', '/doorsign_20x20_plexi.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder .  'gift/', '/30x45.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder .  'gift/', '/50x75.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder .  'gift/', '/Surface1.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder .  'gift/', '/Surface2.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder .  'gift/', '/Canvas.jpg$/', 'move_mediaclip');//la til denne 21.09.2016 for å få inn lerret på mediaclip 5.0
find_files( $mediaclipfolder .  'gift/', '/3045.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder .  'gift/', '/30x45.jpg$/', 'move_mediaclip');





//Gaver
find_files( $mediaclipfolder .  'gift/', '/mousepad.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/inner.jpg$/', 'move_mediaclip'); //la til denne 21.09.2016 for å få inn lerret på mediaclip 5.0
find_files( $mediaclipfolder . 'gift/', '/outer.jpg$/', 'move_mediaclip'); //la til denne 21.09.2016 for å få inn lerret på mediaclip 5.0 
find_files( $mediaclipfolder . 'gift/', '/collage.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/front.jpg$/', 'move_mediaclip'); //la til denne 15.09 for å få inn dørskilt
find_files( $mediaclipfolder . 'gift/', '/bear-label.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/bear-label-large.jpg$/', 'move_mediaclip');
//Etiketter
find_files( $mediaclipfolder . 'gift/', '/pakkelapp.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/wine-label.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/jam-label.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/jam-label-large.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/juice-label.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/food-label-large.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/food-label.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/glass15x8.jpg$/', 'move_mediaclip');
// Fotobok
find_files( $mediaclipfolder . 'photobook/', '/7113000/', 'move_mediaclip');
find_files( $mediaclipfolder . 'photobook/', '/7116000/', 'move_mediaclip');
find_files( $mediaclipfolder . 'photobook/', '/7115000/', 'move_mediaclip');


//hval
find_files( $mediaclipfolder . 'gift/', '/hval-jpg.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/hval-transparent.png$/', 'move_mediaclip');


//Fotolab
find_files( $mediaclipfolder . 'gift/', '/7737.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/922.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/923.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/924.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/925.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/926.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/927.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/928.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/929.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/930.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/15x20_L.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/15x20_P.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/20x30_L.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/20x30_P.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/30x40_L.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/30x40_P.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/20x30_Alu_L.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/20x30_Alu_P.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/30x40_Alu_L.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/30x40_Alu_P.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/40x50_L.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/40x50_P.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/50x70_L.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/50x70_P.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/70x100_L.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/70x100_P.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/40x50_Alu_L.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/40x50_Alu_P.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/50x70_Alu_L.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/50x70_Alu_P.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/70x100_Alu_L.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/70x100_Alu_P.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/30x60_Ledpanel_P.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/30x60_Ledpanel_L.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/60x60_Ledpanel.jpg$/', 'move_mediaclip');
// 2 cm lerret
find_files( $mediaclipfolder . 'gift/', '/canvas-split-Org Bilde$/', 'move_mediaclip');

find_files( $mediaclipfolder . 'gift/', '/canvas-20x20-landscape.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-20x20-portrait.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-20x30-landscape.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-20x30-portrait.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-30x30-landscape.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-30x30-portrait.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-30x40-landscape.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-30x40-portrait.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-20x30-landscape.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-30x50-portrait.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-30x50-landscape.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-40x40-portrait.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-40x40-landscape.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-40x50-portrait.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-40x50-landscape.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-40x90-portrait.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-40x90-landscape.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-50x50-portrait.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-50x50-landscape.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-50x70-portrait.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-50x70-landscape.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-60x90-portrait.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-60x90-landscape.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-20x20-square.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-30x30-square.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-40x40-square.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-50x50-square.jpg$/', 'move_mediaclip');
//lerret 4 cm
find_files( $mediaclipfolder . 'gift/', '/canvas-20x20-landscape_4cm.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-20x20-portrait_4cm.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-20x30-landscape_4cm.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-20x30-portrait_4cm.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-30x30-landscape_4cm.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-30x30-portrait_4cm.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-30x40-landscape_4cm.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-30x40-portrait_4cm.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-20x30-landscape_4cm.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-30x50-portrait_4cm.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-30x50-landscape_4cm.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-40x40-portrait_4cm.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-40x40-landscape_4cm.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-40x50-portrait_4cm.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-40x50-landscape_4cm.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-40x90-portrait_4cm.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-40x90-landscape_4cm.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-50x50-portrait_4cm.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-50x50-landscape_4cm.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-50x70-portrait_4cm.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-50x70-landscape_4cm.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-60x90-portrait_4cm.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-60x90-landscape_4cm.jpg$/', 'move_mediaclip');
//Store storformat
find_files( $mediaclipfolder . 'gift/', '/canvas-70x100-portrait_4cm.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-70x100-landscape_4cm.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-100x100-portrait_4cm.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-100x100-landscape_4cm.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-100x120-portrait_4cm.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-100x120-landscape_4cm.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-100x140-portrait_4cm.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-100x140-landscape_4cm.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-100x160-portrait_4cm.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-100x160-landscape_4cm.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-100x180-portrait_4cm.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-100x180-landscape_4cm.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-100x200-portrait_4cm.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder . 'gift/', '/canvas-100x200-landscape_4cm.jpg$/', 'move_mediaclip');
// flash kalender
find_files( $mediaclipfolder .  'fullsheetwallmountcalendar/', '/0.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder .  'fullsheetwallmountcalendar/', '/1.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder .  'fullsheetwallmountcalendar/', '/2.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder .  'fullsheetwallmountcalendar/', '/3.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder .  'fullsheetwallmountcalendar/', '/4.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder .  'fullsheetwallmountcalendar/', '/5.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder .  'fullsheetwallmountcalendar/', '/6.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder .  'fullsheetwallmountcalendar/', '/7.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder .  'fullsheetwallmountcalendar/', '/8.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder .  'fullsheetwallmountcalendar/', '/9.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder .  'fullsheetwallmountcalendar/', '/10.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder .  'fullsheetwallmountcalendar/', '/11.jpg$/', 'move_mediaclip');
find_files( $mediaclipfolder .  'fullsheetwallmountcalendar/', '/12.jpg$/', 'move_mediaclip');


find_files('/home/produksjon/preview_mediaclip/', '/done.txt$/', 'mediaclip_createC8');

function find_files($path, $pattern, $callback) {
  $path = rtrim(str_replace("\\", "/", $path), '/') . '/';
  $matches = Array();
  $entries = Array();
  $dir = dir($path);
  while (false !== ($entry = $dir->read())) {
    $entries[] = $entry;
  }
  $dir->close();
  foreach ($entries as $entry) {
    $fullname = $path . $entry;
    /*
    $excludedate =  date('Ymd',strtotime('-2 day'));
    $filetime = date ("Ymd", filemtime($fullname));
    
    
    
    if( $filetime < $excludedate && is_dir($fullname) && count( explode( '/' , $fullname) ) > 6){
       //file_put_contents( $fullname . "/exclude.txt" , $excludedate);
       echo " excluded" . $fullname . "\n";
    }
    */
    
    if(file_exists($fullname . "/exclude.txt")){
    	//echo $fullname . "/exclude.txt";
    }else if ($entry != '.' && $entry != '..' && is_dir($fullname)) {
      find_files($fullname, $pattern, $callback);
    }else if (is_file($fullname) && preg_match($pattern, $entry)) {
      call_user_func($callback, $fullname);
    }
  }
}

function move_mediaclip( $filename ){

	if(!file_exists(dirname($filename)."/done.txt")){
		$order_data = explode("/", $filename);
		$xml_filename = $order_data[7] . ".xml";
		$arttype = explode("-",basename($order_data[7]));
	 	print_r( $order_data ); 		  	
		$length  = strlen( $arttype[1] );      
      		$artnr = substr( $arttype[1], 0, $length-3 );
	  	
	  	$antall = $arttype[2];
	  	$unique = $arttype[0];
		$production_mappe = valg_size($artnr);		
		$order_dbinfo = get_sql("SELECT 
									ho.ordrenr,
									ho.antall,
									mo.order_time
								FROM historie_ordrelinje AS ho 
								LEFT JOIN mediaclip_orders AS mo 
								ON ho.id=mo.order_row_id 
								WHERE production_id=$unique;");
	  	$orderid = $order_dbinfo[0]["ordrenr"];
	  	$antall = $order_dbinfo[0]["antall"];
	  	
	  	echo "ORDRENR: " . $orderid . "\n";
	  	$dir =  dirname($filename);
		
		#$mcHotfolder = "/home/produksjon/webspool/mediaclip/";
		$mcHotfolder = "/home/produksjon/preview_mediaclip/";
	  	
		$kunde = $arttype[3];					
		$order_date = date("Y-m-d", strtotime($order_dbinfo[0]["order_time"]));

		$ext = array("jpeg","jpg","tif","tiff");

		if(!file_exists($mcHotfolder . $order_date)){
			mkdir($mcHotfolder . $order_date);
		#	echo "folder created!!!" .$mcHotfolder . $order_date . "\n";
		}
	  	
	  	$destionationDir = $mcHotfolder . $order_date . "/" .$production_mappe;
	  	
		$destionation = $destionationDir . "/" . basename($filename);
		if(!file_exists($destionationDir)){
			mkdir($destionationDir);
		#	echo "folder created!!!" .$destionationDir . "\n";
		}
		
		if(!file_exists($destionationDir . "/" . $orderid)){
			mkdir($destionationDir . "/" . $orderid );
		}
		
		if(stristr( $production_mappe, 'Kalender' ) || stristr( $production_mappe, 'Fotohefte' )){
		 if(!file_exists($destionationDir . "/" . $orderid . "/" . $unique )){
		    mkdir($destionationDir . "/" . $orderid . "/" . $unique);
		  }
		  $orderid = $orderid . "/" . $unique;
		  
		}
		
		print_r($filename);
		
		 $files = new getfiles(dirname($filename),$ext);
	    $list = $files->getlist();
	    if(is_array($list)) sort($list);
	    sort($list, SORT_NUMERIC );
	   
	    $antall_bilder = count( $list );
	    $fotohefte_bildenr=0;
	    $imagenr = 0;
	    
	    foreach ($list as $image){
	      $imagenr++;
	    	$file = dirname($filename) .  "/" . $image;
	    	$file_name = basename($image, ".jpg"); 
	    	
	    	$basename = preg_replace("/[^0-9]/","", $file_name);
	    	
	    	if( $file_name == 'frontpage' ){
	    	   $basename = 0;
	    	}
	    	else if ( $file_name == 'lastpage' ){
	    	   $basename = 25;
	    	}
	    	

	    	$newfile = sprintf("%s/%s/%d-%03d-%03d-%d.jpg",$destionationDir,$orderid,$unique,$basename,$antall,$artnr);
			//echo "file: " . $file . " newfile: " . $newfile . "\n";
			
			$calendar = array( 908, 909, 7119, 911, 910 , 7244, 8000,8001,8002,8003,8004,8005,8006 );
			$fotohefte = array( 7113, 7116, 7115 );
			
			if( in_array( $artnr, $fotohefte ) ){
			   
			   if( $basename & 1){
			      $fotohefte_bildenr++;
			      if( $antall > 1 ){  
			         for ( $i = 1; $i <= $antall; $i++ ) {
			            $newfile = sprintf( "%s/%s/%d_%d-%03d-%03d-%d.jpg",$destionationDir,$orderid,$i,$unique,$fotohefte_bildenr,1,$artnr );
			            if (!copy($file, $newfile)) {
       			        echo "failed to copy $file...\n";
   			         }
			         } 
			      }
			      else{
			         if (!copy($file, $newfile)) {
       			     echo "failed to copy $file...\n";
   			      }
			      } 

			   }
			   else if ($antall_bilder == $imagenr){
			      if( $antall > 1 ){  
			         $fotohefte_bildenr++;
			         for ( $i = 1; $i <= $antall; $i++ ) {
			            $newfile = sprintf( "%s/%s/%d_%d-%03d-%03d-%d.jpg",$destionationDir,$orderid,$i,$unique,$fotohefte_bildenr,1,$artnr );
			            if (!copy($file, $newfile)) {
       			        echo "failed to copy $file...\n";
   			         }
			         }  
			      }
			      else{
			         exec("rm " . $file);
			      }
			   }
			   else{
			      exec("rm " . $file);
			   }
			}
			
			
			else if( in_array( $artnr, $calendar ) ){
			  $calendar_bildenr++;
		      if( $antall > 1 ){  
		         for ( $i = 1; $i <= $antall; $i++ ) {
		            $newfile = sprintf( "%s/%s/%d_%d-%03d-%03d-%d.jpg",$destionationDir,$orderid,$i,$unique,$calendar_bildenr,1,$artnr );
		            if (!copy($file, $newfile)) {
    			        echo "failed to copy $file...\n";
			         }
		         } 
		      }
		      else{
		         if (!copy($file, $newfile)) {
    			     echo "failed to copy $file...\n";
			      }
		      } 
			}
			else{
   			if (!copy($file, $newfile)) {
       			echo "failed to copy $file...\n";
   			}
   			
			}
			
	    }
	    
	    echo "\n\n****************  " . $production_mappe . " ****************\n\n";
	   	if(!strpos($production_mappe,"lab")){
		  	$autoedit = used_images($unique);
		    $autoedit_file = $destionationDir . "/" . $orderid . "/autoedit/";
		    if(!file_exists($autoedit_file)){
				mkdir($autoedit_file);
			}
			if(!copy("/mnt/clipproducer2/mediaclip/complete/" .  $xml_filename , $autoedit_file . $xml_filename)){
				echo "failed to copy /home/produksjon/mediaclip/complete/" .  $xml_filename . "...\n";
			}
			
			//moves the original images used in the project
		  /*  foreach ($autoedit as $autoeditimage){
		    	$file = "/mnt/clipproducer2/mediaclip/ready/" . $kunde . "/" . $autoeditimage['bid'] . ".jpg";
		    	$newfile = $autoedit_file . $autoeditimage['bid'] . ".jpeg";
		    	if (!copy($file, $newfile)){
	    			echo "failed to copy $file...\n";
				}
		    }*/
	    }
	    //create and moves a triggerfil
	  	$output = $unique;
		$donefile= dirname($filename)."/done.txt";
		$file = fopen ($donefile, "w");
		fwrite($file, $output);
		fclose ($file);
	   $newfile = sprintf("%s/%s/done.txt",$destionationDir,$orderid);
			
		if (!copy($donefile, $newfile)) {
    		echo "failed to copy $file...\n";
		}
		
		$endfile = sprintf( "%s/%s/End.txt", $destionationDir, $orderid );
		
		if( file_exists( $endfile )){
		   echo "REMOVING END FILE!!!!!!!!!!!!!!!!\n";
		   exec( "rm $endfile");
		}
		
		
		
	 }
	
}

function mediaclip_createC8($filename){
	if(!file_exists(dirname($filename)."/End.txt")){
		
		$device = 'MyDevice';
		$folder =  dirname( $filename ) .	"/" ;
		$condition	= $folder."Condition.txt";
		$endtxt	= $folder."End.txt";
    	$colorspace = "sRGB";
    	$resize = 'FILLIN';
    	$valg_size = art_array();
    	
    	
		$ext = array("jpeg","jpg","tif","tiff");	
		$files = new getfiles( $folder, $ext, false );
		$list = $files->getlist();
		
		//sortera bilda etter filnavn
		if(is_array($list)) rsort($list);
		
		$antall_bilder = count($list);
		$imagelist_count  = 0;
		$imagecount = $antall_bilder;
		foreach ($list as $image){
    		echo "bilde: " . $image . "\n";	
    		$pathinfo = pathinfo($image);
    		$imagelist_count++;
    		$image_list .= $imagelist_count."=".$image."\n";
    		
    		$image_info = explode("-", $pathinfo['filename']);
    		//$antall = ltrim($image_info[1], "0");
    		$antall = (int)$image_info[2];
    		$artnr = $image_info[3];
    		$unique = $image_info[0];
    		
    		$fotofolder_unique = explode('_', $unique );
    		
    		if( $fotofolder_unique[1] > 0 ){
    		   $unique = $fotofolder_unique[1];
    		}
    		
    		if( is_numeric( $unique ) ){
       		$query = get_sql( sprintf( "SELECT * FROM mediaclip_orders WHERE production_id = %d", $unique ) );
       	   $ordrenr = $query[0]['order_id'];
    		}
    		$image_print_count = $image_info[1];
    		echo "artnr: " . $artnr . "\n";	
    		
    		$sizeName = $valg_size[$artnr];
    		 
    		if( $artnr == 7241 && $image_print_count == 25 ){
    		   $sizeName = '30X40';
    		}
    		
			$image_print_info .="[".$image."]
	SizeName=".$sizeName."
	PrintCnt=".$antall."
	BackPrint=FREE
	BackPrintLine1=" ."Pnr:" . $unique . " Onr:" . $ordrenr . " " .$image_print_count . "av" .	$antall_bilder . "
	BackPrintLine2=www.repix.no" ."
	Resize=" . $resize ."
	DSC_Chk=FALSE
	Color_Space=".$colorspace."\n\n";
			$imagecount--;
   		}
	
    	//start devicename
    	
		$fp = fopen($condition,"w");
    	fwrite($fp,
    		"[OutDevice]\nDeviceName=".$device . 
    		"\n\n[ImageList]\n" .	
    		sprintf("ImageCnt=%s", count($list)) ."\n".	
    		$image_list . "\n" .
    		$image_print_info);
   		fclose($fp);
   		
   		
		$fe = fopen($endtxt,"w");
    	fwrite($fe,"");
     	fclose($fe);
	}
	
}

function art_array(){
		//$valg_art_array = array(
          $num = array(  
            
						876 => '20X30',
						908 => '20X30', #Kalender 20x30
						909 => '30X30', #Kalender 30x30
						910 => '30X40', #Kalender 30x40
						911 => '20X15', #Bordkalender 15x20
						912 => '30X45',  #Poster kalender 30x40
						913 => '50X75',  #Poster Kalender 50x70
						922 => '15X20',  #Fotopapir, 15x20cm
						927 => '70X100', #70x100cm Semigloss Epson
						929 => '40X50L',
						925 => '40X50',
						926 => '50X70',
						930 => '50X70L',
						928 => '30X40L',
                        
                        8000 => '30X45', #Kalender 30X45
                        8001 => '2035', #Kalender 20X30
                        8002 => '30X30', #Kalender 30X30
                        8003 => '20X20', #Kalender 20X20
                        8004 => '20X15',
                        8005 => '10X22',
                        8006 => '30X14',
						
						933 => '30X25',
						932 => '20X25',
						924 => '30X40',
						931 => '70X100L',
						923 => '20X30',
						940 => '15X15',
						939 => '10X18',
						968 => 'gave',
						969 => 'gave',
						970 => 'gave',
						971 => 'gave',
                        3039 => 'gave',
                        3040 => 'gave',
                        7732 => 'gave',
                        7733 => 'gave',
                        7734 => 'gave',
                        7735 => 'gave',
                        7736 => 'gave',
                        7737 => 'gave',
                        7738 => 'gave',
                        3040 => 'gave',
						980 => '20X20LR',
						981 => '20X30LR',
						982 => '30X30LR',
						983 => '30X40LR',
						984 => '30x50LR',
						985 => '40x40LR',
						986 => '40X50LR',
						7004 => '40x50LR',
						7006 => '20X30',
						7005 => '40X50',
						7024 => '15X20',
						7025 => '20X30',
						7026 => '30X40',
						7030 => '30X40',
						7028 => '15X20',
						7029 => '20X30',
						7035 => '20X20LR2', #####################################
						7031 => '20X30LR2', #
						7036 => '30X30LR2', #
						7032 => '30X40LR2', #    LERRET med 2cm ramme
						7033 => '30x50LR2', #
						7037 => '40x40LR2', #
						7034 => '40X50LR2', ####################################
						7113 => '102C',
						7115 => '10X18',
						7116 => '20X15', 
						7128 => '10X18',
						7230 => '2035',
						7237 => '102C',
						7238 => '10X20',
						7241 => '30X30',
						7244 => '20X20',
                        7296 => 'Notebook',
                        7295 => 'Notatblokk',
                        7297 => 'Notatblokk',
						8070 => 'Dørskilt',
						8071 => 'Dørskilt',
						8072 => 'Dørskilt',
						8073 => 'Dørskilt',
						8074 => 'Dørskilt',
						8075 => 'Dørskilt',
						8076 => 'Dørskilt',
						8077 => 'Dørskilt',
						8078 => 'Dørskilt',
                        7456 => 'Tonerose',
                        1041 => 'Tonerose',
                        1044 => 'Tonerose',
                        1047 => 'Tonerose',
                        1043 => 'Tonerose',
                        1049 => 'Tonerose',
                        1048 => 'Tonerose',
                        1026 => 'Tonerose',
                        1025 => 'Tonerose',
                        1046 => 'Tonerose',
                        3100 => 'Splitcanvas',
                        3101 => 'Splitcanvas',
                        3102 => 'Splitcanvas',
                        3103 => 'Splitcanvas',
                        3104 => 'Splitcanvas',
                        3105 => 'Splitcanvas',
                        3106 => 'Splitcanvas',
                        3107 => 'Splitcanvas',
                        3108 => 'Splitcanvas',
                        3109 => 'Splitcanvas',
                        
                      
                 

						
						
					);
		
		return $num;
}

function valg_size($artnr){
	
   $valg_size = art_array();
   
   switch ($valg_size[$artnr]) {
      case "10X18":
      case "10X15":
      case "102C":
      case "10X20":
         $production_mappe = "Photolab_10_CM";
				 $production_mappe = "C8_Photolab_10_CM";
      break;
      case "15X15":
      case "15X20":
         $production_mappe = "Photolab_15_CM";
      break;	
      case "20X30":
      case "20X15":
      case "20X25":
         $production_mappe = "Photolab_20_CM";
      break;
      case "30X30":
      case "30X25":
      case "30X40":
		
		
         $production_mappe = "Photolab_30_CM";
      break;
      case "50X70":
	case "50X70L":
      case "40X50":
      case "70X100":
		
		
		
         $production_mappe = "Ledpanel";
      break;
	  case "30x60_Ledpanel_P":
	  case "30x60_Ledpanel_L":
	  case "60x60_Ledpanel":	
			
			
         $production_mappe = "Plotter_Mediaclip_Lerret_Ramme_4cm";
      break;
      case "20X20LR2":
      case "20X30LR2":
      case "30X30LR2":
      case "30X40LR2":
      case "30x50LR2":
      case "40x40LR2":
      case "40X50LR2":
        
        

			
			
      case "gave":
      	$production_mappe = "Gave";
      	break;

      case "Dørskilt":
      	$production_mappe = "Dørskilt";
      	break;
      default:
      	$production_mappe = "DEFAULT";
      	}

      	if ($artnr == 911 || $artnr == 955 || $artnr == 7244) {
      		$production_mappe = "Photolab_20X15_CM_Kalender";

      	} else if ($artnr == 908 || $artnr == 7244 || $artnr == 876 || $artnr == 8001 || $artnr == 8003 ) {
      		$production_mappe = "Photolab_20X30_CM_Kalender";
			
      	}
        
     
        
		
		else if ($artnr == 909 || $artnr == 910 || $artnr == 7241 || $artnr == 8000 || $artnr == 8002) {
			$production_mappe = "Photolab_30_CM_Kalender";
		} else if ($artnr == 7116) {
			$production_mappe = "Fotohefte_20cm";
		} else if ($artnr == 7115) {
			$production_mappe = "Fotohefte_10cm";
		} else if ($artnr == 928 || $artnr == 929 || $artnr == 930 || $artnr == 931) {
			$production_mappe = "Canvas Uten Ramme";
		
		
		} else if ($artnr == 7509 || $artnr == 7512 || $artnr == 7528 || $artnr == 7510 || $artnr == 7511 || $artnr == 7515 || $artnr == 3022 || $artnr == 3023 || $artnr == 3024 || $artnr == 3025 || $artnr == 3026 || $artnr == 3028 || $artnr == 3029) {
			$production_mappe = "Etiketter";
			
            
			
		} else if ($artnr == 3039 || $artnr == 3040 || $artnr == 7732 ||$artnr == 7733 ||$artnr == 7734 ||$artnr == 7735  ||$artnr == 7737 || $artnr == 7293 || $artnr == 7294|| $artnr == 7511 || $artnr == 7528 || $artnr == 7510 || $artnr == 7296 || $artnr == 8007 || $artnr == 3007 || $artnr == 3000 || $artnr == 3001 || $artnr == 3002 || $artnr == 3003 || $artnr == 3004 || $artnr == 3005  ||  $artnr == 3006 || $artnr == 3007 || $artnr == 3008 || $artnr == 3009 || $artnr == 3010 || $artnr == 3011 || $artnr == 3012 || $artnr == 3013 || $artnr == 3014 || $artnr == 3015 || $artnr == 3016 || $artnr == 3017 || $artnr == 3018 || $artnr == 3019 || $artnr == 3020) {
			$production_mappe = "Gave";
			
			
		
		
        } else if (  $artnr == 3100 || $artnr == 3101 || $artnr == 3102 || $artnr == 3103 || $artnr == 3104 || $artnr == 3105 || $artnr == 3106 || $artnr == 3107 || $artnr == 3108 || $artnr == 3109) { 
			$production_mappe = "Splitcanvas";
			
        
        
        
        
			
		
			} else if ($artnr == 7035 || $artnr == 7031 || $artnr == 7036 || $artnr == 7032 || $artnr == 7033 || $artnr == 7037 || $artnr == 7034 || $artnr == 7503 || $artnr == 7034 || $artnr == 7493 || $artnr == 7492 || $artnr == 7502 || $artnr == 3101 || $artnr == 3102 || $artnr == 3103 || $artnr == 3104 || $artnr == 3105 || $artnr == 3106 || $artnr == 3107 || $artnr == 3108 || $artnr == 3109) {
			$production_mappe = "Lerret_Ramme_2cm";
			
			
			} else if ($artnr == 980 || $artnr == 981 || $artnr == 982 || $artnr == 983 || $artnr == 984 || $artnr == 985 || $artnr == 986 || $artnr == 7501 || $artnr == 7489 || $artnr == 7490 || $artnr == 7500 || $artnr == 7488 || $artnr == 7491 || $artnr == 7499 || $artnr == 7495 || $artnr == 7496 || $artnr == 7498 ) {
			$production_mappe = "Lerret_Ramme_4cm";
		
		
		} else if ($artnr == 7402 || $artnr == 7403 || $artnr == 7404 || $artnr == 7405 || $artnr == 7406 || $artnr == 7407 || $artnr == 7409) {
			$production_mappe = "Chromaluxe";
            
            } else if ($artnr == 7456 ||$artnr == 1044 || $artnr == 1046 || $artnr == 1047 || $artnr == 1041 || $artnr == 1048 || $artnr == 1025 || $artnr == 1026 || $artnr == 1043 || $artnr == 1049) {
			$production_mappe = "Tonerose";
			
			
		} else if ($artnr == 922) {
			$production_mappe = "Photolab_15_CM";
            
            
             } else if ($artnr == 8004) {
			$production_mappe = "Bordkalender 20_CM";
			
			
			} else if ($artnr == 8005) {
			$production_mappe = "Bordkalender 10_CM";
            
            
            } else if ($artnr == 8006) {
			$production_mappe = "Bordkalender 30_CM";
            
            
		}else if ($artnr == 923 || $artnr == 7230) {
			$production_mappe = "Photolab_20_CM";
            
            
		} else if ($artnr == 924 || $artnr == 912) {
			$production_mappe = "Photolab_30_CM";
			
			} else if ($artnr == 7524 || $artnr == 7525 || $artnr == 7526 || $artnr == 7527  ) {
			$production_mappe = "Ledpanel";
			
				} else if ($artnr == 7520 || $artnr == 7521 || $artnr == 7522 || $artnr == 7523) {
			$production_mappe = "Metallic Papir";
			
            
            } else if ($artnr == 7296 || $artnr == 7295 || $artnr == 7297) {
			$production_mappe = "Notatblokk ";
			
			
			
			
			
		} else if ($artnr == 925 || $artnr == 926 || $artnr == 927 || $artnr == 913) {
			$production_mappe = "Mediaclip Poster";
		}
		
		
		
		
			
		
		
		
		
		else if ($artnr == 7113) {
      		$production_mappe = "Fotohefte_10cm";
      	}
      	return $production_mappe;
      	}

      	function get_sql($query) {
      		$connection = pg_connect("host=sidsel.eurofoto.no dbname=eurofoto user=www")
      		or die("Ups PostGres --> ".pg_last_error($conn));

      		$result = pg_query($query);

      		$rows = pg_fetch_all($result);

      		pg_close($connection);

      		return $rows;
      	}

function used_images($orderid){
if($orderid > 0){

		$queryString = sprintf("
      	SELECT
         	project_xml
      	FROM
         	mediaclip_orders
      	WHERE
         	production_id = %d;
   		", $orderid);
			
		$xml_list = get_sql( $queryString );
      	foreach( $xml_list as $image ) {
         $origProjectXML = $image["project_xml"];
     
         if(strpos($origProjectXML,"collage:photoElement") > 1 || strpos($origProjectXML,"collage:backgroundElement") > 1 || strpos($origProjectXML,"model:photo") > 1){
	         $tmpXML = ereg_replace("[\n]", "", $origProjectXML);
			 $tmpXML = ereg_replace("\t\t+", "", $tmpXML);
	         // Hack to fix non-standard namespace definitions from Mediaclip.
	         $tmpXML = preg_replace( "/(xmlns(?::\w+?)*\=\")(.+?)(\")/", '${1}http://www.mediaclip.com/${2}${3}', $tmpXML );
	         $tmpXML = utf8_encode( $tmpXML );
	         $userid = $image["user_id"]."-".$image["id"];
	          
			  
	
			
					     
	         $xmlData = simplexml_load_string( $tmpXML );
	         	       
	         // Find all paths to photos and backgrounds.
	        $result = array();
	        $imageIds = array();
	        $image_test = array();
	       	$collect_nodes = array('//collage:photoElement', '//collage:backgroundElement');
         	if(strpos($origProjectXML,"calendar:photo")!==false){
         		array_push($collect_nodes, '//calendar:photo');
         	}
         	if(strpos($origProjectXML,"model:photo")!==false){
         		array_push($collect_nodes, '//model:photo');
         	}
         	foreach($collect_nodes as $node){
   	  			$result = array_merge($result, $xmlData->xpath($node));
   			}	
	            // Process paths if any was found.
			if( count( $result ) > 0 ) {
				// Loop through all paths.
				foreach( $result as $imagepath ) {
					// Filter out library files.
					if($imagepath['fileName']){
						$resFileName = $imagepath->attributes()->fileName;
					}else $resFileName = $imagepath;
					if ( substr( $resFileName, 0, 9 ) != '{library}' ) {
						// Extract file path.
						$imagepatharray = explode( '\\', $resFileName );
						// Get base file name.
						$fullname = end( $imagepatharray );
						
						$owner = $imagepatharray[3];
						//adds image id to array
						$imageId = basename($fullname, ".jpg");
						if($imageId > 0 && !in_array($imageId, $image_test)){
							$image_test[] = $imageId;
							array_push($imageIds, array("bid"=>$imageId));
						}
					}
				}
			}
				
		}
	}
}
	return  $imageIds;	
}
?>