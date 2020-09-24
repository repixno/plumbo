

function require(jspath) {
    document.write('<script type="text/javascript" src="'+jspath+'"><\/script>');
}

var currenImageWidth = 0;
var currentPrintWidth = 0;
$.event.props = $.event.props.join('|').replace('layerX|layerY|', '').split('|');

$(document).ready(function () {
   
   $('.qualityinfo').click( function(){
   	$('#qualityinfoDialogWindow').dialog({
   	   height: 200,
         width: 400,
         modal: true,
         position: 'center',
         autoOpen:false,
         title:'Kvalitetsberegning.',
         draggable: false ,
          resizable: false,
         overlay: { opacity: 0.5, background: 'black'}
       });
   $('#qualityinfoDialogWindow').dialog('open');   
   return false;
   });
   
   
   //check if images is cropped
   $('#next-step').click( function(){
      var not_cropped = 0;
      
      $('.thumbnail').each( function(){
         if ($(this).hasClass('todo')) {
            not_cropped++;
            
         }
      })
      if( not_cropped > 0 ){
         
         
         
         $('#docrop').dialog({
      	   height: 450,
            width: 400,
            modal: true,
            position: 'center',
            autoOpen:false,
            title:'Advarsel!',
            draggable: false ,
            resizable: false,
            buttons: {
   				"Gå videre": function() {
   					 window.location = '/order-prints/accessories/';
   				},
   				"Fullfør beskjæring": function() {
   					$( this ).dialog( "close" );
   				}
   			}
          });
          $('#docrop').dialog('open');
          
          if( not_cropped == 1 ){
            $('#notcroppedcount').text( not_cropped + ' bilde' );
          }
          else{
            $('#notcroppedcount').text( not_cropped + ' bilder');
          }
          return false;
      }else{
         return true;
      }
      
   });
   
   
   $('#portrait').click( function(){
      
      $('img#cropbox').imgAreaSelect({ remove:true});
     
      var imageinfo=$('img#cropbox').attr('alt').split('-');
      
      newCrop( imageinfo[1], imageinfo[0], 'portrait' );
      
      return false;
      
      
   });
   $('#landscape').click( function(){
      
      $('img#cropbox').imgAreaSelect({ remove:true});
     
      var imageinfo=$('img#cropbox').attr('alt').split('-');
      
      newCrop( imageinfo[1], imageinfo[0], 'landscape' );
      
      return false;
      
   });
   $('#fit-in').change( function(){

      var imageinfo=$('img#cropbox').attr('alt').split('-');
      
      if ($('#fit-in:checked').val() !== undefined) {
         $('#landscape').hide();
         $('#portrait').hide();
         $("#" + $('img#cropbox').attr('alt')).removeClass('todo').addClass('done');
         $('img#cropbox').imgAreaSelect({ remove:true});

            $.ef( 'order.crop.save', {
               fitin:  'set',
               imageid: imageinfo[0],
               prodno:  imageinfo[1]
               }, function( response ) {
               if( response.result ) {
                  return true;
               } else {
                  return false;
                  
               }
            
            } );
      }else{
         $('#landscape').show();
         $('#portrait').show();
         $.ef( 'order.crop.save', {
            fitin:  'remove',
            imageid: imageinfo[0],
            prodno:  imageinfo[1]
         }, function( response ) {
            if( response.result ) {
               return true;
            } else {
               return false;
            
            }
         
         } );
         newCrop( imageinfo[1], imageinfo[0] );
         //updateCrop( imageinfo[1], imageinfo[0] );
      }
      
   });
   
   
       
    $('a.imagethumbnail').click( function(){
       currenImageWidth = $(this).parent().find('#orginalwidth').text();
       
       src = $(this).attr('href');
       prodno = $(this).attr('alt');
       imageid = $(this).attr('id');
       $('#imagecount-cropbox').text( ' ' + $(this).parent().find('#imagecount').text() );
       changeImage( src, imageid, prodno );
       
       return false;
    });
    
    $('.imagethumbnail').mouseover( function(){
      $(this).attr( 'title', printtitle( $(this).attr('alt'))  + "cm" ); 
    });
   
});


$(window).load( function() {
      var imagecount = 0;
      currenImageWidth = $('.imagethumbnail:first').parent().find('#orginalwidth').text();
      var src = $('.imagethumbnail:first').attr('href');
      var imageid = $('.imagethumbnail:first').attr('id');
      var prodno = $('.imagethumbnail:first').attr('alt');
      changeImage( src, imageid, prodno );
      
      
      $('.imagethumbnail').each( function(){
         
         imagecount++;
         
         $(this).parent().find('#imagecount').text(imagecount);
         
      });
      $('#totalimages').text(imagecount);
      
   
})


function updateCrop( prodno, imageid){
     $.ef( 'order.crop.get', {
         prodno:  prodno,
         imageid: imageid
      }, function( response ) {
      
      if( response.result ) {
         if(response.message != null){
            if( response.message.fitin == '1'){
               $('#fit-in').attr('checked', true);
               $('#portrait').hide();
               $('#landscape').hide();
            }
            else{
               getCrop ( response.message, imageid, prodno );
            }
         }
         else{
            newCrop( prodno, imageid );
         }
      } else {
         return false;
      }
   } );
}


function newCrop( prodno, imageid, orientation ){

   var size = printsize( prodno );
   
   printwidth = size[0];
   printheight = size[1];
   
   currentPrintWidth = printwidth;
   
   $('#portrait').removeClass('disabled').show();
   $('#landscape').addClass('disabled').show();
      
	var imgheight = $('img#cropbox').height();
	var imgwidth = $('img#cropbox').width();

	if( orientation == 'portrait' ){
	   $('#portrait').addClass('disabled');
	   $('#landscape').removeClass('disabled');
	   printwidth = Math.min( size[0], size[1] );
	   printheight = Math.max( size[0], size[1] );
	}
	else if ( orientation == 'landscape' ){
	   $('#portrait').removeClass('disabled');
      $('#landscape').addClass('disabled');
	   printwidth = Math.max( size[0], size[1] );
	   printheight = Math.min( size[0], size[1] );
	}
	else if( imgheight > imgwidth ){
	   $('#portrait').addClass('disabled');
	   $('#landscape').removeClass('disabled');
	   var temp = printwidth;
	   printwidth = printheight;
	   printheight = temp;
	}

	var imgratio = imgheight / imgwidth;
	var printratio = printheight / printwidth;
	if( printwidth > printheight ) {
		if(imgratio >  printratio){
			var tx2 = imgwidth;
			var ty2 = imgwidth * ( printheight / printwidth);
		}
		else{
			var ty2 = imgheight;
			var tx2 = imgheight / (printheight / printwidth); 
		}
	} else {
	     if(imgratio < printratio ){
		    var ty2 = imgheight ;
		    var tx2 = imgheight / (printheight / printwidth);
	     }
	     else{
	        var tx2 = imgwidth ;
		     var ty2 = imgwidth * (printheight / printwidth);
	     }
	}
	
	var tx1 = (imgwidth - tx2) / 2;
	tx2 = parseInt(tx1) + parseInt(tx2);
	var ty1 = (imgheight - ty2) / 2;
	ty2 = parseInt(ty1) + parseInt(ty2);
	
	$('img#cropbox').imgAreaSelect({ 
			x1: tx1, 
			y1: ty1,
			x2: tx2,
			y2: ty2,
			persistent: true,
			instance: true,
			aspectRatio: printwidth + ':' + printheight, 
			handles: true,
			keys: true,
			onInit: function ( img, selection ){
			   quality( img, selection );
			   if( orientation ){
			      saveCrop( img, selection );
			   }
			},
		   onSelectEnd: function ( img, selection ) {
		      saveCrop( img, selection );
         },
         onSelectChange: function ( img, selection ){
            quality( img, selection );
         }
		});
}


function saveCrop(img, selection ){
   
   $("#" + img.alt).removeClass('todo').addClass('done');
   
   var imageinfo=img.alt.split('-');
   
   $.ef( 'order.crop.save', {
      width:  img.width,
      height: img.height,
      x1:     selection.x1,
      y1:     selection.y1,
      x2:     selection.x2,
      y2:     selection.y2,
      dx:     selection.width,
      dy:     selection.height,
      imageid: imageinfo[0],
      prodno:  imageinfo[1]
      }, function( response ) {
      if( response.result ) {
         return true;
         //return response.message;
      } else {
         console.log( response.message );
      }
   
   } );
  // alert( response.message );
   
}

function getCrop( msg, imageid, prodno ){

   var size = printsize( prodno );
   currentPrintWidth = size[0];
      
   	$('img#cropbox').imgAreaSelect({ 
			x1: msg.x1, 
			y1: msg.y1,
			x2: msg.x2,
			y2: msg.y2,
			persistent: true,
			instance: true,
			aspectRatio: size[0] + ':' + size[1], 
			handles: true,
			keys: true,
		    onInit: function ( img, selection ){
			   quality( img, selection );
			},
		   onSelectEnd: function ( img, selection ) {
		      //console.log( selection );
		      saveCrop( img, selection );
         },
         onSelectChange: function ( img, selection ){
            quality( img, selection );
         }
		});
   
   
   
}

function printsize( prodno ){
   
   var print = new Array();
   
   if(prodno == '0001'){
      print[0] = 15.2;
      print[1]  = 10.2;
   }
   else if(prodno == '0014'){
      print[0] = 10.2;
      print[1]  = 10.2;
   }
    else if(prodno == '7345'){
      print[0] = 15.2;
      print[1]  = 15.2;
   }
   else if( prodno == '0419'){
      print[0] = 13.6;
      print[1] = 10.2;
   }
   else if( prodno == '0002'){
      print[0] = 20.3;
      print[1] = 15.2;
   }
   else if( prodno == '0005'){
      print[0] = 40.6;
      print[1] = 30.5;
   }
   else if( prodno == '0439'){
      print[0] = 30;
      print[1] = 20.3;
   }
   else if( prodno == '0003'){
      print[0] = 25.4;
      print[1] = 20.3;
   }
   else if( prodno == '0004'){
      print[0] = 30;
      print[1] = 25.4;
   }
   else if( prodno == '0534'){
      print[0] = 60;
      print[1] = 40;
   }
   else if( prodno == '7347' ){
      print[0] = 13;
      print[1] = 18;
   }
   
   else if( prodno == '0006' ){
      print[0] = 17.8;
      print[1] = 10.2;
   }
   
   return print;
}

function printtitle( prodno ){
   
   
   
   var print = new Array();
   
   if(prodno == '0001'){
      print = '10x15';
   }
   else if(prodno == '0014'){
      print = '10x10';
   }
   else if(prodno == '7345'){
      print = '15x15';
   }
   else if( prodno == '0419'){
      print = '10x13';
   }
   else if( prodno == '0002'){
      print = '15x20';
   }
   else if( prodno == '0005'){
      print = '30x40';
   }
   else if( prodno == '0439'){
      print = '20x30';
   }
   else if( prodno == '0003'){
      print = '20x25';
   }
   else if( prodno == '0004'){
      print = '25x30';
   }
   else if( prodno == '0534'){
      print = '40x50';
   }
   else if( prodno == "7347"){
      print = '13x18';
   }
   
    else if( prodno == "0006"){
      print = '10x18';
   }
   
   return print;

}

function quality(img, selection){
      
   var quality =  (currenImageWidth / currentPrintWidth ) / (img.width / selection.width);
   
      if(quality > 0){
      if(quality > 100){
         $('.quality').html("<span>Meget bra</span>");
      }
      else if(quality > 50){
         $('.quality').html("<span>Bra</span>");
      }
      else if(quality > 20){
         $('.quality').html('<span class="bad" style="color: red;">D&#229;rlig</span>');
      }
      else{
         $('.quality').html('<span class="verybad" style="color: red; font-weight: bold">Meget d&#229;rlig</span>');
      }
   }
   
}
function changeImage( src, imageid, prodno ){
   
      // example usage
      var image = $('img#cropbox');
      $('#fit-in').removeAttr('checked');
      
      $('#crop-size-info').text( printtitle( prodno ) +'cm' );

      image.imgAreaSelect({ remove:true});
      image.attr('alt', imageid + '-' + prodno);
      image.hide();
      
      loadImage('img#cropbox', src, function() {
         image.show();
         updateCrop( prodno, imageid );
      });    
}

function loadImage(el, src, callback) {
 var objImagePreloader = new Image();
 objImagePreloader.onload = function() {
   $(el).attr('src', src);
     callback();
   };
 objImagePreloader.src = src;
}