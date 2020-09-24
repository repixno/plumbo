$(document).ready( function() {

		$('.skimmer').skimmer();

		$('.skimmer').bind('click', function() {
		   var next = $(this).next().find('input');
		   forceValue = next.is(':checked') ? 0 : 1;
		   next.trigger('click');
		   forceValue = 2;
		});

		$('#next-step').bind('click', function() {
			if( parseInt( $('#total-number-of-images').text() ) == 0) {
				$('#please-select-some-images').dialog({
					buttons: {
						'OK': function() { 
							$(this).dialog('close')
						}
					}
				});
			return false;
			}
		});

		$('#pop-out-single-images').click( function() {
			$('#single-images').dialog({
				maxHeight: 500
			});
			return false;
		});

		$('li', '#images').remove();

		var forceValue = 2;

		$('input.select').bind('click', function() {
			
			var value = forceValue == 2 ? ( $(this).is(':checked') ? 1 : 0 ) : forceValue;
			
			if ( value ) {
				$(this).parent().parent().find('.skimmer').addClass('selected');
				
				$(this).parent().find('a.popup').addClass('hide').css('display', 'none');
				
				$('#total-number-of-images').text(
					parseInt( $(this).parent().parent().find('.numimages').text() ) + parseInt ( $('#total-number-of-images').text() )
				);
				
				$('.remove.album_' + $(this).attr("value"), '#single-images').trigger('click');

			} else {
			
				$(this).parent().parent().find('.skimmer').removeClass('selected');
				$(this).parent().parent().find('a.popup').removeClass('hide').css('display', 'block');
				$('#total-number-of-images').text(
					parseInt ( $('#total-number-of-images').text() ) - parseInt( $(this).parent().parent().find('.numimages').text() )
				);
			}
		});



		$('#select-all').bind('click', function() {
			$('#albums input:checkbox').attr('checked', 'checked').change();
			return false;
		});

		$('#select-none').bind('click', function() {
			$('#albums input:checkbox').removeAttr('checked').change();
			return false;
		});

		$("a.popup").bind('click', function() {

			if( $(':parent input', this).is(':checked') ) {
				alert( albumIsSelectedText );
			}

			var albumid = $(this).attr('name');

			if( $('#choose-single-images_'+albumid).data('opened') == 'true' ) {
				$('#choose-single-images_'+albumid).dialog('open');
				return false;
			}

			$('#choose-single-images_'+albumid).dialog({

			open: function() {
			
				if( $('#choose-single-images_'+albumid).data('opened') != 'true' ) {
					
					$('#choose-single-images_'+albumid).data('opened', 'true');
					
					$.getJSON('/api/1.0/album/images/enum/basiclist/'+albumid, function(data) {
					
						$('li', '#choose-single-images'+albumid).remove();
						
						$(data.images).each( function(i, item) {
						
							$('div#choose-single-images_'+albumid+' ul.images').append(
								$('<li>', {
									"class": "single-image",
									name: item.id
								}).append(
									$('<div>', {
										"class": "mini-image"
									}).append(
										$('<img/>', {
											src: item.thumb,
											width: 80,
											data: {
												'screensize': item.screen
											}
										})
									)
								).append(
									$('<span>', {
										text: item.title
									})
								).append(										
									$('<a>', {
										"class": 'add',
										text: clickToAdd
									})
								)
							)
						});

						if( $(data.images).length <= 0 ) {
							$('div#choose-single-images_'+albumid).append('<h2 class="center prepend-top">'+albumIsEmpty+'</h2>');
						}
						
						$('ul.images img', '#choose-single-images_'+albumid).imagePreview(5, 5, 'screensize');

						$('.loader', '#choose-single-images_'+albumid).remove();
						
						$('.imagelist', '#choose-single-images_'+albumid).removeClass('hide');

						$('.single-image','#choose-single-images_'+albumid).bind('click', function() {

							var id = $(this).attr('name');

							if ( $('.single-image', '#single-images').attr('name') == id ) {
								alert(alreadySelected);
								return false;
							}

							$('#single-images').removeClass('hide');
							
							$('#total-number-of-images').text( parseInt ( $('#total-number-of-images').text() ) + 1 );

							$('.add', this).fadeOut('slow').hide();

							$('#single-images ul').prepend( $(this).clone() );
							$(this).fadeTo('slow', 0.3);

							// adding hidden input
							$('.single-image[name='+id+']', '#single-images').addClass('album_'+albumid);

							$('.single-image[name='+id+']', '#single-images').prepend('<input type="hidden" name="images[]" value="'+id+'" /><a href="#" class="remove">' + removeText + '</a>');

							$('.single-image[name='+id+']', '#single-images').effect('highlight');

							$('.single-image[name='+id+'] .remove', '#single-images').bind('click',function() {
								$('#total-number-of-images').text( parseInt ( $('#total-number-of-images').text() ) - 1 );
								$('.single-image[name='+id+']', '#choose-single-images_'+albumid).css('opacity', '1');
								$('.single-image[name='+id+'] .add', '#choose-single-images_'+albumid).show();
								$(this).parent().remove();
								return false;
							});
						});

						return false;
					});
					}
				},
				close: function() {
					$('.popupPreview').hide().remove();
					$('ul li', '#choose-single-images'+albumid).remove();
				},
				width: 380,
				height: 400
			});
			return false;
		});
			
		
	});
