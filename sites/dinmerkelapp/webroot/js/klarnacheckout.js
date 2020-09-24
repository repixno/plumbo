var ajaxLoader = '<img src="https://static.repix.no/gfx/gui/ajax-loader.gif" class="loader"/>';

$(document).ready( function() {

   //$('#contact-email').focus();


	//<![CDATA[	
	jQuery.validator.addMethod("fullname", function(value, element) { 
  		return value.split(' ').length > 1; 
	}, "Please provide first name and last name");
	//]]>	
	$("a.popup").fancybox({ 
		'zoomSpeedIn': 300, 
		'zoomSpeedOut': 300, 
		'overlayShow': true,
		'frameWidth': 900,
		'frameHeight': 500 }
	); 
	
	$( '#contact-country' ).change( function() {
	   
	   if( !$( '#enable-other-delivery-address' ).is( ':checked' ) ) {
	     reloadDeliveryOptions();
	   } 
	   
	});
	
	
	$( '#delivery-country' ).change( function() {
	   reloadDeliveryOptions();
	});
	
	function reloadDeliveryOptions() {
	   
	   toggleDeliveryOptionsLoading();
	   
	}
	
	function toggleDeliveryOptionsLoading() {
	   
	   $( '#delivery-methods-wrapper' ).empty();
	   $( '#delivery-methods-wrapper' ).append( '<img style="display:block; margin-left:auto; margin-right:auto; margin-top:20px;" src="http://a.static.repix.no/gfx/gui/ajax-loader.gif" />' );
	   
	   if( $('#enable-other-delivery-address').is(':checked') ) {
	      
	      var country = $( '#delivery-country' ).val();
	      
	   } else {
	      
	      var country = $( '#contact-country' ).val();
	      
	   }
	   
	   $.post( '/api/1.0/checkout/deliveryandpaymentmethods', 
	     { 'countryid': country }, 
	     function( data ) {
	        if( data.result ) {
	           writeDeliveryOptions( data );
	           writePaymentOptions( data );
	        } else {
	           alert( 'failed' );  
	        } 
	     },
	   'json' ); 
	}
	
	function updateDeliveryCostFromElement( element ) {
	   var id = element.value;
	   var price = $( '#deliveryprice_' + id ).html();
	   $( '#deliverycost' ).text( formatPrice( price ) );
	}
	
	
	function updatePaymentCostFromElement( element ) {
	   var id = element.value;
	   var price = $( '#paymentprice_' + id ).html();
	   $( '#paymentcost' ).text( price );
	}
	
	
	function updatePaymentCost( price ) {
	   $( '#paymentcost' ).text( price );
	}
	
	function updateDeliveryCost( price ) {
	   $( '#deliverycost' ).text( price );
	}
	
	function writePaymentOptions( data ) {
	   $( '#payment-methods-wrapper' ).empty();
	   var paymentwrapper = $( '<div class="col-md-12 last"></div>' );

	   var poptions = data.options.payment_options;
	   
	   $.each( poptions, function( i, option ) {
	      var tmpwrapper = $( '<div class="span-12" style="border-bottom: 1px dotted #000;"></div>' );
	      var input = $( '<input type="radio" name="payment-method" value="' + option.refid + '" id="payment_' + option.id + '" />' );
	      input.click( function() {
	         updatePaymentCostFromElement( this );
         });
	      if( option.isPreset ) {
	         input.attr( 'checked', true );
	         tmpwrapper.append( input );
	         updatePaymentCost( option.price );
	      } else {
	         tmpwrapper.append( input );
	      }
	      
	      tmpwrapper.append( '<label class="large" for="payment_' + option.id + '">' + option.title + '<span class="quiet"> kr <span class="price hide" id="paymentprice_' + option.refid + '">' + option.price + '</span><span>' + option.price + '</span></span></label>' );
	      paymentwrapper.append( tmpwrapper );
	      
	   });

	   $('#payment-methods-wrapper').append( paymentwrapper );
	   
	}
	
	
	$( '#delivery-methods-wrapper :radio' ).click( function() {
		
	   deliveryid = $(this).attr('value');
	   $('.storealert').remove();
	   $( '#payment-methods-wrapper' ).empty();
	   $( '#payment-methods-wrapper' ).append( '<img style="display:block; margin-left:auto; margin-right:auto; margin-top: 20px;" src="http://a.static.repix.no/gfx/gui/ajax-loader.gif" />' );
	   
	   $('#deliverytitle').html( $(this).parent().find('.deliveryoption').html()  );
      
      updateDeliveryCostFromElement( this );
      
      updateTotal();
      
	});
	
	
	
	$( document ).on('change', '#payment-methods-wrapper :radio', function() {
	   /*var id = this.value;
	   var pricetag = $( '#paymentprice_' + id ).html();*/
	   updatePaymentCostFromElement( this );
	   
	   updateTotal();
	});
	
	
	$( '#storeon' ).change( function() {
	  if( $( this + ':checked' ) ) {
	     //$( '#delivertostore' ).removeAttr( 'disabled' );
	     $( '#delivertostore' ).attr( 'disabled', 'false' );
	     $( '#delivertostore' ).removeAttr( 'disabled' );
	     $( '#stores' ).css( 'display', 'block' );
	  }
	});

	$( '.storeoff' ).change( function() {
	   $( '#delivertostore' ).attr( 'disabled', 'disabled' );
	   $( '#stores' ).css( 'display', 'none' );
	});

	
});

function updateTotalNoklarna(){
    
    var totalPrice = 0;
	$('.price', '#cart').each( function(i, item) {
		totalPrice += parseFloat( $(item).text().replace(',','.') );
	});
	
	$('.total-price').text( formatPrice( totalPrice ) + ' kr' );
   

}

function updateTotal(){
   
   if( loggedin > 0 ){
	window._klarnaCheckout(function (api) {
		api.suspend();
	});
   }
	var totalPrice = 0;
	$('.price', '#cart').each( function(i, item) {
		totalPrice += parseFloat( $(item).text().replace(',','.') );
	});
	
	$('.total-price').text( formatPrice( totalPrice ) + ' kr' );
   
	if( loggedin > 0 ){
		window._klarnaCheckout(function (api) {
	
			$.post( '/api/1.0/checkout/updateklarna', { 
				'delivery-method' : $("input[name='delivery-method']:checked").val(),
			}, function( data ) {
				window._klarnaCheckout(function (api) {
					api.resume();
				});
				},
			'json');
		});
	}
   
}


