/*
 * 	Merkelapp-silder - jQuery plugin
 * 	@author Tor Inge Løvland tor.inge@eurofoto.no
 */

(function($) {
	$.fn.merkelappslider = function(options){
		var defaults = { 
			speed: 6000
	  	};
	 	var opts = $.extend(defaults, options);
		
		
		$(this).children().hide().css( {'position': 'absolute'} );
		$(this).children().first().show().addClass('active-rotator');
		
		$(this).parent().append( '<div style="position: relative" id="merkelapprotatorcontainer"></div>' );
		
		$('#merkelapprotatorcontainer').append( $(this) );
		
		var refreshIntervalId = setInterval( function(){ play( null, opts.speed ); }, opts.speed );
		
		/*
		$(this).parent().append('<div id="merkelapprotatornext" style="position:absolute; background: grey; z-index: 200; right: 1px; top: 5px; padding: 2px;">Next</div>');
		$( '#merkelapprotatornext' ).click( function(){	
			play( null );
		});*/
	};
	
	
	function play( id  , speed ) { 
		var $active = $('.active-rotator');
		
		if( $active.next().length > 0 ){
			var $next = $active.next();
		}else{
			var $next = $active.parent().children().first();
		}
		$active.removeClass('active-rotator').fadeOut( speed / 2 );
		$next.addClass('active-rotator').fadeIn( speed / 2 );
	}

})(jQuery);