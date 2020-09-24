    fabric.Object.prototype.originX = fabric.Object.prototype.originY = 'center';

	var canvas = null;
	//var activeclipart;
	/*var text = new fabric.Text('hello world', { left: 150, top: 150 });
	canvas.add(text);*/
	var userimg, template, rotateval, zoomval, namelength;
    var zoomingtimer = false;
	var text = new Array();
	var textgroup = new fabric.Group( );
	var yeargroup = new fabric.Group( );

	//var selectclipart = false;

	$( function(){


		if (!isCanvasSupported()){
			$('.editorbox').append('Editoren støttes dessverre ikke av din nettleser. Du kan laste ned en gratis nettleser, vi anbefaler Google Chrome.<br/>\
			Last ned her <a href="http://www.google.com/intl/no/chrome/browser/" target="blank">http://www.google.com/intl/no/chrome/browser/</a>');
		}

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

		canvas.on('touch:drag',function(options) {


			if(userimg.active){
				return  true;
			}


			var x = options.e.layerX;
			var y = options.e.layerY;
			var center = 220;
			var radius = 110;


			if( ( ( ( x- center) * ( x- center) ) + ( (y - center) * (y - center) )  ) < ( radius * radius )  ){
				if( userimg.id == 'preimage'){

					$('#velgbilde').click();

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


        var lastTouch;
        $(document).on( 'touchend',  function(e){
            var now;
            now = new Date().getTime();
            var delta = now - lastTouch;
            if( delta < 500 ){
                e.preventDefault();
            }
            lastTouch = now;
        });


        /*$(document).bind('touchend', function(event){
               var now = new Date().getTime();
               var lastTouch = $(this).data('lastTouch') || now + 1;
               var delta = now - lastTouch;
               if(delta<500 && delta>0){
                       // the second touchend event happened within half a second. Here is where we invoke the double tap code
               }else{
                       // A click/touch action could be invoked here but wee need to wait half a second before doing so.
               }
               $(this).data('lastTouch', now);
        }*/

		$(document).on( 'click', '#doneedit', donedit );

		$('.zoom').bind('touchstart', function(){
			zoomingtimer = true;
			newzoom( $(this).attr('id') );
			return false;
		}).mousedown( function(){
			zoomingtimer = true;
			newzoom( $(this).attr('id') );
			return false;
		}).bind(
			'touchend',
			stopzoom ).mouseup(
			stopzoom
		).mouseleave(
			stopzoom
		);


		$('.move').bind('touchstart', function(){
			zoomingtimer = true;
			movenew( $(this).attr('id') );
			return false;
		}).mousedown( function(){
			zoomingtimer = true;
			movenew( $(this).attr('id') );
			return false;
		}).bind(
			'touchend',
			stopzoom ).mouseup(
			stopzoom
		).mouseleave(
			stopzoom
		);

		$('.rotate').bind('touchstart', function(){
			zoomingtimer = true;
			newrotate( $(this).attr('id') );
			return false;
		}).mousedown( function(){
			zoomingtimer = true;
			newrotate( $(this).attr('id') );
			return false;
		}).bind(
			'touchend',
			stopzoom ).mouseup(
			stopzoom
		).mouseleave(
			stopzoom
		);
		//$(document).on( 'change', '#name', drawTextAlongArc );

		$('#name').keyup( drawTextAlongArc );

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
			showloader();
			var activeObject = canvas.getActiveObject();
			if( activeObject ){
				activeObject.active = false;
				canvas.renderAll();
			}
			//var data = canvas.toJSON();
			var thumb = canvas.toDataURL();

			var name = "stabburet";

			$.post('/api/1.0/stabburet/stabburetthumb', {
			    name: name,
			    image: thumb
			    }, function (response) {
				downloadURL("/bestill/download/" + response.data + "/leverposteilokk.png");
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

			var name = "stabburet";
			$.post('/api/1.0/tedit/stabburetthumb', {
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

                                $('#sharewindow iframe').attr( 'src', "/stabburet/share/" + response.data )

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

	    function stopzoom(){
        zoomingtimer = false;
    }
    function newzoom( zoom ){

        //console.log( zoom );
        var scale = userimg.scaleX;
        if( scale == null ){
            scale = 1;
        }
        var step =  scale / 100;
        if (step < 0) {
            step = -step;
        }
        if( zoom == 'zooml' ){
            userimg.scale( scale + step ).setCoords();
            if( zoomingtimer ){
                setTimeout( 'newzoom("zooml")', 20 );
            }
        }else if( zoom == 'zooms' ){
            userimg.scale( scale - step ).setCoords();
            if( zoomingtimer ){
                setTimeout( 'newzoom("zooms")', 20 );
            }
        }
        if( userimg instanceof Object == true   ){
                $('.zoomlayer-out').css( {
                                       "top" :userimg.top - ( userimg.height * userimg.scaleX / 2 ) + 18 ,
                                       "left" :userimg.left - ( userimg.width * userimg.scaleX / 2 ),
                                       "width" : userimg.width * userimg.scaleX,
                                       "height" : userimg.height * userimg.scaleY,
                                       }  );

        }
        canvas.renderAll();
    }



    function movenew( direction ){

        //console.log(direction);

        donedit();
        var active = canvas.getActiveObject();
        if( !active ){
            active = userimg;
        }
        var left = active.left;
        var top = active.top;
        //console.log( direction );
        if( active.id != 'preimage' ){

            if( direction == 'up' ){
                top = top - 1;
                if( zoomingtimer ){
                    setTimeout( 'movenew("up")', 20 );
                }
            }
            else if( direction == 'left' ){
                left = left - 1;
                if( zoomingtimer ){
                    setTimeout( 'movenew("left")', 20 );
                }
            }
            else if ( direction == 'right' ){
                left = left + 1;
                if( zoomingtimer ){
                    setTimeout( 'movenew("right")', 20 );
                }
            }
            else if( direction == 'down' ){
                top = top + 1;
                if( zoomingtimer ){
                    setTimeout( 'movenew("down")', 20 );
                }
            }
        }
        active.set( { top: top, left: left } );
        canvas.renderAll();
    }


    function newrotate( direction ){

        //console.log(direction);

        donedit();
        if( direction == 'rotateright' ){
            var newangle = userimg.angle + 1;
            if( zoomingtimer ){
                setTimeout( 'newrotate("rotateright")', 20 );
            }
        }
        else{
            var newangle = userimg.angle - 1;
            if( zoomingtimer ){
                setTimeout( 'newrotate("rotateleft")', 20 );
            }
        }

        if( newangle >= 360 ){
            newangle = 360 - newangle;
        }
        userimg.set( { angle: newangle } );
        canvas.renderAll();

    };

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
        var calctext = new Object();
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
		angle = Math.PI * ( len / 12 ) ;
		var totalwidth = 0;

        for(var n = 0; n < len; n++) {
            calctext[n] = new fabric.Text(
						str[n].toUpperCase(),
						{
							fontSize: 55,
							fontFamily: 'FairplexWideOT-Med',
							fill: '#ffffff',
							//textShadow: 'rgba(0,0,0,0.2) 0px 5px 5px',
							textAlign: 'center'
						}
						);
			totalwidth += calctext[n].width + 8;
        }
        left = -( totalwidth / 3.17 );

        if( left < -140){
			left = -140;
		}

        totalwidth = 0;

		for(var n = 0; n < len; n++) {
			var radius = 150;
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
			totalwidth += text[n].width + 10;

            namelength = totalwidth;

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

        if( userimg.id  == "preimage" ){
			//alert( "Det må velges et bilde" );
			return false;
		}
		else{

			showloader();
			//canvas.deactivateAll();
			userimg.set( { opacity: 1 });
			userimg.sendToBack();
			userimg.active = false;
			var objects = canvas.getObjects();
			var imgsrc = objects[0]._element;


			var currentWidth = objects[0].width * objects[0].scaleX;
			var currentHeight = objects[0].height * objects[0].scaleY;

			var imgpos = JSON.stringify(  '[' + objects[0].left + ',' + objects[0].top + ',' + currentWidth + ',' + currentHeight + ',' + objects[0].angle +']' );
			var malsize = $('#c').height();

			//var thumb = canvas.toDataURL();
            var name = $('#name').val();
            var year = $('#year').val();
			var thumb = canvas.toDataURLWithMultiplier('jpeg', 0.8, 90);

      var lokksize = $('input[name="lokksize"]:checked').val();

			var imageid = objects[0].id;

			$.post('/api/1.0/stabburet/save', {
					name: name,
          year: year,
					imageid: imageid,
					imgpos: imgpos,
					thumb: thumb,
					malsize: malsize,
					lokksize: lokksize
			    }, function (response) {
				window.location.href = '/lag-lokk/checkout/bestilling';
			}, 'json');
            return false;
		}
    }


	function shareThumb( media ){
		var thumb = canvas.toDataURL({format: 'png'});
		var sharelink = '';
		if( media == 'facebook' ){
			sharelink = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent('http://stabburetbeta.eurofoto.no/bestill/ShareFacebook/');
		}

		$.post('/api/1.0/stabburet/savethumb', {
		    thumb: thumb
		    }, function (response) {
			var u = 'http://stabburetbeta.eurofoto.no/bestill/ShareFacebook/' + response.data;

			onResultPageGenerated( u );

		}, 'json');

		//return thumbid;
	}


    function onResultPageGenerated ( u ){
        console.log(u);
        sharelink = 'https://www.facebook.com/sharer/sharer.php?u=';
        $.post(
            'https://graph.facebook.com',
            {
                id:u,
                scrape: true
            },
            function(response){
                setTimeout(function() {

					$('.loadingInfo').html('<div><h3>Lokket er nå klart til å kunne deles</h3></div><div><a class="btn large" href="' + sharelink + u + '" onclick="window.open(this.href, \'mywin\', \'left=20,top=20,width=500,height=500,toolbar=1,resizable=0\'); hideloader(); return false;" >\
										   <span><img src="http://static.repix.no/cms/images/del-facebook-2.jpg"/></span> Del lokket</a></div>');
                    //window.open(sharelink + u, "popupWindow", "width=600,height=600,scrollbars=no");
                    //hideloader();
                }, 500);
            }
        );
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
		$('body').prepend( '<div class="loadercontainer"><div class="loadingInfo"><img src="https://static.repix.no/gfx/gui/ajax-loader-gray.gif" />\
                                <br/><br/><h3>Loading.......<span id="status"></span></h3></div><div id="loader-overlay"></div></div>');

			$('.loadercontainer').width( $(window).width() )
                        .height( $(document).height() );
			$('.loadingInfo').width( $(window).width() )
                        .height( $(document).height() );

            $('#loader-overlay')
			.css('opacity', '0.8')
			.css('margin','0')
			.width( $(window).width() )
                        .height( $(document).height() );

		return false;
        }

	function hideloader(){
		$('.loadercontainer').fadeOut('slow', function() {
			$(this).remove();
		});
	}

	function downloadURL(url) {

		  /*if ($idown) {
		    $idown.attr('src',url);
		  } else {
		    $idown = $('<iframe/>', { id:'idown', src:url }).hide().appendTo('body');


		  }
		  */

		  $('.loadingInfo').html('<div><h3>Lokket er nå klart til å kunne lastes ned</h3></div><div><a class="btn large" href="' + url + '" target="_blank" onclick="hideloader();" >\
										   Last ned lokket</a></div>');
		}


	function upenurl(href) {

        window.open(href, "_blank", "toolbar=yes, scrollbars=yes, resizable=yes, top=500, left=500, width=400, height=400");

        return false;
	  var link = document.createElement('a');
	  link.setAttribute('href', href);
	  link.setAttribute('target','_blank');
	  var clickevent = document.createEvent('Event');
	  clickevent.initEvent('click', true, false);
	  link.dispatchEvent(clickevent);
	  return false;
	}


    function isCanvasSupported(){
        var elem = document.createElement('canvas');
        return !!(elem.getContext && elem.getContext('2d'));
    }
