/*
 * 	sitat-silder - jQuery plugin
 * 	@author Tor Inge Løvland tor.inge@eurofoto.no
 */

(function($) {
	$.fn.sitatslider = function(options){
		var defaults = { 
			speed: 12000
	  	};
		
	 	var opts = $.extend(defaults, options);
		
		$(this).children().hide().css( {'position': 'absolute'} );
		
		$(this).children().first().slideDown().addClass('active-rotator');
		var refreshIntervalId = setInterval( function(){ play( null, opts.speed ); }, opts.speed );
	};
	
	
	function play( id  , speed ) { 
		var $active = $('.active-rotator');
		if( $active.next().length > 0 ){
			var $next = $active.next();
		}else{
			var $next = $active.parent().children().first();
		}
		$active.removeClass('active-rotator').fadeOut( speed / 4 );
		$next.addClass('active-rotator').fadeIn( speed / 4 );
	}

})(jQuery);