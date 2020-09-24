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
               var toptextcolor = '';
                imagetext = toptext;
                toptextfont = $('#text-font').val();
                toptextcolor =$('#text-color').val();


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
               'margin' : margin
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
       var dayheight = parseIntUkeplan(margin / ratio) - 2;
       $('img.day').each(function () {
           $(this).attr('src', '/api/1.0/gift/text_ukeplan/?id=0&quality=1&png=1&font=arial&gravity=West&color=' + fontcolor + '&background=' + encodeURIComponent( backgroundcolor ) + '&text=' + $(this).attr('alt') + '&width=' + daywidth + '&height=' + dayheight + '&rotate=' + rotate)
           //console.log( fontcolor );
       });
    }

        
    function updateTopText( text ){
        text = encodeURIComponent( text );
        var height = textheight / ratio;
        var width = ( ( malArray.mal_width - malArray.imagefield_width ) - ( margin * 2 ) )  / ratio;
        var color = encodeURIComponent( $('#text-color').val() );
        var toptextbackgroundcolor = encodeURIComponent( backgroundcolor );
        var font = $('#text-font').val();
        
        var toptextsrc = "/api/1.0/gift/toptext?text=" + text + "&width=" + width + "&height=" + height + "&color=" + color +  "&background=" + toptextbackgroundcolor + "&font=" + font + "&bottom_margin=0";
        
        $('.toptext').html('<img id="toptext" class="cropbox" src="' + toptextsrc + '" imageid="textimage" />');
        
        $('.toptextplaceholder').css({
           position: 'absolute',
           left:    (margin / ratio) + 'px',
           top:     (margin / ratio) + 'px',
           width:   width + 'px',
           "text-align": "center"
           });
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
 
        $('.editorcontainer').css({
            height: (malArray.mal_height / ratio) + 'px',
            width: (malArray.mal_width / ratio) + 'px'
        });
 
        /*$('.imageandtextplaceholder').css({
            height: parseIntUkeplan(malArray.imagefield_height / ratio) + 'px',
            width: ( malArray.imagefield_width / ratio) + 'px',
            left: ( malArray.imagefield_x / ratio ) + 'px',
            top: ( malArray.imagefield_y / ratio) + 'px'
        });
        */
        $('.imageplaceholder').css({
            height: parseIntUkeplan(malArray.imagefield_height / ratio) + 'px',
            width: parseIntUkeplan(malArray.imagefield_width / ratio) + 'px'
        });
 
        $('.grid').css({
            //width: ( ( malArray.imagefield_width / ratio / numberofcolumns ) - image_spaces ) + 'px',
            height: gridheight + 'px'
        }).height(gridheight);
        $('.gridplaceholder').css({
            left: (margin / ratio) + 'px',
            top: ( ( margin + textheight ) / ratio) + 3 + 'px',
            width: ((margin + malArray.imagefield_width) / ratio) + 'px',
        });
        $('.texttable').attr('height', gridheight);
 
        $('div.textplaceholder').css({
            top: ( ( margin + textheight ) / ratio) + 3 + 'px',
            height: gridheight + 'px'
        });
        
        
        $('.rightcolumn').css({
                width: parseIntUkeplan(malArray.imagefield_width / ratio) + 'px',
                top: parseIntUkeplan(malArray.imagefield_y / ratio) + 'px',
                left: parseIntUkeplan(malArray.imagefield_x / ratio) + 'px',
                position: 'relative'
                })
        
        var notatheight = parseIntUkeplan(( malArray.mal_height - malArray.imagefield_height - textheight - margin - margin ) / ratio / 3 );
        
        $('.husk').css({
                'height': notatheight * 2 + 'px'
                
                });
        $('.dagenidag, .Dato').css({
                'height': notatheight / 2 + 'px'
                
                });
        
        if( $('#notatoption').prop('checked' ) ){
             $('.notatfelt').css({
                 height: 1000 / ratio,
                 width:notatwidth + 'px',
                 left: (margin / ratio) + 2 + 'px',
                 top: ((margin + malArray.imagefield_height ) / ratio) + gridheight + 5 + 'px',
                 })
        }
        
        $('table.grid').css('margin-left', '2px');
        updateTopText( toptext );
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
   
   $(document).ready(function () {
        setupgrid();
        setupTemplate();
        /*setupTemplateColor();*/
        setupText();
        
        $('#notatoption').on('change', function(){
                if($(this).prop('checked')){
                    notatheight = 1000;
                    notatwidth = ( ($('.grid').width() + 2) *  $('.grid').size() ) - 4;
                    $('.editorcontainer').append('<div class="notatfelt"><div class="notat">Notat:</div></div>');
                }else{
                    notatheight = 0;
                    $('.notatfelt').remove();
                }
                gridheight = ((malArray.mal_height - textheight - margin  - bottommargin -  notatheight ) / ratio);
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
           //toptext =  $('#toptextedit').val();
           updateTopText( toptext );
           return false;
       });
       
        $('#text-font').change(function () {
           //toptext =  $('#toptextedit').val();
           updateTopText( toptext );
           return false;
       });

       $('.imageplaceholder').html('<div id="cropbox1" class="btn btn-default cropbox" style="position: relative; color: #fff" height="150"  imageid="noimage" >' + chooseimagestring + '</div>');
       
       
       $(document).on( 'click', '.openimagebox', function(){
            ///console.log($(this).parent().parent().find('.cropbox').attr('id'));
        
            selectedcropbox = $(this).parent().parent().find('.cropbox').attr('id');
            $('#imageselect').modal();
        });
       
       $(document).on('click', '.cropbox',  function () {
        
        if($(this).attr('imageid') == 'textimage'){
            //console.log(this);
            var value  = decodeURIComponent( toptext );
            
            value = $.trim( value );
            
            if( value == 'Legg til tekst'){
               value = '';
            }
            
            var boxwidth = $('.toptextplaceholder').width();
            
            $(this).parent().parent().append(
                        '<div class="center edittoptext" " style="width: '
                        + boxwidth
                        + 'px">Sett inn ny tekst<br/><input type="text" id="toptextedit" style="width:300px; text-align:center;" value="'
                        + value
                        + '" placeholder="'
                        + value
                        + '"/>'
                        + '<br/><button id="toptext-confirm">Oppdater</button></div>');
            
            $('#toptextedit').focus();
          
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

       $(document).on('click','#getimagebutton',  function () {
           imagePicker();
           //return false;
       });


       $(document).on('submit', '#order_ukeplan', function () {
           $(".confirmtip").modal();
           return false;
       });
       
       $('#confirmproject').on( 'click', save );

       
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
      
      
      $(document).on( 'click', '#toptext-confirm', function(){
         
         //console.log( $('#toptextedit') );
         //$('input[name=names]').attr('checked', true);
         
         toptext =  $('#toptextedit').val();
         
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
                    }
                    //$('div.cropbox').css('color', '#333' );
                    setupText();
                    $('.dagenidag, .Dato').css({'color': 'black'});
                    $('table.grid td').css(  {"border-color": "white"});
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
                    }
                    //$('.editorcontainer').css( { 'opacity': '1'  } );
                    //$('div.cropbox').css('color', '#333' );
                    setupText();
                    $('.dagenidag, .Dato').css({'color': '#cccccc'});
                    $('table.grid td').css(  {"border-color": "white"});
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
                    }
                    $('.editorcontainer').css( { 'opacity': '1' } );
                    //$('div.cropbox').css('color', '#fff' );
                    setupText();
                    $('.dagenidag, .Dato').css({'color': '#cccccc'});
                    $('table.grid td').css(  {"border-color": "black"});
                    //$('div.editorcontainer').css( "background-image", "url('" + templatefile + "?negate=true')" );
                    $('div.editorcontainer').removeClass('transparent').addClass('black');
                }
        });


   });

   $(window).load(function () {
        $('.loader').fadeOut();
   });