	$(document).ready( function() {
		// initializing the user interface
		$('#accordion').accordion({
			'navigation'		:	true
		});
		if( location.hash.toLowerCase().substring(1) == 'choose-template' ) {
			$('#accordion').accordion('activate', 1);
		}
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
		
		var textID = 1;
		var highestIndex = 210;
		
		$('#add-gift-to-cart').click( function() { save(); });
		
		//stretchToFit();
		
		if(ie6) {
			$('#template-wrapper').ready( function() {
				$('#template-wrapper').pngFix();
			});
		}
		
		$('#template-wrapper').load( function() {
			if(ie6) {
				$('#template-wrapper').pngFix();
			}
			//stretchToFit();
		});


		$('#template').data('malid', $('#initMalid').val() );
		$('#template').data('malpageid', $('#initMalpageid').val() );
		
		// loads the selected album
		$('#select-album').bind('change', function() {
			$('#album-images').empty();
			$('#album-images').append('<div class="loader center prepend-top"><img src="'+bigajaxLoader+'" title="loading" /></div>');
			
			$.getJSON('/api/1.0/album/images/forpurchase/' + $(this).val(),
				function(data) {
					if( data.result ) {
						
						$.each(data.images, function(i, item) {
							$('#album-images').append('<img src="'+item.thumbnail+'" title="'+item.title+'" id="'+item.id+'"height="50" />');
						});
						
						$('.loader', '#album-images').remove();
						
					} else {
						$('#album-images').empty();
						$('#album-images').append('<h3 class="prepend-top center">'+noImagesText+'</h3>');
					}	
				}
			);
		});

		// sets the align to the textarea (its later accessed to set the post)
		$('.text-align', '#text-align').click( function() {
			if( $(this).hasClass('left')) {
				$('#new-text').css('text-align', 'left');
			} else if( $(this).hasClass('center') ) {
				$('#new-text').css('text-align', 'center');
			} else {
				$('#new-text').css('text-align', 'right');
			}
		});
		
		// loads the template when clicked
		$('.template-thumb','#choose-template').live('click', function() {

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
			
			if(ie6) { //fix png
				$('.highrestemplate').remove();
				$('#template-wrapper').append('<img id="template" class="highrestemplate"/>');
				$('#template').attr( 'src', $('.highres', currentTemplate).attr('href') );
				$('#template-wrapper').pngFix();
				$('#template').width( width );
				$('#template').height( height );
			};
			
			stretchToFit();

			}).attr('src', $('.highres', currentTemplate).attr('href') );
			
			
			return false;
		});
		
		// inserts the image to the editor when clicked
		$('img','#album-images').live('click', function() {
		
			$('#template-wrapper').prepend('<div id="template-ajax-loader"><img src="'+bigajaxLoaderGray+'"/></div>');
			
			$('#template-ajax-loader')
				.css('opacity', '0.8')
				.css('margin','0')
				.css('background-color', '#666')
				.css('padding-left', $('#template-wrapper').width() / 2 )
				.css('padding-top', $('#template-wrapper').height() / 2 )
				.css('z-index', '34000')
				.width( '100%' )
				.height( '100%' );

			
			// remove all the dom info about prev image
			$('#image').remove();
			$('#image-wrapper').append('<img id="image" />');
			// inserts the thumb for better expirience
			
			var img = new Image();
			$(img).load( function() {
				$('#template-ajax-loader').remove();
				stretchToFit();
			}).attr('src', getImage( $(this).attr('id') ) );
			
			$('#image').attr('src', $(this).attr('src') );
			
			// remembers the image id
			$('#selected-image').attr('value', $(this).attr('id') );
			
			// inserts the the real photo
			$('#image').attr(  'src', getImage( $(this).attr('id') ) );
			
			var backgroundImage = this;
			
			$('#choose-template .template-thumb').css('background-image', 'url('+ $(this).attr('src') +')' );

			$('#image').load( function() {
				stretchToFit();
			});
			
			stretchToFit();
		});
		
		$('img', '#upload-queue').live('click', function() {
			
			// remove all the dom info about prev image
			var imageId = $(this).attr('id');
			$('#image').remove();
			
			// inserts the thumb for faster feedback
			$('#image').attr('src', $(this).attr('src') );
			$('#selected-image').attr('value', imageId );
			
			var img = new Image();
			
			$(img).load( function() {
				$('#image-wrapper').append('<img id="image" />');
				// inserts the the real photo
				$('#image').attr('src', getImage(imageId) );
				$('#helper').width( img.width );
				$('#helper').height( img.height );
				
				stretchToFit();
				
			}).attr('src', getImage(imageId) );
			
		});		
		
		$('#add-text').click( function() {
	
			$('#template-wrapper').prepend('<div id="textholder'+textID+'" class="textholder"/>');
			
			$('#textholder'+textID).prepend('<img id="text'+textID+'" class="currenttext"/>');
			
			var text = $('#new-text').val();
			if(text != ""){
      		text = text.replace(/\n/g, 'XXNYLINJEXX');
      		text = text.replace('&', 'XXOGXX');
            text=text.replace(/\\/g,'\\\\');
            text=text.replace(/\'/g,'\\\'');
            text=text.replace(/\"/g,'\\"');
            text=text.replace(/\0/g,'\\0');
      
      		var color = $('#new-text-color').val().replace('#', '');
      		var font = $('#fontselect').val();
      		var gravity = convertAlignToTaule( $('#new-text').css('text-align') );
      		
      		$('#text' + textID).addClass('gifteditor_text');
      		
      		var png;
      		if(ie6) {
      			png = 0;
      		} else {
      			png = 1;
      		}
      
      		//$('#text' + textID).attr('src', '/create_im_text.php?id=0&quality=1&png='+png+'&font='+font+'&gravity='+gravity+'&color='+color+'&text='+text+'&encoding=UTF8');
      		$('#text' + textID).attr('src', '/api/1.0/gift/text/?id=0&quality=1&png='+png+'&font='+font+'&gravity='+gravity+'&color='+color+'&text='+text+'&encoding=UTF8');
      		
      		var currentText = $('#textholder' + textID);
      		
      		$('.currenttext', currentText).width( $('#template').width()/3 );
      		
      		$(currentText).css({
      			'z-index': '20' + textID, 
      			'position': 'absolute', 
      			'top': $("#template").height()/ 3, 
      			'left': $("#template").width()/ 3
      		});
      		
      		$(currentText).prepend('<div class="attached_edit_box" id="edit_text_'+textID+'"></div>');
      		
      		
      		$(currentText).data("font",font);
      		$(currentText).data('gravity',gravity);
      		$(currentText).data('color',color);
      		$(currentText).data('text',text);
      		$(currentText).data('zindex', highestIndex );
      		
      		$('.attached_edit_box', currentText).prepend('<div class="sliderContainer">' + 
      			'<img src="http://static.repix.no/gfx/gui/gifteditor_smaller_text.png" class="smallertext" />' + 
      			'<div class="slider"></div>'+
      			'<img src="http://static.repix.no/gfx/gui/gifteditor_larger_text.png" class="largertext" />'+
      			'</div>'
      		);
      		
      		var currentPosition = $(currentText).position();
      			
      		$(currentText).mouseenter( function() {
      			$('.attached_edit_box', currentText).fadeTo("fast", 0.8);
      		}).mouseleave( function() {
      			$('.attached_edit_box', currentText).fadeTo("fast", 0);
      		});
      		
      		$('.attached_edit_box', '#textholder' + textID).append('<a href="#" class="delete button red x-small">'+deleteText+'</a>');
      		$('a.delete', '#textholder' + textID).click( function() {
      			$(currentText).remove();	
      			return false;
      		});
      		
      		// make the text draggable and moves the edit box
      		$(currentText).draggable({
      			containment: '#template-wrapper',
      			handle: '.currenttext'
      		});
      		
      		$('.slider', currentText).slider({
      			value: $('.currenttext', currentText).width(),
      			max: $('#template').width() /*- currentPosition.left*/,
      			min: 50,
      			slide: function(event, ui) { 
      				$('.gifteditor_text' , currentText).width( ui.value );
      			}
      		});
      		$('#new-text').attr('value', '');
      		textID++;
      		highestIndex++;
		    }
		});
		
		// sets the helper width as the same as image
		$("#helper").width( $('#image').width() );
		$("#helper").height( $('#image').height() );
		
		// drags the main image
		$("#helper").draggable({ 
			drag: function(event, ui) {
				// moving the image under the png
				var position = $(this).position();
				$('#image').css({
					'top': position.top,
					'left': position.left,
					'margin-left':0,
					'margin-top' :0
				})
			}
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
		
		// ZOOOOOOOOOOMING
		$('#zoom-in').bind('mousedown', function() {
			startZoomIn();
		}).bind('mouseup', function() {
			endZoom();
		});
		
		$('#zoom-out').bind('mousedown', function() {
			startZoomOut();
		}).bind('mouseup', function() {
			endZoom();
		});
		
		
		// CLIPART		
		$('#clipartgroup-selector').bind('change', function() {
		
			var clipartid =  $(':selected',this).val();
			$('#clipart-selector .clipartcollection').hide();
			$('#clipartcollection_id_' + clipartid).css('display','block');
			$('img', '#clipartcollection_id_' + clipartid).css('display','block');
			$('#clipartcollection_id_' + clipartid).show();

			
			$('#clipart-selector .clipartcollection img').each( function(i, item) {
				$(item).attr('src', $(item).attr('title') );
			});
			

		});
		
		$('.clipartitem-container','#clipart-selector').live('click', function() {
			
			var clipartID =  $(this).attr('id').replace('clipartitem-container_', '');
			
			$('#template-wrapper').prepend('<div id="clipartholder'+clipartID+'-'+highestIndex+'" class="currentclipartholder"/>');
			
			var currentClipart = $('#clipartholder'+clipartID+'-'+highestIndex);
			
			$(currentClipart).prepend('<img id="currentclipart'+clipartID+'-'+highestIndex+'" class="currentclipart"/>');
			
			$('img.currentclipart', currentClipart).attr('src', $('img', this).attr('src'));

			$(currentClipart).css({
				'position': 'absolute', 
				'z-index': highestIndex,
				'top': 50, 
				'left': 50 
			});
			
			var highres = staticurl + "images/clipart/thumbs/width/300/" + clipartID.toString() + ".png";
			
			if(ie6) {
				highres = '/clipart/' + clipartID.toString() + '_thumb.gif';
				$('img.currentclipart', currentClipart).attr('src', highres);	
			}
			
			if(!ie6) { // only png for other browsers than ie6
				$('img.currentclipart', currentClipart).attr('src', highres);	
			}
			
			$('.currentclipart', currentClipart).width(200);
			
			$(currentClipart).prepend('<div class="attached_edit_box" id="edit_clipart_'+clipartID+'"></div>');
			
			$('.attached_edit_box', currentClipart).prepend('<div class="sliderContainer">' + 
				'<img src="'+staticurl+'gfx/gui/gifteditor_smaller_text.png" class="smallertext" />' + 
				'<div class="slider"></div>'+
				'<img src="'+staticurl+'gfx/gui/gifteditor_larger_text.png" class="largertext" />'+
				'</div>'
				);
			
			var currentPosition = $(currentClipart).position();
				
			$('.slider', currentClipart).slider({
				value: $(currentClipart).width(),
				max: $('#template').width() - currentPosition.left,
				min: 30,
				slide: function(event, ui) { 
					$('img.currentclipart', currentClipart).width( ui.value );
				}
			});

			$(currentClipart).mouseenter( function() {
				$('.attached_edit_box', currentClipart).fadeTo("fast", 0.8);
			}).mouseleave( function() {
				$('.attached_edit_box', currentClipart).fadeTo("fast", 0);
			});
			
			$('.attached_edit_box', currentClipart).append('<a href="#" class="delete button red x-small">Delete</a>');
			$('a.delete', currentClipart).click( function() {
				$(currentClipart).remove();	
				return false;
			})
			
			$(currentClipart).draggable({
				handle: '.currentclipart'
			});
			
			$(currentClipart).data('id', clipartID);
			$(currentClipart).data('zindex', highestIndex);
			
			highestIndex++;
			
		})
		
		$('#select-album').trigger('change');
	  /* $('#image').load( function() {
			stretchToFit();
		});*/

	});
	
	
	$(window).load(
    function() {
        stretchToFit();
    }
   );
	
	//set the width of helper and image to fit the template
	function stretchToFit() {
	
		if ( $('#template').data('websize_x') && $('#template').data('websize_y') ) {
		
			$('#template-wrapper').width( $('#template').data('websize_x') );
			$('#template-wrapper').height( $('#template').data('websize_y') );
			
			$('#template').width( $('#template').data('websize_x') );
			$('#template').height( $('#template').data('websize_y') );
			
			var original_template_width = parseFloat( $('#template').data('websize_x'));
   		var original_template_height = parseFloat( $('#template').data('websize_y'));
   		
   		if(  original_template_width > original_template_height ){
   		   var ratio = $('#template').data('fullsize_x') / 630;
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
   		   var ratio = $('#original-template-width').val() / 630;
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
			if(!ie6) {
				var templateRatio = $('#template').height() / maxHeight;
				$('#template').height( maxHeight );
				$('#template').width( $('#template').width() / templateRatio );
				$('#template-wrapper').css('margin-left', parseFloat(630 - $('#template').width() ) / 2);
			}
		} else {
			$('#template-wrapper').css('margin-left', '0');
		}

		var img_mal_image_dy = mal_image_dy;
		var img_mal_image_dx = mal_image_dx;
		
		if( $('#image').width() >= $('#image').height() ){
		  var mal_ratio = mal_image_dx / mal_image_dy;
		  var mal_image_ratio = $('#image').width() / $('#image').height();
		 
	   	   
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
		   var mal_image_ratio = $('#image').height() / $('#image').width();
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


      $('#image').width( img_mal_image_dx );
      $('#image').height( img_mal_image_dy );
      
      $('#image').css( 'left',  mal_image_x );
      $('#image').css( 'top', mal_image_y );
		
		$('#helper').width( img_mal_image_dx ).css( 'left', parseInt( mal_image_x) );
		$('#helper').height( img_mal_image_dy ).css( 'top', parseInt( mal_image_y ) );

		
		if( $('#template').width() && $('#image').width() ) {
			
				// sets the helper width as the same as image
				$('#helper').width( img_mal_image_dx );
				$('#helper').height( img_mal_image_dy );
				
				$('#template-wrapper').width( $('#template').width() );
				$('#template-wrapper').height( $('#template').height() );
				
				if(ie6 && $('#template').data('websize_x') && $('#template').data('websize_y') ) {
					$('#template-wrapper').width( $('#template').data('websize_x') );
					$('#template-wrapper').height( $('#template').data('websize_y') );	
				}
		}
	}
	
	// gets the url from imageID for old ef25 urls
	function getImage(imageid) {
		var height = 400;
		var width = 630;
		//return '/show_image_stream.php?bid='+imageid+'&dx='+width+'&dy='+height;
		return '/images/stream/image/'+imageid+'/'+width+'/'+height;
	}
	
	// Zoooooming functions
	var zoomTimer = false;
	var isZooming = false;
	
	function startZoomIn() {
		if( zoomTimer ) {
			endZoom();
		}
		isZooming = true;
		setTimeout( "zoomIn()", 20 );
	}
	
	function startZoomOut() {
		if( zoomTimer ) {
			endZoom();
		}
		isZooming = true;
		setTimeout( "zoomOut()", 20 );
	}
	
	function endZoom() {
		if( zoomTimer ) {
			clearTimeout( zoomTimer );
			zoomTimer = false;
		}
		isZooming = false;
	}
	
	function zoomIn( imageObject ) {
		
		$('#image').width(  $('#image').width() + $('#image').width()/50  );
		$('#image').height(  $('#image').height() + $('#image').height()/50  );
		$('#helper').width( $('#image').width() );
		$('#helper').height( $('#image').height() );
		
		if( isZooming ) {
			if( zoomTimer ) {
				clearTimeout( zoomTimer );
				zoomTimer = false;
			}
			zoomTimer = setTimeout( "zoomIn('#image')", 20 );
		}
	}
	
	function zoomOut( imageObject ) {
		$('#image').width(    $('#image').width() - $('#image').width()/50    );
		$('#image').height(   $('#image').height() - $('#image').height()/50 );
		$('#helper').width( $('#image').width() );
		$('#helper').height( $('#image').height() );
		if( isZooming ) {
			if( zoomTimer ) {
				clearTimeout( zoomTimer );
				zoomTimer = false;
			}
			zoomTimer = setTimeout( "zoomOut('#image')", 20 );
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
	
		var ratioWidth = $('#original-template-width').val() / $('#template').width();
		var ratioHeight = $('#original-template-height').val() / $('#template').height();
		var position = $('#image').position();
		
		var pages = new Array();
		
		pages.push();
		
		pages[0] = new Object();
		
		pages[0].malid = $('#template').data('malid');
		pages[0].malpageid = $('#template').data('malpageid');
		pages[0].giftQuantity = $('#gift-quantity').val();
		pages[0].productoptionid = $('#productoptionid').val();
		pages[0].productid = $('#productid').val();
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
		
			var position = $(this).position();
			var text = new Object();
			
			text.text = Base64.encode( $(this).data('text') );
			text.font = $(this).data('font');
			text.color = $(this).data('color');
			text.gravity = $(this).data('gravity');
			text.zindex = $(this).data('zindex');
			
			text.x = position.left * ratioWidth;
			text.y = position.top * ratioHeight;
			text.dx = $(this).width() * ratioWidth;
			text.dy = $(this).height() * ratioHeight;
			
			pages[0].texts.push(text);
			
		});
		
		pages[0].cliparts = new Array();
		
		$('.currentclipartholder').each( function(i) {
			
			var position = $(this).position();
			var clipart = new Object();
			
			clipart.id = $(this).data('id');
			clipart.x = position.left * ratioWidth;
			clipart.y = position.top * ratioWidth;
			clipart.dx = $('.currentclipart', this).width() * ratioWidth;
			clipart.dy = $('.currentclipart', this).height() * ratioHeight;
			clipart.zindex = $(this).data('zindex');
			
			pages[0].cliparts.push(clipart);
		});
		
		if( $("input[name='name']").val() ){
			pages[0].name = $("input[name='name']").val();
			pages[0].font = $("input[name='font']:checked").val();
		}
		
		
		
		$.ajax({
			type: 'POST',
			url: '/create/gift/save',
			dataType: 'JSON',
			data: {
				pages: $.toJSON( pages )
			},
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