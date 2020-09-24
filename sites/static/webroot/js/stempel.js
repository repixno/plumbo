 function update() {
    
    $('img#preview').parent().append( '<div id="imageloader" style="width:202px; height:83px"><img src="http://a.static.repix.no/gfx/gui/ajax-loader-gray.gif"/></div>' );
    
    /*$('#imageloader').each( function(){
            $(this).css( { 'width' :'202px', 'height' :  '83px'}  );
        })*/
    

    disabled = false;
    alertmessage = 'true';

    var color = encodeURIComponent( $('input[name$="font-color"]').val() );
    
     
     if( $('input[name$="imagemode"]').val() == 'color' ){
        imagemode = 'color';
     }else{
        imagemode = 'bw';
     }
     
     if (imageselection.selection) {
         var selection = imageselection.selection.x1 + '-' + imageselection.selection.y1 + '-' + imageselection.selection.width + '-' + imageselection.selection.height + '-' +imagemode;
     }
     var line1 = encodeURIComponent($('input[name$="line1"]').val());
     var line2 = encodeURIComponent($('input[name$="line2"]').val());
     var previewlink = '/api/1.0/merkelapp/preview?projectid=' + projectid +
                                                    '&line1=' + line1 +
                                                    '&line2=' + line2 +
                                                    '&font=' + $('select[name$="font-type"]').val() +
                                                    '&gravity=' + gravity +
                                                    '&image=' + ownimage +
                                                    '&clipart=' + clipart +
                                                    '&backgroundfile=' + backgroundfile +
                                                    '&color=' + color +
                                                    '&treshold=' + treshhold +
                                                    '&crop=' + selection +
                                                    '&colormode=' + 'stempel';

     $('img#preview').each(function () {
         $(this).attr('src', previewlink);
     });
    
    $('img#preview').load( function(){
        $('#imageloader').remove();
        });
 }

 function setcrop(selection) {
     $('#ownimage').imgAreaSelect({
         handles: true,
         aspectRatio: '1:1',
         x1: selection.x1,
         y1: selection.y1,
         x2: selection.x2,
         y2: selection.y2,
         onInit: function () {
             ownimage = $('input[name$="image"]').val();
             update();
         },
         onSelectEnd: function (img, selection) {
             imageselection = {
                 selection: selection,
                 imgx: img.width,
                 imgy: img.height
             };
             update();
         }
     });
 }

 function getClipart() {

    if( type == 'fargelapp' ){
       var folder = 'colorclipart'; 
    }
    else{
       var folder = 'clipart'; 
    }
    
     $.post('/api/1.0/merkelapp/clipart', {
         clipart: category,
         type: type
     }, function (response) {
         $(response.images).each(function (index, value) {
             $('#clipart').append('<img id="clip" title="' + value + '"src="' + staticroot + 'gfx/merkelapp/' + folder + '/' + category + '/' + value + '" width="38" style="margin-right: 5px;"/>');
         })
     }, 'json');

 }
 
 
 function getBackgrounds() {
    
     $.post('/api/1.0/merkelapp/backgrounds', {
        backgroundcat: backgroundcat,
     }, function (response) {
         $(response.images).each(function (index, value) {
             $('#background').append('<img id="backgroundfile" title="' + value + '"src="' + staticroot + 'gfx/merkelapp/backgroundfiles/' + backgroundcat + '/thumbs/' + value + '" width="38" style="margin-right: 5px;"/>');
         })
     }, 'json');

 }

 $(document).ready(function () {

     getClipart();
     getBackgrounds();
     
    $('.merkelapp-rotator').merkelappslider();

    $('#select_clipart').change(function () {
         $('#clipart').html('');
         category = $(this).val();
         getClipart();
         return false;
     });
    
    $('#select_background').change(function () {
         $('#background').html('');
         backgroundcat = $(this).val();
         getBackgrounds();
         return false;
     });
     
     $('#cart-price').text(formatPrice( totalprice ));

     $('.radio').mousedown(function () {
         $('.radio').css({
             'background-position-x': '-7px'
         });
         $(this).css({
             'background-position-x': '-67px'
         });
     }).mouseup(function () {
         $(this).css({
             'background-position-x': '-37px'
         });
         gravity = $(this).attr('id');
         update();
     });

     
    $('#font-color').change(function () {
           $("select#font-color option:selected").each(function () {
               $('#font-color').css('background-color', $(this).css('background-color'));
               $('#font-color').css('color', $(this).css('color'));
           });
           return false;
    });

     $('#addtocart').click(function () {
        alertmessage = 'false';
        if( disabled == true ){
            return false;
        }else{
            document.location.href = '/bestilling/addItemByProductOptionId/' + $('input[name$="choose_product"]:checked').val() + '/' + projectid + '/' + type;
        }
         return false;
     });

     $('#removeimg, #removeclipart').click(function () {
         clipart = '';
         ownimage = '';

         update();
         return false;

     });
     
     $('#removebackground').click( function (){
        backgroundfile = null;
        update();
     });

     $('#removetext').mouseup(function () {
         $('input[name$="line1"]').val('');
         $('input[name$="line2"]').val('');
         $('input[name$="line3"]').val('');
         update();
         return false;
     });


     $('#removetext').click(function () {
         return false;
     });

     $('#removeall').click(function () {
         clipart = '';
         ownimage = '';
         backgroundfile = null;
         $('input[name$="line1"]').val('');
         $('input[name$="line2"]').val('');
         $('input[name$="line3"]').val('');
         update();
         return false;
     });

     $('#update').click(function () {
         update();
         return false;
     });

     $('input[name$="choose_product"]').change(function () {
         var valg = $('input[name$="choose_product"]:checked').val();
         if (valg == '2734') {
             $('#sticker').show();
             $('#iron').show();
             $('#not-sticker').hide();
             $('#not-iron').hide();
         } else if (valg == '2732') {
             $('#sticker').show();
             $('#iron').hide();
             $('#not-sticker').hide();
             $('#not-iron').show();
         } else if (valg == '2730') {
             $('#sticker').hide();
             $('#iron').show();
             $('#not-sticker').show();
             $('#not-iron').hide();
         }
     });

     $('#edit').change(update);

     $("#tabs").tabs({
         select: function (event, ui) {
             if (ui.tab.id == 'tab-text' || ui.tab.id == 'tab-clipart' || ui.tab.id == 'tab-background' ) {
                 $('#ownimage').imgAreaSelect({
                     remove: true
                 });
             } else if (ui.tab.id == 'tab-image' && imageselection.selection) {

                 setTimeout(function () {

                     setcrop(imageselection.selection);
                 }, 200);
             }
         }
     });

     var photo = $("#photo").val();

     $('#imagefile').change(function () {

         $(this).upload('/api/1.0/merkelapp/upload', function (res) {
             $('#ownimage').imgAreaSelect({
                 remove: true
             });
             ownimage = res.filename;
             $('input[name$="image"]').val(res.filename);

             $('#ownimage').remove();
             $('.preimage').remove();
             $('.pastimage').show();
             $('#imagecontainer').append('<img  id="ownimage" alt="image" src="/streams/merkelapp/' + res.filename + '" />');
             //$('#ownimage').attr( 'src' , '/streams/merkelapp/' + res.filename );

             $('#ownimage').load(function () {

                 $('#ownimage').imgAreaSelect({
                     handles: true,
                     aspectRatio: '1:1',
                     x1: 50,
                     y1: 50,
                     x2: 150,
                     y2: 150,
                     onInit: function (img, selection) {
                         imageselection = {
                             selection: selection,
                             imgx: img.width,
                             imgy: img.height
                         }
                         update();
                     },
                     onSelectEnd: function (img, selection) {
                         imageselection = {
                             selection: selection,
                             imgx: img.width,
                             imgy: img.height
                         };
                         update();
                     }
                 });

             });
         }, 'json');


         update();
         return false;
     });

    $('#clip').live('click', function () {
         ownimage = '';
         $('.selectedclip').removeClass( 'selectedclip' );
         $(this).addClass( 'selectedclip' );
         clipart = category + '-' + $(this).attr('title');
         update();

     });
     
      $('#backgroundfile').live('click', function () {
         $('.selectedbackground').removeClass( 'selectedbackground' );
         $(this).addClass( 'selectedbackground' );
         backgroundfile = backgroundcat + '-' + $(this).attr('title');
         update();
     });
     
     $('#font-color').change( function(){
        update();
     });
     
     $('#clip, #backgroundfile').live('mouseover mouseout mousedown', function(event) {
        
        if (event.type == 'mouseover') {
          $(this).css( {'background-color' : '#ff9999' ,  'border' : '1px solid #ca0000' } );
        } 
        else if (event.type == 'mousedown') {
          $(this).css(  {'background-color' : '#ff9999' ,  'border' : '1px solid #ca0000' } );
        }
        else {
          $(this).css( {'background-color' : '' ,  'border' : '' });
        }
     });

     $("#slider").slider({
         stop: function (event, ui) {
             treshhold = ui.value;
             update();
         },
         step: 2,
         value: 50,
         max: 100,
         min: 1

     });
     
    /*	
    $(window).bind('beforeunload', function(){
        if( alertmessage != 'false' ){
            return 'Har du ikke lagt merkelappen i handlekurven vil du miste det du har laget.';
        }
    });*/
    
    $('#jquery-colour-picker-text select').colourPicker({
        ico:    'http://andreaslagerkvist.com/aFramework/Modules/Base/gfx/jquery.colourPicker.gif', 
        title:    false
    });
    

 });