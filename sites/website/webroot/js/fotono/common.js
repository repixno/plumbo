$(function(){
    
    
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
        var productprice = parseFloat(  $(this).parent().parent().parent().parent().parent().find('.unitprice').text() );
        if( tvalue > 0 ){
            $(this).parent().parent().parent().parent().parent().find('.productprice').text( formatPrice(  tvalue * productprice )  );  
        }
        
        updatetotal();
    });
        
    
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