   var notatwidth;
   
   
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
                   'margin-left': image.css('left'),
                   'margin-top': image.css('top'),
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
           
           if($('#notatoption').prop('checked')) {
                var notatoption = "true";
            } else {
                var notatoption = "false";
            }
           
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
               'notatoption' : notatoption
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
       var daywidth = parseIntUkeplan( ($('.grid').height() / numberofdays) ) - 2;
       var dayheight = parseIntUkeplan(malArray.imagefield_x / ratio) - 2;
       
       $('.notat').css('color', $('#day-color').val() );
       
       $('img.day').each(function () {
           $(this).attr('src', '/api/1.0/gift/text_ukeplan/?id=0&quality=1&png=1&font=arial&gravity=West&color=' + fontcolor + '&background=' + encodeURIComponent( backgroundcolor ) + '&text=' + $(this).attr('alt') + '&width=' + daywidth + '&height=' + dayheight + '&rotate=' + rotate)
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
         
         var toptext = "/api/1.0/gift/toptext?text=" + text + "&width=" + width + "&height=" + height + "&color=" + color +  "&background=" + toptextbackgroundcolor + "&font=" + font;
         
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
       $('.grid, .notatfelt').css({
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
        
        if( $('#notatoption').prop('checked' ) ){
             $('.notatfelt').css({
                 height: 1000 / ratio,
                 width:notatwidth + 'px',
                 left: (malArray.imagefield_x / ratio) + 2 + 'px',
                 top: ((malArray.imagefield_x + malArray.imagefield_height ) / ratio) + gridheight + 5 + 'px',
                 })
        }
        
        $('table.grid').css('margin-left', '2px');
    }

   function setupNames() {
       $('.namescontainer').css({
           left: 0 + 'px',
           top: $('div.imageplaceholder').height() - textimageheight + 'px'
       });

       var color = $('#text-color').val().replace('#', '');
       //var backgroundcolor = $('#day-color-background').val().replace('#', '');
       var textbackgroundcolor = backgroundcolor.replace('#', '');
       var font = $('#text-font').val();
       var width = $('table.grid').width() - 2;

       //if( imagelayout == 'each' ){
       $('img#imagetext').each(function () {
          
           var name = $(this).attr('alt');
           textimageheight = 20;
           var imgsrc = '/api/1.0/gift/names_ukeplan/?id=0&quality=1&png=1&font=' + font + '&gravity=West&color=' + color + '&background=' + textbackgroundcolor + '&text=' + name + '&width=' + width + '&height=' + textimageheight + '"';

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
       var textbackgroundcolor = backgroundcolor.replace('#', '');
       var font = $('#text-font').val();
       var width = $('table.grid').width() - 2;

       var imgsrc = '/api/1.0/gift/names_ukeplan/?id=0&quality=1&png=1&font=' + font + '&gravity=West&color=' + color + '&background=' + textbackgroundcolor + '&text=' + name + '&width=' + width + '&height=' + textimageheight + '"';

       object.parent().parent().find('.imagetext').attr({
           'src': imgsrc,
           'alt': name
       });

       $('.edittext').remove();

       return false;
   }

   function loginUser() {

        var username = $('input[name="username"]').val();
        var password = $('input[name="password"]').val();

   
        $.post( '/api/1.0/user/login/' , {
                        username: username,
                        password: password
                }, function(data){
                        if( data.message == "OK" ){
                                imagePickerLoaded = false;
                                $('.logininfo span:first-child').text(data.result.email);
                                $('.logininfo span:last-child').html('<a href="/logout">Logg ut</a>');
                                $('.loginbox').remove();
                                $('.getimagebox').show()
                                imagePicker();
                                
                                $('#loginbox').remove();
                        }else{
                                
                                $('#loginbox').append('<span class="label label-warning">'+ data.message +'</span>');
                        }
                }, 'json');
   
        }

   function imagePicker() {
        
        
        if( !imagePickerLoaded ){

                //$('#imagePicker .albums').append(loader);
                
                 $.post('/api/1.0/albums/enum_new', {
                 }, function (response) {

                     if (response.result) {
                         imagePickerLoaded = true;
                         $('#imagePicker .loader').remove();
                         $(response.albums).each(function (i, album) {
                             $('#imagePicker ul.albums').append(
                             $('<li/>').append(
                             $('<a/>', {
                                 'href': '#',
                                 'text': album.title,
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
                     imagePickerLoaded = true;
                 }, 'json');
                 
        }
   }


    function checkquality(e, result){
        
        var imageid = $(e.currentTarget).attr('imageid');
        var imageinfo = originalimage[imageid];
        var ratioimagefield =  malArray.imagefield_height  / ratio;
        var realcmheight = $(e.currentTarget).height() *  ratio / 118;
        var ppi = imageinfo.y / realcmheight * 2.5432;
        
        if( ppi >= 150 ){
            $(e.currentTarget).parent().find('.quality').attr('src', staticsite + 'gfx/ukeplan/new/top.png');
        }
        else if( ppi < 150 && ppi >= 50  ){
            $(e.currentTarget).parent().find('.quality').attr('src',staticsite + 'gfx/ukeplan/new/avg.png');
        }
        else if( ppi < 50 ){
            $(e.currentTarget).parent().find('.quality').attr('src',staticsite + 'gfx/ukeplan/new/bad.png');
        }
        
    }

   function pickImage(image) {
        
       var newimage = new Image();
       

       originalimage[image.id] = image;

       $(newimage).load(function () {
        
            if(  $('#' + selectedcropbox ).attr('imageid') == 'noimage' ){
                $('#' + selectedcropbox ).parent().html('<img id="' + selectedcropbox + '" class="cropbox" imageid="" src=""/>');
            }
            
            var cropwidth = $('.imageplaceholder').width();
            var cropheight =$('.imageplaceholder').height();
            
            $('#' + selectedcropbox ).attr('src', image.screensize).attr('imageid', image.id);
           
           
           
           $('#' + selectedcropbox ).cropbox({width:cropwidth, height:  cropheight}).on('cropbox', checkquality);;
           
           currentImage = image;
           previewHeight = newimage.height;
           previewWidth = newimage.width;

           if (previewHeight > previewWidth) {
               $('#portrait').click();
           } else {
               $('#landscape').click();
           }
           //updateOnChange($('#printtype').attr('value'));
           $('#' + selectedcropbox).removeClass('blackAndWhite');
           $('#' + selectedcropbox ).parent().append('<img class="quality" src="" data-toggle="modal" data-target=".qualitytip"/>');
           $('#cropbutton').removeAttr('disabled');
           
       });
       
       
       newimage.src = image.screensize;
       
       $('#add-to-cart').removeClass('disabled');
       $('.startup').hide();
       
        $('#imageselect').modal('hide');
       
   }
   
   function loadAlbum(element, album, target) {
       //$(target).append( loader );
       
       var selectedImage = false;
       $.post('/api/1.0/albums/images/enum', {
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
       }, 'json');

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
       
       
       if(  $( '#background' ).val() == "black" ){
            var textcolor = "#fff";
       }else{
            var textcolor = "#000";
       }

       if (imagelayout == 'each') {
           $('.imageplaceholder').each(function (index, element) {
               i++;
               if (imageplaceholdercount - last == index + 1) {
                   holdercount++;
                   $(this).after(' <div class="imageplaceholder center"><div id="cropbox' + holdercount + '" class="btn btn-default cropbox" " style="position: relative; color:#fff" height="150" imageid="noimage">' + velgbilde + '<div></div>').addClass('lastholder');
               }
               $(this).find('.cropbox').attr('id', 'cropbox' + i);
           })

           if (holdercount == $('.imageplaceholder').length) {
               $('.grid').each(function (index, item) {
                   if (index == 0) {
                       $(this).parent().after('<div>' + $(this).parent().html() + '</div>');  
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
                   $(this).after('<div class="imagetextplaceholder"><img id="imagetext" alt="' + skrivnytekst + '" class="imagetext" src="" /></div>');
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
       
       
        var imageplacholderheight = $('.imageplaceholder' ).height();
       
       if ( imagelayout == 'each' ){
          $('div.imageplaceholder').css('width', imageholderwidth );
          $('table.grid').css('width', gridholderwidth );
          
          
          $( 'div.imageplaceholder img' ).each( function(){
            
            $( this ).cropbox({width:imageholderwidth, height:  imageplacholderheight});
            
            });
          
          
          
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
       var imageplacholderheight = $('.imageplaceholder' ).height();
       
       if ( imagelayout == 'each' ){
          $('div.imageplaceholder').css('width', imageholderwidth );
          $('table.grid').css('width', gridholderwidth );
          
          
          $( 'div.imageplaceholder img' ).each( function(){    
                $( this ).cropbox({width:imageholderwidth, height:  imageplacholderheight});
            
            });
          
          
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
        
        $('#notatoption').on('change', function(){
                if($(this).prop('checked')){
                    notatheight = 1000;
                    notatwidth = ( ($('.grid').width() + 2) *  $('.grid').size() ) - 4;
                    $('.editorcontainer').append('<div class="notatfelt"><div class="notat">'+notattext+'</div></div>');
                }else{
                    notatheight = 0;
                    $('.notatfelt').remove();
                }
                gridheight = ((malArray.mal_height - malArray.imagefield_height - malArray.imagefield_x - bottommargin -  notatheight ) / ratio);
                setupTemplate();
                setupText();
                //console.log($(this).prop('checked'));
            
            });
        
        
        $(document).on( 'mouseover', '.imageplaceholder' , function(){
            $('.cropControls').css({ "opacity"  : "0", "filter": "alpha(opacity=0) }"} );
            $(this).find('.quality').show();
              
            $(this).find('.cropControls').css({ "opacity"  : ".95", "filter": "alpha(opacity=95) }"} );
            
            
            });
        
        
         $('.editorcontainer').mouseleave( function(){
            
            $('.cropControls').css({ "opacity"  : "0", "filter": "alpha(opacity=0) }"} );
            $('.quality').hide();
            
            });
        
        $("#oriantationmodal .cancel").on("click", function(e) {
            $("#oriantationmodal").modal('hide');
            $('select[name="orientation"]').val($('.selectedoption').val() );
        });
        $("#oriantationmodal .ok").on("click", function(e) {
            $("#oriantationmodal").modal('hide');
            window.location.href = $('select[name="orientation"]').val();
        });
        
        $('select[name="orientation"]').on( 'change' , function(){
            
                $("#oriantationmodal").modal({
                   "backdrop"  : "static",
                   "keyboard"  : true,
                   "show"      : true 
                 });
                    
                //window.location.href = $(this).val();
            
            });
                   
        $('#loginbutton').on( 'click', function(){
                        //loginUser();
                });
        
        $('#loginform').submit(function(e){
                e.preventDefault();
                loginUser();
                return false;
        });
        
        $('#fileupload').fileupload({
            dataType: 'json',
            done: function (e, data) {
                $.each(data.result.files, function (index, file) {
                    //$('<p/>').text(file.name).appendTo('#files');
                    
                    
                    var img = $('<img/>').addClass('uploadedthumb').attr( {src: file.thumbnail , id: file.id} );

                    
                    $('<span/>').append( img ).appendTo('#files');
                    $("#previewimage").attr('src',  file.screensize);
                    pickImage(file);
                    
                });
                $('#progress .progress-bar').css(
                    'width',
                    '0%'
                );
                //$('#imageselect').modal('hide'); 
            },
            progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('#progress .progress-bar').css(
                    'width',
                    progress + '%'
                );
            }
        }).prop('disabled', !$.support.fileInput).parent().addClass($.support.fileInput ? undefined : 'disabled');

        
        
        $(document).on( 'click', 'img.uploadedthumb', function(){
                        var thisimageid = $(this).attr('id');
                        pickImage( originalimage[thisimageid] );
                
                });

       $("select#color option:selected").each(function () {
               $('#color').css('background-color', $(this).css('background-color'));
               $('#color').css('color', $(this).css('color'));
           });
       $("select#text-color option:selected").each(function () {
               $('#text-color').css('background-color', $(this).css('background-color'));
               $('#text-color').css('color', $(this).css('color'));
           });
       $("select#day-color option:selected").each(function () {
               $('#day-color').css('background-color', $(this).css('background-color'));
               $('#day-color').css('color', $(this).css('color'));
           });
       //mask it price		
       $.post('/api/1.0/prices/get', {
           productoptionid: 2870,
           quantity: 1
       }, function (data) {
           $('#maskit-price').text(data.price);
       }, 'json');

       //$('div.logo img').attr('src', 'http://eurofoto.no/images/attachments/thumbs/height/' + (236 / ratio) + '/2867/logo.png')
       
       //$('div.editorcontainer').css( "background-image", "url('" + templatefile + "')" ); 
       
       $(document).on( 'click' ,'.bw' ,function () {

           var image = $(this).parent().parent().find('.cropbox');
           
           fullDomain = window.location.host; 
           brokenstring=image.attr('src').split('/');
           
           var bw = 0;

           if ($(this).attr('id') == 'bw') {
               $(this).attr('id', 'color').text('Farger');
               bw = 1;
               $(image).addClass('blackAndWhite');
           } else if ($(this).attr('id') == 'color') {
               $(this).attr('id', 'bw').text('Sort/hvit');
               $(image).removeClass('blackAndWhite');
           }
           if( !brokenstring[10] ){
              brokenstring[10] = 0;
           }
           
           var src =  "http://" + fullDomain + '/' + brokenstring[3] + '/' +  brokenstring[4] + '/' +  brokenstring[5] + '/'  +  brokenstring[6] + '/'  +  brokenstring[7] + '/'  +  brokenstring[8] + '/' + bw + '/' + brokenstring[10];

           image.attr('src', src);

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
           
           $('#' + selectedcropbox).parent().addClass('dinner');
           
           $('#imageselect').modal('hide');
           
           //$("#imageselect").dialog('close');

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

       $('.imageplaceholder').html('<div id="cropbox1" class="btn btn-default cropbox" style="position: relative; color: #fff" height="150"  imageid="noimage" >' + velgbilde + '</div>');
       
       
       $(document).on( 'click', '.openimagebox', function(){
            ///console.log($(this).parent().parent().find('.cropbox').attr('id'));
        
            selectedcropbox = $(this).parent().parent().find('.cropbox').attr('id');
            $('#imageselect').modal();
        });
       
       $(document).on('click', '.cropbox',  function () {
          
          if(imagelayout == 'text'){
           
            var value  = decodeURIComponent( toptext );
            
            value = $.trim( value );
            
            if( value == 'Legg til tekst'){
               value = '';
            }
            
            var boxwidth = $('.imageplaceholder').width();
            
             $(this).parent().parent().parent().append('<div class="center edittoptext" " style="width: ' + boxwidth + 'px">' +  skrivnytekst  + '<br/>\
                                                        <textarea id="toptext" style="width:300px; height:30px;text-align:center;" wrap="off" placeholder="' + textplaceholder + '">' + value + '</textarea><br/>\
                                                        <button id="toptext-confirm">' + oppdater + '</button></div>');
             
            $('#toptext').focus();
          
          }
          else if( $(this).attr('imageid') == 'noimage'){
             selectedcropbox = $(this).attr('id');
                $('#imageselect').modal();
          }
          else{
            //console.log("laskllas")
            
                //return true;
          }
           //return false;
       });
       
       $(document).on('click','.imagetext' , function () {

           $('.edittext').remove();

           var inputwidt = $('.grid').width() - 22;

           var value = $(this).attr('alt');

           if (value == skrivnytekst ) {
               value = '';
           }

           $(this).parent().append('<div class="edittext"><br/>' +  skrivnytekst  + '<br/><input type="text" name="inputtext" value="' + value + '" style="width: ' + inputwidt + 'px" /><a class="button" id="submittext" href="#" >' + oppdater + '</a></div>');

           $('.edittext').css({
               width: $('.grid').width() - 13 + 'px',
               height: 110 - 12 + 'px',
               top: '-' + (110 - textimageheight - 1) + 'px',
               left: '5px'
           });

           $('input[name=inputtext]').focus();
           return false;
       });

       $(document).on('keypress','input[name=inputtext]',  function (e) {
           if (e.keyCode == 13) {
               updateName($(this));
           }
       });


       $(document).on('click', '#submittext',  function () {

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

       $(document).on('click','#getimagebutton',  function () {
           imagePicker();
           //return false;
       });


       $(document).on('submit', '#order_ukeplan', function () {
           $(".confirmtip").modal();
           return false;
       });
       
       $('#confirmproject').on( 'click', save );


       $('input[name=names]').change(function () {
           $('.edittext').remove();
           if ($('input[name=names]:checked').val() == 'names') {
               $('.textchoices').show();
               $('table.grid').each(function () {
                   $('.namescontainer').prepend('<div class="imagetextplaceholder"><img id="imagetext" alt="' + skrivnytekst + '" class="imagetext" src="" /></div>');
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
           var imageid = 'noimage';
           clipartcolumn = false;
           $('.grid').removeClass('dinner');
           $('input[name=clipart]').removeAttr('checked');
           
           
           if(  $( '#background' ).val() == "black" ){
                var pretextcolor = "#fff";
           }else{
                var pretextcolor = "#000";
           }

           if (imagelayout == 'one') {
               //$('#produksjonsvalg').show();
               
               $('#imagefield_height').removeAttr( 'disabled' );
               $('.textchoices').hide();
               //$('.imageplaceholder').html('<img id="cropbox1" class="cropbox" src="' + velgbilde + '" style="position: relative;" height="150" />');
               $('.imageplaceholder').html('<div id="cropbox1" class="btn btn-default cropbox" " style="position: relative; color: #fff;  height="150" imageid="noimage">' + velgbilde + '<div></div>');
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
              $("#cropbox1").remove();
              
              $('.imageplaceholder').html('<div id="cropbox1" class="btn btn-default cropbox" style="position: relative;" color: #fff"  imageid="noimage" >' + velgbilde + '</div>');
              //$('#produksjonsvalg').show();
               $('.textchoices').hide();
               if ($('div.imageplaceholder').size() < numberofcolumns) {
                   malArray.imagefield_height = imagefield_each;
                   gridheight = ((malArray.mal_height - malArray.imagefield_height - malArray.imagefield_x - bottommargin) / ratio);
                   setupTemplate();
                   setupText();

                   var i = 0;
                   var columns = numberofcolumns - 1;
                   while (i <= columns) {
                       newColumn();
                       i++;

                   }
               }
               //console.log(  );
               /*$('[imageid]').each( function(){
                   
                  $(this).attr( 'height', '150' ).css( 'height', '150px' );;
                  
               })*/
               
              
               $('.imagetext').remove();
               $('input[name=names]').attr('checked', false);
               //newColumn();
           }else if (imagelayout == 'text') {
               //$('#produksjonsvalg').hide();
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
               
               
               
                $('.cropbox').each(function (index, item) {
                   if (($('.cropbox').size() - 1) == index && imagelayout == 'each') {
                    
                    
                    console.log("adsda");
                    
                        selectedcropbox = $(this).attr('id');
                        
                        if(  $('#' + selectedcropbox ).attr('imageid') == 'noimage' ){
                            $('#' + selectedcropbox ).parent().html('<img id="' + selectedcropbox + '" class="cropbox" height="150" imageid="" src=""/>');
                        }
                       $('#' + selectedcropbox ).attr({
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
                       }).parent().addClass('dinner');
                   }
               });
               
           }else {
               var imageid = 'noimage';
               clipartcolumn = false;
               $('.grid').removeClass('dinner');
               
               
                $('.cropbox').each(function (index, item) {
                   if (($('.cropbox').size() - 1) == index && imagelayout == 'each') {
                    
                        var imageplaceholder = $(this).parent();
                    
                    
                        imageplaceholder.removeClass('dinner');
                        $(this).remove();
                        
              
                        imageplaceholder.html('<div id="cropbox' + ( index + 1 ) + '" class="btn btn-default cropbox" style="position: relative; color: #fff" height="150"  imageid="noimage" >' + velgbilde + '</div>');
                   }
               });
               
           }
       });
       
       
       $('input[name=clipart]').change(function () {
             
            gridheight -= 20;
      
            setupTemplate();
            
       });

       $('#columnquantity').change(function () {

           var currentColumns = $('.grid').size();
           var columns = $(this).val();
           numberofcolumns = columns;
           
           savedcrop = [];
           
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
       
       
       
       $(document).on( 'click', '.rotateRight', function(){
            var $thisImage = $(this).parent().parent().find( '.cropbox' );

            ///$thisImage.data('cropbox').remove();
            
            fullDomain = window.location.host; 
            brokenstring=$thisImage.attr('src').split('/');
             if( !brokenstring[10] ){
                brokenstring[10] = 0;
             }
             if( !brokenstring[9] ){
                brokenstring[9] = 0;
             }
            var rotate =  parseInt( brokenstring[10]  ) + 90;
            if( rotate == -90 || rotate == -270){
                rotate = 360 + rotate;
            }  
            if( rotate == 360 ||  rotate == -360 ){
               rotate = 0;
            }   
            var src =  "http://" + fullDomain + '/' + brokenstring[3] + '/' +  brokenstring[4] + '/' +  brokenstring[5] + '/'  +  brokenstring[6] + '/'  +  brokenstring[7] + '/'  +  brokenstring[8] + '/' + brokenstring[9] + '/' + rotate;

            var cropheight =  $('.imageplaceholder').height();
            var cropwidth =  $('.imageplaceholder').width();
            
            $thisImage.attr('src', src)
            
            if( rotate > 0 ){
                $thisImage.addClass('rotate');
            }else{
                $thisImage.removeClass('rotate');
            }
            
            $thisImage.cropbox({width:cropwidth, height:  cropheight});

            
        });
       
        $(document).on( 'click', '.rotateLeft', function(){
            var $thisImage = $(this).parent().parent().find( '.cropbox' );

            //$thisImage.data('cropbox').remove()
            
            fullDomain = window.location.host; 
            brokenstring=$thisImage.attr('src').split('/');
             if( !brokenstring[10] ){
                brokenstring[10] = 0;
             }
             if( !brokenstring[9] ){
                brokenstring[9] = 0;
             }
            var rotate =  parseInt( brokenstring[10]  ) - 90;
            if( rotate == -90 || rotate == -270){
                rotate = 360 + rotate;
            }  
            if( rotate == 360 ||  rotate == -360 ){
               rotate = 0;
            }   
            var src =  "http://" + fullDomain + '/' + brokenstring[3] + '/' +  brokenstring[4] + '/' +  brokenstring[5] + '/'  +  brokenstring[6] + '/'  +  brokenstring[7] + '/'  +  brokenstring[8] + '/' + brokenstring[9] + '/' + rotate;

            var cropheight =  $('.imageplaceholder').height();
            var cropwidth =  $('.imageplaceholder').width();
            
            $thisImage.attr('src', src);
            
            if( rotate > 0 ){
                $thisImage.addClass('rotate');
            }else{
                $thisImage.removeClass('rotate');
            }
            
            $thisImage.cropbox({width:cropwidth, height:  cropheight});

            
        });
      
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
      
      
      $(document).on( 'click', '#toptext-confirm', function(){
         
         //console.log( $('#toptext').val() );
         //$('input[name=names]').attr('checked', true);
         
         toptext =  $('#toptext').val();
         
         /*if( $.trim( toptext  ) == '' ){
            toptext = "       Sett inn tekst       ";
         }*/
         updateTopText( toptext );
          
          $('.edittoptext').remove();
      });
      
      
      $('#background').on( 'change', function(){
                if( $(this).val() == "trans"){
                    backgroundcolor = "#FFFFFF";
                    if( $('#day-color').val() == "#FFFFFF" ){
                        $('#day-color').val("#010101").css( { "background": "#000", "color": "#fff"} );
                    }
                    if( $('#color').val() == "#FFFFFF" ){
                        $('#color').val("#010101").css( { "background": "#000", "color": "#fff"} );
                        setupTemplateColor();
                    }
                    if( $('#text-color').val() == "#FFFFFF" ){
                        $('#text-color').val("#010101").css( { "background": "#000", "color": "#fff"} );
                        setupNames();
                    }
                    //$('div.cropbox').css('color', '#333' );
                    setupText();
                    
                    //$('.editorcontainer').css( { 'opacity': '0.8'  });
                    $('div.editorcontainer').addClass('transparent').removeClass('black');
                }
                else if( $(this).val() == "white"){
                    backgroundcolor = "#FFFFFF";
                    if( $('#day-color').val() == "#FFFFFF" ){
                        $('#day-color').val("#010101").css( { "background": "#000", "color": "#fff"} );
                    }
                    if( $('#color').val() == "#FFFFFF" ){
                        $('#color').val("#010101").css( { "background": "#000", "color": "#fff"} );
                        setupTemplateColor();
                    }
                    if( $('#text-color').val() == "#FFFFFF" ){
                        $('#text-color').val("#010101").css( { "background": "#000", "color": "#fff"} );
                        setupNames();
                    }
                    //$('.editorcontainer').css( { 'opacity': '1'  } );
                    //$('div.cropbox').css('color', '#333' );
                    setupText();
                    //$('div.editorcontainer').css( "background-image", "url('" + templatefile + "')" );
                     $('div.editorcontainer').removeClass('transparent').removeClass('black');
                }
                else if( $(this).val() == "black"){
                    backgroundcolor = "#000000";
                    
                    if( $('#day-color').val() == "#010101" ){
                        $('#day-color').val("#FFFFFF").css( { "background": "#fff", "color": "#000"} );
                    }
                     if( $('#color').val() == "#010101" ){
                        $('#color').val("#FFFFFF").css( { "background": "#fff", "color": "#000"} );
                        setupTemplateColor();
                    }
                    if( $('#text-color').val() == "#010101" ){
                        $('#text-color').val("#FFFFFF").css( { "background": "#fff", "color": "#000"} );
                        setupNames();
                    }
                    $('.editorcontainer').css( { 'opacity': '1' } );
                    //$('div.cropbox').css('color', '#fff' );
                    setupText();
                    //$('div.editorcontainer').css( "background-image", "url('" + templatefile + "?negate=true')" );
                    $('div.editorcontainer').removeClass('transparent').addClass('black');
                }
        });


   });

   $(window).load(function () {

       var i = 1;
       //console.log( numberofcolumns );
       
       while (i < numberofcolumns) {
           newColumn();
           i++;
       }
       
        //$('input[name=imagelayout]').change();

        $('.loader').fadeOut();
   });