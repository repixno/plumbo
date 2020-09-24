(function($) {
	$.fn.tilrotate = function() {
	   
	   var $rotatormenu = $('<div id="rotatormenu" class="span-20 cards last right"/>');
	   //$(this).css( {'position': 'relative ', 'height' : '199px'} );
	   
		$(this).children().hide().css( {'position': 'absolute'} );
		
		$(this).parent().append( $rotatormenu );
		
		$('#rotatormenu').append('<button id="playbutton" class="stopbutton">&nbsp;</button>');
		$(this).children().each( function( index ){
		   $(this).attr('id', 'rotatorimage-' + index );
		   $('#rotatormenu').append('<button class="rotatormenu" id="rotatormeny-' + index + '">' + $(this).find( 'img' ).attr( 'title')  + '</button>');
		});
		
		$('.rotatormenu').first().addClass('selected-button');
		
		var refreshIntervalId = setInterval( function(){ play(null); }, 6000 );
		
		$('#playbutton').click( function(){
		   if( $(this).hasClass('stopbutton') ){
		      clearInterval(refreshIntervalId);
		      $(this).removeClass('stopbutton').addClass('playbutton');
		   }else{
		      refreshIntervalId = setInterval( function(){ play(null); }, 6000 );
		      $(this).removeClass('playbutton').addClass('stopbutton');
		   }
		   
		});
		
	   $('.rotatormenu').live( 'click' , function(){
         clearInterval(refreshIntervalId);
         $('#playbutton').removeClass('stopbutton').addClass('playbutton');
         var n = $(this).attr('id').split( '-' );
		   play(n[1]);
		});

		$(this).children().first().addClass('active').show();


		function play( id  ) { 
          var $active = $('div.active');
          var m = $active.attr('id').split( '-' );
          if( id == null ){
             if( $active.next().length > 0 ){
                var $next = $active.next();
             }else{
                var $next = $active.parent().children().first();
             }
             
             n = $next.attr('id').split( '-' );
             id = n['1'];
          }
          else{
             var $next = $('#rotatorimage-' + id );
          }
          
          $('#rotatormeny-' + m['1'] ).removeClass( 'selected-button', {duration:3000}  ).removeAttr( 'disabled' );
          
          $('#rotatormeny-' + id ).addClass( 'selected-button' , {duration:3000} ).attr('disabled', 'disabled');
          $active.removeClass('active').fadeOut(3000);
          $next.addClass('active').fadeIn(3000);
      }
	};
})(jQuery);