var helpertop;
var helperleft;
var helpertwidth;
var helperheight;

	$(document).ready( function() {
		// initializing the user interface
		$('#accordion').accordion({
			navigation:	true,
			heightStyle: "content" 
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
		
		
		
		$('#helper').on('panzoomchange', function(e, panzoom, matrix, changed) {
			
			var matrixarray = new Array(matrix);
			//console.log(matrixarray[0]);
			//var position = $(this).position();
			var imgWidth = parseInt( $(this).width() ) * matrixarray[0][0];
			var imgHeight = parseInt( $(this).height() ) * matrixarray[0][0];

			var top =  parseInt(  $(this).css('top') ) + parseInt( parseInt( matrixarray[0][5] ) + ( ( helperheight - imgHeight ) / 2 ) );
			var left = parseInt( $(this).css('left') ) + parseInt( parseInt( matrixarray[0][4] )  + ( ( helpertwidth - imgWidth ) / 2 ) );
			
			//console.log( imgWidth );
			
			//$('#image').width( imgWidth );
			//$('#image').height( imgHeight );
			
			$('#image').css({
				'top': top,
				'left': left,
				'margin-left':0,
				'margin-top' :0,
				'width': imgWidth,
				'height': imgHeight
			});
			
		});
		
		
		var textID = 1;
		var highestIndex = 210;
		
		$('#add-gift-to-cart').click( function() { save(); });

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
		$(document).on('click','.template-thumb,#choose-template',  function() {

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
	
				
				stretchToFit();

			}).attr('src', $('.highres', currentTemplate).attr('href') );
			
			
			return false;
		});
		
		// inserts the image to the editor when clicked
		$(document).on('click', '#album-images img', function() {
		
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
			$('#selected-image').val( $(this).attr('id') );
			
			// inserts the the real photo
			$('#image').attr(  'src', getImage( $(this).attr('id') ) );
			
			var backgroundImage = this;
			
			$('#choose-template .template-thumb').css('background-image', 'url('+ $(this).attr('src') +')' );

			$('#image').load( function() {
				stretchToFit();
			});
			
			stretchToFit();
		});
		
				$('button#velgbilde').on( 'click',  function(){
				$('#choosefile').click();
				return false;
			});
				
			$('input#choosefile').on( 'change',  function(){
				$('#lastoppbilde').click();
				return false;
			});
			
		$('#fileupload').fileupload({
			replaceFileInput: false,
			dataType: "json",        
			datatype:"json",
			xhrFields: {withCredentials: true},
			acceptFileTypes: /(\.|\/)(jpe?g|tif)$/i,
			add: function (e, data) {
				
				//console.log(e);
				//console.log(data);
				
				data.context = $('<p id="uploading" style="position: absolute; left: 20px;"/>').text('Uploading...').appendTo($('.progress-bar'));
				data.submit();
				$('.progress').show();
				$('.progress .progress-bar-success').css(
					'width',
					0 + '%'
				);
				
			},
			done: function (e, data) {
				
				//console.log(data.result.files);
				
				$('#uploading').remove();
				
				$.each(data.result.files, function (index, file) {
					
					//console.log( index  );
					//console.log( file  );
					var image = '<img class="imagethumb" id="' + file.id + '"src="' + file.thumbnailUrl +'" style="height: 75px"  />';
					$('#upload-queue').append( image );
					selectedImageId = $(this).attr('id');
					var backgroundImage = this
					$('#choose-template .template-thumb').css('background-image', 'url('+ file.thumbnailUrl +')' );
					$('img#' + file.id ).css( 'border-color', "#0199d8" );
					
					$('.progress').hide();
					
			
				});
			},
			progressall: function (e, data) {
				var progress = parseInt(data.loaded / data.total * 100, 10);
				$('.progress .progress-bar-success').css(
					'width',
					progress + '%'
				);
			}
		});
		
		$(document).on('click','#upload-queue img', function() {
			
			// remove all the dom info about prev image
			var imageId = $(this).attr('id');
			$('#image').remove();
			
			// inserts the thumb for faster feedback
			$('#image').attr('src', $(this).attr('src') );
			$('#selected-image').val(imageId );
			
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
		
		
		
		
		$('#update-text').click( function(){
			
			var  $image =  $(".activeText").find('img');
			
			
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
				
				
				var currentText = $(".activeText");
				
				//$('#text' + textID).addClass('gifteditor_text');
				
				//resizeText( currentResizable, 'textholder'+textID  ); 
				png = 1;
				
		  
				//$('#text' + textID).attr('src', '/create_im_text.php?id=0&quality=1&png='+png+'&font='+font+'&gravity='+gravity+'&color='+color+'&text='+text+'&encoding=UTF8');
				$image.attr('src', '/api/1.0/gift/text/?id=0&quality=1&png='+png+'&font='+font+'&gravity='+gravity+'&color='+color+'&text='+text+'&encoding=UTF8');
			
			
				$(".activeText").data('text', text )
			
				$(currentText).data("font",font);
				$(currentText).data('gravity',gravity);
				$(currentText).data('color',color);
				$(currentText).data('text',text);
				$(currentText).data('zindex', highestIndex );
			}
			
		})
		
		
		$("body").on( 'click', function( e ){
			 if($(e.target).closest("#template-wrapper").length == 1 && !$(e.target).hasClass('gifteditor_text') )
				{
				  if( $('.textholder').hasClass('activeText') ){
					$('.textholder').removeClass('activeText');
					$('.update-text-func').hide();
					$('.add-text-func').show();
				  }
				  
				}
			});
		
		
		
		$('#delete-text').on( 'click', function(){
			$('.activeText').remove();
			$('.update-text-func').hide();
			$('.add-text-func').show();
			});
		
		
		var textSizeActive = false;
		$('input[name="text-size"]').on( 'mousedown touchstart', function(){
			
			textSizeActive = true;
			
			});
		$('input[name="text-size"]').on( 'mouseup touchend', function(){
			
			textSizeActive = false;
			
			}); 
		
		
		$('input[name="text-size"]').on( 'change mousemove', function(){
			
			
			
			var newzoom = $(this).val();
			
			
			//console.log(newzoom);
			
			var Matrix = new Array( $('.activeText' ).panzoom("getMatrix") );
			var zoom = parseFloat( Matrix[0][0] );
			
			//console.log( Matrix);
			
			Matrix[0][0] = newzoom;
			Matrix[0][3] = newzoom;
			
			$('.activeText' ).panzoom("setMatrix", Matrix );
			
			
			});
		
		
		$('#add-text').click( function() {

			var currentResizable = 'currentResizable'+textID+'-'+highestIndex;
			
			/*$('#template-wrapper').append('<div id="textholder'+textID+'" class="textholder draggable">\
							<div id="' + currentResizable + '" class="resizable">\
								<img id="text'+textID+'" class="currenttext img"/>\
								<div class="ui-resizable-handle ui-resizable-ne" style="z-index: 1000;"></div>\
								<div class="ui-resizable-handle ui-resizable-nw delteclipart" style="z-index: 1000;">\
									<img src="' + staticsite + 'gfx/icons/delete.png" />\
									</div>\
							</div>');*/
			
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

			$('#template-wrapper').append('<div id="textholder'+textID+'" class="textholder draggable activeText" data-text="' + text + '">\
							<div id="' + currentResizable + '" class="resizable">\
								<img id="text'+textID+'" class="currenttext img"/>\
									</div>\
							</div>');
			
      		
      		$('#text' + textID).addClass('gifteditor_text');
      		
			
			
			$('.update-text-func').show();
			$('.add-text-func').hide();
			
      		resizeText( currentResizable, 'textholder'+textID  ); 
      		png = 1;
      		
      
      		//$('#text' + textID).attr('src', '/create_im_text.php?id=0&quality=1&png='+png+'&font='+font+'&gravity='+gravity+'&color='+color+'&text='+text+'&encoding=UTF8');
      		$('#text' + textID).attr('src', '/api/1.0/gift/text/?id=0&quality=1&png='+png+'&font='+font+'&gravity='+gravity+'&color='+color+'&text='+text+'&encoding=UTF8');
      		
      		var currentText = $('#textholder' + textID);
      		
      		//$('.currenttext', currentText).width( $('#template').width()/2 );
      		
      		$(currentText).css({
      			'z-index': '20' + textID, 
      			'position': 'absolute', 
      			'top': 0,
      			'left': 0
      		});
      		
			
			
      		
      		
      		$(currentText).data("font",font);
      		$(currentText).data('gravity',gravity);
      		$(currentText).data('color',color);
      		$(currentText).data('text',text);
      		$(currentText).data('zindex', highestIndex );
      		
      		// make the text draggable and moves the edit box
			$(currentText ).panzoom({
				minScale: 0.1,
				maxScale: 3
			});
      		
			
			
			$( currentText ).panzoom("setMatrix", [ 0.7, 0, 0, 0.7, 50, 50 ]);
			
      		$('#new-text').val('');
      		textID++;
      		highestIndex++;
		    }
			
			if( $('.row-offcanvas').hasClass('active') ){
				$('.row-offcanvas').toggleClass('active');
			}
			
		});
		
		// sets the helper width as the same as image
		$("#helper").width( $('#image').width() );
		$("#helper").height( $('#image').height() );
		
		
		$('#helper').panzoom({
			minScale: 0.05,
            $zoomIn: $(".zoom-in"),
            $zoomOut: $(".zoom-out"),
            $zoomRange: $(".zoom-range"),
            $reset: $(".reset")
          });
		
		
		// drags the main image
		/*$("#helper").draggable({ 
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
		});*/
		
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
		

		
		
		// CLIPART		
		$('#clipartgroup-selector').on('change', function() {
		
			var clipartid =  $(':selected',this).val();
			$('#clipart-selector .clipartcollection').hide();
			$('#clipartcollection_id_' + clipartid).css('display','block');
			$('img', '#clipartcollection_id_' + clipartid).css('display','block');
			$('#clipartcollection_id_' + clipartid).show();

			
			$('#clipart-selector .clipartcollection img').each( function(i, item) {
				$(item).attr('src', $(item).attr('title') );
			});
			

		});
		
		
		$(document).on('click', '#clipart-selector .clipartitem-container', function() {
			
			var clipartID =  $(this).attr('id').replace('clipartitem-container_', '');
			var currentClipart = 'currentclipart'+clipartID+'-'+highestIndex;
			var currentResizable = 'currentResizable'+clipartID+'-'+highestIndex;
			var currentDraggable = 'currentDraggable'+clipartID+'-'+highestIndex;
			
			$('#template-wrapper').append('<div id="' + currentDraggable + '" class="draggable currentclipartholder" data-id="' + clipartID + '" ">\
											<div id="' + currentResizable + '" class="resizable">\
												<img id="' + currentClipart + '" class="currentclipart"/>\
												<div class="ui-resizable-handle ui-resizable-ne" style="z-index: 1000;"></div>\
												<div class="ui-resizable-handle ui-resizable-nw delteclipart" style="z-index: 1000;">\
													<img src="' + staticsite + 'gfx/icons/delete.png" />\
												</div>\
											</div>\
											</div>');
			
			var highres = staticurl + "images/clipart/thumbs/width/300/" + clipartID.toString() + ".png";
			
			resizeElement( currentResizable, currentDraggable );
			
			var $currentClipart = $('#currentclipart'+clipartID+'-'+highestIndex);
			
			$('#' + currentDraggable ).panzoom({
					minScale: 0.1,
					maxScale: 3
				});
			
			$('#' + currentDraggable ).css({
				'position': 'absolute', 
				'z-index': highestIndex,
				'top': 0, 
				'left': 0
			}).addClass('selectedclip');
			
			$currentClipart.attr('src', highres);
			
			$('#' + currentDraggable ).panzoom("setMatrix", [ 0.4, 0, 0, 0.4, 50, 50 ]);
			
			
			
			highestIndex++;
			
			
			if( $('.row-offcanvas').hasClass('active') ){
				$('.row-offcanvas').toggleClass('active');
			}
			
		});
		
		
		$('#select-album').trigger('change');
	  /* $('#image').load( function() {
			stretchToFit();
		});*/

	});
	
	
		$(window).load(
		function() {
			stretchToFit();
			$(window).scrollTop(100);
		}
	   );
	
	
	
	
	function resizeText( currentResizable, currentDraggable  ){
		
	
		$('#' + currentDraggable ).on('click', function(){
			
			
			//console.log( $(this).data() );
			
			$('.textholder').removeClass('activeText');
			$('.update-text-func').show();
			//$('#accordion').accordion('activate', 2);
			$('.add-text-func').hide();
			
			var text = $('#' + currentDraggable ).data('text');
			
			text = text.replace( /XXNYLINJEXX/g , "\n" );
      		text = text.replace(/XXOGXX/g, "&");

			$(this).addClass('activeText');
			
			$('#new-text').val(text);
			$('#new-text-color').val( "#" + $(this).data('color') );
			$('.colorPicker-picker').css( 'background-color' , "#" + $(this).data('color') );
			//$('#new-text-color').css({'background': "#" + $(this).data('color')});
			$('#fontselect').val($(this).data('font') );
			
			
			var gravity = convertAlignFromTaule($(this).data('gravity'));
			
			
			$('#new-text').css('text-align', gravity );
			
			//console.log('#text-algin-' +  gravity );
			
			
			$('#text-align-' +  gravity ).prop('checked', true );
			
			//console.log( );
			
			var Matrix = new Array( $('.activeText' ).panzoom("getMatrix") );
			var zoom = parseFloat( Matrix[0][0] );
			
			$('input[name="text-size"').val(zoom);
			
			
			});
		
		
		
	}
	
	
	function resizeElement( currentResizable, currentDraggable ){
		
		$('#' + currentResizable ).on( 'mouseenter mousedown' ,function(){
				$('#' + currentResizable ).find('.ui-resizable-handle').show();
		}).mouseleave( function(){
			$('#' + currentResizable ).find('.ui-resizable-handle').hide();
			});	
		
		$('#' + currentResizable ).on( 'touchstart click', function(){
			$('#' + currentResizable ).find('.ui-resizable-handle').show();
			});
		
		$( document ).on( 'touchstart mousedown', function(e){
			
			//console.log(e);
			
			$('.ui-resizable-handle').hide();
			});
		
		$('#' + currentResizable ).find('.ui-resizable-ne').append('<img src="' + staticsite + 'gfx/icons/resize.png""/>').css( 'display', 'none' );
		
		var active = false;

		$('#' + currentResizable ).find('.ui-resizable-ne').on( 'mousedown touchstart',  function(ret){
			//ret.preventDefault();
			//$('body, .ui-resizable-ne').css('cursor', 'none');
			$('div').addClass('resizing');
			$('#' + currentDraggable ).panzoom('disable');
			active = true;
		
		});
	
		$(document).on( 'mouseup touchend',  function(ret){
			$('div').removeClass('resizing');
			//ret.preventDefault();
			$('#' + currentDraggable ).panzoom('enable');
			active = false;
		
		});
	
		$('#' + currentResizable ).find('.delteclipart').on('mousedown touchstart', function(){
			$('#' + currentDraggable).remove();	
		});
	
		var lastY = 0;
		var lastX = 0;
		$(document).on( 'mousemove touchmove', function(ret){
			
				if( active == true ){

					$('#' + currentDraggable ).panzoom('enable');
					var Matrix = new Array( $('#' + currentDraggable ).panzoom("getMatrix") );
					var zoom = parseFloat( Matrix[0][0] );
					
					
					var ymove = lastY - ret.pageY;
					var xmove = lastX - ret.pageX;
					
					console.log('move');
					
					console.log( xmove );
					console.log( ymove );
					console.log( -ymove + xmove);
					
					//console.log(ret);
					
					if( ( -ymove + xmove ) < 0  ){
						
						
						var move = 0;
						
						if( ymove > xmove ){
							move = ymove * 0.03;
						}
						else{
							move = xmove * 0.03;
						}
						
						//console.log( move );
						
						zoom = ( zoom + 0.02 );
						
						$('#' + currentDraggable ).panzoom("zoom", zoom);
						//console.log('moving up');
					}else{
						
						zoom = zoom - 0.02;
						$('#' + currentDraggable ).panzoom("zoom", zoom);
						//console.log('moving down');
					}
					$('#' + currentResizable ).find('.ui-resizable-ne').show();
					//console.log(zoom);
					
					lastY = ret.pageY;
					lastX = ret.pageX;
					
					
					$('.currentclipart').focus();
					
					ret.preventDefault();
					//console.log( lastY );	
				}
			
		});
		
	}
	
	
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
				
				helpertop = parseInt(  $('#helper').css('top') );
				helperleft = parseInt(  $('#helper').css('left') );
				helpertwidth = parseInt(  $('#helper').css('width') );
				helperheight = parseInt(  $('#helper').css('height') );
				
				$('#template-wrapper').width( $('#template').width() );
				$('#template-wrapper').height( $('#template').height() );

		}
		
		
		if( $('.row-offcanvas').hasClass('active') ){
			$('.row-offcanvas').toggleClass('active');
		}
		
		$(window).scrollTop(100);
		
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
			
			var matrix = new Array(  $(this).panzoom("getMatrix") );
			var textwidth =  $(this).width()  *  matrix[0][0];
			var textheight =  $(this).height()  *  matrix[0][0];
			
			
			text.x = position.left * ratioWidth;
			text.y = position.top * ratioHeight;
			text.dx = textwidth * ratioWidth;
			text.dy = textheight * ratioHeight;
			
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