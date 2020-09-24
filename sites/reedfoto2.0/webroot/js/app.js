$(function(){
    
    
    $('input.qty').addClass('form-control-small');
    
    $('input.qty').before('<div class="input-group-prepend">\
                            <button class="btn btn-dark text-light btn-sm" type="button">&#10094;</button>\
                        </div>').prev().click( function() { 
		if ( parseInt( $(this).next().attr('value') ) <= 0) {
            return false;
        }
		$(this).next().attr('value', parseInt( $(this).next().attr('value') ) - 1);
		$(this).next().trigger('change');
		return false;
	});
	$('input.qty').after('<div class="input-group-append">\
                          <button class="btn btn-dark text-light btn-sm" type="button">&#10095;</button>\
                        </div>').next().click( function() { 
		$(this).prev().attr('value', parseInt( $(this).prev().attr('value') ) + 1);
		$(this).prev().trigger('change');
		return false;
	});
    
}
)