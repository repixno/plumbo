	var me = this;
    var helpertop;
    var helperleft;
    var helpertwidth;
    var helperheight;
	
	$(document).ready( function() {
		// initializing the user interface
		$('#font-list').fontmenu();
		$('.template').first().show().addClass( 'selected-template' );
		$('.select-page').first().addClass('selected-pageselect');
		
		
		
		$( "#zoom-range" ).slider({
			min: 0.05,
			max: 4,
			step: 0.05,
			slide: function( event, ui ) {
				
				
				$('.selected-template .helper').panzoom('zoom',ui.value )
				
				//console.log(ui.value);
				
				
			}
			});
		
		$('button.zooms').on('mousedown touchstart' ,function(event){event.preventDefault()} );
		
		$('.zooms').on('mousedown touchstart', function(event) {
			
			holdbutton = true;
			timeout($(this));
		}).on('mouseup mouseleave touchend', function() {
			holdbutton = false;
		});
		
		
		$(document).on( 'touchend', function(){
			holdbutton = false;
			});
		
		$('#nextindex').on( 'click', function(){
			
				var selectedindex = $('.selected-pageselect').attr('index');
			
				selectedindex++;
			
				$("a[index='"+selectedindex+"']").click();
			
				return false;
			});
		$('#previndex').on( 'click', function(){
			
				var selectedindex = $('.selected-pageselect').attr('index');
			
				selectedindex--;
			
				$("a[index='"+selectedindex+"']").click();
			
				return false;
			});
		
		
		$('.select-page').on( 'click', function(){
			
			
			//$('.buttons').addClass('hide');
			
			$('.selected-template').find('.currentclipartholder').each( function(){
					var $thisclipart = $(this).find('.currentclipart');
					var height = $thisclipart.height();
					$thisclipart.css('height', height );
				});
			
			$('.selected-template').find('.textholder').each( function(){
					var $thistext = $(this).find('.currenttext ');
					$thistext.css('height', $thistext.height() );
				});
			
			
			$('.select-page' ).each( function(){ $(this).removeClass('selected-pageselect') } );
			
			$(this).addClass('selected-pageselect');
			
			//console.log( $(this).attr('id') );
			
			var pageidarray = $(this).attr('id').split("-");
			//console.log( pageidarray[2] );
			
			
			$("." + pageidarray[2] + "-zoom-in").parent().removeClass( 'hide' );
			
			$('.template').each( function(){
				
					$(this).hide().removeClass( 'selected-template' );
				
				
				});
			
			$('.template-wrapper-' + pageidarray[2] ).show().addClass( 'selected-template' );
			
			if( !$('.selected-template').hasClass('stretchToFit') ){
				stretchToFit();
			}else{
				//stretchToFit();
			}
			
            //$('.selected-template').find(".helper").width( $('#image').width() );
            //$('.selected-template').find(".helper").height( $('#image').height() );
		
            
            /*$('.selected-template').find(".helper").panzoom({
                $zoomIn: $(".zoom-in"),
                $zoomOut: $(".zoom-out"),
                $zoomRange: $(".zoom-range"),
                $reset: $(".reset")
              });*/
			
			return false;
		});
		
		
		$('#accordion').accordion({
			'navigation'		:	true,
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
		
		var textID = 1;
		var highestIndex = 210;
		 
		$('#add-gift-to-cart').click( function() {
			
			
			var empty = new Array();
			var k = 0;
			$('.template').each( function(){
				
				var bildeid = $(this).find('#selected-image').val();
				
				k++;
				if( $(this).find('#image').hasClass('noimage') && bildeid == 0 ){
					
					empty.push(k);
				}
				
				
				}); 
			
			
			if( empty.length > 0 ){
				
				var missing = empty.join();
				
				$('#missing').text(missing);
				
				$( "#dialog-confirm" ).dialog({
					resizable: false,
					height:200,
					modal: true,
					buttons: {
						/*"Fortsett til handlekurven":function(){
							$( this ).dialog( "close" );
							me.save();
							},*/
						'OK': function() {
							$( this ).dialog( "close" );
						}
					}
				  });
				
				//console.log( empty  );
			}
			else{
				save();
			}
			
			
			
			return false;
		});
		
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
		
        
        
		// inserts the image to the editor when clicked
		$(document).on('click', '#album-images img', function() {
		
			$('.selected-template').prepend('<div id="template-ajax-loader"><img src="'+bigajaxLoaderGray+'"/></div>');
			
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
			$('.selected-template').find('#image').remove();
			$('.selected-template').find('#image-wrapper').append('<img id="image" />');
			// inserts the thumb for better expirience
			
			var img = new Image();
			$(img).load( function() {
				$('#template-ajax-loader').remove();
				stretchToFit();
			}).attr('src', getImage( $(this).attr('id') ) );
			
			$('.selected-template').find('#image').attr('src', $(this).attr('src') );
			
			// remembers the image id
			$('.selected-template').find('#selected-image').attr('value', $(this).attr('id') );
			
			// inserts the the real photo
			$('.selected-template').find('#image').attr(  'src', getImage( $(this).attr('id') ) );
			
			var backgroundImage = this;
			
			$('#choose-template .template-thumb').css('background-image', 'url('+ $(this).attr('src') +')' );

			$('.selected-template').find('#image').load( function() {
				//stretchToFit();
			});
			
			$('.selected-template input[type="range"].zooms').val(1);
			
			//stretchToFit();
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
		
		
		$(document).on('click', '#upload-queue img' , function() {
			
			// remove all the dom info about prev image
			var imageId = $(this).attr('id');
			$('.selected-template').find('#image').remove();
			
			
			// inserts the thumb for faster feedback
			$('.selected-template').find('#image').remove();
			$('.selected-template').find('#image-wrapper').append('<img id="image" />');
			
			var img = new Image();
			$(img).load( function() {
				$('#template-ajax-loader').remove();
				stretchToFit();
			}).attr('src', getImage( $(this).attr('id') ) );
			
			$(img).load( function() {
				$('.selected-template').find('#image-wrapper').append('<img id="image" />');
				
				$('.selected-template').find('#selected-image').attr('value', imageId );
				
				// inserts the the real photo
				$('.selected-template').find('#image').attr('src', getImage(imageId) );
				$('.selected-template .helper').width( img.width );
				$('.selected-template .helper').height( img.height );
				
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
			
			
				$(".activeText").data('text', text );
			
				$(currentText).data("width",$(currentText).width());
				$(currentText).data("height",$(currentText).height());
				$(currentText).data("font",font);
				$(currentText).data('gravity',gravity);
				$(currentText).data('color',color);
				$(currentText).data('text',text);
				$(currentText).data('zindex', highestIndex );
				
				
				//console.log( $(currentText).width() );
				//console.log($(currentText).height() );
				
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
			
			var Matrix = $('.activeText' ).panzoom("getMatrix");
			
			$('.activeText' ).panzoom("setMatrix", [ newzoom, 0, 0, newzoom, Matrix[4], Matrix[5] ] );
			
			
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

			$('.selected-template').append('<div id="textholder'+textID+'" class="textholder draggable activeText" data-text="' + text + '">\
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
      		//$('#text' + textID).attr('src', '/api/1.0/gift/text/?id=0&quality=1&png='+png+'&font='+font+'&gravity='+gravity+'&color='+color+'&text='+text+'&encoding=UTF8');
			var currentText = $('#textholder' + textID);
			
			$('#text' + textID).load(function(){
				$(currentText).data("width",$(currentText).width());
				$(currentText).data("height",$(currentText).height());
				
				setelementdata(currentText, [ 0.7, 0, 0, 0.7, 50, 50 ] );
				
			}).attr('src','/api/1.0/gift/text/?id=0&quality=1&png='+png+'&font='+font+'&gravity='+gravity+'&color='+color+'&text='+text+'&encoding=UTF8');
      		
      		
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
			
	
			
	
			
			$('input[name="text-size"').val(0.7);
			
      		//$('#new-text').val('');
      		textID++;
      		highestIndex++;
		    }
			
			
			
			
			if( $('.row-offcanvas').hasClass('active') ){
				$('.row-offcanvas').toggleClass('active');
			}
		});
		
		// sets the helper width as the same as image
		$('.selected-template').find(".helper").width( $('#image').width() );
		$('.selected-template').find(".helper").height( $('#image').height() );
		
        
        $('.helper').on('panzoomchange', function(e, panzoom, matrix, changed) {
			
			helpertwidth = $(this).width();
			helperheight = $(this).height();
			
			var matrixarray = new Array(matrix);

			var imgWidth = parseInt( $(this).width() ) * matrixarray[0][0];
			var imgHeight = parseInt( $(this).height() ) * matrixarray[0][0];
			
			var top =  parseInt(  $(this).css('top') ) + parseInt( parseInt( matrixarray[0][5] ) + ( ( helperheight - imgHeight ) / 2 ) );
			var left = parseInt( $(this).css('left') ) + parseInt( parseInt( matrixarray[0][4] )  + ( ( helpertwidth - imgWidth ) / 2 ) );
            
			$('.selected-template').find('#image').css({
				'top': top,
				'left': left,
				'margin-left':0,
				'margin-top' :0,
				'width': imgWidth,
				'height': imgHeight
			});
			
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
		
		$(document).on('click', '#clipart-selector .clipartitem-container', function() {
			
			var clipartID =  $(this).attr('id').replace('clipartitem-container_', '');
			var currentClipart = 'currentclipart'+clipartID+'-'+highestIndex;
			var currentResizable = 'currentResizable'+clipartID+'-'+highestIndex;
			var currentDraggable = 'currentDraggable'+clipartID+'-'+highestIndex;
			
			$('.selected-template').append('<div id="' + currentDraggable + '" class="currentclipartholder draggable" data-id="' + clipartID + '">\
											<div id="' + currentResizable + '" class="resizable">\
												<img id="' + currentClipart + '" class="currentclipart img"/>\
												<div class="ui-resizable-handle ui-resizable-ne" style="z-index: 1000;"></div>\
												<div class="ui-resizable-handle ui-resizable-nw delteclipart" style="z-index: 1000;">\
													<img src="' + staticsite + 'gfx/icons/delete.png" />\
													</div>\
											</div>\
											</div>');
			
			var highres = staticurl + "images/clipart/thumbs/width/300/" + clipartID.toString() + ".png";
			
			var $currentClipart = $('#currentclipart'+clipartID+'-'+highestIndex);
			
			$currentClipart.attr('src', highres);
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
			
			
			$('#' + currentDraggable ).panzoom("setMatrix", [ 0.4, 0, 0, 0.4, 50, 50 ]);
			
			
			$($currentClipart).on('load', function(){
					resizeElement( currentResizable, currentDraggable ); 
					highestIndex++;
				});
			
			
			
			
			
		});
		
		$('#select-album').trigger('change');
	  /* $('#image').load( function() {
			stretchToFit();
		});*/

	});
	
	
	$(window).load(
		function() {
			stretchToFit();
	});