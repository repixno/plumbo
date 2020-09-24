var holdbutton = false;

function timeout(thisbutton) {
	
	var zoomtype = thisbutton.attr('id');
	var helper = $('.selected-template .helper');
	
	if( holdbutton ){
		setTimeout(function () {
			thisbutton.click();
			
			if( zoomtype == 'zoom-in' ){
				helper.panzoom("zoom", false);
			}else if (zoomtype == 'zoom-out' ) {
				helper.panzoom("zoom", true);
			}else if (zoomtype == 'reset' ) {
				helper.panzoom("reset");
			}

			if( zoomtype != 'zoom-range' ){
				
				var thiszoom = helper.panzoom("getMatrix")[0]
			
			
				$('#zoom-range').slider( "value", thiszoom );
				
				//$('.zoom-range').val(  thiszoom  );
				
				
				timeout(thisbutton);
				
			}
			
			
		}, 10);
	}
}

function setelementdata( element, matrix ){
	
	var matrixarray = new Array( matrix );			
	var thisimage = $(element).find('.img');
	var thiswidth = thisimage.width() * parseFloat( matrixarray[0][0] );
	var thisheight = thisimage.height() * parseFloat( matrixarray[0][0] );
	var thisleft = parseInt( matrixarray[0][4] ) + ( (  thisimage.width() - thiswidth ) / 2 ); 
	var thistop = parseInt( matrixarray[0][5] ) + ( (  thisimage.height() - thisheight ) / 2 ) ;
	thisimage.data( { 'width' : thiswidth, 'height': thisheight, 'left': thisleft, 'top': thistop });
	
}


function resizeText( currentResizable, currentDraggable  ){
	
	$('#' + currentDraggable ).on('click', function(){
		 
		var position =  $(this).offset();
		$('.texteditorbox').show().css({'top': position.top - 90 , 'left' : position.left - 50});
		$('.textholder').removeClass('activeText');
		
		var text = $('#' + currentDraggable ).data('text');
		
		text = text.replace( /XXNYLINJEXX/g , "\n" );
		text = text.replace(/XXOGXX/g, "&");
		$(this).addClass('activeText');
		
		$('#edittextbox').val(text);
		$('#new-text-color').val( "#" + $(this).data('color') );
		$('.colorPicker-picker').css( 'background-color' , "#" + $(this).data('color') );
		//$('#new-text-color').css({'background': "#" + $(this).data('color')});
		$('#fontselect').val($(this).data('font') );
		
		var gravity = convertAlignFromTaule($(this).data('gravity'));
		$('#new-text').css('text-align', gravity );
		$('#text-align-' +  gravity ).prop('checked', true );
		
		var Matrix = new Array( $('.activeText' ).panzoom("getMatrix") );
		var zoom = parseFloat( Matrix[0][0] );
		
		//
		$('input[name="text-size"').val(zoom);
		
	});
	
	$( '#' + currentDraggable ).on('panzoomchange', function(e, panzoom, matrix, changed) {
		setelementdata('#' + currentDraggable, matrix );
	});
	
}

function resizeElement( currentResizable, currentDraggable ){
	
	var lastY = 0;
	var lastX = 0;
	
	$('#' + currentResizable ).mouseenter( function(){
			$('#' + currentResizable ).find('.ui-resizable-handle').show();
	}).mouseleave( function(){
		$('#' + currentResizable ).find('.ui-resizable-handle').hide();
		});	
	
	$('#' + currentResizable ).on( 'touchstart click', function(){
		$('#' + currentResizable ).find('.ui-resizable-ne').show();
		});
	
	$('#' + currentResizable ).find('.ui-resizable-ne').append('<img class="icon" src="' + staticsite + 'gfx/icons/resize.png" />').css( 'display', 'none' );
	
	var active = false;

	$('#' + currentResizable ).find('.ui-resizable-ne').on( 'mousedown touchstart',  function(ret){
		ret.preventDefault();
		$('div').addClass('resizing');
		$('#' + currentDraggable ).panzoom('disable');
		active = true;
		
		
		lastY = 0;
		lastX = 0;
		
	
	});

	$(document).on( 'mouseup touchend',  function(ret){
		$('div').removeClass('resizing');
		//ret.preventDefault();
		$('#' + currentDraggable ).panzoom('enable');
		active = false;
	
	});

	$('#' + currentResizable ).find('.delteclipart').on('click', function(){
		$('#' + currentDraggable).remove();	
	});

	
	$(document).on( 'mousemove touchmove', function(ret){
		
			if( active == true ){
				if( !lastX ){
					lastX = ret.pageX;
				}
				if( !lastY ){
					lastY = ret.pageY;
				}
				
				$('#' + currentDraggable ).panzoom('enable');
				var Matrix = new Array( $('#' + currentDraggable ).panzoom("getMatrix") );
				var zoom = parseFloat( Matrix[0][0] );
				
				var $current = $('#' + currentDraggable );
				
				var matrix = $current.panzoom("getMatrix");
					
				var iconwidth = $('#' + currentDraggable ).find('.ui-resizable-handle').width();
				
				//console.log(iconwidth);
				//console.log(matrix[0]);
				
				
				//if( iconwidth  < 30 ){
					$('#' + currentDraggable ).find('.ui-resizable-handle').width( 25 / matrix[0] );
				//}
				var orgwidth =  $current.width();
				var currentorgwidth =  $current.width() * matrix[0];
				
				var resize = ( ret.pageX - lastX ) +  ( lastY - ret.pageY ) ;
				var newzoom =  ( currentorgwidth + resize) / orgwidth;
				//var zoomx =  parseFloat( matrix[0] );
				//var zoomy =  parseFloat( matrix[3] );
				if(newzoom < 0.15 ) return false;
				
				$current.panzoom("setMatrix", [ newzoom, 0, 0, newzoom, matrix[4], matrix[5] ]);
				//console.log('moving down');
				
				$('#' + currentResizable ).find('.ui-resizable-ne').show();
				//console.log(zoom);
				
				lastY = ret.pageY;
				lastX = ret.pageX;
				
				ret.preventDefault();
				//return false;
				//console.log( lastY );	
			}
		
	});
	
	setelementdata('#' + currentDraggable,  $( '#' + currentDraggable ).panzoom("getMatrix") );

	$( '#' + currentDraggable ).on('panzoomchange', function(e, panzoom, matrix, changed) {
	
		setelementdata('#' + currentDraggable, matrix );
		
	});
	
}


//set the width of helper and image to fit the template
function stretchToFit() {
	
	/*$('.buttons').each( function(){
		$(this).addClass('hide');
		})*/
	
	
	//console.log($('.selected-template').prev());
	
	//$('.selected-template').prev().removeClass('hide');

	var $template = $('.selected-template').find( '#template' );
	var $image = $('.selected-template').find('#image' );
	var $helper = $('.selected-template').find('.helper' );
	
	$('.selected-template').addClass('stretchToFit');

	if ( $template.data('websize_x') && $template.data('websize_y') ) {
	
		$('.selected-template').width( $template.data('websize_x') );
		$('.selected-template').height( $template.data('websize_y') );
		
		$template.width( $template.data('websize_x') );
		$template.height( $template.data('websize_y') );
		
		var original_template_width = parseFloat( $template.data('websize_x'));
		var original_template_height = parseFloat( $template.data('websize_y'));
	
		if(  original_template_width > original_template_height ){
		   var ratio = $template.data('fullsize_x') / 630;
		}
		else{
			var ratio = ($template.data('fullsize_y') / maxHeight);
		}
	 
		var mal_image_x = parseFloat(  $template.data('fullsize_pos_x') / ratio );
		var mal_image_y = parseFloat(  $template.data('fullsize_pos_y') / ratio );
		var mal_image_dx = parseFloat(  $template.data('fullsize_pos_dx') / ratio );
		var mal_image_dy = parseFloat(  $template.data('fullsize_pos_dy') / ratio );
	
	}
	else{
		//image size to image template!
	   var original_template_width = parseFloat($('#original-template-width').val());
	   var original_template_height = parseFloat($('#original-template-height').val());
	   
	   if(  original_template_width > original_template_height ){
		  var ratio = $('#original-template-width').val() / 630;
	   }
		else{
		   var ratio = ($('#original-template-height').val() / maxHeight);
		}
	   var mal_image_x = parseFloat( $('.selected-template').find('#original-pos-x').val() / ratio );
	   var mal_image_y = parseFloat( $('.selected-template').find('#original-pos-y').val() / ratio );
	   var mal_image_dx = parseFloat( $('.selected-template').find('#original-pos-dx').val() / ratio );
	   var mal_image_dy = parseFloat( $('.selected-template').find('#original-pos-dy').val() / ratio );
	}
	
	if( $template.height() >= maxHeight) {
		$('.selected-template').css('margin-left', '0');
	}

	var img_mal_image_dy = mal_image_dy;
	var img_mal_image_dx = mal_image_dx;
	
	if( $image.width() >= $image.height() ){
	  var mal_ratio = mal_image_dx / mal_image_dy;
	  var mal_image_ratio = $image.width() / $image.height();
	 
	   
	if(mal_ratio > mal_image_ratio){
	   img_mal_image_dy = parseFloat( mal_image_dx / mal_image_ratio );
		mal_image_y = mal_image_y - ( ( img_mal_image_dy - mal_image_dy) / 2 );
	 }
	 else{
		img_mal_image_dx = parseFloat( mal_image_dy * mal_image_ratio );
		mal_image_x = mal_image_x - ( ( img_mal_image_dx - mal_image_dx ) / 2 );		      
	 }
	}
	else{
	   var mal_image_ratio = $image.height() / $image.width();
	   var mal_ratio = mal_image_dy / mal_image_dx;
	if(mal_image_ratio >= mal_ratio ){
	  img_mal_image_dy = parseFloat( mal_image_dx * mal_image_ratio );
		mal_image_y = mal_image_y - ( ( img_mal_image_dy - mal_image_dy) / 2 );
	 }
	 else{
		img_mal_image_dx = parseFloat ( mal_image_dy / mal_image_ratio );
		mal_image_x = mal_image_x - ( ( img_mal_image_dx - mal_image_dx ) / 2 );		      
	 }
	}


	$image.width( img_mal_image_dx );
	$image.height( img_mal_image_dy );
  
	$image.css( 'left',  mal_image_x );
	$image.css( 'top', mal_image_y );
	
	$helper.width( img_mal_image_dx ).css( 'left', parseInt( mal_image_x) );
	$helper.height( img_mal_image_dy ).css( 'top', parseInt( mal_image_y ) );

	
	if( $template.width() && $image.width() ) {
		
			// sets the helper width as the same as image
			$helper.width( img_mal_image_dx );
			$helper.height( img_mal_image_dy );
			
			
			helpertop = parseInt(  $helper.css('top') );
			helperleft = parseInt( $helper.css('left') );
			helpertwidth = parseInt(  $helper.css('width') );
			helperheight = parseInt(  $helper.css('height') );
			
			
			$('.selected-template').width( $('#template').width() );
			$('.selected-template').height( $('#template').height() );
	}
				
	var hmalpageid = $helper.parent().find('#initMalpageid').val();

	$("." + hmalpageid + "-zoom-in").parent().removeClass( 'hide' );
	
	
	//console.log(hmalpageid);
	
	//$('.selected-template').prev().removeClass('hide');
	
	$helper.panzoom({
		increment: 0.005,
		minScale: 0.05,
		maxScale: 4
		});
	
	$helper.panzoom("reset");
	
	
	var thiszoom = $helper.panzoom("getMatrix")[0]
			
			
	$('#zoom-range').slider( "value", thiszoom );
	
}

// gets the url from imageID for old ef25 urls
function getImage(imageid) {
	var height = 400;
	var width = 630;
	//return '/show_image_stream.php?bid='+imageid+'&dx='+width+'&dy='+height;
	return '/images/stream/image/'+imageid+'/'+width+'/'+height;
}
// converts left to east etc.
function convertAlignToTaule(align) {
	if(align == 'left') {
		return 'West';
	} else if (align == 'center') {
		return 'Center';
	} else if (align == 'right') {
		return 'East';
	} else {
		return 'West';
	}
}

function convertAlignFromTaule(align) {
	if(align == 'West') {
		return 'left';
	} else if (align == 'Center') {
		return 'center';
	} else if (align == 'East') {
		return 'right';
	} else {
		return 'West';
	}
}

// converts left to east etc.
function convertAlignToTaule(align) {
	if(align == 'left') {
		return 'West';
	} else if (align == 'center') {
		return 'Center';
	} else if (align == 'right') {
		return 'East';
	} else {
		return 'West';
	}
}


var Base64 = {

// private property
_keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

// public method for encoding
encode : function (input) {
	var output = "";
	var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
	var i = 0;

	input = Base64._utf8_encode(input);

	while (i < input.length) {

		chr1 = input.charCodeAt(i++);
		chr2 = input.charCodeAt(i++);
		chr3 = input.charCodeAt(i++);

		enc1 = chr1 >> 2;
		enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
		enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
		enc4 = chr3 & 63;

		if (isNaN(chr2)) {
			enc3 = enc4 = 64;
		} else if (isNaN(chr3)) {
			enc4 = 64;
		}

		output = output +
		this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
		this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

	}

	return output;
},

// public method for decoding
decode : function (input) {
	var output = "";
	var chr1, chr2, chr3;
	var enc1, enc2, enc3, enc4;
	var i = 0;

	input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

	while (i < input.length) {

		enc1 = this._keyStr.indexOf(input.charAt(i++));
		enc2 = this._keyStr.indexOf(input.charAt(i++));
		enc3 = this._keyStr.indexOf(input.charAt(i++));
		enc4 = this._keyStr.indexOf(input.charAt(i++));

		chr1 = (enc1 << 2) | (enc2 >> 4);
		chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
		chr3 = ((enc3 & 3) << 6) | enc4;

		output = output + String.fromCharCode(chr1);

		if (enc3 != 64) {
			output = output + String.fromCharCode(chr2);
		}
		if (enc4 != 64) {
			output = output + String.fromCharCode(chr3);
		}

	}

	output = Base64._utf8_decode(output);

	return output;

},

// private method for UTF-8 encoding
_utf8_encode : function (string) {
	
	if( !string ){
		return true;
	}
	string = string.replace(/\r\n/g,"\n");
	var utftext = "";

	for (var n = 0; n < string.length; n++) {

		var c = string.charCodeAt(n);

		if (c < 128) {
			utftext += String.fromCharCode(c);
		}
		else if((c > 127) && (c < 2048)) {
			utftext += String.fromCharCode((c >> 6) | 192);
			utftext += String.fromCharCode((c & 63) | 128);
		}
		else {
			utftext += String.fromCharCode((c >> 12) | 224);
			utftext += String.fromCharCode(((c >> 6) & 63) | 128);
			utftext += String.fromCharCode((c & 63) | 128);
		}

	}

	return utftext;
},

// private method for UTF-8 decoding
_utf8_decode : function (utftext) {
	var string = "";
	var i = 0;
	var c = c1 = c2 = 0;

	while ( i < utftext.length ) {

		c = utftext.charCodeAt(i);

		if (c < 128) {
			string += String.fromCharCode(c);
			i++;
		}
		else if((c > 191) && (c < 224)) {
			c2 = utftext.charCodeAt(i+1);
			string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
			i += 2;
		}
		else {
			c2 = utftext.charCodeAt(i+1);
			c3 = utftext.charCodeAt(i+2);
			string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
			i += 3;
		}

	}

	return string;
}

}

//Save the current setup
function save() {
	
	var pages = new Array();
	
	pages.push();
	
	var i = 0;
	
	$('.template').each( function() {
		
		var $template = $(this).find('#template');
		var malpageid = $(this).find('#initMalpageid').val();
		var malid = $(this).find('#initMalid').val();
		
		
		var ratioWidth = $(this).find('#original-template-width').val() / $template.width();
		var ratioHeight = $(this).find('#original-template-height').val() / $template.height();
		var position = $(this).find('#image').position();
		var positionleft = $(this).find('#image').css('left').replace("px", "");
		var positiontop = $(this).find('#image').css('top').replace("px", "");
		var imagewidth = $(this).find('#image').css('width').replace("px", "");
		var imageheight = $(this).find('#image').css('height').replace("px", "");
		
		var $image = $(this).find('#image');
		
		var $selectedimage = $(this).find('#selected-image');
		
		pages[i] = new Object();
		
		pages[i].malid = malid;
		pages[i].malpageid = malpageid;
		pages[i].giftQuantity = $('#gift-quantity').val();
		pages[i].productoptionid = $('#productoptionid').val();
		pages[i].productid = $('#productid').val();
		pages[i].size = "small";
		pages[i].editor_x = $(this).width();
		pages[i].editor_y = $(this).height();
		
		if ( $('#red-eye').is(':checked') ) {
			pages[i].redeye = 'true';
		}
		
		
		pages[i].image = new Object()
		pages[i].image.x = positionleft * ratioWidth;
		pages[i].image.y = positiontop * ratioHeight;
		pages[i].image.dx = imagewidth * ratioWidth;
		pages[i].image.dy = imageheight * ratioHeight;
		pages[i].image.bid =  $selectedimage.val();
		
		pages[i].texts = new Array();
		
		$(this).find( '.textholder' ).each( function(y){
			
			var text = new Object();
			
			text.text = Base64.encode( $(this).data('text') );
			text.font = $(this).data('font');
			text.color = $(this).data('color');
			text.gravity = $(this).data('gravity');
			text.zindex = $(this).data('zindex');
			
			var textdata = $(this).find('.currenttext').data();
			
			
			text.x = textdata.left  * ratioWidth;
			text.y = textdata.top  * ratioHeight;
			text.dx = textdata.width  * ratioWidth;
			text.dy = textdata.height * ratioHeight;
			
			pages[i].texts.push(text);
			
		});
		
		
		pages[i].cliparts = new Array();
		
		$(this).find('.currentclipartholder').each( function(y) {
			
			//var position = $(this).position();
			var clipart = new Object();
			
			var imagedata = $(this).find('.currentclipart').data();
			
			clipart.id = $(this).data('id');
			clipart.x =  imagedata.left * ratioWidth;
			clipart.y = imagedata.top * ratioWidth;
			clipart.dx = imagedata.width * ratioWidth;
			clipart.dy = imagedata.height * ratioHeight;
			clipart.zindex = $(this).data('zindex');
			
			pages[i].cliparts.push(clipart);
			
		});
		
		i++;
	
	});
	
	
	
	//console.log(pages);
	
	$.ajax({
		type: 'POST',
		url: '/create/newgift/save',
		dataType: 'JSON',
		data: { pages: $.toJSON(pages) },
		success: function(msg) {
			document.location.href = '/cart/accessories/';
			return false;
		},
		complete: function(msg) {
			//document.location.href = '/cart/';
			//return false;
		},
		error: function(msg) {
			alert('error');
		}
	});
}