var ie6 = (navigator.appName == "Microsoft Internet Explorer" && parseInt(navigator.appVersion) == 4 && navigator.appVersion.indexOf("MSIE 6.0") != -1);

jQuery.fn.extend({
	skimmer: function() {
		if(!ie6) {
			$('img', this).error( function() { 
				$(this).attr('src', 'http://static.repix.no/gfx/404/not_found_80px.jpg' ); 
			});
	
			$(this).each( function(i) {
					
				if( $(this).find('ul').hasClass('hide') ) { //checks for collection of preview images
					$(this).css('background-image', 'url('+ $('img', this).attr('src') +')').mousemove( function(e) {
						var image = Math.floor( (e.pageX - $(this).offset().left) / ( $(this).width() / $('ul li', this).length + 1 ));
						var item = $("ul li", this).get(image);
						var background =  $(item).find('img').attr('src');
						$(this).css("background-image", 'url(' + background + ')');
					});
					$('img', this).css('visibility', 'hidden');
				}
			});
		}
	},
	radioLabel: function() {
		$('.radioLabel input:radio').bind('click', function() {
			$(this).parent().parent().find('.radioLabel').removeClass('selected');
			$(this).parent().addClass('selected');
			$(this).parent().find('input.text').select();
		});
		
		$('.radioLabel select').bind('click', function() {
			$(this).parent().find('input:radio').trigger('click');
		})
		$('.radioLabel input.text').bind('focus', function() {
			$(this).parent().find('input:radio').trigger('click');
			$(this).select();
		})
	},
	imagePreview: function(xOffset, yOffset, source) {
	
		$(this).hover(function(e){
			if( !$(this).parent().parent().parent().hasClass('ui-sortable') ) {
				var time = $(this).data('time');
				var self = this;
				if(time) {
					clearTimeout( time );
					$(this).data('time', '');
				}
				$(this).data('time', setTimeout( function() { 
				    self.t = self.title;
					self.title = '';	
					var c = (self.t != '') ? '<br/>' + self.t : '';
					$('body').append('<div id="preview"><img src="'+ $(self).data( source ) +'"/>'+ c +'</div>');	 
					$('#preview').css('top',(e.pageY + yOffset) + 'px').css('left',(e.pageX + xOffset) + 'px').fadeIn('fast')
				 }, 500));
			}
		},
		function(){
			var time = $(this).data('time');
			if(time) {
				clearTimeout( time );
				$(this).data('time', '');

			}
			this.title = this.t;	
			$('#preview').fadeOut('fast', function() {
				$(this).remove();
			});
		}).mousemove(function(e){
			$('#preview').css('top',  e.pageY + yOffset + 'px').css('left', e.pageX + xOffset + 'px');
		});
	}
});

// Adds + - buttons to input.quantity
$(document).ready( function() {
	$('input.quantity').before('<a href="#" class="button changequantity large">-</a>').prev().click( function() { 
		if ( parseInt( $(this).next().attr('value') ) <= 0) { return false };
		$(this).next().attr('value', parseInt( $(this).next().attr('value') ) - 1);
		$(this).next().trigger('change');
		return false;
	});
	$('input.quantity').after('<a href="#" class="button changequantity large">+</a>').next().click( function() { 
		$(this).prev().attr('value', parseInt( $(this).prev().attr('value') ) + 1);
		$(this).prev().trigger('change');
		return false;
	});
});

// Utils
function shorten( str, maxlen ) {
    if( !maxlen ) maxlen = 10;
    if( str.length > maxlen ) {
        return str.substr(0,maxlen) + '...';
    } else {
        return str;
    }
}
// format price
function formatPrice(num) {
	num = num.toString();
	num = num.replace(',','.');	
	price = parseInt( num * 100 ) / 100;
	return price.toFixed(2).toString().replace('.', ',');
}