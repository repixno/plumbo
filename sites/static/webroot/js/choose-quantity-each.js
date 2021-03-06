var totalNumberOfImages = 0;
var totalNumberOfStandardPrints = 0;
var totalTimesUpdated = 0;

// rounds to 2 decimal
function roundNumber(num) {
	return parseInt( num * 100 ) / 100;
}
$(document).ready( function() {
	
	//init
	$('a.back').click(function() {	history.go(-1);	return false; });
	totalNumberOfImages = parseFloat($('#total-num-of-images').text().replace(',','.')); // replaces comma with dot
	totalNumberOfStandardPrints = 0;
	$('.popup').fancybox();

	// Updates the current total price for each product when the input is changed
	$('input.order-print-quantity', '#change-each-print').bind('change', function() {
		
		var totalPrice = parseFloat($('#total-price').text().replace(',','.'));
		
		var priceOnThisItem = parseFloat( $(this).parent().find('.price').text().replace(',','.'));
		//console.log('priceOnThisItem: ' + priceOnThisItem);
		
		var quantityOfThisItem = parseInt( $(this).attr('value') );
		//console.log('quantityOfThisItem: ' + quantityOfThisItem);
		
		var totalPriceOfThisItem = priceOnThisItem * quantityOfThisItem;
		//console.log('totalPriceOfThisItem: ' + roundNumber(totalPriceOfThisItem) );
		
		$(this).parent().find('.totalPriceOfThisItem').text( totalPriceOfThisItem );
		
		updateTotalPrice();
		
		return false;
	});
	
	// Creates the buttons on the input.quantity box
	$('input.order-print-quantity', '#change-each-print').before('<a href="#" class="button changequantity">-</a>').prev().click( function() { 
		if ( parseInt( $(this).next().attr('value') ) <= 0) { return false };
		$(this).next().attr('value', parseInt( $(this).next().attr('value') ) - 1).trigger('change');
		return false;
	});
	$('input.order-print-quantity', '#change-each-print').after('<a href="#" class="button changequantity">+</a>').next().click( function() { 
		$(this).prev().attr('value', parseInt( $(this).prev().attr('value') ) + 1).trigger('change');
		return false;
	});
	
	// adding the price for white borders
	$('#standard-prints-product-options input', '#change-each-print').bind('click', function() {
		updateTotalPrice();
	});
	
	// update prices on white borders when changed
	$('#white-borders input', '#change-each-print').bind('click', function() {
		updateTotalPrice();
	})
	
	
	// show enlargements
	$("[href='showallenlargements']", '#change-each-print').click( function() {
		$(this).parent().prev().slideToggle('slow');
		//$('.white-borders input').trigger('change');
		return false;
		});
	
	$('.remove-print', '#change-each-print').click( function() { 
		$(this).parent().parent().parent().hide('slow').empty();
		updateTotalPrice();
		return false;
	});

	//$('input.order-print-quantity').trigger('change');	

	updateTotalPrice();
});

	function updateTotalPrice() {
		updatePrices();
		
		var totalProductionsMethodsPrice = 0;
		totalNumberOfStandardPrints = 0;
		
		// updates total number of standard prints
		$('.standard-prints input.order-print-quantity', '#change-each-print').each( function() {
			totalNumberOfStandardPrints = totalNumberOfStandardPrints + parseInt( $(this).attr('value') );
		});
		
		// update prices from productionsmethods
		var totalWhiteBorderPrice = 0;
		var totalPaperQualityPrice = 0;
		var totalFillingMethodPrice = 0;
		var totalCorrectionPrice = 0;

		totalProductionsMethodsPrice = 0;
		totalProductionsMethodsPrice = totalWhiteBorderPrice + totalFillingMethodPrice + totalPaperQualityPrice + totalCorrectionPrice;

		var totalPrice = 0;
				
		// getting all the total prices from each product
		$('.totalPriceOfThisItem','#change-each-print').each( function(i) {
			totalPrice =  totalPrice + parseFloat( $(this).text().replace(',','.') );
		});
		
		// adding the price for preductionsmethods
		totalPrice = totalPrice + totalProductionsMethodsPrice;
		
		$('#total-price').text( roundNumber(totalPrice) );
	};

	
	function getPrice( quantity, prodno ) {
		$('td.prodno_'+prodno, 'table#prices').removeClass('added');
		var returnprice = 0;
		$('table#prices').find('td.prodno_' + prodno).each( function() {
			var min = parseInt( $(this).find('.min').text().replace(',','.') );
			var max = parseInt( $(this).find('.max').text().replace(',','.') );
			var price = parseFloat( $(this).find('.price').text().replace(',','.') );
			if( quantity >= min && ( quantity <= max || isNaN( max ) ) ) {
				if( quantity > 0 ) $(this).addClass('added');
				returnprice = price;
			}
		});
		return returnprice;
	}
	
	function getTotalQuantityOfProdno( prodno ) {
		var quantity = 0;
		$('input[title='+prodno+']', '#change-each-print').each( function(i, item) {
			quantity = parseInt(quantity) + parseInt( $(this).attr('value').replace(',','.') );
		});
		return quantity;
	}

	function updatePrices() {
		
		var totalPrice = parseFloat($('#total-price').text().replace(',','.'));
		$('input.order-print-quantity', '#change-each-print').each( function(i) {
			totalTimesUpdated++;
			var quantityOfThisItem = parseInt( $(this).attr('value') );
			var prodno = $(this).attr('title');
			//Getting the price from the price-matrix, depending on the total quantity of each product
			var totalQuantityOfProdno = getTotalQuantityOfProdno(prodno);
			var priceOnThisItem = getPrice( totalQuantityOfProdno, prodno );
			var totalPriceOfThisItem = parseFloat(priceOnThisItem) * parseFloat(quantityOfThisItem);
			$(this).parent().find('.totalPriceOfThisItem').text( roundNumber(totalPriceOfThisItem) );
		});
	}