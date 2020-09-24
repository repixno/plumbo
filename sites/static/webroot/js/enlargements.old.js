function require(jspath) {
    document.write('<script type="text/javascript" src="'+jspath+'"><\/script>');
}

function loginDialog() {
	$('#loginDialog').dialog({
		'modal'		 :	true,
		'width'		 : 	500,
		'height'	    :	320,
		'buttons'    : {
			'Login'	 :	function() {
				$('#loginDialog form').submit();
			}
		}
	});
	$('#loginDialog').dialog('open');
	return false;
}

function uploadOneImageDialog() {
	$('#uploadOneImageDialog').dialog({
		'modal'		:	true
	});
	return false;
};

function imagePicker() {

	if( imagePickerLoaded ) {
		$('#imagePicker').dialog('open');
	} else {
		$('#imagePicker').dialog({
			'width' 	: 	950, //span-18
			'height'	:	570,
			'position' : { my: 'top', at: 'top',of: $('#jau') },
			'modal'		:	true,
			'resizable': false,
			'draggable': false, 
			'open'		: 	function() {
				//load albums
				$('#imagePicker .albums').append( loader );
				var response = $.ef( 'albums.enum_new' );
				if( response.result && !imagePickerLoaded ) {
					imagePickerLoaded = true;
					$('#imagePicker .loader').remove();
					$(response.albums).each( function( i, album ) {
						$('#imagePicker ul.albums').append( 
							$('<li/>').append( 
								$('<a/>', {
									'href'	:	'#',
									'text'	:	shorten(album.title,22),
									'title'	:	album.title,
									'click'	:	function() {
										$('#imagePicker ul.albums li a.selected').removeClass('selected');
										$(this).addClass('selected');
										loadAlbum( $(this), album, '#imagePickerImages' );
									}
								})
							)
						)
					
   					if(i == 0 ){
   					   $(this).addClass('selected');
   					   loadAlbum( $(this), album, '#imagePickerImages' );
   					}
					}
					
					)
				}
			}
		});
	}

	return false;

}

function loadImage(){

      
	var newimage = new Image();
	newimage.src = $('#cropbox').attr('src');
	
	var x1 = $('#x1').attr('value');
	var y1 =  $('#y1').attr('value');
	var bid = $('#bid').attr('value');
	
	var imageinfo = {'x':x1, 'y':y1, 'id':bid};
	
   currentImage = imageinfo;	
	$(newimage).load( function() {
		previewHeight = newimage.height;
      previewWidth = newimage.width;
      
      if(previewHeight > previewWidth){ 
         $('#portrait').click();
      }
      else{
         $('#landscape').click();
      }
      updateOnChange($('#printtype').attr('value'));    
	});
	$('#add-to-cart').removeClass('disabled');
	$('.startup').hide();
	
}


function pickImage( image ) {
	$('#imagePicker').dialog('close');
	var newimage = new Image();
	newimage.src = image.screensize;
	$(newimage).load( function() {
		$('#cropbox, #previewimage').attr('src', image.screensize );
		currentImage = image;
		previewHeight = newimage.height;
      previewWidth = newimage.width;
      
      if(previewHeight > previewWidth){ 
         $('#portrait').click();
      }
      else{
         $('#landscape').click();
      }
      updateOnChange($('#printtype').attr('value'));    
	});
	$('#add-to-cart').removeClass('disabled');
	$('.startup').hide();
	
}
	

function loadAlbum(element, album, target) {
	$(target).append( loader );
	var selectedImage = false;
	$.ef('album.images.enum', 
		{
			albumid: album.id
			
		}, function(result) {
		
			$(target).empty();

			$(result.images).each( function(i, image) {
				$(target).append( 
					$('<li/>').append(
						$('<a/>', {
							'href'	:	'#',
							'click'	: function() {
								pickImage( image );
								return false;
							}
						})
						.append('<img src="'+image.thumbnail+'" />')
					)
				);
			});
	});
	
	return selectedImage;
	
}

function changeSize( size, id ){
		var selectedStr = size;
      var brokenstring=selectedStr.split('x');
      
		printheight = brokenstring[0];
		printwidth = brokenstring[1];
		
		oriantation = $("input[name='oriantation']:checked").val();
	
	  if( oriantation == "portrait"){
	     var tempwidth =  printwidth;
	     printwidth = printheight;
	     printheight = tempwidth;
	  }
		
		updateCrop(printheight, printwidth);
		updateArtnr();
}
	
function updateOnChange(printtype){
   if(printtype == 2.5){
      var selectedStr = $('#canvas2cm').attr('value'); 	      
   }
   else if (printtype == 4){
      var selectedStr = $('#canvas4cm').attr('value');	
   }
   else if(printtype == "2x2"){
      var selectedStr = $('#gallery2x2').attr('value');
   }
   else if(printtype == "1x3"){
      var selectedStr = $('#gallery1x3').attr('value');
   }
   var brokenstring=selectedStr.split('x');
   printheight = brokenstring[0];
   printwidth = brokenstring[1];
   oriantation = $("input[name='oriantation']:checked").val();
	
	if( oriantation == "portrait" && printtype != "1x3" ){
	  var tempwidth =  printwidth;
	  printwidth = printheight;
	  printheight = tempwidth;
   }
   updateCrop(printheight, printwidth);
   updateArtnr();
}

function change_printsize(currentValue){
   
   $('.select').hide();
	   $('.selectedsize').removeClass('selectedsize'); 
   if(currentValue == 2.5){
      $('.select-2cm').show();
      $('.select-2cm').addClass('selectedsize');
   }
   else if(currentValue == 4){
      $('.select-4cm').show();
      $('.select-4cm').addClass('selectedsize');
   }
   else if(currentValue == '2x2'){
      $('.select-2x2').show();
      $('.select-2x2').addClass('selectedsize');
   }
   else if(currentValue == '1x3'){
      $('.select-1x3').show();
      $('.select-1x3').addClass('selectedsize');
   }
   
   updateOnChange(currentValue);
}
	
function updateCrop(printheight, printwidth){

	printtype = $('#printtype').attr('value');
	
	if(printtype == '2x2' || printtype == '1x3'){
	  framedepth = 4;
	}
	else{
	  framedepth = printtype;
	}
	
	printwidth = parseFloat(printwidth) + (parseFloat(framedepth) * 2);
	printheight = parseFloat(printheight) + (parseFloat(framedepth) * 2);
	
	if( (printwidth - printheight) == 0){
	   $('.oriantationval').hide();
	}
	else if(printtype == '1x3'){
	   $('.oriantationval').hide();
	   $('#landscape').click();
	}
	else{
	   $('.oriantationval').show();
	}
		
	var imgheight = previewHeight;
	var imgwidth = previewWidth;
	var imgratio = imgheight / imgwidth;
	var printratio = printheight / printwidth;
	if( printwidth > printheight ) {
		if(imgratio > printratio){
			var tx2 = imgwidth;
			var ty2 = imgwidth * ( printheight / printwidth);
		}
		else{
			var ty2 = imgheight;
			var tx2 = imgheight / (printheight / printwidth); 
		}
	} else {
	     if(imgratio < printratio ){
		    var ty2 = imgheight ;
		    var tx2 = imgheight / (printheight / printwidth);
	     }
	     else{
	        var tx2 = imgwidth ;
		     var ty2 = imgwidth * (printheight / printwidth);
	     }
	}
	
	var tx1 = (imgwidth - tx2) / 2;
	tx2 = parseInt(tx1) + parseInt(tx2);
	var ty1 = (imgheight - ty2) / 2;
	ty2 = parseInt(ty1) + parseInt(ty2);
	
	var ias = $('img#cropbox').imgAreaSelect({ 
			x1: tx1, 
			y1: ty1,
			x2: tx2,
			y2: ty2,
			instance: true,
			aspectRatio: printwidth + ':' + printheight, 
			handles: true,
			keys: true,
			onInit: updateBorderMove,
			onSelectChange: updateBorderMove
		});
		
		var selection = ias.getSelection();
		if(selection.width > 0){
		   currentSelection = selection;
			updateBorder(selection.width, selection.height);
		}
}



function updateBorderMove(img, selection) {
	updateBorder(selection.width, selection.height);	
	currentSelection = selection;
}

function updateBorder(selectionwidth, selectionheight) {
	
	var widthscale = printwidth;
	var framedepth = 0;
	printtype = $('#printtype').attr('value');
	
	if(printtype == '2x2' || printtype == '1x3'){
	   framedepth = 4;
	}
	else{
	  framedepth = printtype;
	}
	
	var border = (framedepth / (parseFloat(widthscale) + ( parseFloat(framedepth) * 2) )) * selectionwidth;
	
	if(border == 0){
		border = 1;
		cropOffset = 0;
	}
	else{
	   cropOffset = 2;
	}
	var width = selectionwidth - (border * 2) + parseFloat(cropOffset);
	var height = selectionheight - (border * 2) + parseFloat(cropOffset);

	$('.imgareaselect-border1').attr('style', 'border: solid '+ border +'px #000;width: ' + width +'px;height: '+ height +'px;');
	
	$('.gallery-handle').remove();
	if(printtype == '2x2'){
   	var xmiddle = parseFloat(width/2) + parseFloat(border / 2); 
   	var ytop = parseFloat(height/2) + parseFloat(border / 2); 
   	
   	var newdiv1 = '<div class="gallery-handle" style="position: absolute; font-size: 0px; z-index: -1; width: ' + border +'px; height: '+ height +'px; left: ' + xmiddle +'px; top: '+ border +'px; "></div>';
   	var newdiv2 = '<div class="gallery-handle" style="position: absolute; font-size: 0px; z-index: -1; width: ' + width +'px; height: '+ border +'px; left: ' + border +'px; top: '+ ytop +'px; "></div>';
   	
   	$('.imgareaselect-border1').parent().append(newdiv1);
   	$('.imgareaselect-border1').parent().append(newdiv2);
	}
   if(printtype == '1x3'){
   	
      var xwidth = parseFloat((selectionwidth - (border * 4)) / 3);
      
   	var x1middle = (xwidth + parseFloat(border));
   	var x2middle =  (xwidth * 2) + parseFloat(border * 2);
      
   	var ytop = parseFloat(height/2) + parseFloat(border / 2); 
   	
   	var newdiv1 = '<div class="gallery-handle" style="position: absolute; font-size: 0px; z-index: -1; width: ' + border +'px; height: '+ height +'px; left: ' + x1middle +'px; top: '+ border +'px; "></div>';
      var newdiv2 = '<div class="gallery-handle" style="position: absolute; font-size: 0px; z-index: -1; width: ' + border +'px; height: '+ height +'px; left: ' + x2middle +'px; top: '+ border +'px; "></div>';
   	
   	
   	$('.imgareaselect-border1').parent().append(newdiv1);
   	$('.imgareaselect-border1').parent().append(newdiv2);
	}
	
	quality(selectionwidth);
}

function quality(selectionwidth){
   
   //console.log(currentImage.x);
   //console.log(printwidth);
   //console.log( (currentImage.x / printwidth) / (previewWidth / selectionwidth));
   
   var quality =  (currentImage.x / printwidth) / (previewWidth / selectionwidth);
   
      if(quality > 0){
      if(quality > 50){
         $('.quality').html("<span>Meget bra</span>");
      }
      else if(quality > 20){
         $('.quality').html("<span>Bra</span>");
      }
      else if(quality > 5){
         $('.quality').html('<span class="bad">D&#229;rlig</span>');
      }
      else{
         $('.quality').html('<span class="verybad">Meget d&#229;rlig</span>');
      }
   }
   
}

function updateArtnr(){
   
   currentProductOptionId = $('.selectedsize option:selected').attr('id');

	$('.quantity').trigger('change');
	
}

function inlineImageUploadCallback( imageid, image ) {

	if ( image ) {
		pickImage( image );
	} else {
		alert( 'Could not retrive image information' + imageid);
	}

   /*	$.ef( 'image.info', {
		'imageid': imageid 
	}, function( response ) {
		if( response.result ) {
			alert( 'works' );
			pickImage( response.image );
		} else {
		alert( 'Could not retrive image information' );
		}
	});*/
}


function save( debug ) {
   
	printtype = $('#printtype').attr('value');
	
	if(printtype == '2x2' || printtype == '1x3'){
	  framedepth = 4;
	}
	else{
	  framedepth = printtype;
	}
	
	var printwidth_final = parseFloat(printwidth) + (parseFloat(framedepth) * 2);
	var printheight_final = parseFloat(printheight) + (parseFloat(framedepth) * 2);

	page = {};
	page.giftQuantity = $('#gift-quantity').attr('value');
	page.productoptionid = currentProductOptionId;
	/*page.productid = $('#productid').attr('value');*/
	page.size = 'small';
	page.redeye = false;
	page.varnish = false;
	
	if ( $('#red-eye').is(':checked') ){
		page.redeye = 'true';
	}
	if( $('#varnish').is(':checked')){
		page.varnish = 'true';
	}
        if( $('#upgrade').is(':checked')){
		page.upgrade = $('#upgrade').val();
	}
	if(currentSelection.width == 0){
	   alert("Du m&#229; velge et utsnitt!");
	   return(false);
	}

	page.image     = {};
	page.image.printtype = printtype;
	page.image.printsize_x = printwidth_final;
	page.image.printsize_y = printheight_final;
	page.image.fullsize_x = currentImage.x;
	page.image.fullsize_y = currentImage.y;
	page.image.editor_x = previewWidth;
	page.image.editor_y = previewHeight;
	page.image.x   = currentSelection.x1;
	page.image.y   = currentSelection.y1;
	page.image.dx  = currentSelection.width;
	page.image.dy  = currentSelection.height;
	page.image.bid = currentImage.id;
   
	
	// push the pages
	var pages = [page];

	if( debug ) {
		console.log( $(pages) );
	} else {
		$.post( '/create/enlargements/save', { 
			pages: $.toJSON(pages),
			web: true
		}, function(msg) {
			document.location.href = '/cart/accessories/';
			return false;
		});
	}

}