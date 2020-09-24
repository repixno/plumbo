fabric.Object.prototype.originX = fabric.Object.prototype.originY = 'center';

var canvas = new fabric.Canvas('c');
var template = {};
var userimg = {};
var malid, text1, text2, maldata, malpageid;
var zoomingtimer = false;

var txtdata = templatedata(7188);

function updateText( ){
  
    var font = $('#font-list-btn').css('font-family');
    
    console.log( font );
    
    text2.set("fontFamily",  font );
    text1.set("fontFamily",  font );
    
    canvas.renderAll();
    return false;
}

$(function(){
    
    $('.text1input').on('focus', function(){
        canvas.setActiveObject(text1);
        canvas.renderAll();
    });
    $('.text2input').on( 'focus', function(){
        canvas.setActiveObject(text2);
        canvas.renderAll();
    });
    
    $('.text1input').on( 'keyup', function(){
        text1.setText( $(this).val());
        canvas.renderAll();
    });
    
    $('.text2input').on( 'keyup', function(){
        text2.setText( $(this).val());
        canvas.renderAll();
    });
    
    
    $('.text1-plus').on( 'click', function(){
        canvas.setActiveObject(text1);
        zoomplus( text1 );
    });
    
    $('.text2-plus').on( 'click', function(){
        canvas.setActiveObject(text2);
        zoomplus( text2 );
    });
    
    $('.text1-minus').on( 'click', function(){
        canvas.setActiveObject(text1);
        zoomminus( text1 );
    });
    
    $('.text2-minus').on( 'click', function(){
        canvas.setActiveObject(text2);
        zoomminus( text2 );
    });
    
    
    function zoomplus(object){
        var scale = object.scaleX;
        var step =  scale / 60;
        
        //console.log( step );
        
        if (step < 0) {
            step = -step;
        }
        object.scale(scale + step ).setCoords();
        canvas.renderAll(); 
    }
    function zoomminus(object){
        var scale = object.scaleX;
        var step =  scale / 60;
        
        console.log( step );
        if (step < 0) {
            step = -step;
        }
        object.scale(scale - step ).setCoords();
        canvas.renderAll(); 
    }
    
    $('#font-list').fontmenu();
    
    $('#add-gift-to-cart').click( function() { save(); });
    
    $(document).on( 'mouseout', '.canvas-container', function(){
        
        //alert("mouse out");
        if( userimg.width  ){
            //console.log( 'mouse out' );
            userimg.set( { opacity: 1 });
			canvas.discardActiveObject();
            canvas.renderAll();
        }
    });
    
    text1 = new fabric.IText("Din text här", {
        borderColor: 'red',
        cornerColor: 'green',
        cornerSize: 10,
        transparentCorners: false,
        left: parseInt( txtdata.text1.left ),
        top: parseInt( txtdata.text1.top ),
        textAlign: 'center', //"left", "center", "right" or "justify".
        fontSize: txtdata.text1.fontsize,
        fill: txtdata.text1.color,
        //hasRotatingPoint: false, //fjerna rotering
        fontFamily :txtdata.text1.fontfamily
       
        });
    
    canvas.add(text1);
    canvas.renderAll();
    canvas.bringToFront(text1);
    
    $('#skygge').on('change', function(){
        
        if( $(this).prop('checked') ){
            text1.setShadow("2px 2px 2px rgba(100, 100, 100, 0.5)");
            text2.setShadow("2px 2px 2px rgba(100, 100, 100, 0.5)");
        }
        else{
            text1.shadow = null;
            text2.shadow = null;
        }
        canvas.renderAll();
        
        })

    
    $('.text1input').val( "Din text här" );
    $('.text2input').val( "Din text här" );
    
    text2 = new fabric.IText("Din text här", {
        borderColor: 'red',
        cornerColor: 'green',
        cornerSize: 10,
        transparentCorners: false,
        left: parseInt( txtdata.text2.left ),
        top: parseInt( txtdata.text2.top ),
        textAlign: 'center', //"left", "center", "right" or "justify".
        fontSize: txtdata.text2.fontsize,
        fill: txtdata.text2.color,
        //hasRotatingPoint: false, //fjerna rotering
        fontFamily :txtdata.text2.fontfamily
        });
    canvas.add(text2);
    canvas.bringToFront(text2);
    
    
    canvas.on('text:changed', function(e) {
      var Myobj= canvas.getActiveObject();
        if(Myobj){ 
           var typeDesc = Myobj.type;
               if (typeDesc == 'i-text'){
                    textOriginal = Myobj.getText();
                    //hindre linjeskift
                    textRevised = textOriginal.replace(/(\r\n|\n|\r)/gm,"");
                    Myobj.set({ text: textRevised });
                    //oppdatere input tekstfelta
                    $('.text1input').val(text1.text);
                    $('.text2input').val(text2.text);
                }
                canvas.renderAll();  // Update canvas
        }            
  });
    
    // Left Image
    /*fabric.Image.fromURL(`https://static.repix.no/images/gifttemplates/thumbs/width/630/7188_3144_0.png`, (img) => {
        img.set({
          left: 199,
          top: 276
          // Scale image to fit width / height ?
        });
        img.scaleToHeight(552);
        img.scaleToWidth(397);
        img.selectable = false;
        template = img; 
        canvas.add(template);
        canvas.sendToBack(template);
    });*/
    
    //$(".template-thumb").first().click();
    
    changetemplate( $(".template-thumb").first()[0] );
    
    $(document).on('click','.template-thumb',  function() {
        changetemplate(this);
        return false;
    });
    
    function changetemplate(currentTemplate){
        
        //var currentTemplate = this;
        malid = $('.malid', currentTemplate).val();
        maldata =  $('.mal', currentTemplate).data();
        malpageid =  $('.malpageid', currentTemplate).val();
        var newtemplate = $(currentTemplate).find('.highres').attr('href');
        
        //console.log( maldata.fullsize_pos_dx );
        
        var textdata =  templatedata(malpageid);
        
        var scale = setimagesize( canvas );
        
        var fontfile = "arialblk.ttf";
			
        if( textdata.text1.fontfamily == "Comic Sans MS"){
            fontfile = "comic.ttf";
        }
        else if( textdata.text1.fontfamily == "Monotype Corsiva"){
            fontfile = "MTCORSVA.ttf";
        }
        else if( textdata.text1.fontfamily == "Arial"){
            fontfile = "arial.ttf";
        }
        else if( textdata.text1.fontfamily == "Times New Roman"){
            fontfile = "times.ttf";
        }
        
        
        else if( textdata.text1.fontfamily == "Lucida"){
            fontfile = "Lucida.ttf";
        }
        else if( textdata.text1.fontfamily == "MyriadRegular"){
            fontfile = "Myriad.ttf";
        }
        
        else if( textdata.text1.fontfamily == "Alex Brush"){
            fontfile = "AlexBrush.ttf";
        }
        
        $('#font-list button ').css('font-family', textdata.text1.fontfamily ).data('font', fontfile );
        $('#font-list b').text(textdata.text1.fontfamily);
        
        canvas.remove(template);
        fabric.Image.fromURL(newtemplate, (img) => {
            
            template = img;
            img.set({
              left: 199,
              top: 276
              // Scale image to fit width / height ?
            });
            
            console.log("userimga",  userimg );
            
            if( userimg.width ){
                console.log( userimg );
                userimg.scaleToHeight( Math.min( scale.dy, scale.dx ) );
                userimg.scaleToWidth( Math.min( scale.dy, scale.dx ) );
            }
            
            img.scaleToHeight(552);
            img.scaleToWidth(397);
            
            img.selectable = false;
            
            template = img;
            
            canvas.add(template);
            //canvas.sendToBack(template);
            canvas.bringToFront(text1);
            canvas.bringToFront(text2);
            
            //canvas.renderAll();
            
            text2.set({
                    fill : textdata.text2.color,
                    fontFamily : textdata.text2.fontfamily,
                    top:  parseInt( textdata.text2.top )
                });
            text1.set({
                    fill : textdata.text1.color,
                    fontFamily : textdata.text1.fontfamily,
                    top:  parseInt( textdata.text1.top )
                });
            
            //text2.setColor( textdata.text2.color ).set("fontFamily",  textdata.text2.fontfamily ).set( 'top', parseInt( textdata.text2.top ) ) ;
            //text1.setColor( textdata.text1.color ).set("fontFamily",  textdata.text1.fontfamily ).set( 'top', parseInt( textdata.text1.top ) );
            
            canvas.renderAll();
            
            $("#sp-light input").spectrum("set", textdata.text2.color );
            
          });
        
        return false;
    }
    
    $('.choose-product').on( 'change', function(){
			
			var quantity = 0;
			if( $(this).prop('checked') ){
				quantity = 1;
			}
			$(this).parent().find('.productquantity').val(quantity);
			
			
			});
    
    function setimagesize( canvas ){
        
        var original_template_width = canvas.getWidth();
        var original_template_height = canvas.getHeight();
    
        if(  original_template_width > original_template_height ){
            var ratio = maldata.fullsize_x / maxtemplatewidt;
        }
        else{
            var ratio = (maldata.fullsize_y / maxHeight);
        }
     
        var scaledata = {};
        
        scaledata.x = parseFloat(  maldata.fullsize_pos_x / ratio );
        scaledata.y = parseFloat(  maldata.fullsize_pos_y / ratio );
        scaledata.dx = parseFloat(  maldata.fullsize_pos_dx / ratio );
        scaledata.dy = parseFloat(  maldata.fullsize_pos_dy / ratio );
        
        return scaledata;
    }
    
    $('.reset').on( 'click', function(){
        userimg.set({
                      left: 199,
                      top: 276,
                      scaleX: 1,
                      scaleY: 1
                    });
        canvas.requestRenderAll();
        })
    
		$('.zooms').bind('touchstart', function(){
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
    
    
    $("#sp-light input").spectrum({
			theme: "sp-light",
			move: function(color){
                text1.setColor(  color.toHexString() );
                text2.setColor(  color.toHexString() );
                canvas.renderAll();
			},
			chooseText: "Bekräftelse",
			cancelText: "Annullera",
			showPalette: true,
			palette: [
				["rgb(0, 0, 0)", "rgb(255, 255, 255)","rgb(255, 205, 0)", "rgb(0, 75, 135)", "rgb(67, 67, 67)", "rgb(240,128,128)" ],
				["rgb(152, 0, 0)", "rgb(255, 0, 0)", "rgb(255, 153, 0)", "rgb(255, 255, 0)", "rgb(0, 255, 0)",
				"rgb(0, 255, 255)", "rgb(74, 134, 232)", "rgb(0, 0, 255)", "rgb(153, 0, 255)", "rgb(255, 0, 255)", "rgb(231, 0, 147)","rgb(255, 203, 56)"]
			]
		});
    
    canvas.on('mouse:down', function(opt) {
        // inside opt you found `e`
        // you should be able to calculate where you clicked
        var point = opt.target.canvas.getPointer(opt.e);
        
        //console.log(userimg.width);
        
        if( userimg.width && ( point.x > 70 && point.y > 110 ) && ( point.x < 303 && point.y < 431 ) ){
            //console.log("TEST", opt.target.text);
            if( !opt.target.text ){
                userimg.set( { opacity: 0.8 })
                canvas.setActiveObject(userimg);
                canvas.renderAll();
            }
        }
        
        //console.log( point );
      });
    
    canvas.on('before:selection:cleared', function(options){
        
        //console.log( options );
        //console.log("activew imavgew", userimg.active);
            
				if( userimg.width ){
					userimg.set( { opacity: 1 });
					//userimg.sendToBack();
					//userimg.active = false;
				}
	});
    
    /*canvas.on('mouse:down', function(e) {
        
        if( !e.target.text ){
            canvas.setActiveObject(userimg);
            canvas.renderAll();
        }
        
        console.log( e.target );
        
    });*/
    
    /* canvas.on('mouse:over', function(e) {
        //e.target.set('fill', 'red');
        
        console.log(e);
        
        if( template != e.target && e.target.text ){
            console.log(e.target);
            canvas.setActiveObject(e.target);
            canvas.renderAll();
         }
         else{
            canvas.setActiveObject(userimg);
            canvas.renderAll();
         }
      });
     */
     canvas.on('mouse:out', function(e) {
        var active = canvas.getActiveObject();
        //console.log(active);
        if( active ){
            var objType = active.type;
            //console.log( objType );
            if( objType !== "i-text" || objType !== "image" ){
                //canvas.discardActiveObject(e.target);
                //canvas.renderAll();
            }
        }
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
             canvas.remove(userimg);
            $.each(data.result.files, function (index, file) {
                
                //console.log( index  );
                //console.log( file  );
                var image = '<img class="imagethumb" id="' + file.id + '"src="' + file.thumbnailUrl +'" style="height: 75px"  />';
                $('#upload-queue').append( image );
                selectedImageId = $(this).attr('id');
                var backgroundImage = this
                //$('#choose-template .template-thumb').css('background-image', 'url('+ file.thumbnailUrl +')' );
                $('img#' + file.id ).css( 'border-color', "#0199d8" );
                $('.progress').hide();
                var imageId = file.id;
                $('#image').remove();
                
                // inserts the thumb for faster feedback
                $('#image').attr('src', file.thumbnailUrl );
                $('#selected-image').val(imageId );
                
                fabric.Image.fromURL(getImage(imageId) , (img) => {
                    img.set({
                      left: 199,
                      top: 276,
                      hasControls: false
                      // Scale image to fit width / height ?
                    });
                    
                    
                    img.scaleToHeight(400);
                    img.scaleToWidth(400);
                    
                      userimg = img;
                      
                      canvas.add(userimg);
                      canvas.sendToBack(userimg)
                });
                
                var img = new Image();
                
                $(img).load( function() {
                    
                    
                    //stretchToFit();
                    
                    $('#add-gift-to-cart').removeAttr('disabled');
                                            
                }).attr('src', getImage(imageId) );
                
                
        
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
    

      
});


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

function save(){
		
		$( '.productquantity' ).each( function(){
			
			if( $(this).val() > 0 ){
        
        //console.log( $(this).data());
			
				var productoptionid = $(this).data('productoptionid');
				var productid = $(this).data('productid');
				var quantity = $(this).val();
				
				if( productid == 4134 ){
					
					$.post( '/api/1.0/cart/add', {
						'prodno' : 7462,
						'quantity' : quantity
						});
					
				}else{
          //console.log(maldata);
					var ratioWidth = maldata.fullsize_x / canvas.width;
					var ratioHeight = maldata.fullsize_y / canvas.height;
					var position = $('#image').position();
					var pages = new Array();
					pages.push();
					pages[0] = new Object();
					
					pages[0].malid = malid;
					pages[0].malpageid = malpageid;
					pages[0].giftQuantity = $('#gift-quantity').val();
					pages[0].productoptionid = productoptionid;
					pages[0].productid = productid;
					pages[0].size = "small";
					pages[0].editor_x = canvas.width;
					pages[0].editor_y = canvas.height;
					
					if ( $('#red-eye').is(':checked') ) {
						pages[0].redeye = 'true';
					}
                    
        //console.log( userimg );
        
        var imw = userimg.width * userimg.scaleX;
        var imh = userimg.height * userimg.scaleY;
        
        var imx = userimg.left - ( imw / 2 );
        var imy = userimg.top - ( imh / 2 );
                    
					pages[0].image = new Object()
					pages[0].image.x = imx * ratioWidth;
					pages[0].image.y = imy * ratioHeight;
					pages[0].image.dx = imw * ratioWidth;
					pages[0].image.dy = imh * ratioHeight;
					pages[0].image.bid = $('#selected-image').val();
                    
					pages[0].texts = new Array();
		
					canvas._iTextInstances.forEach( function(txtobj) {
            
            //console.log(txtobj);
            
            
           
            
						var positiontop =  txtobj.top - ( txtobj.height / 2 );
						var positionleft  = txtobj.left - ( txtobj.width / 2 );
						var text = new Object();
						text.text = Base64.encode( txtobj.text );

						if( txtobj.shadow ){
							text.shadow = "on";
						}
						
            //console.log( txtobj.fontFamily );
            var fontfile = "";

            if( txtobj.fontFamily == '"Comic Sans MS"'){
                fontfile = "comic.ttf";
            }
            else if( txtobj.fontFamily == 'Comic Sans MS'){
                fontfile = "comic.ttf";
            }
            else if( txtobj.fontFamily == '"Lucida Sans"' ){
              fontfile = "Lucida.ttf";
            }
            else if( txtobj.fontFamily == '"Monotype Corsiva"'){
                fontfile = "MTCORSVA.ttf";
            }
            else if( txtobj.fontFamily == "Arial"){
                fontfile = "arial.ttf";
            }
            else if( txtobj.fontFamily == "Impact" ){
              fontfile = "impact.ttf";
            }
            else if( txtobj.fontFamily == '"Alex Brush"' ){
              fontfile = "AlexBrush.ttf";
            }
            else if( txtobj.fontFamily == '"Times New Roman"' ){
              fontfile = "times.ttf";
            }
            
            
            //console.log(fontfile);
            //return false;
            
            text.font = fontfile;
                        
						text.color = txtobj.fill;
						text.gravity = 'center';
						text.zindex = 1;
						var textwidth =  txtobj.width;
						var textheight =  txtobj.height;
						
						//console.log( $(this).find('span').text()  );
						//console.log( "textwidth " + textwidth + "textheighth " + textheight + " posttop " +  positiontop );
						//console.log(  );
						
						text.x = positionleft * ratioWidth;
						text.y = positiontop * ratioHeight;
						text.dx = textwidth * ratioWidth;
						text.dy = textheight * ratioHeight;
            text.rotate = txtobj.angle ? parseInt( txtobj.angle ) : 0;
						//console.log(text);
						pages[0].texts.push(text);
						
					});
					
          //return false;
					pages[0].cliparts = new Array();
					
					/*
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
					*/
          
          //console.log( pages );
          //return false;
					
					$.ajax({
						type: 'POST',
						url: '/create/save',
						dataType: 'JSON',
						data: {
							pages: JSON.stringify( pages )
						},
						success: function(msg) {
							
							//console.log(msg);
							
							// document.location.href = '/cart/';
							// return false;
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
			}
		});
		
		
		$('#confirm').modal();
	}

function getImage(imageid) {
    var height = 400;
    var width = maxtemplatewidt;
    //return '/show_image_stream.php?bid='+imageid+'&dx='+width+'&dy='+height;
    return '/images/stream/image/'+imageid+'/'+width+'/'+height;
}
	
    
function templatedata(malid){
    
    var temptext = {								
        //mal 1
        7023 :{
            text1 : {
                top : '48',
                fontsize: '80',
                color : '#118c98',
                fontfamily: 'Arial Black'
            },
            text2 : {
                top : '477',
                left: '100',
                fontsize: '80',
                color : '#118c98',
                fontfamily: 'Arial Black'
            }
        },
        
        
        //mal 2 standard 
        6997 :{
            text1 : {
                top : '70',
                fontsize: '50',
                color : '#ffffff',
                fontfamily: 'Comic Sans MS'
            },
            text2 : {
                top : '503',
                left: '10x',
                fontsize: '40',
                color : '#ffffff',
                fontfamily: 'Comic Sans MS'
            }
        },
        
        
         //mal  2020
         
        7188 :{
            text1 : {
                top : '70',
                left: '199',
                fontsize: '50',
                color : '#004B87',
                fontfamily: 'Comic Sans MS'
            },
            text2 : {
                top : '477',
                left: '199',
                fontsize: '40',
                color : '#004B87',
                fontfamily: 'Comic Sans MS'
            }
        },
        
        
        
        //mal 2 2020 
        7202 :{
            text1 : {
                top : '70',
                fontsize: '50',
                color : '#ffffff',
                fontfamily: 'Comic Sans MS'
            },
            text2 : {
                top : '503',
                left: '10x',
                fontsize: '40',
                color : '#ffffff',
                fontfamily: 'Comic Sans MS'
            }
        },
        
        
         //mal 3 2020 
        7203 :{
            text1 : {
                top : '70',
                fontsize: '50',
                color : '#ffffff',
                fontfamily: 'Comic Sans MS'
            },
            text2 : {
                top : '503',
                left: '10x',
                fontsize: '40',
                color : '#ffffff',
                fontfamily: 'Comic Sans MS'
            }
        },
        
        
        
        //mal 4 2020 
        7204 :{
            text1 : {
                top : '70',
                fontsize: '50',
                color : '#ffffff',
                fontfamily: 'Comic Sans MS'
            },
            text2 : {
                top : '503',
                left: '10x',
                fontsize: '40',
                color : '#ffffff',
                fontfamily: 'Comic Sans MS'
            }
        },
        
        
          //mal 5 2020 
        7205 :{
            text1 : {
                top : '30',
                fontsize: '30',
                left: '10x',
                color : '#ffffff',
                fontfamily: 'Comic Sans MS'
            },
            text2 : {
                top : '503',
                left: '10x',
                fontsize: '40',
                color : '#ffffff',
                fontfamily: 'Comic Sans MS'
            }
        },
        
        
        
         //mal 6 2020 
        7206 :{
            text1 : {
                top : '70',
                fontsize: '50',
                color : '#ffffff',
                fontfamily: 'Comic Sans MS'
            },
            text2 : {
                top : '503',
                left: '10x',
                fontsize: '40',
                color : '#ffffff',
                fontfamily: 'Comic Sans MS'
            }
        },
        
         //mal 7 2020 
        7208 :{
            text1 : {
                top : '70',
                fontsize: '50',
                color : '#ffffff',
                fontfamily: 'Comic Sans MS'
            },
            text2 : {
                top : '503',
                left: '10x',
                fontsize: '40',
                color : '#ffffff',
                fontfamily: 'Comic Sans MS'
            }
        },
        
        
        
        
        
       //mal 8 2020 
        7207 :{
            text1 : {
                top : '70',
                fontsize: '50',
                color : '#ffffff',
                fontfamily: 'Comic Sans MS'
            },
            text2 : {
                top : '503',
                left: '10x',
                fontsize: '40',
                color : '#ffffff',
                fontfamily: 'Comic Sans MS'
            }
        },
        
        
        
        //mal 9 2020 
        7209 :{
            text1 : {
                top : '70',
                fontsize: '50',
                color : '#ffffff',
                fontfamily: 'Comic Sans MS'
            },
            text2 : {
                top : '503',
                left: '10x',
                fontsize: '40',
                color : '#ffffff',
                fontfamily: 'Comic Sans MS'
            }
        },
          
          
           //mal 10 2020 
        7210 :{
            text1 : {
                top : '70',
                fontsize: '50',
                color : '#ffffff',
                fontfamily: 'Comic Sans MS'
            },
            text2 : {
                top : '503',
                left: '10x',
                fontsize: '40',
                color : '#ffffff',
                fontfamily: 'Comic Sans MS'
            }
        },
        
        
        
        
         //mal 11 2020 
        7211 :{
            text1 : {
                top : '50',
                fontsize: '50',
                color : '#2977d5',
                fontfamily: 'Comic Sans MS'
            },
            text2 : {
                top : '503',
                left: '10x',
                fontsize: '40',
                color : '#2977d5',
                fontfamily: 'Comic Sans MS'
            }
        },
        
        
          //mal 12 2020 
        7212 :{
            text1 : {
                top : '50',
                fontsize: '50',
                color : '#ffffff',
                fontfamily: 'Comic Sans MS'
            },
            text2 : {
                top : '503',
                left: '10x',
                fontsize: '40',
                color : '#ffffff',
                fontfamily: 'Comic Sans MS'
            }
        },
        
          //mal 13 2020 
        7213 :{
            text1 : {
                top : '70',
                fontsize: '50',
                color : '#ffffff',
                fontfamily: 'Comic Sans MS'
            },
            text2 : {
                top : '503',
                left: '10x',
                fontsize: '40',
                color : '#ffffff',
                fontfamily: 'Comic Sans MS'
            }
        },
        
        
           //mal 14 2020 
        7214 :{
            text1 : {
                top : '60',
                fontsize: '50',
                color : '#ffffff',
                fontfamily: 'Comic Sans MS'
            },
            text2 : {
                top : '513',
                left: '10x',
                fontsize: '40',
                color : '#ffffff',
                fontfamily: 'Comic Sans MS'
            }
        },
        
          //mal 15 2020 
        7215 :{
            text1 : {
                top : '50',
                fontsize: '50',
                color : '#ffffff',
                fontfamily: 'Comic Sans MS'
            },
            text2 : {
                top : '503',
                left: '10x',
                fontsize: '40',
                color : '#ffffff',
                fontfamily: 'Comic Sans MS'
            }
        },
        
        
          //mal 16 2020 
        7216 :{
            text1 : {
                top : '50',
                fontsize: '50',
                color : '#ffffff',
                fontfamily: 'Comic Sans MS'
            },
            text2 : {
                top : '503',
                left: '10x',
                fontsize: '40',
                color : '#ffffff',
                fontfamily: 'Comic Sans MS'
            }
        },
        
        
        
          //mal 17 2020 
        7217 :{
            text1 : {
                top : '70',
                fontsize: '50',
                color : '#000000',
                fontfamily: 'Comic Sans MS'
            },
            text2 : {
                top : '503',
                left: '10x',
                fontsize: '40',
                color : '#000000',
                fontfamily: 'Comic Sans MS'
            }
        },
        
          //mal 18 2020 
        7218 :{
            text1 : {
                top : '60',
                fontsize: '50',
                color : '#3f99ff',
                fontfamily: 'Alex Brush'
            },
            text2 : {
                top : '503',
                left: '10x',
                fontsize: '40',
                color : '#3f99ff',
                fontfamily: 'Alex Brush'
            }
        },
        
          //mal 19 2020 
        7219 :{
            text1 : {
                top : '50',
                fontsize: '50',
                color : '#3f99ff',
               fontfamily: 'Alex Brush'
            },
            text2 : {
                top : '450',
                left: '10x',
                fontsize: '40',
                color : '#3f99ff',
               fontfamily: 'Alex Brush'
            }
        },
        
        
          //mal 20 2020 
        7220 :{
            text1 : {
                top : '70',
                fontsize: '50',
                color : '#006ed2',
                fontfamily: 'Comic Sans MS'
            },
            text2 : {
                top : '503',
                left: '10x',
                fontsize: '40',
                color : '#006ed2',
                fontfamily: 'Comic Sans MS'
            }
        },
        
          //mal 21 2020 
        7221 :{
            text1 : {
                top : '50',
                fontsize: '50',
                color : '#3f99ff',
                fontfamily: 'Comic Sans MS'
            },
            text2 : {
                top : '523',
                left: '10x',
                fontsize: '40',
                color : '#3f99ff',
                fontfamily: 'Comic Sans MS'
            }
        },
        
        
          //mal 22 2020 
        7222 :{
            text1 : {
                top : '70',
                fontsize: '50',
                color : '#3f99ff',
                fontfamily: 'Comic Sans MS'
            },
            text2 : {
                top : '503',
                left: '10x',
                fontsize: '40',
                color : '#3f99ff',
                fontfamily: 'Comic Sans MS'
            }
        },
        
           //mal 23 2020 
        7223 :{
            text1 : {
                top : '50',
                fontsize: '50',
                color : '#db850e',
                fontfamily: 'Alex Brush'
            },
            text2 : {
                top : '503',
                left: '10x',
                fontsize: '40',
                color : '#db850e',
                 fontfamily: 'Alex Brush'
            }
        },
        
        
           //mal 24 2020 
        7224 :{
            text1 : {
                top : '70',
                fontsize: '50',
                color : '#0d88cc',
                fontfamily: 'Alex Brush'
            },
            text2 : {
                top : '503',
                left: '1x',
                fontsize: '40',
                color : '#0d88cc',
              fontfamily: 'Alex Brush'
            }
        },
        
        
        
        
             //mal 1 test 2020 
        7227 :{
            text1 : {
                top : '70',
                fontsize: '50',
                color : '#0d88cc',
                fontfamily: 'Alex Brush'
            },
            text2 : {
                top : '503',
                left: '1x',
                fontsize: '40',
                color : '#0d88cc',
              fontfamily: 'Alex Brush'
            }
        },
        
        
        
      
        
        
        
       
        
        
    };
    return temptext[malid];
}
    
    
    
    
    
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
        if( zoom == 'zoom-in' ){
            userimg.scale( scale + step ).setCoords();
            if( zoomingtimer ){
                setTimeout( 'newzoom("zoom-in")', 20 );
            }
        }else if( zoom == 'zoom-out' ){
            userimg.scale( scale - step ).setCoords();
            if( zoomingtimer ){
                setTimeout( 'newzoom("zoom-out")', 20 );
            }
        }

        canvas.renderAll(); 
    }

	  function stopzoom(){
        zoomingtimer = false;
    } 
    
        /*$("#text").on("click", function(e) {
            
            
        text = new fabric.Text("Kake", { left: 100, top: 100 });
              canvas.add(text);
        });

        rect = new fabric.Rect({
        left: 40,
        top: 40,
        width: 50,
        height: 50,      
        fill: 'transparent',
        stroke: 'green',
        strokeWidth: 5,
                  });  
      canvas.add(rect);

        rect = new fabric.Circle({
        left: 40,
        top: 40,
        radius: 50,     
        fill: 'transparent',
        stroke: 'green',
        strokeWidth: 5,
                  });  
      canvas.add(rect);

    
    $("#save").on("click", function(e) {
        $(".save").html(canvas.toSVG());
    });*/

