var totalNumberOfImages = 0;
var totalNumberOfStandardPrints = 0;
	
$(document).ready( function() {
	
	$('a.back').click(function() {
		history.go(-1);
		return false;
	});
	
	//init
	totalNumberOfImages = parseFloat($('#total-num-of-images','#choose-quantity').text().replace(',','.')); // replaces comma with dot
	totalNumberOfStandardPrints = 0;
	
	$('.popup').fancybox();

	// Updates the current total price for each product when the input is changed
	$('input.order-print-quantity').bind('change', function() {
		var totalPrice = parseFloat($('#total-price').text().replace(',','.'));
		
		var priceOnThisItem = parseFloat( $(this).parent().find('.price').text().replace(',','.') );
		var quantityOfThisItem = parseInt( $(this).attr('value') )
		
		var totalPriceOfThisItem = totalNumberOfImages * priceOnThisItem * quantityOfThisItem;
		
		$(this).parent().find('.totalPriceOfThisItem').text( roundNumber(totalPriceOfThisItem) );
		
		updateTotalPrice();
		
		return false;
	});
	

	
	// adding the price for white borders
	$('#standard-prints-product-options input').bind('click', function() {
		updateTotalPrice();
	});
	
	
	// disable filling methods when white borders is active
	$('.white-borders input').bind('click', function() {
		
		if( $(this).is(':checked') ) {
			$('#filling-method').hide('slow');
		} else {
			$('#filling-method').show('fast');
		}
	
		
	});

	$('input.order-print-quantity').trigger('change');	

});

	function updateTotalPrice() {

		updatePrices();
		
		var totalProductionsMethodsPrice = 0;
		totalNumberOfStandardPrints = 0;
		
		// updates total number of standard prints
		
		$('.standard-prints input.order-print-quantity','#choose-quantity').each( function() {
			totalNumberOfStandardPrints = parseInt(totalNumberOfStandardPrints) + parseInt( parseInt( $(this).attr('value') ) * parseInt(totalNumberOfImages) );
		});

		
		
		// update prices from productionsmethods
		var totalWhiteBorderPrice = 0;
		$('#standard-prints-product-options #white-borders input','#choose-quantity').each( function() {
			if( $(this).is(':checked') ) {
				totalWhiteBorderPrice = 0;
				totalWhiteBorderPrice = parseFloat( totalNumberOfStandardPrints) * parseFloat( $(this).next().find('.price').text().replace(',','.') );
			}
		});
		
		var totalFillingMethodPrice = 0;
		$('#standard-prints-product-options #filling-method input','#choose-quantity').each( function() {
			if( parseFloat( $(this).next().find('.price').text().replace(',','.') ) > 0 && $(this).is(':checked') ) {
				totalFillingMethodPrice = parseFloat( totalNumberOfStandardPrints) * parseFloat( $(this).next().find('.price').text().replace(',','.') );
			}
		});
		
		
		var totalPaperQualityPrice = 0;
		$('#standard-prints-product-options #paperquality input','#choose-quantity').each( function() {
			if( parseFloat( $(this).next().find('.price').text().replace(',','.') ) > 0 && $(this).is(':checked') ) {
				totalPaperQualityPrice = parseFloat( totalNumberOfStandardPrints) * parseFloat( $(this).next().find('.price').text().replace(',','.') );
			}
		});
		
		
		var totalCorrectionPrice = 0;
		$('#standard-prints-product-options #correction input','#choose-quantity').each( function() {
			if( parseFloat( $(this).next().find('.price').text().replace(',','.') ) > 0 && $(this).is(':checked') ) {
				// correction is each image not each print
				totalCorrectionPrice = parseFloat( totalNumberOfImages ) * parseFloat( $(this).next().find('.price').text().replace(',','.') );
			}
		});
		
		 
		totalProductionsMethodsPrice = 0;
		totalProductionsMethodsPrice = 
			parseFloat( totalWhiteBorderPrice ) + 
			parseFloat( totalFillingMethodPrice ) + 
			parseFloat( totalPaperQualityPrice ) + 
			parseFloat( totalCorrectionPrice );

		var totalPrice = 0;
		
		// getting all the total prices from each product
		$('.totalPriceOfThisItem','#choose-quantity').each( function(i) {
			totalPrice =  parseFloat(totalPrice) + parseFloat( $(this).text().replace(',','.') );
		});
		
		// adding the price for preductionsmethods
		totalPrice = parseFloat(totalPrice) + parseFloat( totalProductionsMethodsPrice );
		
		$('#total-price','#choose-quantity').text( formatPrice(totalPrice) );
	};

	
	function getPrice( quantity, prodno ) {
		
		$('td.prodno_'+prodno, 'table#prices').removeClass('added');
		
		var returnprice = 0;
		$('table#prices','#choose-quantity').find('td.prodno_' + prodno).each( function() {
         
			var min = parseInt( $(this).find('.min').text().replace(',','.') );
			var max = parseInt( $(this).find('.max').text().replace(',','.') );
			var textprice = $(this).find('.price').text();
			var price = parseFloat( textprice.replace(',','.') );
         
			if(quantity>= min){
				if (quantity<=max||isNaN(max)){
					if(quantity>0) {
					   $(this).addClass('added');
					   $('div.prodno_'+prodno+' label span.quiet span.price' ).text( textprice );
					   returnprice = price;
					}
					
				}
			}
		});
		
		return returnprice;
		
	}

	
	// rounds to 2 decimal
	function roundNumber(num) {
		return parseInt( num * 100 ) / 100;
	}

	function updatePrices() {
		
		$('input.order-print-quantity', '#choose-quantity').each( function(i) {
		
			var totalPrice = parseFloat($('#total-price','#choose-quantity').text().replace(',','.'));
			
			var quantityOfThisItem = parseInt( $(this).attr('value') );
			
			var totalQuantityOfThisItem = quantityOfThisItem * totalNumberOfImages;
			
			var prodno = $(this).attr('name');
			
			// Getting the price from the price-matrix, depending on the total quantity of each product
			var priceOnThisItem = getPrice( totalQuantityOfThisItem, prodno );
			var totalPriceOfThisItem = parseFloat(totalNumberOfImages) * parseFloat(priceOnThisItem) * parseFloat(quantityOfThisItem);
			
			var totalPriceString = roundNumber(totalPriceOfThisItem).toString().replace('.',',') ;
			
			$(this).parent().find('.totalPriceOfThisItem').text( totalPriceString );
		});
	}
