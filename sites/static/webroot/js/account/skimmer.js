jQuery.fn.extend({
	newSkimmer: function() {
	
		if(!ie6) {
		
			/*$('img', this).error( function() { 
				$(this).attr('src', 'http://static.repix.no/images/static/thumbs/width/95/gfx/404/no_images.jpg' ); 
			});*/

			$(this).each( function(i, item) {
				//	t	=	thumbnail
				//	si	=	start image
				//	st	=	skimmerthumb
				//	s	=	skimmer
				//	sl	=	skimmer length
				//	im	=	mouseposition / thumbnail width / quantity of thumbs

				var loadedThumbs = false;
				
				var skimmer = $(item).parent();
				
				var t = item;
				var si = $('img',t).attr('src');
				var img = new Image();
				var s = $(t).next()	;		
				var sl = $('li', s).length;
				img.src = si;
				
				$('input[type=checkbox]', skimmer).bind('change', function() {
					if( $(this).is(':checked') ) {
						$(skimmer).parent().addClass('selected');
					} else {
						$(skimmer).parent().removeClass('selected');
					}
				});

				
				if( sl > 1 ) { // activate skimmer if more than one image
				
								
					$(img).load( function() {
						if( img.width <= img.height ) { 
							$(t).css('background-size', '100% auto');
						} 
						else { 
							$(t).css('background-size', 'auto 100%');
						}
					});
								
					$(skimmer).bind('mouseleave', function() {
					
						if( !$(skimmer).parent().hasClass('selected') ) {
							$('input[type=checkbox]', skimmer).hide();
						}
						
						if( img.width <= img.height ) {
							$(t).css('background-size', '100% auto');
						} else {
							$(t).css('background-size', 'auto 100%');
						}
							$(t).css('background-image', 'url('+si+')');
					});
				
					$(skimmer).bind('mouseenter', function() {
					
						$('input[type=checkbox]', skimmer).show();
					
						if( $(item).data('loadedThumbs') != 'true' ) {
							loadThumbs( $(item).next() );
						}
					});
				}
				
				function loadThumbs( ul ) {
					$('li', ul).each( function( i, li) {
						var img = new Image();
						img.src = $('a', li).attr('href');
						img.onload = function() {
							loadedThumbs++;

							if( loadedThumbs == sl	 ) {
							
								$(item).data('loadedThumbs', 'true' );
					
								$(t).css('background-image', 'url('+ $('img', t).attr('src') +')')
									.mousemove( function(e) {
										var im = Math.floor( (e.pageX - $(skimmer).offset().left) / ( $(skimmer).width() / sl + 1 ));
										var itm = $('li', s).get(im);
										var bg =  $('a', itm).attr('href');
										$(t).css('background-image', 'url('+bg+')');
										// Switch portrait or landscape
										if( $('img', itm).hasClass('portrait') ) { $(t).css('background-size', 'auto 100%'); } else {  $(t).css('background-size', '100% auto'); }
								});
								$('img', t).remove();
								
							}
						};
					});
				}
			});

		}
	}
});
