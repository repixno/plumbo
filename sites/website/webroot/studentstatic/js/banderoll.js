var helpertop;
var helperleft;
var helpertwidth;
var helperheight;
var textheight;

	$(document).ready( function() {
		
		
		$("#sp-light input").spectrum({
			theme: "sp-light",
			move: function(color){
				$('.textholder span').css('color', color.toHexString() );
			},
			chooseText: "Bekräfta",
			cancelText: "Annullera",
			showPalette: true,
			palette: [
				["rgb(255, 205, 0)", "rgb(0, 75, 135)", "rgb(0, 0, 0)", "rgb(67, 67, 67)" ],
				["rgb(152, 0, 0)", "rgb(255, 0, 0)", "rgb(255, 153, 0)", "rgb(255, 255, 0)", "rgb(0, 255, 0)",
				"rgb(0, 255, 255)", "rgb(74, 134, 232)", "rgb(0, 0, 255)", "rgb(153, 0, 255)", "rgb(255, 0, 255)", "rgb(231, 0, 147)","rgb(255, 203, 56)"]
			]
		});
		
		$('#font-list').fontmenu();
		
		$('.text span').on( 'blur', function(){
			if( /^\s+$/.test( $(this).text() ) || $(this).text() == "" ){
				$(this).text('Din text här');
			}
			
			var str2 = $(this).text().replace(/\n|\r/g, "");
			
			$(this).text(str2);
			
		});
		$('.text span').on( 'focus', function(){
			if( $(this).text() == "Din text här" ){
				$(this).text("");
			}
		});
		
		$('.choose-product').on( 'change', function(){
			
			var quantity = 0;
			if( $(this).prop('checked') ){
				quantity = 1;
			}
			$(this).parent().find('.productquantity').val(quantity);
			
			
			});
		
		$('.text span').on( 'keyup keypress', function(e){
			if(e.which == 13) {
					e.preventDefault();
					//console.log('You pressed enter!');
				}
			if(   $(this).width() < 360  ){
				//console.log( $(this).width());
				$(this).css('font-size', $(this).data('fontsize'));
			}
			
			while(  $(this).width() > 320  ){
				var textlength = $(this).text().length;
				if( textlength > 25 ){
					e.preventDefault();
					return false;
				}
				var fontsize = parseInt( $(this).css('font-size') ) - 1 ;
				$(this).css('font-size', fontsize + 'px');	
				text1width = $(this).width();
			}
            
            
            var templateheight = $('#template').height();
            textheight = $(this).height() + 10;
			
			if( textheight < 80 ){
				var texttop = ( templateheight -  textheight) / 2;
				$('.text1').css('top', texttop );
			}else{
				$('.text1').css('top', 1 );
			}
			});
		
		// red eye price		
		$.post('/api/1.0/prices/get', {
				productoptionid: 1417,
				quantity: 1
			}, function(data) {
				$('#redeye-price').text( data.price );
		}, 'json');
		
		// change price on quantity
		$('.quantity').bind('change', function() {
			var quantity = $(this).val();
			$.post('/api/1.0/prices/get', {
				productoptionid: productoptionid,
				quantity: quantity
			}, function(data) {
				$('#gift-price').text( data.price );
			}, 'json');
		});
		
		var highestIndex = 210;
		
		$('#add-gift-to-cart').click( function() { save(); });

		$('#template').data('malid', $('#initMalid').val() );
		$('#template').data('malpageid', $('#initMalpageid').val() );
		
		
		// loads the template when clicked
		$(document).on('click','.template-thumb',  function() {
			
			
			$('.active-template').removeClass('active-template');
			
			$(this).addClass('active-template');

			$('#template').remove();
			
			$('#template-wrapper').prepend('<div id="template-ajax-loader"><img src="'+bigajaxLoaderGray+'"/></div>');
			
			var currentTemplate = this;
			
			var width = $('.mal', currentTemplate).data('websize_x');
			var height = $('.mal', currentTemplate).data('websize_y');

			$('#template-ajax-loader')
				.css('opacity', '0.8')
				.css('margin','0')
				.css('background-color', '#666')
				.css('padding-left', width /2 )
				.css('padding-top', height /2 )
				.css('z-index', '34000')
				.width( '100%' )
				.height( '100%' );
				
			var templateImage = new Image();
			
			
			var malpageid =  $('.malpageid', currentTemplate).val();
			
			var temptext = {
                             2956:{
								text1 : {
									top : '2px',
									fontsize: '70px',
									color : '#004B87',
									fontfamily: 'Monotype Corsiva'
								}
							},
							0 :{
								text1 : {
									top : '0px',
									fontsize: '70px',
									color : '#004B87',
									fontfamily: 'Monotype Corsiva'
								}
							}
						}
			
			
			
			
			
			var textvars =  temptext[malpageid];
			
			if( !textvars ){
				textvars =  temptext[0]
				
			}
			
			$('#font-list span').css('font-family', textvars.text1.fontfamily ).text(textvars.text1.fontfamily);
			
			
			$(templateImage).load( function() {
				$('#template-ajax-loader').remove();
				$('#image-wrapper').before('<img id="template" src="" height="'+height+'" width="'+width+'" style="height:'+height+'px ; width:'+width+'px "/>');
			
				$('#template').addClass('highrestemplate');
				
				$('#template').attr( 'src', $('.highres', currentTemplate).attr('href') );
				$('#template').data('malid', $('.malid', currentTemplate).val() );
				
				
				// sets the with and height from backend
				$('#template').data('websize_x', width );
				$('#template').data('websize_y', height );
				
				$('#template').data('fullsize_pos_x', $('.mal', currentTemplate).data('fullsize_pos_x'));
				$('#template').data('fullsize_pos_y', $('.mal', currentTemplate).data('fullsize_pos_y'));
				$('#template').data('fullsize_pos_dx', $('.mal', currentTemplate).data('fullsize_pos_dx'));
				$('#template').data('fullsize_pos_dy', $('.mal', currentTemplate).data('fullsize_pos_dy'));
				$('#template').data('fullsize_x', $('.mal', currentTemplate).data('fullsize_x'));
				$('#template').data('fullsize_y', $('.mal', currentTemplate).data('fullsize_y'));
	
				$('#template').width( width );
				$('#template').height( height );
				
				$('#template').data('malid', $('.malid', currentTemplate).val() );
				$('#template').data('malpageid', $('.malpageid', currentTemplate).val() );
				
				$('#original-template-width').val( $('.mal', currentTemplate).data('fullsize_x') );
				$('#original-template-height').val( $('.mal', currentTemplate).data('fullsize_y') );
				
				
				
				$('.text1').css({
					'top' : textvars.text1.top
					})
				
				
				$('.text1 span').data('fontsize', textvars.text1.fontsize );
				$('.text1 span').css({
					'font-size': textvars.text1.fontsize,
					'color': textvars.text1.color,
					'font-family' : textvars.text1.fontfamily
					});
                
                $("#sp-light input").spectrum("set", textvars.text1.color );
                
				stretchToFit();
				
				while(  $('.text1 span').width() > 320  ){
					var fontsize = parseInt( $('.text1 span').css('font-size') ) - 1 ;
					$('.text1 span').css('font-size', fontsize + 'px');	
				}
				
				var templateheight = $('#template').height();
				var texttop = ( templateheight -  textheight) / 2;
				
				console.log(texttop);
				$('.text1').css('top', texttop );

			}).attr('src', $('.highres', currentTemplate).attr('href') );
			
			
			return false;
		});
		
		
		$('button#velgbilde').on( 'click',  function(){
			$('#choosefile').click();
			return false;
		});
			
		$('input#choosefile').on( 'change',  function(){
			$('#lastoppbilde').click();
			return false;
		});
		// let the user see through the template
		$('#see-throug-template').click( function() {
			if( !$(this).hasClass('clicked') ) {
				$('#template').fadeTo("slow", 0.7);
				$(this).addClass('clicked');
			} else {
				$('#template').fadeTo("slow", 1);
				$(this).removeClass('clicked');
			}
		});


		


	});
	
	
		$(window).load(
			function() {
				stretchToFit();
				$('.template-thumb').first().click();
				//$(window).scrollTop(100);
			}
	   );
	
	var holdbutton = false;


	function rgbToHex(color) {
		if (color.substr(0, 1) === "#") {
			return color.replace("#", "");;
		}
		var nums = /(.*?)rgb\((\d+),\s*(\d+),\s*(\d+)\)/i.exec(color),
			r = parseInt(nums[2], 10).toString(16),
			g = parseInt(nums[3], 10).toString(16),
			b = parseInt(nums[4], 10).toString(16);
		return (
			(r.length == 1 ? "0"+ r : r) +
			(g.length == 1 ? "0"+ g : g) +
			(b.length == 1 ? "0"+ b : b)
		);
	}

	function updateText( ){
		var font = $('#font-list span').css('font-family'); 
		$('.textholder span').css('font-family' , font);
		
		
	}


	
	//set the width of helper and image to fit the template
	function stretchToFit() {
	
		if ( $('#template').data('websize_x') && $('#template').data('websize_y') ) {
		
			$('#template-wrapper').width( $('#template').data('websize_x') - 1 );
			$('#template-wrapper').height( $('#template').data('websize_y') );
			
			$('#template').width( $('#template').data('websize_x') );
			$('#template').height( $('#template').data('websize_y') );
			
			var original_template_width = parseFloat( $('#template').data('websize_x'));
			var original_template_height = parseFloat( $('#template').data('websize_y'));
   		
			if(  original_template_width > original_template_height ){
				var ratio = $('#template').data('fullsize_x') / maxtemplatewidt;
			}
			else{
				var ratio = ($('#template').data('fullsize_y') / maxHeight);
			}
         
			var mal_image_x = parseFloat(  $('#template').data('fullsize_pos_x') / ratio );
			var mal_image_y = parseFloat(  $('#template').data('fullsize_pos_y') / ratio );
			var mal_image_dx = parseFloat(  $('#template').data('fullsize_pos_dx') / ratio );
			var mal_image_dy = parseFloat(  $('#template').data('fullsize_pos_dy') / ratio );
		}
		else{
			//image size to image template!
			var original_template_width = parseFloat($('#original-template-width').val());
			var original_template_height = parseFloat($('#original-template-height').val());
   		
			if(  original_template_width > original_template_height ){
				var ratio = $('#original-template-width').val() / maxtemplatewidt;
			}
			else{
				var ratio = ($('#original-template-height').val() / maxHeight);
			}
			var mal_image_x = parseFloat( $('#original-pos-x').val() / ratio );
			var mal_image_y = parseFloat( $('#original-pos-y').val() / ratio );
			var mal_image_dx = parseFloat( $('#original-pos-dx').val() / ratio );
			var mal_image_dy = parseFloat( $('#original-pos-dy').val() / ratio );
		}
		
		if( $('#template').height() >= maxHeight) {
			var templateRatio = $('#template').height() / maxHeight;
			$('#template').height( maxHeight );
			$('#template').width( $('#template').width() / templateRatio );
			$('#template-wrapper').css('margin-left', parseFloat(maxtemplatewidt - $('#template').width() ) / 2);
		}
		else if( $('#template').width() >= maxtemplatewidt ){
			var templateRatio = $('#template').width() / maxtemplatewidt;
			$('#template').height( $('#template').height() / templateRatio  );
			$('#template').width( maxtemplatewidt);
			//$('#template-wrapper').css('margin-left', parseFloat(maxtemplatewidt - $('#template').width() ) / 2);
		}
		else {
			$('#template-wrapper').css('margin-left', '0');
		}
		
		$('#template-wrapper').css('margin-left', '0');
		
		
		if( $('.row-offcanvas').hasClass('active') ){
			$('.row-offcanvas').toggleClass('active');
		}
		
		
		//var templateheight = $('#template').height();
		
		//$(window).scrollTop(100);
		
	}
	
	// gets the url from imageID for old ef25 urls
	function getImage(imageid) {
		var height = 400;
		var width = maxtemplatewidt;
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
	function save(){
		
		$( '.productquantity' ).each( function(){
			
			if( $(this).val() > 0 ){
			
				var productoptionid = $(this).data('productoptionid');
				var productid = $(this).data('productid');
			
				var ratioWidth = $('#original-template-width').val() / $('#template').width();
				var ratioHeight = $('#original-template-height').val() / $('#template').height();
				var position = $('#image').position();
				
				var pages = new Array();
				
				pages.push();
				
				pages[0] = new Object();
				
				pages[0].malid = $('#template').data('malid');
				pages[0].malpageid = $('#template').data('malpageid');
				pages[0].giftQuantity = $('#gift-quantity').val();
				pages[0].productoptionid = productoptionid;
				pages[0].productid = productid;
				pages[0].size = "small";
				pages[0].editor_x = $('#template-wrapper').width();
				pages[0].editor_y = $('#template-wrapper').height();
				
				if ( $('#red-eye').is(':checked') ) {
					pages[0].redeye = 'true';
				}
				
				
				pages[0].image = new Object()
				pages[0].image.x = position.left * ratioWidth;
				pages[0].image.y = position.top * ratioHeight;
				pages[0].image.dx = $('#image').width() * ratioWidth;
				pages[0].image.dy = $('#image').height() * ratioHeight;
				pages[0].image.bid = $('#selected-image').val();
				
				pages[0].texts = new Array();
				
				$('.textholder').each( function(i) {
					var textspan = $(this).find('span');
					templateoffset = $('#template').offset();
					
					var holderpos = $(this).offset();
					
					var position = textspan.offset();
					var positiontop =  position.top -  templateoffset.top + 5;
					var positionleft  = position.left -  templateoffset.left + 10;
					var text = new Object();
					var fontname =  textspan.css('font-family').replace(/'/g, "");
					text.text = Base64.encode( $(this).find('span').text() );
					text.font = fontdata[fontname];
					
					var textcolor =  rgbToHex( textspan.css('color') );
					
					text.color = textcolor;
					text.gravity = 'center';
					text.zindex = 1;
					var textwidth =  textspan.width();
					var textheight =  textspan.height();
					
					//console.log( $(this).find('span').text()  );
					//console.log( "textwidth " + textwidth + "textheighth " + textheight + " posttop " +  positiontop );
					//console.log(  );
					
					text.x = positionleft * ratioWidth;
					text.y = positiontop * ratioHeight;
					text.dx = textwidth * ratioWidth;
					text.dy = textheight * ratioHeight;
					//console.log(text);
					pages[0].texts.push(text);
					
				});
				
				pages[0].cliparts = new Array();
				
				$('.currentclipartholder').each( function(i) {
					
					var position = $(this).position();
					var clipart = new Object();
					
					var matrix = new Array(  $(this).panzoom("getMatrix") );
					var clipartwidth =  $(this).find('.currentclipart').width() *  matrix[0][0];
					var clipartheight =  $(this).find('.currentclipart').height() *  matrix[0][0];
					
					clipart.id = $(this).data('id');
					clipart.x = position.left * ratioWidth;
					clipart.y = position.top * ratioWidth;
					clipart.dx = clipartwidth * ratioWidth;
					clipart.dy = clipartheight * ratioHeight;
					clipart.zindex = $(this).data('zindex');
					
					pages[0].cliparts.push(clipart);
				});
				
				if( $("input[name='name']").val() ){
					pages[0].name = $("input[name='name']").val();
					pages[0].font = $("input[name='font']:checked").val();
				}
				
				//console.log( pages);
				
				$.ajax({
					type: 'POST',
					url: '/create/save',
					dataType: 'JSON',
					data: {
						pages: JSON.stringify( pages )
					},
					success: function(msg) {
						
						//console.log(msg);
						
						document.location.href = '/cart/';
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
		});
	}