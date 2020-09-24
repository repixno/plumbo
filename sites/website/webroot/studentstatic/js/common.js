$(function(){
    
	
	
		
	$('.quantity').wrap('<div class="input-group spinner"></div>').after('<div class="input-group-btn-vertical">\
							  <button class="btn btn-default" type="button"><i class="glyphicon glyphicon-chevron-up"></i></button>\
							  <button class="btn btn-default" type="button"><i class="glyphicon glyphicon-chevron-down"></i></button>\
						   </div>');

    $('.spinner .btn:first-of-type').on('click', function() {
        var input = $(this).parent().parent().find('input');
        var newvalue = parseInt(input.val(), 10) + 1;
        
        if( newvalue > 0 ){
            $('#box' + input.attr('id') ).prop('checked', true);
        }else{
            $('#box' + input.attr('id') ).prop('checked', false);
        }
        
        
        input.val( newvalue );
        
    });
    
    $('.spinner .btn:last-of-type').on('click', function() {
        var input = $(this).parent().parent().find('input');
        var newvalue = parseInt(input.val(), 10) - 1;
        if( newvalue < 0 ){
           newvalue = 0;
        }
        
        if( newvalue > 0 ){
            $('#box' + input.attr('id') ).prop('checked', true);
        }else{
            $('#box' + input.attr('id') ).prop('checked', false);
        }
        
        input.val( newvalue );
    });
    
    $('.input-number').on('change', function(){
        
        var value = $(this).val();
        if( isNaN(value) || value < 0 ){
            $(this).val(0);
        }
        if( value > 0 ){
            $('#box' + $(this).attr('id') ).prop('checked', true);
        }else{
            $('#box' + $(this).attr('id') ).prop('checked', false);
        }
        return true;
    });
    
    
    $('input.choose_product').on('change', function(){
        
        var unitprice = parseFloat( $(this).parent().parent().find('.unitprice').text() );
        
        if( $(this).prop('checked') ){
            $(this).parent().parent().find('.input-number').val(1);
            $(this).parent().parent().find('.productprice').text( formatPrice( unitprice ) );
        }else{
            $(this).parent().parent().find('.input-number').val(0);
            $(this).parent().parent().find('.productprice').text(0);
        }
        
        updatetotal();
    });
    
    
    
    $('.productquantity').on( 'change', function(){
        
        var tvalue = $(this).val();
        var productprice = parseFloat(  $(this).parent().parent().parent().find('.unitprice').text() );
        
        if( tvalue > 0 ){
        
            $(this).parent().parent().parent().find('.productprice').text( formatPrice( tvalue * productprice ) );
            
            
            
        }
        
        updatetotal();
    });
    
    
    $('.input-group-btn-vertical button').on('click', function(){
        
        $(this).parent().parent().find('.productquantity').change();
        
        })
    
    $('.reload').on('click', function () {
        location.reload();
        return false;
       }) 
        
    
})


function updatetotal(){
    var totalprice = 0;
    $('.productline').each( function(){
        
        var price = parseFloat(  $(this).find('.unitprice').text() ) ;
        var quantity = parseFloat(  $(this).find('.productquantity').val() );
        
        //console.log(price);
        
        if( price > 0 && quantity > 0 ){
           totalprice += ( price * quantity); 
        }
        
        
        });
    
    $('.totalprice').text( formatPrice(  totalprice ) );
}


function formatPrice(num) {
    
    if(isNaN(num)){
       return num; 
    }
    else{
        num = num.toString();
        num = num.replace(',','.');	
        price = parseInt( num * 100 ) / 100;
        return price.toFixed(2).toString().replace('.', ',');
    }
}

function showloader(){  
    $('body').prepend( '<div class="loadercontainer"><div class="loadingInfo"><img src="http://a.static.eurofoto.no/gfx/gui/ajax-loader-gray.gif" />\
                            <br/><br/><h3>Loading.......<span id="status"></span></h3></div><div id="loader-overlay"></div></div>');
        
        $('.loadercontainer').width( $(window).width() )
                    .height( $(document).height() );
        $('.loadingInfo').width( $(window).width() )
                    .height( $(document).height() );
                            
        $('#loader-overlay')
        .css('opacity', '0.8')
        .css('margin','0')
        .width( $(window).width() )
                    .height( $(document).height() );
                                    
    return false;
    }

function hideloader(){
		$('.loadercontainer').remove();
}

jQuery.fn.extend({
    live: function (event, callback) {
       if (this.selector) {            
            jQuery(document).on(event, this.selector, callback);
        }
    }
});