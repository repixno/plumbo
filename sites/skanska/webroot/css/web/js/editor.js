	var canvas = null;
	//var activeclipart;
	/*var text = new fabric.Text('hello world', { left: 150, top: 150 });
	canvas.add(text);*/
	var userimg, template, rotateval, zoomval;
	var text = new Array();
	var textgroup = new fabric.Group( );
	var yeargroup = new fabric.Group( );
	
	//var selectclipart = false;
    
	$( function(){
            
                canvas = new fabric.Canvas('c');
                
                //var wrapper = $('<div/>').css({height:0,width:0,'overflow':'hidden'});
                //var fileInput = $(':file').wrap(wrapper);
                
                /*fileInput.change(function(){
                    $this = $(this);
                    $('#file').text($this.val());
                })*/
                
                $('#file').click(function(){
                    fileInput.click();
                    return false;
                }).show();
		
		$(document).on( 'click', '.disabled' , function(){return false;});
		
		$(document).on( 'click', '#imageval', function(){ $('.imageval').show(); $('.textval').hide(); return false;});
		$(document).on( 'click', '#textval', function(){ $('.imageval').hide(); $('.textval').show(); return false;});
		
		$( "#zoom" ).slider( {
			slide: zoom,
			change: zoom,
			value: 100,
			min: 1,
			max: 200,
			disabled: 'true'
			});
		$( "#rotate" ).slider({
			slide: rotate,
			change: rotate,
			value: 0,
			min: -180,
			max: 180,
			disabled: 'true'
			});
		
		$(document).on( 'mouseout', '.canvas-container', donedit );
      
		$('#deletebutton').hide();
		$(document).on("click", ".clipart", function(){
			addClipart( $(this).attr('id') );
		});
		
		canvas.findTarget = (function(originalFn) {
			return function() {
			  var target = originalFn.apply(this, arguments);
			  if (target) {
			    if (this._hoveredTarget !== target) {
			      canvas.fire('object:over', { target: target });
			      if (this._hoveredTarget) {
				canvas.fire('object:out', { target: this._hoveredTarget });
			      }
			      this._hoveredTarget = target;
			    }
			  }
			  else if (this._hoveredTarget) {
			    canvas.fire('object:out', { target: this._hoveredTarget });
			    this._hoveredTarget = null;
			  }
			  return target;
			};
		      })(canvas.findTarget);
		
		canvas.on('object:over', function(e) {
			activeclipart = e;
			//console.log( "INN"  );
			if( e.target.id == 'clipart' ){
				selectclipart = true;
			}else{
				selectclipart = false; 
			}
		}); 
		canvas.on('object:out', function(e) {
			if( activeclipart.target.top == e.target.top ){
				if( e.target.id == 'clipart' ){
					selectclipart = false;
				}
			}
		});
		
		$('#imageupload').change(function() {
			$('.imagethumb').css( 'border-color', "#fff" );
			$(this).upload('/api/1.0/tedit/upload', function(res) {
				//console.log( res.name ); 
				var image = '<img class="imagethumb" id="' + res.name + '"src="/tedit/thumbs/' + res.name +'"  />';
				//image.attr( 'id', res.name );
				//image.attr( 'src' , "/tedit/thumbs/" + res.name  );
                                $('#imagelist').append( image );
				ChangeImageSrc( res.name );
				//$( "#zoom" ).slider( "option", "disabled", false );
				//$( "#rotate" ).slider( "option", "disabled", false );
                                $('img#' + res.name).css( 'border-color', "#0199d8" );
			}, 'json');
		    });
		
                $(document).on( 'click', '.imagethumb', function(){
                        $('.imagethumb').css( 'border-color', "#fff" );
                        $(this).css( 'border-color', "#0199d8" );
                        ChangeImageSrc( $(this).attr('id') );
                    });
                
		fabric.Image.fromURL( malimage, function(img) {
			template = img.set({
			  left: 219.5,
			  top: 219.5,
			  selectable: false
			});
			canvas.add( img );
		    });
		fabric.Image.fromURL(blankimage, function(img) {
			userimg = img.set({
				left: 220,
				top: 220,
				id: "preimage",
				lockUniScaling : true,
				selectable : false,
				textShadow: 'rgba(0,0,0,0.2) 0 0 5px'
			});
			//console.log( userimg );
			canvas.add( img );
			userimg.sendBackwards();
		    });
		
		canvas.on('mouse:down',function(options) {
			
			var x = options.e.layerX;
			var y = options.e.layerY;
			var center = 220;
			var radius = 110;
			if( ( ( ( x- center) * ( x- center) ) + ( (y - center) * (y - center) )  ) < ( radius * radius )  ){
				if( userimg.id == 'preimage'){
					
					$('#imageupload').click();
					
				}else{
					userimg.set( { opacity: 0.7 })
					userimg.bringToFront();
					canvas.setActiveObject(userimg);	
				}
	
			}
			else{
				/*if( userimg.active == true ){
					userimg.set( { opacity: 1 });
					userimg.sendBackwards();
					userimg.active = false;
				}*/
			}
			
			
				
		})
		
		
		canvas.on('before:selection:cleared', function(options){
				if( userimg.active == true ){
					userimg.set( { opacity: 1 });
					userimg.sendToBack();
					//userimg.active = false;
				}
			});
		/*canvas.on( 'selection:cleared' , function(){
			$('#deletebutton').hide();
			});*/
		
		//$(document).on( 'click' , '.image', ChangeImageSrc );
		
		//$('#zoom').val( 50 );
		
		$( '#editimage' ).on( 'click', function(){
			userimg.set( { opacity: 0.7 })
			//console.log( "true?" );
			userimg.bringForward();
			canvas.setActiveObject(userimg);	
		});
		
		/*$('#name').keyup(function() {
			//this.value = this.value.toUpperCase();
			drawTextAlongArc();
		});*/
		
		
		/*$(document).keydown(function(e) {
			switch( e.which ){
				case 38:
					$('#up').click();
					return false;
					break;
				case 40:
					$('#down').click();
					return false;
					break;
					
				case 39:
					$('#right').click();
					break;
				case 37:
					$('#left').click();
					break;
				case 107:
				case 187:
					$( '#zooml' ).click();
					break;
				case 109:
				case 189:
					$( '#zooms' ).click();
					break;
				case 13:
					$( '#doneedit' ).click();
					var activeObject = canvas.getActiveObject();
						if( activeObject ){
							activeObject.active = false;
							canvas.renderAll();
						}
						$('#deletebutton').hide();
					break;
				case 46:
				case 110:
					var activeObject = canvas.getActiveObject();
					if( activeObject ){
						activeObject.remove();	
					}
                                        return false;
					
					break;
				default:	
					//console.log(  e.which  );
			}
		    });*/
		
		
		
		$(document).on( 'click', '#doneedit', donedit );
		
		
		$( "#zooml" ).on( 'click',  function(){
			var active = canvas.getActiveObject();
			if( !active ){
				active = userimg;
			}
		
			var scaleX = active.scaleX + scale;
			var scaleY = active.scaleY + scale;
			
			active.set( { scaleX: scaleX, scaleY: scaleY } );
			canvas.renderAll();
		});
		$( "#zooms" ).on( 'click',  function(){
			
			var active = canvas.getActiveObject();
			if( !active ){
				active = userimg;
			}
			var scaleX = active.scaleX - scale;
			var scaleY = active.scaleY - scale;
			
			active.set( { scaleX: scaleX, scaleY: scaleY } );
			canvas.renderAll();
		});
		
		
		
		
		$(document).on( 'click', '.move' , function(){
			var direction = $(this).attr( 'id' );
			var active = canvas.getActiveObject();
			if( !active ){
				active = userimg;
			}
			var left = active.left;
			var top = active.top;			
			//console.log( direction );
			if( active.id != 'preimage' ){
			
				if( direction == 'up' ){
					top = top - 5;
				}
				else if( direction == 'left' ){
					left = left - 5;
				}
				else if ( direction == 'right' ){
					left = left + 5;
				}
				else if( direction == 'down' ){
					top = top + 5;
				}
			}
			active.set( { top: top, left: left } );
			canvas.renderAll();	
		});
		
		$(document).on( 'change', '#zoom', function(){
			var zommval = $(this).val() / 100;
			userimg.set( { scaleX: zommval, scaleY: zommval } );
			//console.log( $(this).val() );
			canvas.renderAll();
			
		});
		
		
		$('.rotate').on( 'click', function(){
			if( $(this).attr('id')  == 'roteteright' ){
				var newangle = userimg.angle + 5;
			}
			else{
				var newangle = userimg.angle - 5;
			}
			
			if( newangle >= 360 ){
				newangle = 360 - newangle;
			}
			userimg.set( { angle: newangle } );
			canvas.renderAll();
			
		});
		
		//$(document).on( 'change', '#name', drawTextAlongArc );
		
		$('#name').keyup(drawTextAlongArc);
		
		$('#name').focus( function(){
			if( $(this).val() == "LEVERPOSTEI" ){
				$(this).val( '' );
			}
		});
		$('#year').focus( function(){
			if( $(this).val() == "1949" ){
				$(this).val( '' );
			}
		});
		
		
		$(document).on( 'change', '#year', drawYearAlongArc );
		$(document).on( 'click', '#year', function(){ return false} );
		
		$(document).on( 'click', '#download', function(){
			var activeObject = canvas.getActiveObject();
			if( activeObject ){
				activeObject.active = false;
				canvas.renderAll();
			}
			//var data = canvas.toJSON();
			var thumb = canvas.toDataURL();
			
			var name = "skanska";
			
			$.post('/api/1.0/skanska/skanskahumb', {
			    name: name,
			    image: thumb
			    }, function (response) {
				downloadURL("/bestill/thumb/" + response.data + "/leverposteilokk.png");
				/*window.open(
					"/bestill/thumb/" + response.data + "/leverposteilokk.png",
					'_blank'
				);*/
				//console.log( response.data );
			}, 'json');
                        return false;
			//canvas.toDataURL('jpg')
			//console.log( canvas.toDataURL('jpg') );	
		});
                
                
                $(document).on( 'click', '#share', function(){
                        var activeObject = canvas.getActiveObject();
			if( activeObject ){
				activeObject.active = false;
				canvas.renderAll();
			}
			//var data = canvas.toJSON();
			var thumb = canvas.toDataURL();
			
			var name = "skanska";
			$.post('/api/1.0/tedit/skanskathumb', {
			    name: name,
			    image: thumb
			    }, function (response) {
                                $('#sharewindow').dialog( {
                                    width: 640,
                                    height: 600,
                                    title: "Del ditt leverposteilokk",
                                    open: function(event, ui) {
                                        $(".ui-dialog-content").css({ overflow: 'hidden' })
                                    },
                                    beforeClose: function(event, ui) {
                                        $(".ui-dialog-content").css({ overflow: 'auto' })
                                    }
                                    });
                                
                                $('#sharewindow iframe').attr( 'src', "/skanska/share/" + response.data )
                            
				/*indow.open(
					"/stabburet/share/" + response.data + "/leverposteilokk.png",
					'_blank'
				);*/
			}, 'json');
                    });
                
                
		$('.bestillknapp').on( 'click', save );
		
		/*$(document).on( 'click' , '#deletebutton' ,function(){
			var activeObject = canvas.getActiveObject();
			if( activeObject.id == 'clipart' ){	
				activeObject.remove();
			}
			});*/
		
	});
	
	function donedit(){
		if( userimg.active == true ){
			//console.log( 'mouse out' );
			userimg.set( { opacity: 1 });
			userimg.sendToBack();
			userimg.active = false;
			canvas.renderAll();
		}
	}
	
	function zoom(){
		zommval = $( "#zoom" ).slider( "value" ) / 100;
		if( zommval > 1 ){
			zommval = zommval * zommval;
		}
		userimg.set( { scaleX: zommval, scaleY: zommval } );
		canvas.renderAll();
	}
	
	function rotate(){
		rotateval = $( "#rotate" ).slider( "value" );
		userimg.set( { angle: rotateval  } );
		//console.log( $(this).val() );	
		canvas.renderAll();
	}
	
	$(window).on( 'load' , function(){
                        
			drawTextAlongArc();
			drawYearAlongArc();
			
			setTimeout(function (){
				canvas.renderAll();
			},150);
		});

	function ChangeImageSrc( name ){
		canvas.remove( userimg );
		var newsrc2 = $(this).attr('src');
		var newsrc = "/images/stream/image/" + name  + '/400/400';
		fabric.Image.fromURL(newsrc , function(img) {			
			userimg = img.set({
			  left: 220,
			  top: 220,
			  lockUniScaling : true,
			  id: name
			});
			//console.log( userimg );
			canvas.add( img );
			userimg.sendToBack();
		});
		
	}
	
	function positionBtn(obj) {
		$('#deletebutton').show()
		var offset = $('#c').offset();
		$('#deletebutton').css( { top: ( obj.top  + offset.top - (obj.currentHeight / 2 ) - 30 ) + 'px', left: ( obj.left + offset.left +  (obj.currentWidth / 2 ) + 15 )  + 'px' });
		}
	
	function drawTextAlongArc(){
		var testtext = '';
		var step = 0;
		var textobjects = textgroup.getObjects();
		var tolen = textobjects.length;
		while (tolen>0){
			textobjects = textgroup.getObjects();
			tolen = textobjects.length;
			for( var i = 0; i<tolen; i++ ){
				textgroup.remove( textobjects[i] );
				//console.log( textobjects[i] );
			}
			
		}
		var str = $('#name').val();
		//var str = "LEVERPOSTEI";
		if( str.length < 1 ){
			str = "LEVERPOSTEI";
		}
		var len = str.length;
		var s, move = 0 ;
		var left = -140;
		left = left + ( ( 12 - len ) * 11 );
		
		if( left < -140){
			left = -140;
		}
		
		angle = Math.PI * ( len / 12 ) ;
		var totalwidth = 0;
		
		
		for(var n = 0; n < len; n++) {
			var radius = 145;
			var center = 218;
			var rotate =  angle / len * 100;

			left = left +  move;
			var top  = ( radius * radius ) - ( left * left );
			top = Math.sqrt(top);
			var sinusa = left / radius;
			sinusa = Math.acos(sinusa) * (180 / Math.PI) - 90 ;
			
			text[n] = new fabric.Text(
						str[n].toUpperCase(),
						{
							fontSize: 55,
							fontFamily: 'FairplexWideOT-Med',
							fill: '#ffffff',
							//textShadow: 'rgba(0,0,0,0.2) 0px 5px 5px',
							textAlign: 'center'
						}
						);
			totalwidth += text[n].width + 8;
			
			text[n].set( {left: left + center,top: top + center, angle: sinusa} );
			
			var angle = text[n].angle;
			
			//console.log( angle );
			
			
			
                        var testangle = angle;
			if( angle < 5 ){
				angle = -angle + ( text[n].width / 2 );
			}
			
			
			if( len > n + 1  ){
				testtext = new fabric.Text(
						str[n + 1].toUpperCase(),
						{
							fontSize: 55,
							fontFamily: 'FairplexWideOT-Med',
							angle: sinusa,
							fill: '#ffffff',
							//textShadow: 'rgba(0,0,0,0.2) 0px 5px 5px',
							textAlign: 'center'
						});
			}
			
                        if(testangle < 10 ){
                            step = 1; 
                        }
			else if( angle > 60){
				step = 3;
				//console.log( "step" );
			}
			else{
				step = 3;
			}
	
			var c1 = ( ( text[n].width / 2 ) + step )  *  Math.cos( sinusa / (180 / Math.PI) );
			var c =  ( ( testtext.width / 2 ) + step )  *  Math.cos( angle / (180 / Math.PI) );

                        if( sinusa < -60 ){
				$('#name').attr( 'maxlength', len );
			}
			else{
				$('#name').attr( 'maxlength', 111 );
			}
			
			move = c + c1;
			textgroup.add( text[n]  );
		}
		
		canvas.add( textgroup );
		canvas.renderAll();		
		textgroup.bringToFront();
                
                return false;

	}
	
	
	
	
	function drawYearAlongArc() {
		var textobjects = yeargroup.getObjects();
		var tolen = textobjects.length;
		while (tolen>0){
			textobjects = yeargroup.getObjects();
			tolen = textobjects.length;
			for( var i = 0; i<tolen; i++ ){
				yeargroup.remove( textobjects[i] );
				//console.log( textobjects[i] );
			}
			
		}
		var str = $('#year').val();
		//var str = "LEVERPOSTEI";
		if( str.length < 1 ){
			str = "ORIGINALEN SIDEN 1949";
		}else{
			str = "ORIGINALEN SIDEN " + str; 
		}
		var len = str.length;
		var s, move = 90 ;
		var left = -118;
		left = left;
		
		//console.log( left );
		
		angle = Math.PI * ( len / 12 ) ;
		
		for(var n = 0; n < len; n++) {
			var radius = 189;
			var center = 218;
			var rotate =  angle / len * 100;
			move = 17;
			left = left + ( move  / 1.6 );
			var top  = ( radius * radius ) - ( left * left );
			top = Math.sqrt(top);
			var sinusa = left / radius;
			sinusa = Math.acos(sinusa) * (180 / Math.PI) - 90 ;
			move = Math.round( sinusa );

			
			//console.log( left );
			//console.log( top );
			//console.log( sinusa);
			
			text[n] = new fabric.Text(
						str[n].toUpperCase(),
						{
							left: left + center,
							top: top + center,
							fontSize: 20,
							fontFamily: 'FairplexWideOT-Med',
							angle: sinusa,
							fill: '#e3202d'
						}
						);
			
			yeargroup.add( text[n]  );

		}
		
		canvas.add( yeargroup );
		canvas.renderAll();		
		yeargroup.bringToFront();
                
                return false;

	}
	
	this.save = function(category , name ){
		showloader();
                //canvas.deactivateAll();
		userimg.set( { opacity: 1 });
		userimg.sendToBack();
		userimg.active = false;
		var objects = canvas.getObjects();
		var imgsrc = objects[0]._element;
		
		//console.log( objects[0] );

		var imgpos = JSON.stringify(  '[' + objects[0].left + ',' + objects[0].top + ',' + objects[0].currentWidth + ',' + objects[0].currentHeight + ',' + objects[0].angle +']' );
		var malsize = $('#c').height();
		
		//console.log( imgpos );
		var name = $('#name').val();
                var year = $('#year').val();
                //var thumb = canvas.toDataURL();
                var thumb = canvas.toDataURLWithMultiplier('jpeg', 0.8, 90);
                var lokksize = $('#lokksize:checked').val();
		var imageid = objects[0].id;
		
		$.post('/api/1.0/skanska/save', {
		    name: name,
		    year: year,
		    imageid: imageid,
		    imgpos: imgpos,
		    thumb: thumb,
		    malsize: malsize,
		    lokksize: lokksize
		    }, function (response) {
			window.location.href = '/checkout/';
		}, 'json');
		
	    
		
            return false;
        }
	
	
	function shareThumb( media ){
		var thumb = canvas.toDataURL();
		var sharelink = '';
		if( media == 'facebook' ){
			sharelink = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent('https://stabburetbeta.eurofoto.no/bestill/ShareFacebook/');
		}

		$.post('/api/1.0/skanska/savethumb', {
		    thumb: thumb
		    }, function (response) {
			/*window.open(
				sharelink + response.data,  
				'facebook-share-dialog', 
				'width=626,height=436');*/
			
			upenurl( sharelink + response.data );
			hideloader();
		}, 'json');

		//return thumbid;
	}
	
	
	// Quick address bar hide on devices like the iPhone dont work on fuckings android
	//---------------------------------------------------
	function quickHideAddressBar() {
		setTimeout(function() {
			if(window.pageYOffset !== 0) return;
			window.scrollTo(0, window.pageYOffset + 1);
	
		}, 1000);
	
	}
	
	function showloader(){  
		$('body').prepend( '<div id="loader-overlay"><div class="loadingInfo"><img src="http://static.repix.no/gfx/gui/ajax-loader-gray.gif" />\
                                <br/><br/><h3>Loading.......<span id="status"></span></h3></div></div>');
                                
                $('#loader-overlay')
			.css('opacity', '0.8')
			.css('margin','0')
			.width( $(window).width() )
                        .height( $(document).height() );
                                        
		return false;
        }
                                    
	function hideloader(){
		$('#loader-overlay').fadeOut('slow', function() {
			$(this).remove();
		});
	}
	
	function downloadURL(url) {
		  if ($idown) {
		    $idown.attr('src',url);
		  } else {
		    $idown = $('<iframe/>', { id:'idown', src:url }).hide().appendTo('body');
		  }
		}
	

	function upenurl(href) {
	  var link = document.createElement('a');
	  link.setAttribute('href', href);
	  link.setAttribute('target','_blank');
	  var clickevent = document.createEvent('Event');
	  clickevent.initEvent('click', true, false);
	  link.dispatchEvent(clickevent);
	  return false;
	}
	    