fabric.Object.prototype.originX = fabric.Object.prototype.originY = 'center';

/**************************
 *Html5 Felix editor by TIL
 *
 *
 **/
(function($) {

	var FelixEditor = function(element, options) {

		var settings = $.extend({
			'width': 330,
			'height': 510,
			'id': $(element).attr('id'),
			'isMobile': false
		}, options || {});

		var _this = this;
		var canvas = new fabric.Canvas(settings.id);
		var text, $idown, imageid;
		var canvasimage = null;
		var templateimage = null;
		var textimage = null;
		this.canvas = canvas;

		var texttop = 300;
		var textheight = 250;
		var leftalign = 165;
		var productid = 7438;

		var editlock = false;

		var selectedtemplate = 'gul1.png';

		this.selectedtemplate = selectedtemplate;


		canvas.setHeight(510);
		canvas.setWidth(330);



		if (settings.isMobile) {
			canvas.setZoom(0.5);

			var cHeight1 = 254;
			var cWidth1 = 164;

			var cHeight05 = 237;
			var cWidth05 = 164;

			var cHeight125 = 270;
			var cWidth125 = 135;
		} else {
			var cHeight1 = 510;
			var cWidth1 = 330;

			var cHeight05 = 474;
			var cWidth05 = 329;

			var cHeight125 = 535;
			var cWidth125 = 267;

		}

		canvas.setHeight(cHeight1);
		canvas.setWidth(cWidth1);


		canvas.renderAll();
		//setBGcolor('#ffb815');

		canvas.setBackgroundImage('/felix/templates/background_gul1.png', canvas.renderAll.bind(canvas), {
			// Needed to position backgroundImage at 0/0
			originX: 'left',
			originY: 'top'
		})

		/*
		$('.1kg').on('click', function() {
			$('.btn-selected').removeClass('btn-selected');
			$(this).addClass('btn-selected');
			//$('#1kg, .insp-1').show();
			//$('#125kg, .insp-125').hide();
			_this.changeTemplate("gul1.png", "#ffb815", "1");

		});
		$('.125kg').on('click', function() {
			$('.btn-selected').removeClass('btn-selected');
			$(this).addClass('btn-selected');
			//$('#125kg, .insp-125').show();
			//$('#1kg, .insp-1').hide();
			_this.changeTemplate("gul125.png", "#ffb815", "125");
		})
		*/
		
		$('input[name="size"]').on('change', function() {

			var tempsize = $(this).val();
			$('.choosetemplate').hide();

			if (tempsize == '1kg') {
				$('.btn-selected').removeClass('btn-selected');
				$(this).addClass('btn-selected');
				$('#1kg, .insp-1').show();
				_this.changeTemplate("gul1.png", "#ffb815", "1");
			} else if (tempsize == '125kg') {
				$('.btn-selected').removeClass('btn-selected');
				$(this).addClass('btn-selected');
				$('#125kg, .insp-125').show();
				_this.changeTemplate("gul125.png", "#ffb815", "125");
			} else if (tempsize == '05kg') {
				$('.btn-selected').removeClass('btn-selected');
				$(this).addClass('btn-selected');
				$('#05kg, .insp-05').show();
				_this.changeTemplate("gul05.png", "#ffb815", "05");
			}

			//console.log($(this).val());

		})


		canvas.on('mouse:down', function(options) {

			var y = options.e.layerY;
			var top = 182;
			var topbottom = 317;
			if (y > top && y < topbottom && canvasimage) {

				if (canvasimage.selectable) {
					canvasimage.set({
						opacity: 0.7
					})
					canvasimage.bringToFront();
					canvas.setActiveObject(canvasimage);
				}
			} else {
				if (canvasimage.active == true) {
					canvasimage.set({
						opacity: 1
					});
					canvasimage.sendBackwards();
					canvasimage.active = false;
				}
			}
		})

		canvas.on('before:selection:cleared', donedit);


		$('#createown').on('click', function() {
			_this.changeTemplate('gul1.png', '#ffb815', "1");


		});

		$(document).on('mouseout', '.canvas-container', donedit);


		$('#sharefacebook').on('click', function() {
			showloader("Vänta, strax kommer du att kunna dela din etikett på Facebook…");
			shareThumb('facebook');
			return false;
		});


		$(document).on('click', '.shareonfacebook', function() {

			hideloader();

		});

		$(document).on('click', '#download', function() {
			showloader("Vänta, strax kommer du att kunna ladda ned din etikett...");
			var activeObject = canvas.getActiveObject();
			if (activeObject) {
				activeObject.active = false;
				canvas.renderAll();
			}
			//var data = canvas.toJSON();
			var thumb = canvas.toDataURL();

			var name = "felix";

			$.post('/api/1.0/felix/felixthumb', {
				name: name,
				image: thumb
			}, function(response) {
				downloadURL("/skapa/download/" + response.data + "/felix.jpg");
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

		/*$('#sharefacebook').on( 'click', function(){
			showloader();
			shareThumb( 'facebook' );
			return false;
		});*/


		$('#skapa').on('click', function() {
			skapa();
		})


		/*$('.uppdateratext').on( 'click', function(){
			_this.updateText($('.felixtext').val());
			});*/

		$('.felixtext').on('change', function(e) {
			editlock = true;
			var lines = $(this).val().split("\n");
			var newlines = '';

			for (var a = 0; a < lines.length; a++) {

				if (a < 5) {
					if (a + 1 == lines.length || a == 3) {
						newlines += lines[a];
					} else {
						newlines += lines[a] + "\n";
					}
				}

			}
			$(this).val(newlines);

			//var thistext = $('textarea[name="felixtext"]').val();
			_this.updateText(newlines);

		});


		$('.zoom').bind('touchstart', function() {
				zoomingtimer = true;
				newzoom($(this).attr('id'));
				return false;
			}).mousedown(function() {
				zoomingtimer = true;
				newzoom($(this).attr('id'));
				return false;
			}).bind('touchend', stopzoom)
			.mouseup(
				stopzoom
			).mouseleave(
				stopzoom
			);


		$('.move').bind('touchstart', function() {
			zoomingtimer = true;
			movenew($(this).attr('id'));
			return false;
		}).mousedown(function() {
			zoomingtimer = true;
			movenew($(this).attr('id'));
			return false;
		}).bind(
			'touchend',
			stopzoom).mouseup(
			stopzoom
		).mouseleave(
			stopzoom
		);

		$('.rotate').bind('touchstart', function() {
			zoomingtimer = false;
			newrotate($(this).attr('id'));
			return false;
		}).mousedown(function() {
			zoomingtimer = true;
			newrotate($(this).attr('id'));
			return false;
		}).bind(
			'touchend',
			stopzoom).mouseup(
			stopzoom
		).mouseleave(
			stopzoom
		);




		$('.removeclipart').on('click', function() {
			removeClipart();
		});


		$('.choosetemplate img').on('click', function() {
			_this.changeTemplate($(this).data('src'), $(this).data('color'), $(this).data('size'));
		});

		$(document).on('click', '.item img', function() {
			_this.changeTemplate($(this).data('src'), "#fff", "1");
		});

		$('.insp').on('click', function() {
			load($(this).data('src'), $(this).data('color'), $(this).data('size'));
		})




		$('#next-template').on('click', function() {

			//$(this).hide();
            
            $('#next-template').hide();

			if (_this.selectedtemplate == 'FELI0076_Felix_ketchup_ekologisk_1kg_mk-7 kopi.jpg') {
				var nexttemplate = $('a[data-src="gul1.png"]');
			} else if (_this.selectedtemplate == 'FELP0076_Felix-ketchup_1,25kg_ORIGINAL_SVENSKT_ok-7 kopi.jpg') {
				var nexttemplate = $('a[data-src="gul125.png"]');
			} else if (_this.selectedtemplate == 'FELI0076_Felix_ketchup_ekologisk_500g_mk-6.jpg') {
				var nexttemplate = $('a[data-src="gul05.png"]');
			} else {
				var nexttemplate = $('a[data-src="' + _this.selectedtemplate + '"]').next();
			}

			if (nexttemplate.hasClass('insp')) {
				load(nexttemplate.data('src'), nexttemplate.data('color'), nexttemplate.data('size'));
			} else {
				_this.changeTemplate(nexttemplate.data('src'), nexttemplate.data('color'), nexttemplate.data('size'));
			}
		});


		$('#prev-template').on('click', function() {

            $('#prev-template').hide();
            
			if (_this.selectedtemplate == 'gul1.png') {
				var nexttemplate = $('a[data-src="FELI0076_Felix_ketchup_ekologisk_1kg_mk-7 kopi.jpg"]');
			} else if (_this.selectedtemplate == 'gul125.png') {
				var nexttemplate = $('a[data-src="FELP0076_Felix-ketchup_1,25kg_ORIGINAL_SVENSKT_ok-7 kopi.jpg"]');
			} else if (_this.selectedtemplate == 'gul05.png') {
				var nexttemplate = $('a[data-src="FELI0076_Felix_ketchup_ekologisk_500g_mk-6.jpg"]');
			} else {
				var nexttemplate = $('a[data-src="' + _this.selectedtemplate + '"]').prev();
			}
			if (nexttemplate.hasClass('insp')) {
				load(nexttemplate.data('src'), nexttemplate.data('color'), nexttemplate.data('size'));
			} else {
				_this.changeTemplate(nexttemplate.data('src'), nexttemplate.data('color'), nexttemplate.data('size'));
			}
		});



		$('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
			if ($(e.target).attr('aria-controls') == 'home') {
				if (canvasimage.id == 'userimage') {
					$('.functionsbuttonsmobile').show();
				}
			} else {
				$('.functionsbuttonsmobile').hide();
			}
		})


		function downloadURL(url) {



			var deviceAgent = navigator.userAgent;
			// Set var to iOS device name or null
			var ios = deviceAgent.toLowerCase().match(/(iphone|ipod|ipad)/);

			if (ios) {

				$('.loadingInfo').find('h3').text('Etiketten är redo att laddas ner');
				$('.loadingInfo').find('p').html('<a class="btn btn-default shareonfacebook" href="' + url + '" target="_blank" >Öppna i ett nytt fönster för att ladda ner</a>');

			} else {
				if ($idown) {
					$idown.attr('src', url);
				} else {
					$idown = $('<iframe/>', {
						id: 'idown',
						src: url
					}).hide().appendTo('body');
				}
				hideloader();
			}
		}


		function load(src, color, size) {

			predef = true;
			_this.selectedtemplate = src;

			if (canvasimage) {
				canvasimage.remove();
			}
			if (textimage) {
				textimage.remove();
			}

			if (size == 1) {
				productid = 7438;
				canvas.setHeight(510);
				canvas.setWidth(330);
				canvas.renderAll();
			} else if (size == 05) {
				productid = 7554;
				canvas.setHeight(474);
				canvas.setWidth(330);
				canvas.renderAll();
			} else {
				productid = 7441;
				canvas.setHeight(535);
				canvas.setWidth(267);
				canvas.renderAll();
			}

			var img = new Image();
            
        
            
			$(img).load( function(){

				$('.canvas-container').css('background', color);

				templateimage.setElement(img);

				leftalign = canvas.width / 2;

				templateimage.set({
					left: leftalign,
					top: canvas.height / 2,
					selectable: false,
				});

				if (text) {
					text.set({
						left: leftalign
					});
				}
				if (canvasimage) {
					canvasimage.set({
						left: leftalign
					});
				}

				setBGcolor(color);

				if (size == 1) {
					canvas.setHeight(cHeight1);
					canvas.setWidth(cWidth1);
				} else if (size == 05) {
					canvas.setHeight(cHeight05);
					canvas.setWidth(cWidth05);
				} else {
					canvas.setHeight(cHeight125);
					canvas.setWidth(cWidth125);
				}
				canvas.renderAll();
                $('#next-template').show();
                $('#prev-template').show();
			});
			img.src = "/felix/templates/web/" + src;

		}


		function skapa() {
			var canvasimageobject = new Object();

			if (canvasimage) {
				canvasimageobject = {
					'imagetype': canvasimage.id,
					'imageid': imageid,
					'top': canvasimage.top,
					'left': canvasimage.left,
					'width': canvasimage.width,
					'height': canvasimage.height,
					'scaleX': canvasimage.scaleX,
					'scaleY': canvasimage.scaleY,
					'src': canvasimage._element.src,
					'angle': canvasimage.angle
				};
			}


			var projectobject = new Object({
				'textimage': {
					'top': textimage.top,
					'left': textimage.left,
					'width': textimage.width,
					'height': textimage.height,
					'text': $('.felixtext').val()
				},
				'canvasimage': canvasimageobject,
				'templateimage': {
					'top': templateimage.top,
					'left': templateimage.left,
					'width': templateimage.width,
					'height': templateimage.height,
					'scaleX': templateimage.scaleX,
					'scaleY': templateimage.scaleY,
					'src': templateimage._element.src
				}
			});


			var project = JSON.stringify(projectobject);

			//console.log(projectobject);



			project = encodeURIComponent(project);

			var thumb = canvas.toDataURLWithMultiplier('jpeg', 0.5, 90);

			$.post('/api/1.0/felix/save', {
				canvas: project,
				thumb: thumb,
				productid: productid
			}, function(response) {
				//console.log(response);

				if (response.message == 'OK') {
					window.location.href = '/bekrafta/' + response.felixid;
				} else {
					alert("noe gjekk gale");
				}
				//
			}, 'json');

		}

		function skapainsp() {
			var project = JSON.stringify(canvas);

			project = encodeURIComponent(project);

			var thumb = canvas.toDataURLWithMultiplier('jpeg', 0.4, 90);

			//$.post('/api/1.0/felix/save', {
			$.post('/api/1.0/felix/inspirasjon', {
				canvas: project,
				thumb: thumb,
				productid: productid
			}, function(response) {
				//console.log(response);

				if (response.message == 'OK') {
					//window.location.href = '/bekrafta/' + response.felixid;
				} else {
					alert("noe gjekk gale");
				}
				//
			}, 'json');


		}

		function stopzoom() {
			zoomingtimer = false;
		}

		this.newzoom = newzoom();

		function newzoom(zoom) {

			//if (canvasimage && canvasimage.id != 'clipart') {
			if (canvasimage) {
				//console.log( zoom );
				var scale = canvasimage.scaleX;
				if (scale == null) {
					scale = 1;
				}
				var step = scale / 100;
				if (step < 0) {
					step = -step;
				}
				if (zoom == 'zooml') {
					canvasimage.scale(scale + step).setCoords();
					if (zoomingtimer) {
						setTimeout(function() {
							newzoom("zooml");
						}, 20);
					}
				} else if (zoom == 'zooms') {
					canvasimage.scale(scale - step).setCoords();
					if (zoomingtimer) {
						setTimeout(function() {
							newzoom("zooms");
						}, 20);
					}
				}
				/*if( canvasimage instanceof Object == true   ){
						$('.zoomlayer-out').css( {
											   "top" :canvasimage.top - ( canvasimage.height * canvasimage.scaleX / 2 ) + 18 ,
											   "left" :canvasimage.left - ( canvasimage.width * canvasimage.scaleX / 2 ),
											   "width" : canvasimage.width * canvasimage.scaleX,
											   "height" : canvasimage.height * canvasimage.scaleY,
											   }  );

				}*/
				canvas.renderAll();
			}
		}

		function movenew(direction) {
			donedit();

			//if (canvasimage && canvasimage.id != 'clipart') {
			if (canvasimage) {
				var active = canvas.getActiveObject();
				if (!active) {
					active = canvasimage;
				}
				var left = active.left;
				var top = active.top;
				//console.log( direction );
				if (active.id != 'preimage') {

					if (direction == 'up') {
						top = top - 1;
						if (zoomingtimer) {
							setTimeout(function() {
								movenew("up")
							}, 20);
						}
					} else if (direction == 'left') {
						left = left - 1;
						if (zoomingtimer) {
							setTimeout(function() {
								movenew("left")
							}, 20);
						}
					} else if (direction == 'right') {
						left = left + 1;
						if (zoomingtimer) {
							setTimeout(function() {
								movenew("right")
							}, 20);
						}
					} else if (direction == 'down') {
						top = top + 1;
						if (zoomingtimer) {
							setTimeout(function() {
								movenew("down")
							}, 20);
						}
					}
				}
				active.set({
					top: top,
					left: left
				});
				canvas.renderAll();

			}
		}

		function newrotate(direction) {
			donedit();
			//if (canvasimage && canvasimage.id != 'clipart') {
			if (canvasimage) {
				if (direction == 'rotateright') {
					var newangle = canvasimage.angle + 90;
					/*if( zoomingtimer ){
						setTimeout( function(){
							newrotate("rotateright")
							}, 20 );
					}*/
				} else {
					var newangle = canvasimage.angle - 90;
					/*if( zoomingtimer ){
						setTimeout( function(){
							newrotate("rotateleft")
							}, 20 );
					}*/
				}

				if (newangle >= 360) {
					newangle = 360 - newangle;
				}
				canvasimage.set({
					angle: newangle
				});
				canvas.renderAll();
			}
		};

		function donedit() {
			if (canvasimage) {
				if (canvasimage.active == true) {
					//console.log( 'mouse out' );
					canvasimage.set({
						opacity: 1
					});
					canvasimage.sendToBack();
					canvasimage.active = false;
					canvas.renderAll();
				}
			}

		}

		this.setTemplate = function() {

			var image = "/felix/templates/gul1.png";

			fabric.Image.fromURL(image, function(img) {
				templateimage = img.set({
					left: 330 / 2,
					top: 510 / 2,
					selectable: false,
				});

				canvas.add(img);

				img.sendToBack();
			});

		}

		function setBGcolor(color) {
			canvas.setBackgroundColor(color, canvas.renderAll.bind(canvas));
		}

		this.changeTemplate = function(src, color, size) {
			predef = false;

			//$('.hideinsp').show();
			//$('.showinsp').hide();

			this.selectedtemplate = src;

			if (size == 1) {
				productid = 7438;
				canvas.setHeight(510);
				canvas.setWidth(330);
				//canvas.renderAll();
			}
			else if (size == '05') {
				productid = 7554;
				canvas.setHeight(474);
				canvas.setWidth(330);
				//canvas.renderAll();
			} else {
				productid = 7441;
				canvas.setHeight(535);
				canvas.setWidth(267);
				//canvas.renderAll();
			}


			if (canvasimage) {
				if (size == 1) {
					texttop = 365;
					textheight = 100;
				} else if (size == 05) {
					texttop = 340;
					textheight = 90;
				} else {
					texttop = 385;
					textheight = 140;
				}
			} else {
				textheight = 250;
			}

			var img = new Image();

            
			$(img).load( function(){
               
               $('#next-template').show();
                $('#prev-template').show();
				//$('.canvas-container').css('background', color );

				templateimage.setElement(img);

				leftalign = canvas.width / 2;

				templateimage.set({
					left: leftalign,
					top: canvas.height / 2,
					selectable: false
				});
				textimage.set({
					left: leftalign,
					//top: canvas.height / 2,
					selectable: false
				});

				//console.log(textimage);

				if (textimage && canvasimage) {
					textimage.set({
						height: textheight
					});
				}
				if (canvasimage) {
					canvasimage.set({
						left: leftalign
					});
				}

				canvas.setBackgroundImage('/felix/templates/background_' + src, canvas.renderAll.bind(canvas), {
					// Needed to position backgroundImage at 0/0
					originX: 'left',
					originY: 'top'
				})


				if (size == 1) {
					canvas.setHeight(cHeight1);
					canvas.setWidth(cWidth1);
				} else if (size == 05) {
					canvas.setHeight(cHeight05);
					canvas.setWidth(cWidth05);
				} else {
					canvas.setHeight(cHeight125);
					canvas.setWidth(cWidth125);
				}
				
				//setBGcolor(color);
				canvas.renderAll();
			});
            
            
			img.src = "/felix/templates/" + src;

             
		}

		//text functions
		this.addText = function() {

			//console.log("add text" );
			//var texturl = "/skapa/text?text=" + encodeURIComponent( "KALLES\n KETCHUP" ) + "&height=" + textheight;
			var texturl = "/skapa/text?text=" + encodeURIComponent(" ") + "&height=" + textheight;

			fabric.Image.fromURL(texturl, function(img) {

				textimage = img.set({
					left: leftalign,
					top: 270,
					id: 'textimage',
					selectable: false,
				});
				canvas.add(textimage);

			});

			/*var color = "#dc3224";
			var shadow = {
				color: '#7d0404',
				blur: 7,
				offsetX: 0,
				offsetY: 0,
				opacity: 0.7,
				fillShadow: false,
				strokeShadow: true
			}

            text = new fabric.Text("KALLES\n KETCHUP", {
								left: leftalign,
								top: 270,
								id: 'text',
								fill: color,
								fontFamily: 'felix_scriptregular',
								fontWeight: 'bold',
								textAlign: 'center',
								fontSize: 100,
								scaleX :0.5,
								scaleY: 0.5,
								selectable: false,
								//shadow: '#dc3224 1px 1px 7px',
								lineHeight: 0.8
							});

			text.setShadow(shadow);
            canvas.add(text);*/
			canvas.renderAll();

		}

		this.updateText = function() {
			editlock = true;
			var thistext = $('.felixtext').val();
			var texturl = "/skapa/text?text=" + encodeURIComponent(thistext) + "&height=" + textheight;


			//console.log(canvas._objects);


			$(canvas._objects).each(function() {

				if (this.id == 'textimage') {
					//console.log( this );
					this.remove();
				}
			});

			//console.log( text );

			/*text.set(  {
				text: thistext
			});
			canvas.renderAll();

			console.log( "height: " + text.height);
			console.log( "width: " + text.width);


			if( text.width > 500 ){
				var xw = 250 / text.width ;
				text.scaleX = xw;
			}else{
				text.scaleX = 0.5
			}

			var filter = new fabric.Image.filters.Convolute({
					matrix: [-2, -1, 0, -1, 1, 1, 0, 1, 2]
				  });
			text.filters.push(filter);
			text.applyFilters(canvas.renderAll.bind(canvas));*/

			//text.scaleX = 0.5;
			//text.scaleY = 0.5;
			/*text.scaleToHeight(150);

			if(text.height > 150){
				text.scaleToHeight(150);
			}else{
				text.scaleToWidth(250);
			}*/

			textimage.remove();

			//canvas.remove( textimage );

			fabric.Image.fromURL(texturl, function(img) {

				textimage = img.set({
					left: leftalign,
					top: texttop,
					id: 'textimage',
					selectable: false,
				});

				canvas.add(textimage);

				editlock = false;

			});

			canvas.renderAll();

		}

		function removeClipart() {
			texttop = 300;
			textheight = 250;
			_this.updateText();

			canvasimage.remove();

			canvasimage = null;

			canvas.renderAll();
		}

		//image functions
		this.updateImage = function(image, type) {


			if (editlock) {
				return false;
			}

			var ifx = 246;
			var ify = 135;

			if (type == 'load') {
				var selectable = false;
				imageid = image.split('/').reverse()[0];
				type = 'clipart';
			} else if (type == 'clipart') {
				$('.functionsbuttonsmobile').hide();
				image = '/felix/clipart/' + image + '.png';

				imageid = image.split('/').reverse()[0];
				var selectable = false;
			} else {

				$('.functionsbuttonsmobile').show();

				type = 'userimage';
				var selectable = false;
				imageid = image;
				image = "/skapa/stream/" + image + '/400/400';
			}
			//console.log(image);

			if (canvasimage) {
				canvas.remove(canvasimage);
			} else {
				if (productid == 7438) {
					textheight = 100;
					texttop = 365;
				} else if (productid == 7554) {
					texttop = 340;
					textheight = 90;
				} else {
					textheight = 140;
					texttop = 385;
				}

				_this.updateText();

			}
			fabric.Image.fromURL(image, function(img) {

				if (type != 'clipart') {
					if (img.width > img.height) {
						var imageratio = ifx / img.width;
					} else {
						var imageratio = ify / img.height;
					}

				}


				canvasimage = img.set({
					left: leftalign,
					top: 250,
					scaleX: imageratio,
					scaleY: imageratio,
					selectable: selectable,
					id: type
				});




				canvas.add(img);
				if (type == 'clipart') {
					img.bringToFront();
				} else {
					img.sendToBack();
				}

			});




			canvas.renderAll();
			return true;
		}

		function showloader(text) {
			$('body').prepend('<div id="loader-overlay"><div class="loadingInfo"><p><img src="http://a.static.repix.no/gfx/gui/ajax-loader-gray.gif" /></p>\
									<br/><br/><h3>' + text + '<span id="status"></span></h3></div></div>');

			$('#loader-overlay')
				.css('opacity', '0.8')
				.css('margin', '0')
				.width($(window).width())
				.height($(document).height());

			return false;
		}

		function hideloader() {
			$('#loader-overlay').fadeOut('slow', function() {
				$(this).remove();
			});
		}


		shareThumb = function(media) {
			//showloader();
			//var thumb = canvas.toDataURL({format: 'jpeg', quality: 1 });
			var thumb = canvas.toDataURLWithMultiplier('jpeg', 1, 90);

			var sharelink = '';
			if (media == 'facebook') {
				sharelink = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent('http://felixbeta.eurofoto.se/skapa/ShareFacebook/');
			}
			$.post('/api/1.0/felix/felixthumb', {
				image: thumb
			}, function(response) {

				var u = 'http://felixbeta.eurofoto.se/skapa/ShareFacebook/' + response.data;
				//var u = 'http://felixbeta.eurofoto.se/skapa/ShareFacebook/d6098ef2f6246afb155e3cbd1f892977';

				onResultPageGenerated(u);

				//window.open(sharelink + response.data, "popupWindow", "width=600,height=600,scrollbars=no");
				//hideloader();

				/*setTimeout(function() {

						//$('body').append('<div class="facebookcontainer"><iframe class="facebookiframe" src="'+ sharelink + response.data +'"/></div>')

						console.log(response);
						onResultPageGenerated( u );
						//window.open(sharelink + response.data, "popupWindow", "width=600,height=600,scrollbars=no");

						//window.location.href = sharelink + response.data ;
						hideloader();
					}, 11500);*/
			}, 'json');
			//return thumbid;
		}

		function onResultPageGenerated(u) {
			//console.log(u);
			sharelink = 'https://www.facebook.com/sharer/sharer.php?u=';
			$.post(
				'https://graph.facebook.com', {
					id: u,
					scrape: true
				},
				function(response) {

					//$('#btn-fb-share').attr({'href': sharelink + u, 'target': '_top'});

					setTimeout(function() {

						var deviceAgent = navigator.userAgent;
						// Set var to iOS device name or null
						var ios = deviceAgent.toLowerCase().match(/(iphone|ipod|ipad)/);

						if (ios) {
							$('.loadingInfo').find('h3').text('Etiketten är redo att delas.');
							$('.loadingInfo').find('p').html('<a class="btn btn-default shareonfacebook" href="' + sharelink + u + '" target="_blank" >Klicka här för att dela din etikett</a>');
						} else {
							window.open(sharelink + u, "popupWindow", "width=600,height=600,scrollbars=no");
							hideloader();
						}

					}, 2500);
				}
			);
		}

	}



	$.fn.FelixEditor = function(methodOrOptions) {

		//console.log( methodOrOptions );

		return this.each(function() {
			var element = $(this);
			// Return early if this element already has a plugin instance
			if (element.data('FelixEditor')) return;
			var felixditorplugin = new FelixEditor(this, methodOrOptions);
			// Store plugin object in this element's data
			element.data('FelixEditor', felixditorplugin);
		});

	}
})(jQuery);




/************************
 *
 *Felix functions
 *
 ************************/


function wrapText(text, maxChars) {
	var ret = [];
	var words = text.split(/\b/);

	var currentLine = '';
	var lastWhite = '';
	words.forEach(function(d) {
		var prev = currentLine;
		currentLine += lastWhite + d;

		var l = currentLine.length;

		if (l > maxChars) {
			ret.push(prev.trim());
			currentLine = d;
			lastWhite = '';
		} else {
			var m = currentLine.match(/(.*)(\s+)$/);
			lastWhite = (m && m.length === 3 && m[2]) || '';
			currentLine = (m && m.length === 3 && m[1]) || currentLine;
		}
	});

	if (currentLine) {
		ret.push(currentLine.trim());
	}

	return ret.join("\n");
}