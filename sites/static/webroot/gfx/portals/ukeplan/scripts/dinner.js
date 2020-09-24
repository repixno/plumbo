   
   //adds project to cart.
   function save(){
           var image = null;
           var name = null;
           order = new Array();
           var i = 0;

           var data = new Object();
           var images = new Object();
           var names = new Object();
           var bw = 0;
           var rotate = 0;
           
           $('div.imageplaceholder').each(function () {
               image = $(this).find('.cropbox');
               if (image.hasClass('blackAndWhite')) {
                   bw = 1;
               } else {
                   bw = 0;
               }
               if (image.hasClass('rotate')) {
                  var url = $(this).find('img').attr("src");
                  rotate = url.split('/').pop();
               }else{
                  rotate = 0;
               }
               var imagetext = '';
               var toptextfont = '';
               var toptextcolor = '';
               if( imagelayout == 'text'){
                  imagetext = toptext;
                  toptextfont = $('#text-font').val();
                  toptextcolor =$('#text-color').val();
               }

               order[i] = new Array();

               images['image' + i] = {
                   'sorting': i,
                   'imagefield_width': $(this).width(),
                   'imagefield_height': $(this).height(),
                   'width': image.width(),
                   'height': image.height(),
                   'margin-left': image.css('margin-left'),
                   'margin-top': image.css('margin-top'),
                   'imageid': image.attr('imageid'),
                   'blackandwhite': bw,
                   'rotate': rotate,
                   'toptext'  : imagetext,
                   'toptextfont': toptextfont,
                   'toptextcolor': toptextcolor,
               };
               i++;
           });


           i = 0;
           $('.imagetext').each(function () {
               names['name' + i] = {
                   'sorting': i,
                   'width': $(this).width(),
                   'height': $(this).height(),
                   'font': $('#text-font').val(),
                   'color': $('#text-color').val(),
                   'text': $(this).attr('alt')
               }
               i++;
           });
           
           data.template = {
               'prodno': malArray.prodno,
               'malid': malArray.mal_id,
               'mal_width': malArray.mal_width,
               'mal_height': malArray.mal_height,
               'imagefield_height': malArray.imagefield_height,
               'imagefield_width': malArray.imagefield_width,
               'imagefield_x': malArray.imagefield_x,
               'imagefield_y': malArray.imagefield_y,
               'imagefield_space': malArray.imagefield_space,
               'editorcontainer_width': editorcontainer_width,
               'ratio': ratio,
               'gridcolor': $('#color').val(),
               'numberofcolumns': numberofcolumns,
               'background': $('#background').val(),
               'clipart': $('input[name=clipart]:checked').val(),
               'maskit': $('input[name=maskit]:checked').val(),
               'imagelayout': imagelayout,
               'plantype' : 'middagsplanlegger' 
           };
           
           data.grid = {
              'color' : $('#color').val(),
              'quantity' : numberofdays,
              'gridvalues' : gridarray
           }

           data.weekdays = {
               'font': 'Century_Gothic',
               'color': $('#day-color').val(),
               'background' : $('#day-color-background').val()
           };

           data.product = {
               'productid': $('#productid').val(),
               'productoptionid': $('#productoptionid').val(),
               'quantity': $('#quantity').val()
           };

           data.images = images;
           data.names = names;
           
           
           
            if ( portal == "UP-DK" ) {
                var redirecturl = "/kurv/ugeplan/accessories";
            //code
           }else{
                var redirecturl = "/cart/ukeplan/accessories/";
           }
           
           
           $.ajax({
               type: 'post',
               cache: false,
               url: '/api/1.0/editor/ukeplan',
               data: data,
               success: function (msg) {
                   document.location.href = redirecturl;
                   return false;
               },
               complete: function (msg) {
                   //document.location.href = '/cart/';
                   return false;
               },
               error: function (msg) {
                   alert('error');
               }
           });
           
           
           
           return false;
   }

   
   function parseIntUkeplan( rnum ) { 
            var newnumber = Math.round(rnum * Math.pow(10, 0)) / Math.pow(10, 0);
            return newnumber;
   }
   
   function setupgrid(){
      $('.textplaceholder').append('<table class="texttable"></table>');
      $(gridarray).each( function( index, value ){
         $('.textplaceholder table').append( '<tr><td><img class="day" alt="' + value + '" src="" /></td></tr>');
      });
   }
   
   //Setup color and width for weekdays
   function setupText() {
       var fontcolor = $('#day-color').val().replace('#', '');
       var backgroundcolor = $('#day-color-background').val().replace('#', '');
       
       var daywidth = parseIntUkeplan( ($('.grid').height() / numberofdays) ) - 2;
       var dayheight = parseIntUkeplan(malArray.imagefield_x / ratio) - 2;
       
       $('img.day').each(function () {
           $(this).attr('src', '/api/1.0/gift/text_barnehageplan/?id=0&quality=1&png=1&font=arial&gravity=West&color=' + fontcolor + '&background=' + backgroundcolor + '&text=' + $(this).attr('alt') + '&width=' + daywidth + '&height=' + dayheight + '&rotate=' + rotate)
           //console.log( fontcolor );
       });
   }

        
   function updateTopText( text ){
      
         text = encodeURIComponent( text );
         var height = $('.imageplaceholder').height();
         var width = $('.imageplaceholder').width();
         var color = encodeURIComponent( $('#text-color').val() );
         var toptextbackgroundcolor = encodeURIComponent( backgroundcolor );
         var font = $('#text-font').val();
         
         var toptext = "/api/1.0/gift/toptext?text=" + text + "&width=" + width + "&height=" + height + "&color=" + color +  "&background=" + toptextbackgroundcolor + "&font=" + font + '&bottom_margin=0';
         
         $('.imageplaceholder').html('<img id="cropbox1" class="cropbox" src="' + toptext + '" style="position: relative;" height="'+height+'" imageid="textimage" />');
 
   }
   
        
   
   //setup the template color
   function setupTemplateColor() {
       var bordercolor = $('#color').val();

       if( imagelayout == 'text' ){
         $('.imageplaceholder').css({'border-color': backgroundcolor});
       }else{
         $('.imageplaceholder').css({'border-color': bordercolor});
       }
       $('.grid').css({
           'border-color': bordercolor
       });
       $('table.grid td').css({
           'border-color': bordercolor
       })
   }


   //initial setup of template
   function setupTemplate() {
       var image_spaces = (malArray.imagefield_space / ratio);
       $('.logo').css({
           width: ((malArray.imagefield_width) / ratio) + 'px'
       });

       $('.editorcontainer').css({
           height: (malArray.mal_height / ratio) + 'px',
           width: (malArray.mal_width / ratio) + 'px'
       });

       $('.imageandtextplaceholder').css({
           height: parseIntUkeplan(malArray.imagefield_height / ratio) + 'px',
           width: ((malArray.imagefield_x + malArray.imagefield_width) / ratio) + 'px',
           left: (malArray.imagefield_x / ratio) + 'px',
           top: (malArray.imagefield_y / ratio) + 'px'
       });

       $('.imageplaceholder').css({
           height: parseIntUkeplan(malArray.imagefield_height / ratio) + 'px'
       });

       $('.grid').css({
           //width: ( ( malArray.imagefield_width / ratio / numberofcolumns ) - image_spaces ) + 'px',
           height: gridheight + 'px'
       }).height(gridheight);
       $('.gridplaceholder').css({
           left: (malArray.imagefield_x / ratio) + 'px',
           top: ((malArray.imagefield_x + malArray.imagefield_height) / ratio) + 3 + 'px',
           width: ((malArray.imagefield_x + malArray.imagefield_width) / ratio) + 'px',
       });
       $('.texttable').attr('height', gridheight);

       $('div.textplaceholder').css({
           top: ((malArray.imagefield_x + malArray.imagefield_height) / ratio) + 3 + 'px',
           height: gridheight + 'px'
       });

       $('table.grid').css('margin-left', '2px');
   }



   function preview(img, selection) {

       var scaleX = $('#' + selectedcropbox).parent().width() / (selection.width || 1);
       var scaleY = $('#' + selectedcropbox).parent().height() / (selection.height || 1);

       $('#' + selectedcropbox).css({
           width: (scaleX * $('#previewimage').width()) + 'px',
           height: (scaleY * $('#previewimage').height()) + 'px',
           marginLeft: '-' + (scaleX * selection.x1) + 'px',
           marginTop: '-' + (scaleY * selection.y1) + 'px'
       });

       var orgimg = originalimage[$('#' + selectedcropbox).attr('imageid')];
       var quality = parseIntUkeplan((orgimg.y * 2) / $('#' + selectedcropbox).height() * ratio);
       if (quality > 100) {
           quality = 100;
       }

       $("#pb1").progressBar(quality, {
           showText: false
       });
   }


   function setupNames() {

       $('.namescontainer').css({
           left: 0 + 'px',
           top: $('div.imageplaceholder').height() - textimageheight + 'px'
       });

       var color = $('#text-color').val().replace('#', '');
       var backgroundcolor = $('#day-color-background').val().replace('#', '');
       var font = $('#text-font').val();
       var width = $('table.grid').width() - 2;

       //if( imagelayout == 'each' ){
       $('img#imagetext').each(function () {
          
           var name = $(this).attr('alt');
           textimageheight = 20;
           var imgsrc = '/api/1.0/gift/names_ukeplan/?id=0&quality=1&png=1&font=' + font + '&gravity=West&color=' + color + '&background=' + backgroundcolor + '&text=' + name + '&width=' + width + '&height=' + textimageheight + '"';

           $(this).attr({
               'src': imgsrc,
               'alt': name
           });
       })

       $('img.imagetext').css({
           'width': width + 'px',
           'margin-left': '4px'
       });
       if( imagelayout == 'text' ){
         updateTopText( toptext )
       }
       //}
   };

   function updateName(object) {
       var name = $('input[name=inputtext]').val();
       var color = $('#text-color').val().replace('#', '');
       var backgroundcolor = $('#day-color-background').val().replace('#', '');
       var font = $('#text-font').val();
       var width = $('table.grid').width() - 2;

       var imgsrc = '/api/1.0/gift/names_ukeplan/?id=0&quality=1&png=1&font=' + font + '&gravity=West&color=' + color + '&background=' + backgroundcolor + '&text=' + name + '&width=' + width + '&height=' + textimageheight + '"';

       object.parent().parent().find('.imagetext').attr({
           'src': imgsrc,
           'alt': name
       });

       $('.edittext').remove();

       return false;
   }

   function loginDialog() {
       $('#loginDialog').dialog({
           'modal': true,
           'width': 500,
           'height': 320,
           'buttons': {
               'Login': function () {
                   $('#loginDialog form').submit();
               }
           }
       });
       $('#loginDialog').dialog('open');
       return false;
   }

   function inlineImageUploadCallback(imageid, image) {

       $("#imageselect").dialog('close');
       if (image) {
           pickImage(image);
       } else {
           alert('Could not retrive image information' + imageid);
       }

       /*	$.ef( 'image.info', {
		'imageid': imageid 
	}, function( response ) {
		if( response.result ) {
			alert( 'works' );
			pickImage( response.image );
		} else {
		alert( 'Could not retrive image information' );
		}
	});*/
   }

   function imagePicker() {

       //console.log( imagePickerLoaded );
       if (imagePickerLoaded) {
           $('#imagePicker').dialog('open');
       } else {
           $('#imagePicker').dialog({
               'width': 950,
               //span-18
               'height': 570,
               'modal': true,
               'resizable': false,
               'draggable': false,
               'open': function () {
                   //load albums
                   if (!imagePickerLoaded) {
                       $('#imagePicker .albums').append(loader);
                       var response = $.ef('albums.enum_new');

                       if (response.result) {
                           imagePickerLoaded = true;
                           $('#imagePicker .loader').remove();
                           $(response.albums).each(function (i, album) {
                               $('#imagePicker ul.albums').append(
                               $('<li/>').append(
                               $('<a/>', {
                                   'href': '#',
                                   'text': shorten(album.title, 22),
                                   'title': album.title,
                                   'click': function () {
                                       $('#imagePicker ul.albums li a.selected').removeClass('selected');
                                       $(this).addClass('selected');
                                       loadAlbum($(this), album, '#imagePickerImages');
                                   }
                               })))

                               if (i == 0) {
                                   $(this).addClass('selected');
                                   loadAlbum($(this), album, '#imagePickerImages');
                               }
                           }

                           )
                       }
                   }

               }
           });
       }

       return false;

   }

   /*function loadImage(){

      
	var newimage = new Image();
	newimage.src = $('#' + selectedcropbox ).attr('src');
	
	var x1 = $('#x1').attr('value');
	var y1 =  $('#y1').attr('value');
	var bid = $('#bid').attr('value');
	
	var imageinfo = {'x':x1, 'y':y1, 'id':bid};
	
   currentImage = imageinfo;	
	$(newimage).load( function() {
		previewHeight = newimage.height;
      previewWidth = newimage.width;
      
      if(previewHeight > previewWidth){ 
         $('#portrait').click();
      }
      else{
         $('#landscape').click();
      }  
	});
	$('#add-to-cart').removeClass('disabled');
	$('.startup').hide();
	
}*/


   function pickImage(image) {
       $('#imagePicker').dialog('close');
       var newimage = new Image();
       newimage.src = image.screensize;

       originalimage[image.id] = image;

       $(newimage).load(function () {
           $('#' + selectedcropbox + ', #previewimage').attr('src', image.screensize).attr('imageid', image.id);
           currentImage = image;
           previewHeight = newimage.height;
           previewWidth = newimage.width;

           if (previewHeight > previewWidth) {
               $('#portrait').click();
           } else {
               $('#landscape').click();
           }
           //updateOnChange($('#printtype').attr('value'));
           $('#' + selectedcropbox).removeClass('blackAndWhite')
           $('#cropbutton').removeAttr('disabled');
           loadImgSelect();
       });
       $('#add-to-cart').removeClass('disabled');
       $('.startup').hide();

       $('#imagecropbox').dialog({
           title: "Rediger bilde",
           resizable: false,
           draggable: false,
           modal: true,
           height: 550,
           width: 650,
           close: function () {
               $('#previewimage').imgAreaSelect({
                   remove: true
               });
           }
       });
   }



   function loadImgSelect() {

       if ($('#' + selectedcropbox).hasClass('blackAndWhite')) {
           $('#blackandwhite').val('color').text('Farger');
       } else {
           $('#blackandwhite').val('bw').text('Sort/hvit');
       }

       var marginwidth = 20;

       var imgwidth = $('#previewimage').width() - (marginwidth * 2);
       var imgheight = $('#previewimage').height() - (marginwidth * 2);
       var printwidth = $('#' + selectedcropbox).parent().width();
       var printheight = $('#' + selectedcropbox).parent().height();
       var imgratio = imgheight / imgwidth;
       var printratio = printheight / printwidth;



       if (printwidth > printheight) {
           if (imgratio > printratio) {
               var tx2 = imgwidth;
               var ty2 = imgwidth * (printheight / printwidth);
           } else {
               var ty2 = imgheight;
               var tx2 = imgheight / (printheight / printwidth);
           }
       } else {
           if (imgratio < printratio) {
               var ty2 = imgheight;
               var tx2 = imgheight / (printheight / printwidth);
           } else {
               var tx2 = imgwidth;
               var ty2 = imgwidth * (printheight / printwidth);
           }
       }


       var tx1 = ((imgwidth - tx2) / 2) + marginwidth;
       tx2 = (tx1) + (tx2);
       var ty1 = ((imgheight - ty2) / 2) + marginwidth;
       ty2 = (ty1) + (ty2);
       $('#previewimage').imgAreaSelect({
           x1: tx1,
           y1: ty1,
           x2: tx2,
           y2: ty2,
           handles: true,
           persistent: true,
           aspectRatio: printwidth + ':' + printheight,
           onInit: preview,
           onSelectEnd: preview,
       });


   }


   function loadAlbum(element, album, target) {
       //$(target).append( loader );
       var selectedImage = false;
       $.ef('album.images.enum', {
           albumid: album.id

       }, function (result) {

           $(target).empty();

           $(result.images).each(function (i, image) {
               $(target).append(
               $('<li/>').append(
               $('<a/>', {
                   'href': '#',
                   'click': function () {
                       pickImage(image);
                       return false;
                   }
               }).append('<img src="' + image.thumbnail + '" />')));
           });
       });

       return selectedImage;

   }

   function newColumn(imageonly) {

       var holdercount = $('.grid').length;
       var imageplaceholdercount = $('.imageplaceholder').length;
       var image_spaces = (malArray.imagefield_space / ratio);
       
       var i = 0;

       if (clipartcolumn == true) {
           var last = 1;
       } else {
           var last = 0;
       }

       if (imagelayout == 'each') {
           $('.imageplaceholder').each(function (index, element) {
               i++;
               if (imageplaceholdercount - last == index + 1) {
                   holdercount++;
                   $(this).after(' <div class="imageplaceholder center"><img id="cropbox' + holdercount + '" class="cropbox" src="' + velgbilde + '" style="position: relative;" height="150" imageid="noimage"/></div>').addClass('lastholder');
               }
               $(this).find('img.cropbox').attr('id', 'cropbox' + i);
           })

           if (holdercount == $('.imageplaceholder').length) {
               $('.grid').each(function (index, item) {
                   if (index == 0) {
                       $(this).parent().after('<div>' + $(this).parent().html() + '</div>');
                       //console.log( $(this) );     
                   }
               });
           } else {
               holdercount--;
           }
           imageholderwidth = ( malArray.imagefield_width / ratio  -  ( image_spaces * ( holdercount - 1 ) ) - ( holdercount * 2 ) )  / holdercount;
       } else{
           holdercount++;
           var holderwidth = (malArray.imagefield_width / ratio / 1) - image_spaces;
           $('.grid').each(function (index, item) {
               if (index == 0) {
                   $(this).parent().after('<div>' + $(this).parent().html() + '</div>');
                   //console.log( $(this) );     
               }
           });
       }

       if ($('input[name=names]:checked').val() == 'names') {
           var namecount = $('.imagetextplaceholder').length;

           $('.imagetextplaceholder').each(function (index, element) {
               if (namecount - last == index + 1) {
                   $(this).after('<div class="imagetextplaceholder"><img id="imagetext" alt="Sett inn tekst" class="imagetext" src="" /></div>');
               }
           });


       }
       //console.log( holderwidth );
       //$( '.current-grid' ).removeClass('current-grid').after( $('.gridplaceholder').html() ).addClass('current-grid');
       setupTemplate();
       setupTemplateColor();
       //console.log( imageholderwidth );
       //var gridholderwidth = imageholderwidth.toFixed(0);
       
       if( imagelayout == 'each' ){
          gridholderwidth = imageholderwidth + 2; 
       }
       else{
          gridholderwidth = (malArray.imagefield_width / ratio / holdercount) - image_spaces + 2;
       }
       
       imageholderwidth = parseIntUkeplan( imageholderwidth );
       gridholderwidth = parseIntUkeplan( gridholderwidth );
       
       if ( imagelayout == 'each' ){
          $('div.imageplaceholder').css('width', imageholderwidth );
          $('table.grid').css('width', gridholderwidth );
       }else{
          $('div.imageplaceholder').css('width', ( gridholderwidth * holdercount ) + holdercount + (  holdercount  - 4 ) );
          $('table.grid').css('width', gridholderwidth );
       }
       
       $('table.grid').css('margin-left', '2px');

       setupNames();

       return false;

   }


   
   function deleteColumn() {

       //clipartcolumn = false;
       $('input[name=clipart]').attr('checked', false);


       var columnlength = $('.grid').size();
       var image_spaces = (malArray.imagefield_space / ratio);
       var holdercount = columnlength - 1;
       if (imagelayout == 'each') {
           $('.imageplaceholder').each(function (index, item) {
               if (index == columnlength - 1) {

                   $(this).remove();
               }
           });
           imageholderwidth = ( malArray.imagefield_width / ratio  -  ( image_spaces * ( holdercount - 1 ) ) - ( holdercount * 2 ) )  / holdercount;
       } else{
           imageholderwidth = (malArray.imagefield_width / ratio) - image_spaces;
       }
       
       $('.grid').each(function (index, item) {
           if (index == columnlength - 1) {
               $(this).parent().remove();
           }
       });
       
       setupTemplate();
       setupTemplateColor();
       //var gridholder = (malArray.imagefield_width / ratio / holdercount) - image_spaces;
       
       if( imagelayout == 'each' ){
          gridholderwidth = imageholderwidth + 2; 
       }
       else{
          gridholderwidth = ( (malArray.imagefield_width / ratio / holdercount) - image_spaces ) + 2; 
       }

       imageholderwidth = parseIntUkeplan( imageholderwidth );
       gridholderwidth = parseIntUkeplan( gridholderwidth );
       
       if ( imagelayout == 'each' ){
          $('div.imageplaceholder').css('width', imageholderwidth );
          $('table.grid').css('width', gridholderwidth );
       }else{
          $('div.imageplaceholder').css('width', ( gridholderwidth * holdercount ) + holdercount + (  holdercount  - 4 ) );
          $('table.grid').css('width', gridholderwidth );
       }
       
       $('table.grid').css('margin-left', '2px');
       if ($('input[name=names]:checked').val() == 'names') {

           $('.imagetextplaceholder').each(function (index, item) {
               if (index == columnlength - 1) {

                   $(this).remove();
               }
           });


           setupNames();
       }

       return false;

   }
   
   $(document).ready(function () {
       setupgrid();
       setupTemplate();
       /*setupTemplateColor();*/
       setupText();

       //mask it price		
       $.post('/api/1.0/prices/get', {
           productoptionid: 2870,
           quantity: 1
       }, function (data) {
           $('#maskit-price').text(data.price);
       }, 'json');

       $(".tip").click(function () {

           $('#tip').dialog({
               'title': 'Hjelp'
           });
           return false;
       });

       $(".qualitytip").click(function () {
           $('#qualitytip').dialog({
               'title': 'Hjelp',
               'zIndex': '3999',
               'modal': 'true'
           });
           return false;
       });
       $(".maskittip").click(function () {
           $('#maskittip').dialog({
               'width' : '550',
               'width' : '550',
               'title': 'Hjelp'
           });
           return false;
       });

       //$('div.logo img').attr('src', 'http://eurofoto.no/images/attachments/thumbs/height/' + (236 / ratio) + '/2867/logo.png')
       
       $('div.editorcontainer').css( "background-image", "url('http://c.static.repix.no/images/gifttemplates/thumbs/width/750/" + templatefile + "')" );
       
       $('#blackandwhite').click(function () {

           var image = $(this).parent().parent().find('img#previewimage');
           
           fullDomain = window.location.host; 
           brokenstring=image.attr('src').split('/');
           
           var bw = 0;

           if ($(this).val() == 'bw') {
               $(this).val('color').text('Farger');
               bw = 1;
               $('#' + selectedcropbox).addClass('blackAndWhite');
           } else if ($(this).val() == 'color') {
               $(this).val('bw').text('Sort/hvit');
               $('#' + selectedcropbox).removeClass('blackAndWhite');
           }
           if( !brokenstring[10] ){
              brokenstring[10] = 0;
           }
           
           var src =  "http://" + fullDomain + '/' + brokenstring[3] + '/' +  brokenstring[4] + '/' +  brokenstring[5] + '/'  +  brokenstring[6] + '/'  +  brokenstring[7] + '/'  +  brokenstring[8] + '/' + bw + '/' + brokenstring[10];

           image.attr('src', src);
           $('#' + selectedcropbox).attr('src', src);

       });

       $('#clipart').click(function () {

           $('#' + selectedcropbox).attr({
               'src': middagsbestikk,
               'height': malArray.imagefield_height / ratio + 'px',
               'imageid': 'dinner'
           }).css({
               'height': malArray.imagefield_height / ratio + 'px',
               'top': '',
               'left': '',
               'margin-left': '',
               'margin-top': '',
               'width': ''
           });

           $("#imageselect").dialog('close');

           return false;

       });

       $('#color').change(function () {
           $("select#color option:selected").each(function () {
               $('#color').css('background-color', $(this).css('background-color'));
               $('#color').css('color', $(this).css('color'));
           });

           setupTemplateColor();
           setupText();
           return false;
       });

       $('#day-color').change(function () {

           $("select#day-color option:selected").each(function () {
               $('#day-color').css('background-color', $(this).css('background-color'));
               $('#day-color').css('color', $(this).css('color'));
           });
           setupText();
           return false;
       });
       $('#text-color').change(function () {

           $("select#text-color option:selected").each(function () {
               $('#text-color').css('background-color', $(this).css('background-color'));
               $('#text-color').css('color', $(this).css('color'));
           });
           setupNames();
           return false;
       });
       $('#text-font').change(function () {
           setupNames();

       })

       $('#cropbutton').live('click', function () {
           $("#imageselect").dialog('close');

           $('#imagecropbox').dialog({
               modal: true,
               height: 500,
               width: 650
           });


           $("#previewimage").attr('src', $('#' + selectedcropbox).attr('src'));

           loadImgSelect();

           return false;

       });

       $('.imageplaceholder').html('<img id="cropbox1" class="cropbox" src="' + velgbilde + '" style="position: relative;" height="150" />');

       $('.cropbox').live('click', function () {
          
          if(imagelayout == 'text'){
           
            var value = decodeURIComponent( toptext );
            
            value = $.trim( value );
            
            if( value == 'Sett inn tekst'){
               value = '';
            }
            
            var boxwidth = $('.imageplaceholder').width();
            
            $(this).parent().parent().parent().append('<div class="center edittoptext" " style="width: ' + boxwidth + 'px">Skriv ny tekst<br/><textarea id="toptext" style="width:300px; height:30px;text-align:center;" wrap="off">' + value + '</textarea><br/><button id="toptext-confirm">Oppdater</button></div>');
            
            $('#toptext').focus();
          
          }
          else{
              if ($(this).attr('imageid') == 'dinner') {
                  return false;
              }
              if ($(this).attr('imageid') == 'noimage') {
                  $('#cropbutton').attr('disabled', 'disabled');
              } else {
                  $('#cropbutton').removeAttr('disabled');
              }
   
              $('#previewimage').imgAreaSelect({
                  remove: true
              });
              $("#imagecropbox").hide();
   
              selectedcropbox = $(this).attr('id');
   
              $("#imageselect").dialog({
                  title: "Velg bilde",
                  modal: true,
                  height: 300,
                  width: 450
              });
          }
           return false;
       });

       $('.imagetext').live('click', function () {

           $('.edittext').remove();

           var inputwidt = $('.grid').width() - 22;

           var value = $(this).attr('alt');

           if (value == 'Sett inn tekst') {
               value = '';
           }

           $(this).parent().append('<div class="edittext"><br/>Skriv ny tekst<br/><input type="text" name="inputtext" value="' + value + '" style="width: ' + inputwidt + 'px" /><a class="button" id="submittext" href="#" >Oppdater tekst</a></div>');

           $('.edittext').css({
               width: $('.grid').width() - 13 + 'px',
               height: 110 - 12 + 'px',
               top: '-' + (110 - textimageheight - 1) + 'px',
               left: '5px'
           });

           $('input[name=inputtext]').focus();
           return false;
       });

       $('input[name=inputtext]').live('keypress', function (e) {
           if (e.keyCode == 13) {
               updateName($(this));
           }
       });


       $('#submittext').live('click', function () {

           updateName($(this));

           return false;

       })

       $('a#openLoginDialog').click(function () {
           loginDialog();
           return false;
       });

       $('.container').on('hover', '.imageandtextplaceholder', function (e) {
           $('.imageplaceholder').css('cursor', 'pointer');
       });



       $('#new_column').click(function () {
           newColumn();
           return false;
       });

       $('#remove_column').click(function () {
           deleteColumn();
           return false;
       });



       $('#closecropbox').click(function () {
           $('#previewimage').imgAreaSelect({
               remove: true
           });
           $("#imagecropbox").dialog('close');
       })

       $('a.choose-image.button').live('click', function () {
           $("#imageselect").dialog('close');
           imagePicker();
           return false;
       });


       $('#order_ukeplan').live('submit', function () {
          
           $("#confirmtip").dialog( {
                 'buttons': {
                     'Jeg aksepterer': function () {
                         save();
                     },
                     'Avbryt' : function(){
                         $(this).dialog("close");
                     }
                     
                 },
                 'width': 360,
                 'modal': true
             }
           
           );
           
           return false;
       });


       $('input[name=names]').change(function () {
           $('.edittext').remove();
           if ($('input[name=names]:checked').val() == 'names') {
               $('.textchoices').show();
               $('table.grid').each(function () {
                   $('.namescontainer').prepend('<div class="imagetextplaceholder"><img id="imagetext" alt="Sett inn tekst" class="imagetext" src="" /></div>');
               });
               setupNames();
           } else {
              
              if( imagelayout != 'text' ){
                  $('.textchoices').hide();
              }
               $('.imagetext').remove();
           }

       });

       $('input[name=imagelayout]').change(function () {
           var image_spaces = malArray.imagefield_space / ratio;
           imagelayout = $(this).val();
           $('#imagefield_height').attr( 'disabled', 'true' );
           $('#textfield_height').attr( 'disabled', 'true' );
           src = velgbilde;
           var imageid = 'noimage';
           clipartcolumn = false;
           $('.grid').removeClass('dinner');
           $('input[name=clipart]').removeAttr('checked');
           $('.cropbox').each(function (index, item) {

           if (($('.cropbox').size() - 1) == index && imagelayout == 'each') {
                   $(this).attr({
                       'src': src,
                       'height': malArray.imagefield_height / ratio + 'px',
                       'imageid': imageid
                   }).css({
                       'height': malArray.imagefield_height / ratio + 'px',
                       'top': '',
                       'left': '',
                       'margin-left': '',
                       'margin-top': '',
                       'width': ''
                   });
               }
           });

           if (imagelayout == 'one') {
               $('#produksjonsvalg').show();
               
               $('#imagefield_height').removeAttr( 'disabled' );
               $('.textchoices').hide();
               $('.imageplaceholder').html('<img id="cropbox1" class="cropbox" src="' + velgbilde + '" style="position: relative;" height="150" />');
               malArray.imagefield_height = imagefield_one;
               gridheight = ((malArray.mal_height - malArray.imagefield_height - malArray.imagefield_x - bottommargin ) / ratio);
               setupTemplateColor();
               setupTemplate();
               setupText();

               columnlength = 1;
               var i = 0;
               $('.imageplaceholder').each(function (index, item) {
                   if (index > 0) {
                       $(this).remove();
                   }
               });

               $('.grid').each(function () {
                   i = $(this).width() + i;
               })

               i = ($('.grid').size() * 2) + i - 4;

               $('div.imageplaceholder').css('width', i);
               $('.imagetext').remove();
               $('input[name=names]').attr('checked', false);


           } else if (imagelayout == 'each') {
              $('#produksjonsvalg').show();
               $('.textchoices').hide();
               if ($('div.imageplaceholder').size() < numberofcolumns) {
                   malArray.imagefield_height = imagefield_each;
                   gridheight = ((malArray.mal_height - malArray.imagefield_height - malArray.imagefield_x - bottommargin) / ratio);
                   setupTemplate();
                   setupText();

                   var i = 0;
                   var columns = numberofcolumns - 1;
                   while (i < columns) {
                       newColumn();
                       i++;

                   }
               }
               //console.log(  );
               $('[imageid]').each( function(){
                   
                  $(this).attr( 'height', '150' ).css( 'height', '150px' );;
                  
               })
               
              
               $('.imagetext').remove();
               $('input[name=names]').attr('checked', false);
               //newColumn();
           }else if (imagelayout == 'text') {
               $('#produksjonsvalg').hide();
               $('.imageplaceholder').css({'border-color': backgroundcolor});
               $('#textfield_height').removeAttr( 'disabled' );
               malArray.imagefield_height = imagefield_each;
               gridheight = ((malArray.mal_height - malArray.imagefield_height - malArray.imagefield_x - bottommargin) / ratio);
               setupTemplate();
               setupText();

               columnlength = 1;
               var i = 0;
               $('.imageplaceholder').each(function (index, item) {
                   if (index > 0) {
                       $(this).remove();
                   }
               });

               $('.grid').each(function () {
                   i = $(this).width() + i;
               })

               i = ($('.grid').size() * 2) + i - 4;

               $('div.imageplaceholder').css('width', i);
               
               if( !$('input[name=names]').is(':checked') ){
                  $('input[name=names]').click();
               }

               updateTopText( toptext );
               //$('.imagetext').remove();
               //$('input[name=names]').attr('checked', true);
           }

           //console.log( i );      
           //var holderwidth = ( ( malArray.imagefield_width / ratio ) - image_spaces );
           //console.log( holderwidth );
           //$( 'div.imageplaceholder' ).css( 'width' , i ); 
       });

       $('input[name=clipart]').change(function () {

           var cropboxlength = $('.cropbox').size();

           if ($('input[name=clipart]:checked').val() == 'dinner') {
               src = middagsbestikk;
               var imageid = 'dinner';
               clipartcolumn = true;
               $('.grid').each(function (index, item) {
                   if ($('.grid').size() - 1 == index) {
                       $(this).addClass('dinner');
                       var dinnerheight = $('table.dinner td').height();
                       $('table.dinner td').css('background-size', (dinnerheight / 3.57) + 'px ' + dinnerheight + 'px');
                       //console.log( $(this) );
                   }
               });
           } else {
               src = velgbilde;
               var imageid = 'noimage';
               clipartcolumn = false;
               $('.grid').removeClass('dinner');
           }

           $('.cropbox').each(function (index, item) {

               if (($('.cropbox').size() - 1) == index && imagelayout == 'each') {
                   $(this).attr({
                       'src': src,
                       'height': malArray.imagefield_height / ratio + 'px',
                       'imageid': imageid
                   }).css({
                       'height': malArray.imagefield_height / ratio + 'px',
                       'top': '',
                       'left': '',
                       'margin-left': '',
                       'margin-top': '',
                       'width': ''
                   });
               }


           });




       });

       $('#columnquantity').change(function () {

           var currentColumns = $('.grid').size();
           var columns = $(this).val();
           numberofcolumns = columns;
           var i = 0;
           if (columns < currentColumns) {
               while (i < currentColumns - columns) {
                   deleteColumn();
                   i++;
               }
           } else {
               while (i < columns) {
                   if (currentColumns <= i) {
                       newColumn();
                   }
                   i++;
               }
           }

       });
       
       
      $('#rotateleft,#rotateright').click( function(){
         
         $('#previewimage').imgAreaSelect({
                  remove: true
              });
         
         var image = $(this).parent().parent().find('img#previewimage');
         fullDomain = window.location.host; 
         brokenstring=image.attr('src').split('/');
         
         if( !brokenstring[10] ){
            brokenstring[10] = 0;
         }
         if( !brokenstring[9] ){
            brokenstring[9] = 0;
         }
         var rotate =  parseInt( brokenstring[10]  ) + parseInt(  $(this).val() ) ;
         
         if( rotate == -90 || rotate == -270){
            rotate = 360 + rotate;
         }  
         if( rotate == 360 ||  rotate == -360 ){
            rotate = 0;
         }         
         var src =  "http://" + fullDomain + '/' + brokenstring[3] + '/' +  brokenstring[4] + '/' +  brokenstring[5] + '/'  +  brokenstring[6] + '/'  +  brokenstring[7] + '/'  +  brokenstring[8] + '/' + brokenstring[9] + '/' + rotate;
         
         $('#' + selectedcropbox).addClass('rotate');
         image.attr('src', src);
         $('#' + selectedcropbox).attr('src', src).load( loadImgSelect );
      })
      
      $('#imagefield_height').change( function(){
         
         imagefield_one = ( ( malArray.mal_height - malArray.imagefield_x - 877 ) * $(this).val()) / 100;
         malArray.imagefield_height = imagefield_one;
         gridheight = (malArray.mal_height - malArray.imagefield_height - malArray.imagefield_x - 877) / ratio;
         setupTemplate();
         setupText();
   
         columnlength = 1;
         var i = 0;

   
         $('.grid').each(function () {
             i = $(this).width() + i;
         })
   
         i = ($('.grid').size() * 2) + i - 4;
   
         $('div.imageplaceholder').css('width', i);
         $('.imagetext').remove();
         $('input[name=names]').attr('checked', false);
               
               
               
         
         //console.log( $(this).val() )
         
      });
      
      $('#textfield_height').change( function(){
         
         imagefield_one = ( ( malArray.mal_height - malArray.imagefield_x - 877 ) * $(this).val()) / 100;
         malArray.imagefield_height = imagefield_one;
         gridheight = (malArray.mal_height - malArray.imagefield_height - malArray.imagefield_x - 877) / ratio;
         setupTemplate();
         setupText();
   
         columnlength = 1;
         var i = 0;

   
         $('.grid').each(function () {
             i = $(this).width() + i;
         })
   
         i = ($('.grid').size() * 2) + i - 4;
   
         $('div.imageplaceholder').css('width', i);
         setupNames();
         updateTopText( toptext );    
               
               
         
         //console.log( $(this).val() )
         
      });
      
      
      $('#toptext-confirm').live( 'click', function(){
         
         //console.log( $('#toptext').val() );
         //$('input[name=names]').attr('checked', true);
         
         toptext =  $('#toptext').val();
         
         /*if( $.trim( toptext  ) == '' ){
            toptext = "       Sett inn tekst       ";
         }*/
         updateTopText( toptext );
          
          $('.edittoptext').remove();
      });


   });

   $(window).load(function () {

       var i = 1;
       //console.log( numberofcolumns );
       
       while (i < numberofcolumns) {
           newColumn();
           i++;
       }
       
        $('input[name=imagelayout]').change();

   });