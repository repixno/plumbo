(function($) {
	$.fn.fontmenu = function(options){
		var container, active;
		
        //$('body').append('<div class="fonter-container" style="display:none"><ul id="fonter" /></div>');
        container = $(this);
        
        $.post( "/api/1.0/create/fontloader", function( data ) {    
            var i = 0;
			
            $.each( data.data, function( key, value ) {
                    var fontfamily = key;
					var u = 0;
					var fontfile = "";
                    $.each( value, function( fkey, fvalue ) {
						if( u == 0 ){
							fontfile = fvalue;
						}
						//if(fkey == 'Regular'){
							$('style').append( '@font-face{ font-family: "' + fontfamily + '"; src: url("/static/editorfonts/' + fvalue + '"); font-weight: ' + fkey + '; font-style: '+ fkey +' ;}');
							//console.log( fkey + '-' + fvalue );
                        //}
						u++;
						//return false;
                    });
                    i++;
					
					fontdata[key] = fontfile;
					
					$('#fonter2').append( '<li style="cursor:pointer; font-family:'+fontfamily+'" id="' + fontfamily + '" data-filename="' + fontfile + '"><a href="#">'+ fontfamily + '</a></li>' );
            });
        
        },'json');
		
        container.on( 'click' , function(){
            var position =  $(this).offset() ;
            $(".fonter-container").css( {top:position.top + 20, left: position.left } )
            $(".fonter-container").show();
            active = true;
        });
    
        $(document).on( 'click' , "#fonter2 li" , function(){
            
			console.log($( container).find('button').data() );
			
            $( container).find('b').text( $(this).text() );
            $( container).find('button').css( 'font-family', $(this).text() );
			
			$( container ).find('button').data('font', $(this).data('filename') );
            updateText();
            return false;
            });
    };
	
	
    
    getFocusedSelect = function() {
        var _ref1;
        return (_ref1 = $('.fonter-container')) != null ? _ref1.selectInstance : void 0;
    };



})(jQuery);