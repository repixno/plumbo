/**************************
 *Html5 Product editor by TIL
 *
 *
 **/

(function($) {
    
    var TeEditor = function(element, options){

        var settings = $.extend({
			'width'  : 595,
			'height' : 341,
			'id'     : 'c'
		}, options || {});
        
		var _this = this;
		
        var index = 0;
		var historyindex = new Array();
		var thumbs = new Array();
        var pages = new Array();
        
        var canvas = new fabric.Canvas( settings.id );
        this.canvas = canvas;
        
        canvas.on('object:selected', function( options ) {
			
			var type = options.target.type;
			var id = options.target.id;
			
			/*$( '#imagemenu' ).css( { 'left' : options.target.left - ( options.target.currentWidth / 2 )  - 120,
									  'top' : options.target.top - ( options.target.currentHeight / 2 )});
			
			$( '#textedit' ).css( { 'left' : options.target.left - ( options.target.currentWidth / 2 )  - 120,
									  'top' : options.target.top - ( options.target.currentHeight / 2 )});*/
			
			if( id == 'clipart' ){
				$( '#imagemenu' ).show();
				$("#tabs").tabs("option", "active", 2);
				$('#new_text_container').show();
				$( '#text-options-container' ).hide();
			}
			else if( type == 'newimage' ){
				$( '#imagemenu' ).show();
				$( '#textedit' ).hide();
				$("#tabs").tabs("option", "active", 0);
				$('#new_text_container').show();
				$( '#text-options-container' ).hide();
			}
			else if( type == 'i-text' ){
				$( '#text-options-container' ).show();
				$( '#imagemenu, #new_text_container' ).hide();
				//$("#tabs").tabs("option", "active", 2);
				options = options.target;
				_this.changeText( options );
			}
		});
		
        canvas.on( 'selection:cleared' , function(){
            //thumbs[index] = canvas.toDataURLWithMultiplier('jpg', 0.2, 60);

            $('#new_text_container').show();

			$( '#text-options-container' ).hide();
            //$( '#preview-' + index ).attr( 'src', thumbs[index]);
            $( '#imagemenu' ).hide();
		});
		
		canvas.on( 'object:modified', function(){
			_this.updateThumbs();
			_this.savehistory();
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
						
					
						console.log(canvas._objects[i]);
					
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
							_this.changesrc(  '/images/stream/image/' + id  , id , canvas._objects[i]  );
							selected = true;
							break;
						}else{
							//addnewimage( '/images/stream/image/' + id, id, newPosX, newPosY, scaleX , scaleY, angle,  strokeWidth, stroke);
						}
					
					}
				
				
				}
				if( selected == false ){
					addnewimage( '/images/stream/image/' + id , id, newPosX, newPosY, scaleX , scaleY, angle,  strokeWidth, stroke);
				}
				
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
							canvas.setWidth( editwidth );
							canvas.setHeight( editheight );
							canvas.calcOffset();
						}
						else{
							$('#wrap1').css('width', editwidth );
							canvas.setWidth( editwidth );
							canvas.setHeight( editheight );
							canvas.calcOffset();
							canvas.loadFromJSON( data.content , canvas.renderAll.bind(canvas));
							index = malpageid;
							
						}

				}, 'json');
			}
			//canvas.renderAll();
            return false;
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
			
			console.log(activeObject.orgSrc);
			
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
			
			console.log(activeObject);
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
		
        
        this.addnewimage = addnewimage;
		function addnewimage( newsrc2, id, newPosX, newPosY, scaleX , scaleY, angle,  strokeWidth, stroke, oldObject  ) {
            // Init canvas:
			
            var img = new Image(); 
            img.onload = function() {
                var fImg = new fabric.Newimage(this, {
                    originX: 'center',
                    originY: 'center',
                    left: newPosX,
                    top: newPosY,
					scaleX :scaleX,
					scaleY: scaleY,
					id: id,
					angle: angle,
					strokeWidth: strokeWidth,
					stroke: stroke
					
                });
				
                canvas.add(fImg);
                canvas.setActiveObject(fImg);
				
				if( oldObject ){
					oldObject.remove();
				}
				
				canvas.renderAll();
            };
            img.src = newsrc2;  
        }
		
		
		this.addframe = function(){
			
			var activeObject = canvas.getActiveObject();
			
			activeObject.set({
					strokeWidth: 20,
					stroke: '#FFF'
				});
			
			
			canvas.renderAll();
			
		}
		
		
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
        
		
		this.addtext = function( txt ){
			
			var color = $('#colorselect').val();
			//var font = $('#font-list').val();
			var font = $('#font-list span').css('font-family');
			var textAlign = $('#gravity').val();
			var textWeight = $('#textweight').val();
			var textopacity = $('#colorselect').attr( 'data-opacity' );
			
            var text = new fabric.IText(txt, {
								left: 100,
								top: 100,
								id: 'text',
								fill: color,
								opacity: textopacity,
								fontFamily: font,
								textAlign: textAlign,
								fontWeight: textWeight,
								fontSize: 100,
								scaleX :0.5,
								scaleY: 0.5
							});
            canvas.add(text);
			
			canvas.setActiveObject(text)
			
			return false;
        }
		
        
        this.updateText = function( txt ){
			
			//var font = $('#font-list').val();
			var font = $('#font-list span').css('font-family');
			var textAlign = $('#gravity').val();
			var textWeight = $('#textweight').val();
			
			var text = canvas.getActiveObject();
			
			
			if( text.type  != 'i-text' ){
				
				return false;
			}
			
			
			var fillcolor = $('#colorselect').val();
			var textopacity = $('#colorselect').attr( 'data-opacity' );
			
			var strokeWidth =  parseFloat(   $('input[name="strokewidth"]').val() );
			var stroke = $('#strokecolor').val();
			
			if( strokeWidth == 0 ){
				strokeWidth = null;
				stroke = null;
			}
			
			text.set(  {
				text: txt,
				fill: fillcolor,
				opacity: textopacity,
				fontFamily: font,
				textAlign: textAlign,
				fontWeight: textWeight,
				stroke: stroke,
				strokeWidth: strokeWidth
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
			var stroke = options.stroke;
			var strokeWidth = options.strokeWidth;

			font = font.replace("'", '');
			font = font.replace("'", '');
			
			$('#strokecolor').val(stroke);
			$('input[name="strokewidth"]').val(strokeWidth);
			$('textarea#text').val( txt );
			$('#colorselect').val( color);
			$('#font-list span').html(font).css({'font-family': font});
			$("#gravity").val(  textAlign );
			$("#textweight").val(  fontWeight );
			$('.radio, .textweight').css( 'background-position-x', '-7px' );
			$('#' + textAlign).css( 'background-position-x', '-37px' );
			$('#' + fontWeight).css( 'background-position-x', '-37px' );
			$('#colorselect').minicolors( 'destroy' );
			$('#colorselect').minicolors({
					opacity : true,
					change: function(hex, opacity) {

						_this.updateText( $('.textarea').val() );
					}
				});
			
			$('.minicolors-swatch span').css( 'background-color', color );
				$( "#tabs" ).tabs({
					active: 3
				});
		}
		
		
		
		
		this.savepage = function(){

		
			console.log( "STT" );
		
			var thumb = canvas.toDataURLWithMultiplier('jpg', 0.2, 60);			
			
			var data = JSON.stringify( canvas );
			var id = $('.selectedpage').attr('id');

            $.post('/api/1.0/tedit/savepage', {
                malid: templateid,
				malpageid: id,
				thumb: JSON.stringify( thumb ),
                data: data,
                }, function (response) {
                    console.log( response.message );
            }, 'json');
			
		}
		
		
		this.save = function(){

			$('.malpage').each( function(){
				
				
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
			});
			
			$.post('/api/1.0/tedit/saveorder', {
					pages: pages,
					malid: templateid
				}, function (response) {
						console.log( response );
						console.log( response );
				}, 'json' );
			
			
			
			console.log(pages);
			
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