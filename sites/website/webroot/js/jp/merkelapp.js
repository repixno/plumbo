 function update() {
    
    $('img#preview').parent().append( '<div id="imageloader" style="width:202px; height:83px"><img src="http://a.static.eurofoto.no/gfx/gui/ajax-loader-gray.gif"/></div>' );
    
    /*$('#imageloader').each( function(){
            $(this).css( { 'width' :'202px', 'height' :  '83px'}  );
        })*/
    
    $('#addtocart').removeAttr('disabled');

    disabled = false;
    alertmessage = 'true';

    var color = encodeURIComponent( $('input[name="font-color"]').val() );
     
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
     var line3 = encodeURIComponent($('input[name$="line3"]').val());
     var previewlink = '/api/1.0/merkelapp/preview?projectid=' + projectid + '&line1=' + line1 + '&line2=' + line2 + '&line3=' + line3 + '&font=' + $('select[name$="font-type"]').val() + '&gravity=' + gravity + '&image=' + ownimage + '&clipart=' + clipart + '&backgroundfile=' + backgroundfile + '&color=' + color + '&treshold=' + treshhold + '&crop=' + selection + '&colormode=' + colormode;

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
             $('#clipart').append('<img id="clip" title="' + value + '"src="' + staticroot + 'gfx/merkelapp/' + folder + '/' + category + '/' + value + '" width="50" style="margin-right: 5px;"/>');
         })
     }, 'json');

 }
 
 
 function getBackgrounds() {
    
     $.post('/api/1.0/merkelapp/backgrounds', {
        backgroundcat: backgroundcat,
     }, function (response) {
         $(response.images).each(function (index, value) {
             $('#background').append('<img id="backgroundfile" title="' + value + '"src="' + staticroot + 'gfx/merkelapp/backgroundfiles/' + backgroundcat + '/thumbs/' + value + '" style="width: 50px; margin-right: 5px;"/>');
         })
     }, 'json');

 }

 $(document).ready(function () {

     getClipart();
     getBackgrounds();
     
    //$('.merkelapp-rotator').merkelappslider();

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

     
     
     $('.alignment').on( 'click', function(){
         gravity = $(this).attr('id');
         $('.alignment').removeClass('active');
         $(this).addClass('active');
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
      
         var items = new Array();
         if(disabled == true ){
            return false;
         }
         $('.productline').each( function(){
            var productoptionid = parseFloat(  $(this).find('.choose_product').data('refid') ) ;
            var quantity = parseFloat(  $(this).find('.productquantity').val() );

            //productoptionid = 7548 ;
            
            console.log( "Projectid",  projectid );
            console.log( "Productoptionid",  productoptionid );
                        
            if(  quantity > 0 ){
               items.push({
                     "eurofoto_article_id": productoptionid,
                     "quantity": quantity,
                     "project_id": projectid,
                     "thumb_url": "https://staging-jp.eurofoto.no/bestilling/thumb/" + projectid
                  });
            }
              
         });
         
         var data = JSON.stringify({'action': 'addToCart', 'items' : items });
         
         top.postMessage(data, 'https://as.photoprintit.com');
         
         console.log( "Add to cart" );
         
         //$('#myModal').modal();
      
         /*alertmessage = 'false';
        if( disabled == true ){
            return false;
        }else{   
            document.location.href = '/bestilling/addItemByProductOptionId/' + $('input[name$="choose_product"]:checked').val() + '/' + projectid + '/' + type;
        }*/
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


     $('#edit').on( 'change' ,function(){
      
         update();
         return false;
      
      });
     
     $('#edit').on( 'submit' ,function(e){
         e.preventDefault();
         update();
      
      });
     
     
     $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
			
			
		
            
            if ($( e.target ).attr('aria-controls') == 'ownimagecontainer' && imageselection.selection) {

            
                  //console.log( imageselection.selection )
            
                 setTimeout(function () {
                     setcrop(imageselection.selection);
                 }, 200);
             }
             else{
                 $('#ownimage').imgAreaSelect({
                     remove: true
                 });
             }
        
		  })
   

     var photo = $("#photo").val();
         



    $(document).on('click', '#clip',function () {
         ownimage = '';
         $('.selectedclip').removeClass( 'selectedclip' );
         $(this).addClass( 'selectedclip' );
         clipart = category + '-' + $(this).attr('title');
         update();

     });
     
      $(document).on('click','#backgroundfile', function () {
         $('.selectedbackground').removeClass( 'selectedbackground' );
         $(this).addClass( 'selectedbackground' );
         backgroundfile = backgroundcat + '-' + $(this).attr('title');
         update();
     });
     
     $('#font-color').change( function(){
        update();
     });
     
     $(document).on('mouseover mouseout mousedown','#clip, #backgroundfile', function(event) {
        
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

     /*$("#slider").slider({
         stop: function (event, ui) {
             treshhold = ui.value;
             update();
         },
         step: 2,
         value: 50,
         max: 100,
         min: 1

     });*/
     
    /*	
    $(window).bind('beforeunload', function(){
        if( alertmessage != 'false' ){
            return 'Har du ikke lagt merkelappen i handlekurven vil du miste det du har laget.';
        }
    });*/
    
    $('#jquery-colour-picker-text select').colourPicker({
        ico:    '//static.eurofoto.no/gfx/icons/colorpicker.png', 
        title:    false
    });
    

 });