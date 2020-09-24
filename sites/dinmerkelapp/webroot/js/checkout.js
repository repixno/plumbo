var ajaxLoader = '<img src="https://static.repix.no/gfx/gui/ajax-loader.gif" class="loader"/>';

$(document).ready( function() {

   //$('#contact-email').focus();
	$('#checkout-login-button').live('click',  function() {
		$(this).after( ajaxLoader );
		$.ajax({
			url: '/api/1.0/user/login',
			type: 'post',
			dataType: 'json',
			data: {
				username: $('#contact-email').attr('value'),
				password: $('#password').attr('value')
			},
			success: function(msg) {

				if( msg.result != false) {
				   window.location = '/checkout'
				   //location.reload();
				} else {
					$('#password').next('.loader').remove();
					$('#password').after('<label class="inputError" i18n:translate="">Wrong password</label>');
				}
			},
			error: function(msg) {
				$('#password').next('.loader').remove();
				alert('error: ' + msg);
			}
		});
		
		
	});

	//<![CDATA[	
	jQuery.validator.addMethod("fullname", function(value, element) { 
  		return value.split(' ').length > 1; 
	}, "Please provide first name and last name");
	//]]>

	$('#enable-other-delivery-address').bind('click', function() {
		if( $(this).is(':checked') ) {
			$('#other-delivery-address').show('fast');
			$.validator.addClassRules({
				dname: {
					required: true,
					fullname: true
				},
 				dadress: {
					required: true
				},
 				dzipcode: {
					required: true,
					minlength: 4
				},
 				dcity: {
					required: true
				}
			});
		} else {
			$('#other-delivery-address').hide('fast');
			$('#other-delivery-address input').val('');
			$.validator.addClassRules({
				dname: {
					required: false,
					fullname: false
				},
 				dadress: {
					required: false
				},
 				dzipcode: {
					required: false,
					minlength: false
				},
 				dcity: {
					required: false
				}
			});
		}
	});
	



	
	/*$("a.popup").fancybox({ 
		'zoomSpeedIn': 300, 
		'zoomSpeedOut': 300, 
		'overlayShow': true,
		'frameWidth': 900,
		'frameHeight': 500 }
	); */
	
	
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
	   $( '#delivery-methods-wrapper' ).append( '<img style="display:block; margin-left:auto; margin-right:auto; margin-top:20px;" src="https://static.repix.no/gfx/gui/ajax-loader.gif" />' );
	   
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
	   $( '#deliverycost' ).text( price );
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
	   var paymentwrapper = $( '<div class="col-md-12"></div>' );

	   var poptions = data.options.payment_options;
	   
	   $.each( poptions, function( i, option ) {
	      var tmpwrapper = $( '<div class="col-md-12" style="border-bottom: 1px dotted #000;"></div>' );
	      var input = $( '<input type="radio" name="payment-method" value="' + option.refid + '" id="payment_' + option.id + '" /> ' );
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
	      
	      tmpwrapper.append( ' <label class="large" for="payment_' + option.id + '">' + option.title + '<span class="quiet"> kr <span class="price hide" id="paymentprice_' + option.refid + '">' + option.price + '</span><span>' + option.price + '</span></span></label>' );
	      paymentwrapper.append( tmpwrapper );
	      
	   });

	   $('#payment-methods-wrapper').append( paymentwrapper );
	   
	}
	
	
	function writeDeliveryOptions( data ) {
	   
	   var deliveryoptions  = data.options.delivery_options;
	   var paymentoptions   = data.options.payment_options;
	   var dwrapper         = $( '<div></div>' );

	   $.each( deliveryoptions, function( i, option ) {
	      var main = $( '<div class="span-12 " style="border-bottom: 1px dotted grey; padding: 3px;"></div>' );
	      var sub1 = $( '<div class="span-5"></div>' );
	      var sub2 = $( '<div class="span-3"><span class="large"> kr <span class="price hide" tal:content="' + option.price + '"></span><span id="deliveryprice_' + option.refid + '"> ' + option.price + ' </span></span></div>' );

	      var span = $( '<span></span>' );

	      if( option.isPreset == true ) {
	         var input = $( '<input name="delivery-method" id="storeoff" class="storeoff" type="radio" value="' + option.refid + '" checked="checked" />' );
	         updateDeliveryCost( option.price );
	      } else {
	         var input = $( '<input name="delivery-method" id="storeoff" class="storeon" type="radio" value="' + option.refid + '"/>' );
	      }
	      
	      input.click( function() {
	         updateDeliveryCostFromElement( this );
	         loadPaymentOptions( this.value );
	         if( option.artnr == 7109 ) {
	            $( '#stores' ).css( 'display', 'block' );
	         } else {
	            $( '#stores' ).css( 'display', 'none' );
	         }
	      });
	      
	      var label = $( '<label for="delivery_' + option.id + '" class="large">' + option.title + '</label>' );

	      var trackingtext = $( '<div class="span-4 last"></div>' );

	      if( option.title == "A-post" ) {
	         trackingtext.append( '<span style="display: block; margin-top:8px;">1 til 2 dagers leveransetid</span>' );
	      } else if( option.title == "Servicepakke" ) {
	         trackingtext.append( '<span style="display: block; margin-top:8px;">3 til 5 dagers leveringstid.</span>' );
	      } else if( option.title == "Dør til dør" ) {
	         trackingtext.append( '<span style="display: block; margin-top:8px;">1 til 2 dagers leveransetid</span>' );
	      } else if( option.title == "Hente i butikk" ) {
	         trackingtext.append( '<span style="display: block; margin-top:8px;">3 til 5 dagers leveringstid.</span>' );
	      }

	      // Artnr for storedelivery
	      if( option.artnr == 7109 ) {
	         if( option.isPreset == true ) {
	           var stores = $( '<div id="stores"><label>Velg butikk: </label><select name="delivertostore" class="select"><tal:block tal:repeat="chain storechains"><optgroup label="${chain/chain}"><tal:block tal:repeat="store chain/stores"><option value="${store/store/id}">${store/store/name}:${store/store/address}</option></tal:block></optgroup></tal:block></select></div>' );
	         } else {
               var stores = $( '<div id="stores" style="display:none;"><label>Velg butikk: </label><select name="delivertostore" class="select"><tal:block tal:repeat="chain storechains"><optgroup label="${chain/chain}"><tal:block tal:repeat="store chain/stores"><option value="${store/store/id}">${store/store/name}:${store/store/address}</option></tal:block></optgroup></tal:block></select></div>' );	            
	         }
	      }

	      sub1.append( input );
	      sub1.append( label );
	      if( stores ) {
	        sub1.append( stores );
	      }

	      main.append( sub1 );
	      main.append( sub2 );
	      main.append( trackingtext );

	      dwrapper.append( main );

	   });

	   $( '#delivery-methods-wrapper' ).empty();
	   $( '#delivery-methods-wrapper' ).append( dwrapper );
	   
	}
	
	
	function loadPaymentOptions( inputvalue ) {
	   
	   $( '#payment-methods-wrapper' ).empty();
	   $( '#payment-methods-wrapper' ).append( '<img style="display:block; margin-left:auto; margin-right:auto; margin-top: 20px;" src="https://static.repix.no/gfx/gui/ajax-loader.gif" />' );
	   
      $.post( '/api/1.0/checkout/paymentmethods', { 
         'delivery-method' : inputvalue,
         'countryid'       : $( '#contact-country' ).val()
      }, function( data ) {
         
         if( data.result ) {
            $( '#payment-methods-wrapper' ).empty();
            var paymentwrapper = $( '<div class="row"></div>' );
            var has_preset = false;
            var presetelement = null;
            
            var poptions = data.paymentoptions;
            $.each( poptions, function( i, option ) {
               var tmpwrapper = $( '<div class="col-md-12" style="border-bottom: 1px dotted #000;"></div>' );
               var input = $( '<input type="radio" name="payment-method" value="' + option.refid + '" id="payment_' + option.id + '" /> ' );
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
               tmpwrapper.append( ' <label class="large" for="payment_' + option.id + '">' + option.title + '<span class="quiet">   &nbsp kr <span class="price hide" id="paymentprice_' + option.refid + '">' + option.price + '</span><span>' + option.price + '</span></span></label>' );
               paymentwrapper.append( tmpwrapper );
            });
            
            $('#payment-methods-wrapper').append( paymentwrapper );
           
         }
         
      }, 'json' );
	   
	}
	
	
	
	
	$( '#delivery-methods-wrapper :radio' ).click( function() { 
	   
	   $( '#payment-methods-wrapper' ).empty();
	   $( '#payment-methods-wrapper' ).append( '<img style="display:block; margin-left:auto; margin-right:auto; margin-top: 20px;" src="https://static.repix.no/gfx/gui/ajax-loader.gif" />' );
	   
      $.post( '/api/1.0/checkout/paymentmethods', { 
         'delivery-method' : this.value,
         'countryid'       : $( '#contact-country' ).val() 
      }, function( data ) {
         
         if( data.result ) {
            $( '#payment-methods-wrapper' ).empty();
            var paymentwrapper = $( '<div class="col-md-12"></div>' );
            
            var poptions = data.paymentoptions;
            $.each( poptions, function( i, option ) {
               var tmpwrapper = $( '<div class="col-md-12" style="border-bottom: 1px dotted #000;"></div>' );
               var input = $( '<input type="radio" name="payment-method" value="' + option.refid + '" id="payment_' + option.id + '" /> ' );
               if( option.isPreset ) {
                  input.attr( 'checked', true );
                  tmpwrapper.append( input );
                  updatePaymentCost( option.price );
               } else {
                  tmpwrapper.append( input );
                  updatePaymentCostFromElement( input );
               }
               tmpwrapper.append( ' <label class="large" for="payment_' + option.id + '">' + option.title + ' <span class="quiet">&nbsp kr <span class="price hide" id="paymentprice_' + option.refid + '">' + option.price + '</span><span>' + option.price + '</span></span></label>' );
               paymentwrapper.append( tmpwrapper );
               
               input.click( function() {
                  updatePaymentCostFromElement( this );
               });
               
            });
            
            $('#payment-methods-wrapper').append( paymentwrapper );
            
         }
         
      }, 'json' );
      
      updateDeliveryCostFromElement( this );
      
      updateTotal();
      
	});
	
	
	
	$( '#payment-methods-wrapper :radio' ).live('change',  function() {
	   /*var id = this.value;
	   var pricetag = $( '#paymentprice_' + id ).html();*/
	   updatePaymentCostFromElement( this );
	   
	   updateTotal();
	});
	
	
	$( '#storeon' ).change( function() {
	  if( $(this).prop('checked') ) {
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



function updateTotal(){
   
   var productpricetext =  $('#productcost').text().replace( '.','' );  
  
   var productcost = parseFloat( productpricetext );
   var deliverycost = parseFloat( $('#deliverycost').text() );
   var paymentcost = parseFloat( $('#paymentcost').text() );
   $('.total-price').text( formatPrice( productcost + deliverycost + paymentcost ) );
   
}


function formatPrice(num) {
	num = num.toString();
	num = num.replace(',','.');	
	price = parseInt( num * 100 ) / 100;
	return price.toFixed(2).toString().replace('.', ',');
}

