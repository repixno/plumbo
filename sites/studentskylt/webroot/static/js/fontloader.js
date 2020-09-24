(function($) {
	$.fn.fontmenu = function(options){
		var container, active;
		
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
                    
					if( i == 0 ){
                        //container.append( '<span style="font-family:'+key+'" id="' + key + '">'+ key + '</span>' );
                    }
                    i++;
					
					fontdata[key] = fontfile;
					
					$('#fonter').append( '<li style="font-family:'+fontfamily+'" id="' + fontfamily + '" data-filename="' + fontfile + '">'+ fontfamily + '</li>' );
            });
        
        },'json');
		
        $(document).on( 'click' , "#fonter li" , function(){
            
			//console.log($( container).find('span').data() );
			
            $( container).find('span').text( $(this).text() );
            $( container).find('span').css( 'font-family', $(this).text() );
			
			$( container ).find('span').data('font', $(this).data('filename') );
			
			//console.log($(this).data('filename'));
			
			//$( container).find('span').data('filename', $(this).data('filename'));
            
            $('li.selected').each( function(){
                
                $(this).removeClass('selected');
                
                });
            
            $(this).addClass('selected');
            $(".fonter-container").hide();
            active = false;
            updateText();
            });
        $(document).on( 'mouseout' , "#fonter li" , function(){
                $(this).removeClass('active');
            });
        $(document).on( 'mouseout', '#font-list' , function(){
            $(this).find( 'ul' ).hide();
            });
        
        $(document).on( 'keydown', function(e){
                        
            if( $('.active' ).length == 0){
                $("#fonter li").first().addClass('active');
            }
            
            if( active == true ){
                if( e.keyCode == 40 ){
                    $('.active').removeClass('active').next().addClass('active');
                }
                else if( e.keyCode == 38 ){
                    $('.active').removeClass('active').prev().addClass('active');
                }
                else if( e.keyCode == 13 ){
                    
                    $( container).find('span').text( $('.active').text() );
                    $( container).find('span').css( 'font-family',$('.active').text() );
                    
                    $('li.selected').each( function(){
                        
                        $(this).removeClass('selected');
                        
                        });
                    
                    $('.active').removeClass('active').addClass('selected');
                    $(".fonter-container").hide();
                    active = false;
                    return false;
                }
                else{
                    return true;
                }
            }
                 
            return true;
            });
    };
	
	
    
    getFocusedSelect = function() {
        var _ref1;
        return (_ref1 = $('.fonter-container')) != null ? _ref1.selectInstance : void 0;
    };



})(jQuery);
