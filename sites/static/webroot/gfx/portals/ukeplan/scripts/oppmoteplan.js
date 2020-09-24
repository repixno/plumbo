   
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
               'plantype' : plantype
           };

            var headergrid = new Object();
            
            //console.log( $('.toptextplaceholder') );
            
            $('.headergridplaceholder table').each( function(index, item ){
              var position =  $(this).position();
              headergrid[index] = {
                      'title' : $(this).find('img').attr('title'),
                      'height' : $(this).height() * ratio,
                      'width' : $(this).width()  * ratio, 
                      'border-color' : $(this).css('border-color'),
                      'background-color' : $(this).css('background-color'),
                      'x'            : position.left * ratio,
                      'y'            : position.top * ratio,
                      'tr'           : $(this).find('tr').size()
              }
              
               //console.log( $(this).height() * ratio );
               //console.log( $(this).width()  * ratio );
               //console.log( $(this).css('border-color'));
               //console.log( $(this).position());
           });
           
           /*var topgridcontainer = new Array();
           
           $('.topgridcontainer table').each( function(index, item ){
                     var position =  $(this).position();
                     
                     topgridcontainer[index] = {
                        'height' : $(this).height() * ratio,
                        'width' : $(this).width()  * ratio, 
                        'border-color' : $(this).css('border-color'),
                        'background-color' :  $(this).find('td').first().css('background-color'),
                        'x'            : position.left * ratio,
                        'y'            : position.top * ratio,
                        'tr'           : $(this).find('tr').size()
                     }
            
              //console.log( item )
              
              
           });*/
           
           
            var bottomgrid = new Object();
            
            $('.bottomgridplaceholder table').each( function(index, item ){
              var position =  $(this).position();
              bottomgrid[index] = {
                      'title' : $(this).find('img').attr('title'),
                      'height' : $(this).height() * ratio,
                      'width' : $(this).width()  * ratio, 
                      'border-color' : $(this).css('border-color'),
                      'background-color' : $(this).css('background-color'),
                      'x'            : position.left * ratio,
                      'y'            : position.top * ratio,
                      'tr'           : $(this).find('tr').size()
              }
              
               //console.log( $(this).height() * ratio );
               //console.log( $(this).width()  * ratio );
               //console.log( $(this).css('border-color'));
               //console.log( $(this).position());
           });
           
           //data.headergrid = headergrid;
           //data.topgridcontainer = topgridcontainer;
           
           var maingridrows  = $('.maingrid').size() / 2;
            
           data.grid = {
              'color' : $('#color').val(),
              'quantity' : numberofdays,
              'gridvalues' : gridarray,
              'maingridrows' : maingridrows
           }

           data.weekdays = {
               'font': 'Century_Gothic',
               'color': $('#text-color').val(),
               'background' : $('#day-color-background').val()
           };

           data.product = {
               'productid': $('#productid').val(),
               'productoptionid': $('#productoptionid').val(),
               'quantity': $('#quantity').val()
           };

           data.images = images;
           data.names = names;
           data.headergrid = headergrid;
           data.bottomgrid = bottomgrid;
           
           //console.log( data );
           
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
      $('.textplaceholder').append('<table class="texttable"><tr></tr></table>');
      $(gridarray).each( function( index, value ){
         $('.textplaceholder tr').append( '<td><img class="day" alt="' + value + '" src="" /></td>');
      });
   }
   
   //Setup color and width for weekdays
   function setupText() {
       var fontcolor = $('#day-color').val().replace('#', '');
       var backgroundcolor = $('#day-color-background').val().replace('#', '');
       
       var daywidth = parseIntUkeplan( ( $('.gridplaceholder').width() / numberofdays ) );
       var dayheight = imagetext_heigth;
       
       $('img.day').each(function () {
            
          if( $(this).attr('alt') == 'Navn:' || $(this).attr('alt') == "ANSATT:"){
             var align = "&align=left";
          }else{
             var align = '';
          }
            
           fontcolor = "000000";
           $(this).attr('src', '/api/1.0/gift/text_barnehageplan/?id=0&quality=1&png=1&font=arial&gravity=West&color=' + fontcolor + '&background=' + backgroundcolor + '&text=' + $(this).attr('alt') + '&width=' + dayheight  + '&height=' + daywidth + '&rotate=' + rotate + '&orientation=landscape' + align)
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
         
         var headtext = $('.headergridplaceholder img').attr('title');
         var headertext = "/api/1.0/gift/toptext?text=" + headtext + "&width=" +  (width / 2 )  + "&height=" + ( height / 2 ) + "&color=" + color +  "&background=" + toptextbackgroundcolor + "&font=Century_Gothic.TTF&gravity=left" + '&bottom_margin=0';
         
         $('.headergridplaceholder img').attr( { 'src' : headertext, height : height / 2 } );
         
         //$('.bottomgridplaceholder td').html('<img id="cropbox3" class="bottomtext" src="' + bottomtext + '" style="position: relative;" height="'+( height / 2 ) +'" imageid="bottomtext" />'); 
         
         $('.bottomgridplaceholder img').each( function(){
            var bottomtext ="/api/1.0/gift/toptext?text=" + $(this).attr('title') + "&width=" + 200 + "&height=" + 34 + "&color=" + color +  "&background=" + toptextbackgroundcolor + "&font=Century_Gothic.TTF&gravity=left" + '&bottom_margin=0';
            $(this).attr( { 'src' : bottomtext, height : height / 2 } );
            
         })

         
   }
   
        
   
   //setup the template color
   function setupTemplateColor() {
       //var bordercolor = $('#color').val();
       var bordercolor = "#000000";
       var backgroundcolor = $('#color').val();

       if( imagelayout == 'text' ){
         $('.imageplaceholder').css({'border-color': "#FFF"});
       }else{
         $('.imageplaceholder').css({'border-color': "#FFF"});
       }
       $( 'table.grid td, .grid, .main-content td, .topgrid td ' ).css({
           'border-color': "#FFF"
       });
       
       $( 'table.bottomgrid td' ).css({
           'border-color': "#000"
       });
       
       var i = 0;
       $('.maingrid').each( function( index, item ){

          if( i < 2 ){
            $(this).find('.main-content td').css( {'background-color': backgroundcolor, 'border-color': backgroundcolor } );
            $(this).find('.main-first td').css( {'background-color': backgroundcolor, 'border-color': backgroundcolor} );
            $(this).find('.main-content').css( {'border-color': backgroundcolor } );
          }else{
            $(this).find('td').css( {'background-color': "#ffffff" ,'border-color': bordercolor } );
            $(this).find('.main-content').css( {'border-color': "#000" } );
          }
          i++;
          if( i == 4 ){
             i = 0;
          }
          //console.log( index );
          
       });
      
      
      if( plantype == 'oppmoteplan'  ){
        $('.main-content').each( function(){
                  
                  if( $(this).find('td').css( 'background-color') == 'rgb(255, 255, 255)' ){
                          $(this).find( 'td' ).first().css( { 'border-right':'1px solid ' + bordercolor , 'width' : '50%'} );
                  }
                  else{
                          $(this).find( 'td' ).first().css( { 'border-right':'1px solid #fff' , 'width' : '50%'} );
                          
                  }
  
          });
        }
      //$('.main-content td').first().css( { 'border-right':'1px solid black' , 'width' : '50%'} );
      $('.grid').first().css( {'border-color': '#ffffff'} );
      $('.grid').first().find('td').css( {'border-color': '#ffffff'} );
   }


   //initial setup of template
   function setupTemplate() {
       var image_spaces = (malArray.imagefield_space / ratio);
       var top = parseIntUkeplan(malArray.imagefield_y / ratio);
       var imagefieldheigth = parseIntUkeplan( malArray.imagefield_height   / ratio  );
       var imagefieldwidth  = parseIntUkeplan( malArray.imagefield_width   / ratio  );
       var imagemargin = parseIntUkeplan( malArray.imagefield_x   / ratio  );
       
       $('.logo').css({
           width: imagefieldwidth+ 'px'
       });

       $('.editorcontainer').css({
           height: parseIntUkeplan(malArray.mal_height / ratio) + 'px',
           width: parseIntUkeplan(malArray.mal_width / ratio) + 'px'
       });

       $('.imageandtextplaceholder').css({
           height: imagefieldheigth + 'px',
           width: imagemargin + imagefieldwidth + 'px',
           left: imagemargin + 'px',
           top: top + 'px'
       });
       
       top += imagefieldheigth;
       $('.headergridplaceholder').css({
           height: imagefieldheigth / 3  + 'px',
           width: imagemargin + imagefieldwidth  + 'px',
           left: imagemargin + 'px',
           top: top + 'px'
       });
       $('.imageplaceholder').css({
           height: imagefieldheigth + 'px'
       });

       var gridwidth = parseIntUkeplan( ( imagefieldwidth / numberofcolumns ) - 2 );
       top += imagefieldheigth / 1.5  ;
       
       $('div.textplaceholder').css({
           top: top  + 'px',
           height: imagetext_heigth + 'px',
           width: ( malArray.imagefield_width  / ratio ) + 'px',
           left: (malArray.imagefield_x / ratio) + 2 + 'px'
       });
       
       //top +=imagetext_heigth / 1.5;
       top += imagetext_heigth;
       
       var mainheight = $('.editorcontainer').height() - top  - ( imagemargin  * 2 ) - 100;
            
       $('.maingrid').css({
           height: ( mainheight / $('.firstmain').size() ) - 2  + 'px'
       });
       
       var firstmain  = parseIntUkeplan( ( imagefieldwidth /  numberofcolumns ) - image_spaces ) *  1.5 ;

       $('.firstmain').css({
           width:  firstmain +  'px'
       });
    
        $('div.textplaceholder td').first().css('width', firstmain + 'px');
       
       $('.secondmain').css({
           width: ( ( imagefieldwidth - firstmain )  + 6 ) + 'px'
       });
       
       
       var mainwidth = ( ( malArray.imagefield_width  / ratio ) - firstmain  ) / ( numberofcolumns  - 1 ) - 2;
       $('.main-content').css({
           width: mainwidth + 'px',
           height: ( mainheight / $('.firstmain').size() ) - 2  + 'px',
           'margin-bottom': '0em',
           'float':'left',
           'margin-left': '0.2em',
       })
       
       $('.main-first').css({
         height: ( mainheight / $('.firstmain').size() ) - 2  + 'px',
         'margin': '0em'
       })
       
      $('td.gridspace').css({
         'height' : '2px',
         'border' : '0px'
      });
   
       
       $('.gridplaceholder').css({
           left: (malArray.imagefield_x / ratio) + 'px',
           //top: ((malArray.imagefield_x + malArray.imagefield_height) / ratio) + ( ( malArray.imagefield_height / 2 )  / ratio) + 'px',
           top: top + 'px',
           width: ((malArray.imagefield_x + malArray.imagefield_width) / ratio) + 'px',
       });
       //$('.texttable').attr('height', ( malArray.imagefield_height / 2 )  / ratio );



       $('table.grid, table.maingrid').css( {'margin-left': '0px', 'margin-top': '2px'});
       
       
       top += mainheight + 5 ; 
       //console.log(top);

        $('.bottomgridplaceholder').css({
                width: ((malArray.imagefield_x + malArray.imagefield_width) / ratio) + 'px',
                left: (malArray.imagefield_x / ratio) + 'px',
                top: top + 'px',
        });     
       
       
       var bottomgridlength = $('.bottomgrid').size();
       
       if( bottomgridlength > 1 ){
          var bottomgridwidth  = parseIntUkeplan(malArray.imagefield_width / ratio / bottomgridlength)  -  1;
       }else{
          var bottomgridwidth  = parseIntUkeplan(malArray.imagefield_width / ratio / bottomgridlength);
       }
       
       $('.bottomgrid').css({
            height: $('.editorcontainer').height() - top - imagemargin + 'px',
            width: bottomgridwidth + 'px',
       });
          
       $('.bottomgrid').each( function(key, item ){
          
          if( key > 0 ){
            $(this).css({
               'margin-left' : '2px'
             });
          }
       });

   }

   
   function setupMaingrid( quantity ){
         var i = 1;

         if( ( quantity  > ( $('.maingrid').size() / 2 ) ) ){
            var add = quantity - ( $('.maingrid').size() / 2 );
            //console.log( 'wtf ');
            while( i <= add ){
                  $('.maingrid').last().parent().after('<div>' + $('.maingrid').last().parent().html() + '</div>');
                  i++;
            }
         }else{
            var remove = ( $('.maingrid').size() ) - ( quantity * 2 );
            i = 0;                     
            while( i < remove ){
                  $('.maingrid').last().remove();
                  i++;
            }
         }
         setupTemplate();
         setupTemplateColor();     
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
              
        holdercount++;
        var holderwidth = (malArray.imagefield_width / ratio / 1) - image_spaces;
        $('.grid').each(function (index, item) {
            if (index == 0) {
                $(this).parent().after('<div>' + $(this).parent().html() + '</div>');
                //console.log( $(this) );     
            }
        });
       $('.topgrid').each(function (index, item) {
             if (index == 0) {
                 $(this).parent().after('<div>' + $(this).parent().html() + '</div>');
                 //console.log( $(this) );     
             }
       });
       

       //console.log( holderwidth );
       //$( '.current-grid' ).removeClass('current-grid').after( $('.gridplaceholder').html() ).addClass('current-grid');
       setupTemplate();
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
       

       $('div.imageplaceholder').css('width', ( gridholderwidth * holdercount ) + holdercount + (  holdercount  - 4 ) );
       $('table.grid, table.topgrid').css('width', gridholderwidth );

       


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

       $( '#numberofrows' ).change( function(){
          //console.log( " test ");
          //console.log( $(this).val());
          
          setupMaingrid( $(this).val() );
          
       })

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
                 'title': "Bekreft ditt prosjekt",
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

       var i = 0;
       while (i < 5) {
           newColumn();
           i++;
       }
       
       setupMaingrid(20);
       setupTemplateColor();
       
       
       

   });