// format price
function formatPrice(num) {
    num = num.toString();
    num = num.replace(',','.');	
    price = parseInt( num * 100 ) / 100;
    return price.toFixed(2).toString().replace('.', ',');
}
// Adds + - buttons to input.quantity
$(document).ready( function() {
    $('input.quantity').before('<a href="#" class="btn btn-default changequantity">-</a>').prev().click( function() { 
        if ( parseInt( $(this).next().attr('value') ) <= 0) { return false };
        $(this).next().attr('value', parseInt( $(this).next().attr('value') ) - 1);
        $(this).next().trigger('change');
        return false;
    });
    $('input.quantity').after('<a href="#" class="btn btn-default changequantity">+</a>').next().click( function() { 
        $(this).prev().attr('value', parseInt( $(this).prev().attr('value') ) + 1);
        $(this).prev().trigger('change');
        return false;
    });
});