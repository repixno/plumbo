(function($) {
	$.fn.fontmenu = function(options){
		var container, active;
        $('html').append('<div class="fonter-container hide"><ul id="fonter" /></div>');
        container = $(this);
        
        $.post( "/api/1.0/gift/fontloader", function( data ) {  
            var i = 0;
            var name = '';
            
            $.each( data.data, function( key, value ) {
                    if( i == 0 ){
                        container.append( '<span style="font-family:'+key+'" id="' + key + '">'+ key + '</span>' );
                    }
                    i++;
                    var fontfamily = key;
                    
                    $.each( value, function( fkey, fvalue ) {
                        name = fvalue.substr(0, fvalue.lastIndexOf('.'));
                        $('style').append( '@font-face{ font-family: "' + fontfamily + '"; src: url("/editorfonts/' + fvalue + '"); font-weight: ' + fkey + '; font-style: '+ fkey +' ;}');
                    });
                
                    $('#fonter').append( '<li style="font-family:'+fontfamily+'" id="' + name + '">'+ fontfamily + '</li>' );
                    
            });
        
        },'json');
		
        container.on( 'click' , function(){
            var position =  $(this).offset() ;
            $(".fonter-container").css( {top:position.top + 20, left: position.left } )
            $(".fonter-container").show();
            active = true;
        });
    
        $(document).on( 'mouseover' , "#fonter li" , function(){
                 $('.active').each( function(){
                    $(this).removeClass('active');
                    });
            
                $(this).addClass('active');
            });
        $(document).on( 'click' , "#fonter li" , function(){
            
            $( container ).find('span').text( $(this).text() );
            $( container ).find('span').css( 'font-family', $(this).text() );
            
            $('li.selected').each( function(){
                
                $(this).removeClass('selected');
                
                });
            
            $(this).addClass('selected');
            $(".fonter-container").hide();
            active = false;
            //updateText();
            
            var idname = $(this).attr('id');
            
            $('#fontselect').val(idname);
            
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