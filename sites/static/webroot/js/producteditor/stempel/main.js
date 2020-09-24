fabric.Object.prototype.originX = fabric.Object.prototype.originY = 'center';

var templateid = $('#templateid').val();
var canvas;
var $tabs;

$(document).ready( function(){
    
    $( 'body' ).loadPage();
    $('#font-list').fontmenu();
    $('.loader').show(); 
    // create a wrapper around native canvas element (with id="c")
    $('#wrap1').teditor( {'id' : 'c1', 'width': 400, 'height' : 50 } );
    var teditor = $('#wrap1').data('teditor');


    $('#fileupload').fileupload({
       dataType: 'json',
       done: function (e, data) {
           
           //console.log( data ) ;
           
           $('#imagelist').append( '<img class="image ui-draggable" id="' + data.result.message + '" src="/images/stream/thumbnail/' + data.result.message + '"/>' );
       
    
       },
       progressall: function (e, data) {
       var progress = parseInt(data.loaded / data.total * 100, 10);
       $('#progress .bar').css(
           'width',
           progress + '%'
       );
       }
    });
    
    /*$('#frame').on( 'change', function(){
        
        
        if( $(this).prop('checked') ){
            teditor.addFrame();
        }else{
            teditor.removeFrame();
        }
        
        });*/
    
    $('.centerselected').on('click', function(){
        
        teditor.center();
        return false;
        
        } );
    
    $(document).on('click', '.badge', function(){
        
            id = $(this).parent().data('id');
            
            $(this).parent().remove();
            teditor.deleteobject( id );
        
        });
    $('.move').on('click', function(){
        return false;
    });
    
    $('.move').on('mousedown', function(){
        teditor.zoomingtimer = true;
        teditor.move( $(this).data( 'direction' ), true );
        
        
        return false;
        
        });
    
    $(document).on('mouseup mouseout', function(){
        teditor.zoomingtimer = false;
        return false;
    });
    
    
    $('#velgingen').on( 'click', function(){
        
            teditor.deactivateAllObjects();
        });
    
    $(document).on( 'click', '.layer', function( ret ){
        if( $(ret.target).hasClass('fa-trash-o') ){
            return false;  
        }
    
        $(this).addClass('active');        
        var id = $(this).data('id');
        
        teditor.deactivateAllObjects();
        
        teditor.canvas.setActiveObject(teditor.canvas.item( id ));
        
    });
 
 
    $('.stempelcolor').on( 'click', function(){
        
            $('.selectedcolor').removeClass('selectedcolor');
        
            //console.log( $(this).data('color')); 
        
            teditor.changecolor( $(this).data('color') );
            
            $(this).addClass('selectedcolor');
            
            return false;
        
        });

    $('#closecrop').on( 'click', function(){
            $('#cropwindow').dialog('close');
        });


    $('#editorzoom').on( 'change', function(){
        
        var zoomvalue = $(this).val();
        
        $('#wrap1').css( { '-webkit-transform' : 'scale(' + zoomvalue + ')', 'height' : canvasheight * zoomvalue } );
        
        
        });

        
    $('.frame').on( 'click', function(){
        
        var type = $(this).attr('id');
        teditor.addFrame(type);
        
        })
        

    $('#addframe').on( 'click', function(){
            
            var type="";
        
            teditor.addframe(type);
        });

    $(document).on( 'click', '.album', function(){
        var albumid = $(this).attr('id');
        teditor.getUploaded(albumid);
        return false;
        });
    
    $(document).on( 'click', '#level_up' , function(){
        
        
        $('#imagelist').html('');
        $('.albumlist').show();
        
        return false;
        });
    
    $(document).on( 'click', '.image', function(){
        
        
        teditor.addnewimage( '/images/stream/editorimage/' + $(this).attr('id') , $(this).attr('id'), 1, 1, 1, 1 );
        
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

/*
$('#wrap1').dblclick( function(){

    console.log( "bad ");	
    });
*/


$('#largerfont').on( 'click', function(){
    var thisvalue = $(this).prev().val();
    $(this).prev().val(  parseInt( thisvalue )  + 5 );
    teditor.updateText( $('#text').val() );
    return false;
});
$('#smallerfont').on( 'click', function(){
    var thisvalue = $(this).next().val();
    $(this).next().val( thisvalue - 5 )
    teditor.updateText( $('#text').val() );
    return false;
});

$('#underline').on( 'click', function(){
    if(  $(this).hasClass('active') ){
        $(this).data('decoration', '');
    }else{
        $(this).data('decoration', 'underline');
    }
    $(this).toggleClass('active');
});

$('#bold').on( 'click', function(){
    if(  $(this).hasClass('active') ){
        $(this).data('bold', 'normal');
    }else{
        $(this).data('bold', 'bold');
    }
    $(this).toggleClass('active');
});
$('#italic').on('click', function(){
    if(  $(this).hasClass('active') ){
        $(this).data('italic', 'normal');
    }else{
        $(this).data('italic', 'italic');
    }
    $(this).toggleClass('active');
})
    
$('.textweight').on('click', function(){
    
     teditor.updateText();
    
}); 

$('.textalign').mousedown(function () {

    if( $(this).hasClass( 'textalign' ) ){
        $('.textalign').removeClass('active');
        $("#gravity").val(  $(this).attr('id') );
    }
    
    $(this).addClass('active');
    teditor.updateText();
    return false;
});

//teditor.getBackgrounds();
//teditor.getClipart('Baby');
//teditor.getTemplate();
//teditor.getUploaded();

//$tabs = $( "#tabs" ).tabs();

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

$('#addtocart').click( function(){
    
    var canvasthumb = teditor.canvas.toDataURLWithMultiplier('jpg', 0.2, 60);
    
    //teditor.createPreview();
    teditor.save( );
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
        
        teditor.updateText( $('#text').val() );
        //teditor.updateText();
        ///$('head').append('<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=' + font + '">');
    });

    
$('#new_image').on( 'click', function(){
    teditor.deactivateAllObjects();
    $('#image-options-container').show();
    
    });

$('#velgalle').on( 'click', function(){
    
        teditor.selectAll();
        
        return false;
    
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
$('#select_background').change(
    function(){
        teditor.getBackgrounds();
        }
    );

$(document).on('click', '#new_mage' , function(){
    teditor.newBlankImage();
    })


$('.deletebackground').on( 'click', function(){
    teditor.deleteBackground();
    })

$(document).on("click", ".templatethumb", function(){
    var name = $(this).attr('id');
    $('#themename').val( name );
    teditor.load( name );
});

$('#imageupload').change(function() {
    $(this).upload('/api/1.0/tedit/upload', function(res) {
        
        //console.log( res.name ); 
        var image = $('<img class="image"/>');
        image.attr( 'id', res.name );
        image.attr( 'src' , "/tedit/thumbs/" + res.name  );
        image.draggable( { containment: "document",
            helper: "clone",
            cursor: "move"  } );
        
        $('#imagelist').prepend( image );
        
    }, 'json');
    });

$( window ).resize(setdimmensions);

$( window ).load(function() {
    
    var  p = $( ".canvas-container" ).position();
    var offset = $('.canvas-container').offset();
    $('#imagemenu').css( { 'left' :  offset.left, 'top': 50 }); 
    //$('.malpage').first().click();
    
    teditor.loadpage( templateid , malpageid );
    
  });
    
});

function setdimmensions(){
    
    var ccw = $('.canvascontainer').width();
    var cc = $('.canvas-container').width();
    var wh= $('#wrap1').height();
    var ww= $('.canvas-container').width();
    
    //console.log('ccw', ccw);
    //console.log('cc', cc);
    
    var left = ( ccw - cc )  / 2 ;
    
    //console.log( 'left', left );
    
    $('.dimmensionsheight').css({ left: left - 30 , top: 0, height: wh });
    
    $('.dimmensionswidth').css({ left: left + 15 , top: -10, width: ww});
    
    var dimxw = ( ww -  $('.dim-x').width() - 10 ) / 2;
    $( '.dim-x-left, .dim-x-right').width(dimxw);
    
    var dimxy = ( wh -  $('.dim-y').height() ) / 2;
    $( '.dim-y-top, .dim-y-bottom').height(dimxy);
    
}
