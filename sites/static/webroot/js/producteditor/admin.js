var templateid = $('#templateid').val();
var canvas;
var $tabs;

$(document).ready( function(){
    
    $( 'body' ).loadPage();
    $('#font-list').fontmenu();
    $('.loader').show(); 
    // create a wrapper around native canvas element (with id="c")
    $('#wrap1').teditor( {'id' : 'c1' } );
    var teditor = $('#wrap1').data('teditor');


 /*$('#fileupload').fileupload({
    dataType: 'json',
    done: function (e, data) {
        
        console.log( data.files[0].name ) ;
        
        $('#imagelist').append( '<img class="image ui-draggable" id="' + data.files[0].name + '" src="/tedit/thumbs/' + data.files[0].name + '"/>' );
    

    },
    progressall: function (e, data) {
    var progress = parseInt(data.loaded / data.total * 100, 10);
    $('#progress .bar').css(
        'width',
        progress + '%'
    );
    }
});*/

    $('#closecrop').on( 'click', function(){
        
            $('#cropwindow').dialog('close');
        
        
        });



    $('#addframe').on( 'click', function(){
            teditor.addframe();
        });

    $(document).on( 'click', '.album', function(){
        var albumid = $(this).attr('id');
        teditor.getUploaded(albumid);
        return false;
        });
    
    $(document).on( 'click', '#level_up' , function(){
        
        console.log("what the ficn");
        
        $('#imagelist').html('');
        $('.albumlist').show();
        
        return false;
        });

/*$(document).on("click", ".image" , function(){
    
    //teditor.changeSelectedImage( '/images/stream/image/' + $(this).attr('id') , $(this).attr('id') );
    //teditor.addnewimage( '/images/stream/image/' + $(this).attr('id') , $(this).attr('id') );
    //teditor.changeImageNew( '/images/stream/image/' + $(this).attr('id') , $(this).attr('id') );
    teditor.changeImageNew( '/images/stream/image/' + $(this).attr('id') , $(this).attr('id') );
    
    return false;
});*/

$('#zoominimage').on( 'click', function(){teditor.zoomBy(0,0,10)});
$('#zoomoutimage').on( 'click', function(){ teditor.zoomBy(0,0,-10)});

$('#moveup').on( 'click', function(){ teditor.zoomBy(0,20,0)});
$('#movedown').on( 'click', function(){ teditor.zoomBy(0,-20,0)});
$('#moveleft').on( 'click', function(){ teditor.zoomBy(20,0,0)});
$('#moveright').on( 'click', function(){ teditor.zoomBy(-20,0,0)});

$('.malpage').on( 'click' , function(){
            
    $( '.selectedpage' ).each( function(){
        $(this).removeClass('selectedpage');
        });
    teditor.loadpage( templateid , $(this).attr('id') );
    $(this).addClass( 'selectedpage' );
    
    return false;
    
});

$('.backgrounds').click( function(kkake, ksdkakd){
    var background = $(this).attr('id');
    teditor.changeBackground(background);
    return false;
});

//teditor.load( 'Brullup' );

/*
$('#wrap1').dblclick( function(){

    console.log( "bad ");	
    });
*/
 $('.textalign, .textweight').mousedown(function () {
    
    if( $(this).hasClass( 'textalign' ) ){
        $('.textalign').css( 'background-position-x', '-7px' );
        $("#gravity").val(  $(this).attr('id') );
    }else{
        $('.textweight').css( 'background-position-x', '-7px' );
        $("#textweight").val(  $(this).attr('id') );
    }
    
    $(this).css({
        'background-position-x': '-67px'
    });
    }).mouseup(function () {
    $(this).css({
        'background-position-x': '-37px'
    });
    teditor.updateText( $('#text').val() );
    return false;
});

//teditor.getBackgrounds();
//teditor.getClipart('Baby');
//teditor.getTemplate();
//teditor.getUploaded();

$tabs = $( "#tabs" ).tabs();

$('#zoomout').click( function(){ teditor.zoomOut() }  );
$('#zoomin').click( function(){ teditor.zoomIn() }  );
$('#zoomreset').click( function(){ teditor.resetZoom() }  );
$('#undo').click( function(){ teditor.undo() }  );
$('#redo').click( function(){ teditor.redo() }  );

$(document).on( 'change', '#text-options',  function(){
    teditor.updateText( $('#text').val() );
    return false;
    });

$('#imagemenu').draggable();
$('#colorselect').minicolors();


$(document).on( 'click', '#crop', function(){
    teditor.openCrop();
    });
$(document).on("click", ".backgroundfile", function(){
    teditor.changeBackground( $(this).attr('id') );
});
$(document).on("click", ".cliparts", function(){
    teditor.newClipart( $(this).attr('id') );
});
$('.image').draggable( {
    containment: "document",
    helper: "clone",
    cursor: "move" } );

/*$( '#c1' ).droppable({
    over: function( event, uid ){
        console.log( event );	
    },
    drop: function( event, ui ) {
        console.log( ui );
        var pos = $(this).position();
        var newPosX = ui.offset.left - $(this).offset().left;
        var newPosY = ui.offset.top - $(this).offset().top;
        teditor.addImage( ui.draggable.attr('id'), ui.draggable.attr('src') , newPosX, newPosY );
        $( this ).find( ".placeholder" ).remove();
    }
});*/

$('#resize').click( function(){
    teditor.resizeBackground();
    });

$(document).on('click' , '#delete_image' , function(){
    teditor.deleteImage();
    $('#imagemenu').hide();
});

$('#savepage').click( function(){
    
    var canvasthumb = teditor.canvas.toDataURLWithMultiplier('jpg', 0.2, 60);
    
    //teditor.createPreview();
    teditor.savepage( );
});

$('#addtheme').click( function(){
    var category = 'Baby';
    var name = $('#themename').val();
    
    if( name.length > 2 ){
        teditor.save( category, name );
    }
    else{
        alert( "du må sette et navn" );
    }
});

$('#load').click( function(){
    teditor.load();	
});


$(document).on( 'click' , '.fontlist', function(){
        //var font = $(this).val();
        
        console.log("sjo2");
        teditor.updateText( $('#text').val() );
        //teditor.updateText();
        ///$('head').append('<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=' + font + '">');
    });

$('#new_text').click( function(){
    var txt = $('.textarea').val();
    if( txt.length == 0 ){
        txt = "Skriv ny text";
    }
    teditor.addtext( txt  );
    return false;
});
$('#addtext').click( function(){
    teditor.updateText( $('.textarea').val() );
    return false;
    
});
$(document).on('click' , '#move-front', function(){
    teditor.moveForward();
    return false;
});
$(document).on('click' , '#movebackward', function(){
    teditor.moveBackward();
    return false;
});
$('#select_clipart').change( function(){
    teditor.getClipart( $(this).val()  );
    return false;
});

$('.deletebackground').on( 'click', function(){
    teditor.deleteBackground();
    })

$('#select_background').change(
    function(){
        teditor.getBackgrounds();
        }
    );

$(document).on('click', '#new_mage' , function(){
    teditor.newBlankImage();
    })


$(document).on("click", ".templatethumb", function(){
    var name = $(this).attr('id');
    $('#themename').val( name );
    teditor.load( name );
});

$('#imageupload').change(function() {
    $(this).upload('/api/1.0/tedit/upload', function(res) {
        
        console.log( res.name ); 
        var image = $('<img class="image"/>');
        image.attr( 'id', res.name );
        image.attr( 'src' , "/tedit/thumbs/" + res.name  );
        image.draggable( { containment: "document",
            helper: "clone",
            cursor: "move"  } );
        
        $('#imagelist').prepend( image );
        
    }, 'json');
    });

$( window ).load(function() {
    $('.malpage').first().click();
    
    teditor.getUseralbums();
    
    var  p = $( ".canvas-container" ).position();

    var offset = $('.canvas-container').offset();
    
    $('#imagemenu').css( { 'left' :  offset.left, 'top': 50 });
    
  });
    
});