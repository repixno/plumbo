(function( $ ) {
 
    $.fn.loadPage = function() {
        
        this.append('<div class="loader"><h1 style="position: relative; top: 200px;">Page is loading.</h1></div>');
        
        
        var refreshIntervalId = setInterval( function(){
                   var thistext = $(".loader h1" ).text();
               
                   $(".loader h1" ).text( thistext + '.' );
                },
         500 );
         
         
         $( window ).load(function() {
            clearInterval(refreshIntervalId);
            $('.loader').fadeOut( "slow", function() {
                 $(this).remove();
              });
           
            
        });
         
         
 
    };
 
}( jQuery ));