/**************************
 *Html5 Product editor by TIL
 *
 *
 **/

(function($) {
    
    var TeEditor = function(element, options){

        var settings = $.extend({
			'width'  : 900,
			'height' : 200,
			'id'     : 'c'
		}, options || {});
        
		var _this = this;
		
        var index = 0;
		var historyindex = new Array();
		var thumbs = new Array();
        var pages = new Array();
		var zoomingtimer = false;
		var ramme;
		
		var fillcolor = "#000000";
		
		this.zoomingtimer = zoomingtimer;
		
        var canvas = new fabric.Canvas( settings.id );
		
		canvas.borderColor = 'rgba(0,0,0,0)';
		canvas.selectionBorderColor = 'red';
		canvas.selectionLineWidth = 5;
		
        this.canvas = canvas;
		
        canvas.on('object:selected', function( options ) {
			//$('.layer').removeClass('active');
			var type = options.target.type;
			var id = options.target.id ? options.target.id : '0' ;
			
			
			//$('#object-' + id  ).addClass('active'); 
			
			/*$( '#imagemenu' ).css( { 'left' : options.target.left - ( options.target.currentWidth / 2 )  - 120,
									  'top' : options.target.top - ( options.target.currentHeight / 2 )});
			
			$( '#textedit' ).css( { 'left' : options.target.left - ( options.target.currentWidth / 2 )  - 120,
									  'top' : options.target.top - ( options.target.currentHeight / 2 )});*/
			$('.editorcontrols').show();
			
			
			if( id == 'clipart' ){
				//$( '#imagemenu' ).show();
				$("#tabs").tabs("option", "active", 2);
				//$('#new_text_container').show();
				$( '#text-options-container' ).hide();
			}
			else if( type == 'image' ){
				//$( '#imagemenu' ).show();
				$( '#image-options-container' ).show();
				$( '#textedit' ).hide();
				$("#tabs").tabs("option", "active", 0);
				//$('#new_text_container').show();
				$( '#text-options-container' ).hide();
			}
			else if( type == 'i-text' ){
				$( '#text-options-container' ).show();
				$( '#image-options-container' ).hide();
				//$( '#imagemenu, #new_text_container' ).hide();
				//$("#tabs").tabs("option", "active", 2);
				options.target.hasControls = false;
				options = options.target;				
				_this.changeText( options );
			}
			updatelist();
		});
		
        canvas.on( 'selection:cleared' , function(){
			$('.layer').removeClass('active');
			
			$('.editorcontrols').hide();
			
            //thumbs[index] = canvas.toDataURLWithMultiplier('jpg', 0.2, 60);

            //$('#new_text_container').show();

			$( '#text-options-container' ).hide();
			$( '#image-options-container' ).hide();
            //$( '#preview-' + index ).attr( 'src', thumbs[index]);
            $( '#imagemenu' ).hide();
		});
		
		

		
		canvas.on( 'object:modified', function(){
			_this.updateThumbs();
			_this.savehistory();
			updatelist();
		});
		
		$('#' + settings.id ).droppable( {
			over: function( event, ui ){
				//console.log( event );
				//console.log( ui );
			},
			drop: function( event, ui ){
				var id =  ui.draggable[0].id;
				var newPosX = ui.offset.left - $(this).offset().left;
				var newPosY = ui.offset.top - $(this).offset().top;
				var selected = false;
				var scaleX = 0.5, scaleY = 0.5, angle = 0, strokeWidth = 0, stroke = "#fff";
				
				for (var i=canvas._objects.length-1;i>0;i--){
				
				
					if( canvas._objects[i].type == 'newimage' ){
						
					
						//console.log(canvas._objects[i]);
					
						var obx = canvas._objects[i].left;
						var oby = canvas._objects[i].top;
						
						var dropx = newPosX +  ( ui.helper[0].width  / 2 );
						var dropy = newPosY +  ( ui.helper[0].height  / 2 );
						
						var xdiff = Math.max(obx,dropx) - Math.min( obx, dropx);
						var ydiff = Math.max( oby, dropy ) - Math.min( oby, dropy );
						
						var xtotal = ( canvas._objects[i].currentWidth / 2 ) - xdiff;
						var ytotal = ( canvas._objects[i].currentHeight / 2 ) - ydiff;

						if( xtotal > 0 && ytotal > 0 ){
	
							//canvas._objects[i].active = true;
							canvas.renderAll();
							_this.changesrc(  'http://normerk.eurofoto.no/images/stream/editorimage/' + id  , id , canvas._objects[i]  );
							selected = true;
							break;
						}else{
							//addnewimage( '/images/stream/image/' + id, id, newPosX, newPosY, scaleX , scaleY, angle,  strokeWidth, stroke);
						}
					
					}
				
				
				}
				if( selected == false ){
					addnewimage( 'http://normerk.eurofoto.no/images/stream/editorimage/' + id , id, newPosX, newPosY, scaleX , scaleY, angle,  strokeWidth, stroke);
				}
				
			}
		});
		
		$(document).on( 'keyup', function( data  ){
			
			var target  =  $(data.target).attr('id');
			
			var activeObject = canvas.getActiveObject();
			if( activeObject && target != 'text' ){
				if( activeObject.type  == 'i-text' ){
					$('#text').val(activeObject.text);
				}	
			}else{
				
				_this.updateText( $(data.target).val() );
			}
			});
		
		
		$('#drawing-mode').on( 'click'  , function() {
			canvas.isDrawingMode = !canvas.isDrawingMode;
			if (canvas.isDrawingMode) {
			  $(this).text( 'Cancel drawing mode' );
			  
				canvas.freeDrawingBrush.color = $('#drawing-color').val();
				canvas.freeDrawingBrush.width = parseInt( $('#drawing-line-width').val() );
			  
			  //drawingOptionsEl.style.display = '';
			}
			else {
			   $(this).text(  'Enter drawing mode' );
			  //drawingOptionsEl.style.display = 'none';
			}
		});
		
		
		
		this.deactivateAllObjects = function(){
			canvas.deactivateAllWithDispatch().renderAll();
		}
		
		
		this.selectAll = function(){
			
			
			canvas.deactivateAllWithDispatch().renderAll();
			
			canvas.setActiveGroup(new fabric.Group(canvas.getObjects())).renderAll();
			
			
			$('.layer').addClass('active');
			
		}
		
		this.center = function(){
			var activeobject =  canvas.getActiveObject();
			var activegroup = canvas.getActiveGroup();
			
			if( activeobject){
				activeobject.center();
				
				activeobject.setCoords();
				
				//_this.updateText();
			}
			else if( activegroup ){
				
				//console.log( activegroup );
				
				var top = canvas.height / 2;
				var left = canvas.width / 2;
				
				//activegroup.center();
				
				activegroup.set({'top': top, 'left': left});
				activegroup.setCoords();
				canvas.renderAll();
				
				//activeobject.setCoords();
				
				$(activegroup._objects).each( function(){
					var ch = canvas.height;
					var cv = canvas.width;
					var oh = this.height;
					var ov = this.width;
					
					//console.log(canvas);
					
					//console.log( this );
						
				});
			}	
		}
		
		this.move  = function( direction, start ){
			
			
			if( !_this.zoomingtimer ){
				
				return false;
			}
			var timer = 20;
			if( start ){
				timer = 500;
			}
			
			if( canvas.getActiveObject() ){
				var activeobject =  canvas.getActiveObject();
			}else if( canvas.getActiveGroup() ){
				var activeobject =  canvas.getActiveGroup();
			}
			
			if( direction == 'left' ){
				var ad = activeobject.left;
				activeobject.set('left', ad - 2 );
			}
			else if( direction == 'right' ){
				var ad = activeobject.left;
				activeobject.set('left', ad + 2 );
			}
			else if( direction == 'up' ){
				var ad = activeobject.top;
				activeobject.set('top', ad - 2 );
			}
			else if( direction == 'down' ){
				var ad = activeobject.top;
				activeobject.set('top', ad + 2 );
			}
			else if( direction == 'larger' ){
				
				
				if( activeobject.type == 'i-text' ){

					var fontsize = parseInt( $('#fontsize').val() ) ;
					
					activeobject.set( { fontSize : fontsize + 5 }   );
					$('#fontsize').val(fontsize + 5);
					
				}
				else{
					
					var scaler =  Math.pow(  activeobject.scaleX, 2  ) / 10 ;
					
					if( scaler > 0.1 ){
						scaler = 0.1;
					}
					if( scaler < 0.001 ){
						scaler = 0.001;
					}
					
					//console.log(  "scale",  scaler )
					
					var scale = parseFloat( activeobject.scaleX ) + scaler;
					activeobject.set('scaleX', scale );
					activeobject.set('scaleY', scale );
				}
			}
			else if( direction == 'smaller' ){
				
				if( activeobject.type == 'i-text' ){
					var fontsize = parseInt( $('#fontsize').val() ) ;
					
					fontsize = fontsize -5;
					if( fontsize <= 5 ){
						fontsize = 5;
					}
					
					activeobject.set( { fontSize : fontsize }   );
					$('#fontsize').val(fontsize - 5);
					
				}
				else{
					
					var scaler =  Math.pow(  activeobject.scaleX, 2  ) / 10 ;
					//console.log(  "scale",  scaler );
					
					
					if( scaler > 0.1 ){
						scaler = 0.1;
					}
					
					var scale = parseFloat( activeobject.scaleX )  - scaler;
					activeobject.set('scaleX', scale );
					activeobject.set('scaleY', scale );
				}
			}
			
			
			setTimeout( function(){ _this.move(direction) }, timer );
			
			
			activeobject.setCoords();
			canvas.renderAll();
		
			return false;
			
		}
		
		this.undo = function(){
			if( historyindex[index] > 0 ){
				historyindex[index]--;
				canvas.loadFromJSON( history[index][historyindex[index]] );
				canvas.renderAll();
			}
		}
		
		this.redo = function(){
			if( historyindex[index] <  history[index].length ){
				historyindex[index]++;
				canvas.loadFromJSON( history[index][historyindex[index]] );
				canvas.renderAll(); 
			}
		}
		this.updateThumbs = function(){
			thumbs[index] = canvas.toDataURLWithMultiplier('jpg', 0.2, 60);
			$( '.preview-' + index + ' img' ).attr( 'src', thumbs[index] );
			return false;
		
		}
		this.savehistory = function(){
			
			if( !history[index] ){
				history[index] = new Array();
			}
			if( !historyindex[index] ){
				historyindex[index] = 1;
			}
			

			
			if( canvas.toJSON() != history[index][historyindex[index]] ){
				history[index][historyindex[index]] = new Object();
				history[index][historyindex[index]] = canvas.toJSON();
			}
			
			historyindex[index]++;
			
		}
        
        this.loadpage = function( malid, malpageid ){
			
			if( index > 0 ){
				pages[index] = canvas.toJSON();
			}
			
			
			if( index == malpageid ){
				return false;
			}
			else{
				index = malpageid;
				canvas.clear();
			}  
			
			var editwidth = 900;
			canvas.clear();
			
			if( pages[index] ){
				canvas.loadFromJSON(pages[index]);
			}
			else{
				
				$.post('/api/1.0/tedit/loadpage', {
						malid: malid,
						malpageid: malpageid
					}, function (response) {
						
						var data = response.data;
						var ratio = data.printwidth / editwidth;
						var editheight = data.printheight / ratio;
						if( data.content.length  == 0 ){
							canvas.setWidth( data.printwidth );
							canvas.setHeight( data.printheight );
							canvas.calcOffset();
						}
						else{
							//$('#wrap1').css('width', editwidth );
							//canvas.setWidth( editwidth );
							//canvas.setHeight( editheight );
							
							
							
							canvas.setWidth( data.printwidth );
							canvas.setHeight( data.printheight );
							canvas.calcOffset();
							
							
							var content = data.content.replace( '%2\n', "" ) ;
							
							//console.log( unescape( content )  );
							
							canvas.loadFromJSON( unescape( content )  , canvas.renderAll.bind(canvas));
							
							
							//console.log(canvas);
							
							updatelist();
							
							initAligningGuidelines ( canvas );
							initCenteringGuidelines( canvas );
							
							index = malpageid;
							
						}
						
						setdimmensions();

				}, 'json');
			}
			//canvas.renderAll();
            return false;
		}
        
		
		
		this.updatelist = updatelist;
		
		function updatelist(){
			
			var i = 0;
			
			//$( ".layers" ).sortable( "destroy" );
			
			$('.layers').html('');
			
			$(canvas._objects).each( function(){
				
				//if( this.selectable ){
				
					
					//console.log( this.type );
				
				
					if( this.text ){
						var layer  = $('<li id="object-' + i + '" data-id="'+  i +'" class="layer list-group-item">\
											<span class="badge"><a href=""><i class="fa fa-trash-o fa-2" aria-hidden="true"></i></a></span> <label>TEKST: </label>'
											+ this.text + '</li>');
					}
					else if( this.id == "frame" ){
						var layer = $('<li id="object-' + i + '" data-id="'+  i +'" class="layer list-group-item">\
											<span class="badge"><a href=""><i class="fa fa-trash-o fa-2" aria-hidden="true"></i></a></span> <label>Ramme: </label></li>');
					}
					else if( this.type  == "image" ){
						var layer = $('<li id="object-' + i + '" data-id="'+  i +'" class="layer list-group-item">\
											<span class="badge"><a href=""><i class="fa fa-trash-o fa-2" aria-hidden="true"></i></a></span> <label>Bilde: </label></li>');
					}
					
					
					if( this.active ){
						layer.addClass('active');;
					}
					
					$('.layers').append(layer);
					
				//}
				
				/*
				$(".layers").sortable({
					tolerance: 'pointer',
					revert: 'invalid',
					forceHelperSize: true,
					stop: function( event, ui ) {
						updatelist();
						//console.log(event);
						//console.log(ui);
						
					}
				});
				*/
				i++;
				//console.log( this.text );
				
				});
		}
		
		
        this.getUploaded = getUploaded;
        function getUploaded( albumid ) {
			$('#imagelist').html('');
            $.post('/api/1.0/tedit/loadimages', {
                albumid: albumid,
                }, function (response) {
					
					$('#imagelist').append( $('<a href="#" id="level_up">\
											  <img src="https://encrypted-tbn3.gstatic.com/images?q=tbn:ANd9GcTVKPZud7IM53DlrqBinIJSEqtXLCtXsy02PLSXShuacBQ0GpKI8IsF5w"\>\
											</a>'));
					
                    $(response.data).each(function (index, value) {

						$('.albumlist').hide();
						var image = $('<img class="image" style="z-index:1000; margin: 2px" height="80"/>');
						image.attr( { 'id': value.id , 'x' : 100, 'y': 100 } );
						image.attr( 'src' , value.thumbnail );
                        image.draggable({
								containment: "document",
								helper: "clone",
								cursor: "move"
							}
						);  
                        $('#imagelist').append( image );
                })
            }, 'json');
        }

		
		this.getUseralbums = function(){
			
			var type = "uploaded";
			$.post( '/api/1.0/tedit/useralbums/',{
					type : type},
					function(response){
						$(response.data).each(function (index, value) {							
							var album = $('<a href="#"  class="album" id="' + value.id + '">\
											<img  src="' + value.thumbnailurl + '" height="80" style="margin: 2px" />\
										</a>');	
							
						
						$('.albumlist').append( album );
						});
						
					}, 'json');
		}
		
		this.openCrop =  function(){
			var activeObject = canvas.getActiveObject();
			
			//console.log(activeObject.orgSrc);
			
			var newSrc = activeObject.orgSrc;
			$('#cropwindow').dialog({
				width: 500,
				height: 500,
				modal: true,
				draggable: false,
				resizable: false,
				beforeClose: function(event, ui){
					$('#cropimage').imgAreaSelect( {'remove' : true} );
				}
			});
			
			image = new Image();
			image.onload = function() {
				$('#cropimage').attr( 'src', newSrc + '?crop=true');
				//$('#cropimage').imgAreaSelect();
				$('#cropimage').imgAreaSelect({ x1: 120, y1: 90, x2: 280, y2: 210, onSelectEnd: updateFromCrop });
			}
			image.src = newSrc;
			
			
		}
	
		function updateFromCrop(img, selection) {
			
			var activeObject = canvas.getActiveObject();
			var newsrc = activeObject.orgSrc;
			
			//console.log(activeObject);
			var id=activeObject.id;
			
			var newsrc = '/producteditor/thumbs/' +  activeObject.id + '/' + img.width +  '/' + img.height + '/' + selection.width + '/' + selection.height + '/' + selection.x1 + '/' + selection.y1;
			
			var newPosX = activeObject.left; 
			var newPosY = activeObject.top; 
			activeObject.remove();
			
			_this.changesrc( newsrc, id, activeObject );
			
		}
		
        this.changeSelectedImage = function( newsrc2, id, object  ){
            
            var activeObject = canvas.getActiveObject();
            var img=new Image();
            img.onload=function(){

                var x = activeObject.width/ 2 ;
                var y = activeObject.width/ 2 ;
                
                activeObject.setElement(img);
                
                activeObject.clipTo = function (ctx) {
                    ctx.rect (-x, -y, img.width, img.height);
                }
                canvas.renderAll();
            }
            img.src=newsrc2;
        }
        
        
        
        this.changeImageNew = function(newsrc2, id, object){
           
            var img = new Image();
            var obj = canvas.getActiveObject();
            
            img.onload = function() {
            
                var imagecanvas = fabric.util.createCanvasElement();
                var imageratio = img.width / img.height;
                var canvasratio = obj.width / obj.height;
                var cy = 0, cx = 0;
                

                
                if(imageratio == canvasratio ){
                    
                    var width = obj.width;
                    var height = obj.height;
                    
                }
                else if( imageratio > canvasratio  ){
                    var width = img.width;
                    var height = img.height;
                }else{

                    var width = img.width;
                    var height = img.width / canvasratio;
                }

                imagecanvas.width = width;
                imagecanvas.height = height;
                imagecanvas.getContext('2d').drawImage( img, 0, 0, width, height, 0 ,0 , width, height  );
                
                
                var orgsr = img;
                
                img.onload = function() {
                    var scale = obj.width / imagecanvas.width;
                    obj.setElement(this);
                    obj.set({
                        scaleX: obj.scaleX * scale,
                        scaleY: obj.scaleY * scale,
                        top: obj.top,
                        left: obj.left,
                        angle: obj.angle,
						_originalElement: orgsr,
						orgSrc: newsrc2
                    });
                    obj.setCoords();
                    canvas.renderAll();
	
                };
                img.src = imagecanvas.toDataURL('image/jpg');
                
            }
            
            img.src = newsrc2;
        }
		
		this.changesrc = function(newsrc2, id, object ){
			
				if( !object ){
					var activeObject = canvas.getActiveObject();
					//var activeObject = object;
					//var activeElement = activeObject.getElement();
				}else{
					var activeObject = object;
				}
				if( id != null ){
					activeObject.set({id: id});
				}
				
				var newsrc = newsrc2;
				var newPosX = activeObject.left; 
				var newPosY = activeObject.top;
				var scaleX = activeObject.scaleX;
				var scaleY = activeObject.scaleY;
				var strokeWidth = activeObject.strokeWidth;
				var angle = activeObject.angle;
				var stroke = activeObject.stroke;

				
				this.addnewimage(  newsrc, id, newPosX, newPosY, scaleX , scaleY, angle,  strokeWidth, stroke, activeObject );
				//activeObject.remove();
				
					
			
		}
		
        
		this.removeFrame = function(){
		
			ramme.remove();
			canvas.renderAll();
		}
		
		this.addFrame = function( type ){
			
			var rammewidth = 4;
			var corner = 0;
			
			if( canvas.width < canvas.height ){
				
				rammewidth =  canvas.width / 25;
				
			}else{
				rammewidth =  canvas.height / 25;
			}
			
			//console.log( canvas.width );
			
			if( ramme ){
				ramme.remove();
				canvas.renderAll();
			}
			
			
			if(  type == 'square1' ){
				rammewidth = rammewidth;
			}
			else if(  type == 'square2' ){
				 rammewidth = rammewidth * 2;
			}
			else if(  type == 'oval1' ){
				rammewidth = rammewidth;
				corner = 30;
			}
			else if(  type == 'oval2' ){
				rammewidth = rammewidth * 2;
				corner = 30;
			}
			else if(  type == 'removeframe' ){
				return false;
			}

			ramme = new fabric.Rect({
				top: ( canvas.height / 2  ),
				left: ( canvas.width / 2 ),  
				width: canvas.width - ( rammewidth * 2 ),
				height: canvas.height - (  rammewidth * 2 ),
				fill: '#fff',
				stroke: fillcolor,
				strokeWidth: rammewidth,
				selectable: false,
				rx: corner, ry: corner,
				id: "frame"
			});
			canvas.add(ramme);
			ramme.sendToBack();
			canvas.renderAll();
			
		}
		
        this.addnewimage = addnewimage;
		function addnewimage( newsrc2, id, newPosX, newPosY, scaleX , scaleY   ) {
            // Init canvas:
			var color = $('.selectedcolor').data('color');
            var img = new Image(); 
            img.onload = function() {
                var fImg = new fabric.Image(this, {
                    originX: 'center',
                    originY: 'center',
                    left: newPosX,
                    top: newPosY,
					scaleX :scaleX,
					scaleY: scaleY,
					lockUniScaling: true,
					id: id
                });
				
                canvas.add(fImg);
				
				var imageh = fImg.height;
				var imagew = fImg.width;
				
				var ratio = canvas.height / imageh;
				
				
				if( ( imagew * ratio ) > canvas.width  ){
					ratio = canvas.width / imagew;
				}
				
				fImg.set({scaleX :ratio,scaleY: ratio } ); 
				fImg.center();
				
				fImg.setCoords();
				
				fImg.filters.push(new fabric.Image.filters.Tint({
					color: color,
					opacity: 1
				}));
				
				fImg.applyFilters(canvas.renderAll.bind(canvas));
			
                canvas.setActiveObject(fImg);
				
				try{
					if( oldObject ){
						oldObject.remove();
					}
				}catch(err){}
				
				canvas.renderAll();
            };
			
            img.src = newsrc2;  
        }
		
		
		/*this.addframe = function(){
			
			var activeObject = canvas.getActiveObject();
			
			activeObject.set({
					strokeWidth: 20,
					stroke: '#FFF'
				});
			
			
			canvas.renderAll();
			
		}*/
		
		
		this.deleteBackground = function(){
			canvas.backgroundImage = 0;
			canvas.backgroundColor = '#FFFFFF';
			canvas.renderAll();
		}
		
	    this.changeBackground = function( background ){
			
            backgroundcat = $('#select_background').val();
            src = '/assets/producteditor/web/backgrounds/' + background;
			canvas.setBackgroundImage(             
                    src,
					canvas.renderAll.bind(canvas),
					{
						opacity: 1,
						//angle: 45,
						left: 0,
						top: -400
						//originX: 'left',
						//originY: 'top'
					}
                );
		}
		
		this.newClipart = function ( src ){
            src = '/assets/producteditor/web/clipart/' + src;
			var img = new Image(); 
            img.onload = function() {
                var fImg = new fabric.Newimage(this, {
                    originX: 'center',
                    originY: 'center',
                    left: canvas.getWidth()/2,
                    top: canvas.getHeight()/2,
					id: 'clipart'
                });
                //fImg.setCrossOrigin('anonymous');
                canvas.add(fImg);
                canvas.setActiveObject(fImg);
            };
            img.src = src; 
			
			canvas.renderAll.bind(canvas);
        }
        
        this.zoomBy = function(x, y, z ) {

            var activeObject = canvas.getActiveObject();
				
            if (activeObject) {
				
				if( activeObject.crop.z ){
					z = activeObject.crop.z + z;
				}
				if( activeObject.crop.x ){
					x = activeObject.crop.x + x;
				}
				if( activeObject.crop.y ){
					y = activeObject.crop.y + y;
				}				
				
				
				activeObject.crop = { 'z' : z, 'x' : x, 'y' : y };
				activeObject.applyCrop(canvas.renderAll.bind(canvas));
                
            }
        }
		this.deleteImage = function(){
            canvas.getActiveObject().remove();
        }
        this.moveForward = function(){
            canvas.getActiveObject().bringForward();
        }
        this.moveBackward = function(){
            canvas.getActiveObject().sendBackwards();
        }
        
		
		
		this.deleteobject = function(id){
			
			canvas.item( id ).remove();
			
			
			updatelist();
			
			};
		
		this.changecolor = function( color ){
			
			fillcolor = color;
			
			$(canvas._objects).each( function(){
				
				if( this.text ){
					this.set({
						fill: color
					});
				}
				else if( this.stroke ){
					this.set({
						
						stroke: color
						
						})
				}
				else if( this.filters ){
					
					this.filters=[];			
					//console.log( this );
					
					this.filters.push(new fabric.Image.filters.Tint({
						color: color,
						opacity: 1
					}));
				
					this.applyFilters(
						canvas.renderAll.bind(canvas)
					);
					
					
				}
				
				canvas.renderAll();
				
			});
			
			
			
		}
		
		this.addtext = function( txt ){
			
			var color = $('.selectedcolor').data('color');
			//var font = $('#font-list').val();
			var font = $('#font-list span').css('font-family');
			var textAlign = $('#gravity').val();
			var textWeight = $('#textweight').val();
			var textopacity = 1;
			
			var fontsize = $('#fontsize').val();
			
			var newid =  canvas._objects.length;
			
            var text = new fabric.IText(txt, {
								left: 100,
								top: 100,
								id: newid,
								fill: color,
								opacity: textopacity,
								fontFamily: font,
								textAlign: textAlign,
								fontWeight: textWeight,
								fontSize: fontsize,
								hasControls: false
							});
			
			
			
			while( text.width > canvas.width ){
				//console.log( text.width );
				text.set( {fontSize: text.fontSize - 5 });
			}
			
			text.set( {fontSize: text.fontSize - 5 });
			
			//console.log( text.width );
			
            canvas.add(text);
			
			
			
			text.center();
				
			text.setCoords();
			
			
			canvas.setActiveObject(text);
			
			return false;
        }
		
        
        this.updateText = function( txt ){
			
			//var font = $('#font-list').val();
			var font = $('#font-list span').css('font-family');
			var textAlign = $('#gravity').val();
			var textWeight = $('#bold').data('bold');
			var textDecoration = $('#underline').data('decoration');
			var fontStyle = $('#italic').data('italic');
			var fontSize = $('#fontsize').val();
			
			
			if( fontSize <= 5 ){
				fontSize = 5;
				$('#fontsize').val(5);
			}
			
			var text = canvas.getActiveObject();
			
			if( text.type  != 'i-text' ){	
				return false;
			}

			
			
			
			var textval = txt ? txt : text.text;
			
			var color = $('.selectedcolor').data('color');
			//var textopacity = $('#colorselect').attr( 'data-opacity' );
			var textopacity = 1;
			text.set(  {
				text: textval,
				fill: color,
				opacity: textopacity,
				fontFamily: font,
				textAlign: textAlign,
				fontWeight: textWeight,
				fontSize:fontSize,
				fontStyle: fontStyle,
				textDecoration: textDecoration
				}
				
				);
			canvas.renderAll();
            
        }
		
		this.changeText = function( options  ){
			
			var txt = options.text;
			var color = options.fill;
			var font = options.fontFamily;
			var textAlign = options.textAlign;
			var fontWeight = options.fontWeight;
			var fontStyle = options.fontStyle;
			var textDecoration = options.textDecoration;
			var stroke = options.stroke;
			var strokeWidth = options.strokeWidth;
			var fontSize = options.fontSize;

			font = font.replace("'", '');
			font = font.replace("'", '');
			
			
			if( fontStyle == 'italic' ){
				$('#italic').data('italic', 'italic' ).addClass('active');
			}else{
				$('#italic').data('italic', '' ).removeClass('active');
			}
			
			if( textDecoration == 'underline' ){
				$('#underline').data('underline', 'underline' ).addClass('active');
			}else{
				$('#underline').data('underline', '' ).removeClass('active');
			}
			
			if( fontWeight == 'bold' ){
				$('#bold').data('bold', 'bold' ).addClass('active');
			}else{
				$('#bold').data('bold', '' ).removeClass('active');
			}
			
			
			$('#strokecolor').val(stroke);
			$('input[name="strokewidth"]').val(strokeWidth);
			$('textarea#text').val( txt );
			//$('#colorselect').val( color);
			$('#font-list span').html(font).css({'font-family': font});
			$("#gravity").val(  textAlign );
			$('#fontsize').val(fontSize);
			//$("#textweight").val(  fontWeight );
			$('.radio, .textweight').css( 'background-position-x', '-7px' );
			$('#' + textAlign).css( 'background-position-x', '-37px' );
			$('#' + fontWeight).css( 'background-position-x', '-37px' );

		}
		
		this.savepage = function(){
            
            var multipier = 50 / canvas.height ;
			
			var thumb = canvas.toDataURLWithMultiplier('jpg', multipier, 60);
			
			var data = JSON.stringify( canvas );
			
			data = escape(data);
			
			var id = $('.selectedpage').attr('id');
			
			
			var editsize = { 'x': $('#c1').attr('width') , 'y' : $('#c1').attr('height') };
			

            $.post('/api/1.0/tedit/savepage', {
                malid: templateid,
				editsize: editsize,
				malpageid: id,
				thumb: JSON.stringify( thumb ),
                data: data,
                }, function (response) {
                    //console.log( response.message );
            }, 'json');
			
		}
		
		
		this.save = function(){

			/*$('.malpage').each( function(){
				
				
				var id = $(this).attr('id');
				
				
				console.log(id);
				
				if( $('.selectedpage').attr('id') == id ){
					
					pages[id] = canvas.toJSON();
					
				}else if( !pages[id] ){
					
					$.post('/api/1.0/tedit/loadpage', {
						malid: templateid,
						malpageid: id
					}, function (response) {
							var data = response.data;
							pages[id] = JSON.parse( data.content );
					}, 'json');
					
					//pages[id] = "dont exist";
					
				}
			});*/
			
			
			pages = canvas.toJSON();
			
			//pages = canvas.toSVG();
			
			
			var multipier = 100 / canvas.width ;
			
			
			var thumb = canvas.toDataURLWithMultiplier('jpg', multipier, 60);
			var color = { 'color' : $('.selectedcolor' ).data('color'), 'name' : $('.selectedcolor' ).data('colorname') };
			
			var editsize = { 'x': $('#c1').attr('width') , 'y' : $('#c1').attr('height') };
			
			$.post('/api/1.0/tedit/saveorder', {
					pages: pages,
					color: color,
					malid: templateid,
					editsize: editsize,
					thumb: thumb
				}, function (response) {
						//console.log( response );
						
						var prodno = $('#prodno').val();
						var quantity = $('.quantity').val();
						
						var attributes = {projectid:response.refid, color:color };
						
						$.post( '/api/1.0/cart/add', {
							prodno: prodno,
							quantity:quantity,
							attributes: attributes
							}, function(response){
								//console.log( response );
								
								location.href = "/cart";
								
					
						}, 'json' );
						
				}, 'json' );
			
			
			
			
			//console.log(pages);
			
		}
		
    }
    
    
    
    
    $.fn.teditor = function( methodOrOptions ) {
	
        return this.each(function(){
               var element = $(this);
               // Return early if this element already has a plugin instance
               if (element.data('teditor')) return;
               var teditorplugin = new TeEditor(this, methodOrOptions );
               // Store plugin object in this element's data
               element.data('teditor', teditorplugin);
        });
      
    }
})(jQuery);