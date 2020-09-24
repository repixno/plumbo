

function orderPrintsDialog( albums, images ) {


	var buttons = {};
	
	buttons['Add to cart'] = function() {
		alert('adding to cart')
	};
	
	buttons['Add to collection'] = function() {
		alert('adding to collection')
	};
	
	buttons['Cancel'] = function() {
		alert('Cancels')
	};

	if( typeof( albums ) == 'object' ) {
	
		if( $(albums).length == 1 ) {
			// This is only one album
			
			$('#order-prints-dialog').dialog({
				'buttons'	:	buttons
			});
			
		};
	
	}
}

function printAlbum( element ) {
	var album = $(element).data('info');
	window.location.href = '/order-prints/fromalbum/' + album.id;
}


function orderPrints( albums, images ) {

	console.log( albums, images );

	if( !albums ) {
		if( !images ) {
			alert('Found no images')
		} else {
			//console.log( $(images) );
			//orderPrintsDialog( '', images );
		}
	} else if( albums && $(images).length > 0 ) {
		$('<div/>', {
			'title'	:	'You have selected images'
		}).append(
			$('<p/>', { 'text' : 'Which one do you want to order?' } )
		).dialog({
			'modal'	:	true,
			'buttons': {
				'Entire album'	:	function() {
					window.location.href = '/order-prints/fromalbum/' + albums.id;
				},
				'Only selected'	:	function() {
					orderPrints( false, images);
				} 
			}
		}).appendTo('body')
		
	} else {
		window.location.href = '/order-prints/fromalbum/' + albums.id;
	}
}


function setDefaultImage( imageid, albumid ) {
	var response = $.ef( 'album.set.defaultimage', {
		'albumid': albumid, 
		'imageid': imageid
	});
	
	if( response.result ) {
		alert('Default image is set');
	} else {
		alert('error');
	}
}


function deleteImageDialog( image, element ) {

	$('#deleteImageDialog .imageTitle').text( image.title );
	$('#deleteImageDialog img.image').attr('src', image.thumbnail );
	
	
	var buttons = {};
	buttons[texts.deleteThisImage] = function() {
		if( removeImage( image.id ) ) {
			$(element).remove();
			$(this).dialog('close');	
		}
	};
	
	buttons[texts.cancel] = function() {
		$(this).dialog('close');
	};
	
	$('#deleteImageDialog').dialog({
		'autoOpen'		:	false,
		'resizable'		:	false,
		'height'		:	240,
		'dialogClass'	:	'negative',
		'width'			:	13*40,// span-12+1
		'modal'			:	true,
		'buttons'		:	buttons
	});
	
	$('#deleteImageDialog').dialog('open');
	
}

function removeImage( imageid ) {

	var result = false;

	var response = $.ef( 'image.delete', {
		'imageid': imageid 
	});
	
	if( response.result ) {
		result = true;
	} 
	
	return result;
}


function addImageToBlog( image ) {

	var created = $.ef( 'blog.post.create', {
		'title'	:	image.title,
		'intro'	:	image.description,
		'body'	:	image.description
	});

	if( created.result ) {
		var postedImage = $.ef( 'blog.image.add', {
			'postid'	:	created.post.id,
			'imageid'	:	image.id
		});
		
		if( !postedImage ) {
			//console.error( postedImage );
		}
		
	} else {
		//console.error( post );
	}
}

function addToCollectionDialog( albums, images, collection, source ) {

	var result = false;

	if( albums.length > 0 ) {
		$('#addToCollectionDialog .albums').show();
		$(albums).each( function(i, album) {
			$('#addToCollectionDialog ul.albums').append(
				'<li>' + $(album).data('title') + ' (' + $(album).data('numimages') + ' images)</li>'
			);
		});
	} else {
		$('#addToCollectionDialog .albums').hide();
	}
	
	if( images.length > 0 ) {
		$('#addToCollectionDialog').append('<h3>Remeber this images?</h3><ul class="albums"/>');
		$(images).each( function(i, image) {
			$('#addToCollectionDialog ul.albums').append(
				'<li><img width="50" src="'+$(image).data('thumbnail')+'"/></li>'
			);
		});
	}
	
	var buttons = {};
	
	buttons[texts.addToCollection] = function() {
		$('#addToCollectionDialog').empty();
		$('#addToCollectionDialog').append( bigLoader );
		if( addToCollection( albums, images, false, false ) ) {
			window.location.href = $(source).attr('href');
		}
	};
	
	buttons[texts.cancel] = function() {
		window.location.href = $(source).attr('href');
	};

	$('#addToCollectionDialog').dialog({
		'width'		:	700,
		'modal'		:	true,
		'resizable'	:	false,
		'buttons'	:	buttons 
	});
	
	return result;
	
}


var debug;

function printImagesDialog( images ) {


	var buttons = {};
	
	buttons['Add to cart'] = function() {
		console.log('Add to cart');
	}
	
	buttons[texts.cancel] = function() {
		$('#printImagesDialog').dialog('destroy');	
	}


	$('#printImagesDialog').dialog({
		'modal'		:	true,
		'resizable'	:	false,
		'buttons'	:	buttons
	});
	
	
	$('#printImagesDialog').append( 
		$('<p/>', {
			'text'	:	images.length + ' images'
		})
	);
	
	
	$.ef( 'products.enum', {
		'group': 'prints, enlargements' 
	}, function(result) {
		$(result.products[0]).each( function(i, item) {
			$('#printImagesDialog').append( 
				$('<h3>', {
					'text'	:	item.title
				})
			);
			console.log( item );
		})
	});
	

}


function downloadAlbumDialog( albumid, element ) {

	$('#downloadAlbumDialog').dialog({
		'modal'		:	true,
		'resizable'	:	false,
		'open'		:	function(event, ui) {
			console.log('open');
		}
	});
	
	$.ef( 'album.download', {
			'albumid': albumid
		}, function( response ) {

			if( response.result ) {
				window.location.href = response.albumdownloadpath;
				$('#downloadAlbumDialog').dialog('close');
			}
	});

}

function albumPreferences( albumid, element, reload ) {

	var response = $.ef( 'album.info', {
		'albumid': albumid // Optional
	});
	
	$('#albumPreferencesDialog img.thumbnail').attr('src', response.album.thumbnailurl ).css('width', '100%');
	
	$('#albumPreferencesDialog .title').val( response.album.title );
	$('#albumPreferencesDialog .description').val( response.album.descriptionraw );
	
	$('#albumPreferencesDialog .created').text( response.album.created);
	$('#albumPreferencesDialog .numimages').text( response.album.numimages);
	$('#albumPreferencesDialog .numviewed').text( response.album.numviewed);
	
	
	if( response.album.owner.preferences.purchase ) {
		$('#albumPreferencesPurchase').attr('checked', 'true');
	}
	if( response.album.owner.preferences.download ) {
		$('#albumPreferencesDownload').attr('checked', 'true');
	}
	
	$('#albumPreferencesDialog a.guessYear').click( function() {
		
		$(this).before( inlineLoader );
		
		var guessedYear = guessAlbumYear( albumid );
		
		if( guessedYear) {
			$(this).parent().find('.loader').remove();
			$('#albumPreferencesDialog select.year option').removeAttr('selected');
			$('#albumPreferencesDialog select.year').prepend( $('<option>', { 'value': guessedYear, 'text': guessedYear, 'selected': 'selected' } ) );
		} else {
			$(this).parent().find('.loader').remove();
			alert('Could not guess year');
		}
	
		return false;
	});
	
	
	var range = 100;
	var currentYear = $('#albumPreferencesDialog select.year option').val();
	var firstYear = parseInt( currentYear, 10 ) - parseInt( range, 10 );
	
	// sets the year to blank if not provided
	if( response.album.owner.preferences.year === false ) {
		$('#albumPreferencesDialog select.year option.thisYear').remove();
		$('#albumPreferencesDialog select.year').append( $('<option>', { 'value': false, 'text': '', 'selected': 'selected' } ) );
	}
	
	if ( $('#albumPreferencesDialog select.year').length <= 2 ) {
		var i = currentYear;
		for (currentYear -1; i>=firstYear; i--)	{
			if( response.album.owner.preferences.year == i ) {
				$('#albumPreferencesDialog select.year').append( $('<option>', { 'value': i, 'text': i, 'selected': 'selected' } ) );
			} else {
				$('#albumPreferencesDialog select.year').append( $('<option>', { 'value': i, 'text': i} ) );
			}
		}
	} else {
		$('#albumPreferencesDialog select.year option').each( function(i, item) {
			if( $(this).val() == currentYear ) {
				$(this).attr('selected', 'selected');
			}
		});
	}
	
	
	// get integer from boolean
	
	function getIntegerFromBoolean( value ) {
	
		var boolean = 0;
	
		if(value) {
			boolean = 1;
		} 
		
		return boolean;
	}
	
	
	var buttons = {};
	
	buttons[texts.save] = function() {
		// Setting album title
		$('#albumPreferencesDialog .title').after( loader );
		
		
		if ( setAlbumTitle( albumid, $('#albumPreferencesDialog .title').val() ) ) {
			$(element).data('title', $('#albumPreferencesDialog .title').val() );
			$('.loader').remove();
		} else {
			alert('error');
		}
		
		// Setting album description
		$('#albumPreferencesDialog .description').after( loader );
		
		var setAlbumDescription = $.ef( 'album.set.description', {
			'albumid': albumid,
			'description': $('#albumPreferencesDialog .description').val()
		});
		
		if( setAlbumDescription.result ) {
			$(element).data('description', $('#albumPreferencesDialog .description').val() );
			$('.loader').remove();
		}
		
		
		$('#albumPreferencesPurchase').parent().parent().append( loader );
		var accessPref = $.ef( 'album.set.prefs', {
			'albumid': albumid, 
			'purchaseaccess'	:	getIntegerFromBoolean( $('#albumPreferencesPurchase').is(':checked') ),
			'downloadaccess'	:	getIntegerFromBoolean( $('#albumPreferencesDownload').is(':checked') ),
			'year'				:	$('#albumPreferencesDialog select.year').val() 
		});
		
		if( accessPref.result ) {
			$('.loader').remove();
		}
		
		/* refreshes the view if change in year (moves the album to the right year */
		if( currentYear != $('#albumPreferencesDialog select.year option:selected').val() ) {
			//console.log(currentYear + '!=' + $('#albumPreferencesDialog select.year option:selected').val() );
			//window.location.reload( true );
		} 
		
		if( setAlbumDescription.result && setAlbumDescription.result &&  accessPref.result ) {
			$(this).dialog('close');
		}

	};
	
	buttons[texts.cancel] = function() {
		$(this).dialog('close');
	};
	
	$('#albumPreferencesDialog').dialog({		
		'resizable'		:	false,
		'width'			:	630, // span-14
		'modal'			:	true,
		'buttons'		:	buttons,
		'close'			:	function() {
			if(reload) {
				//console.log( reload );
			} else{
				$(this).dialog('destroy');
			}
			
		}
	});
}


function shareAlbumDialog( albumid, element ) {

	var albumResponse;

	if ( albumid > 0 ) {
		
		// get friends
		var friendsResponse = $.ef( 'user.friends.enum' );	
		if( friendsResponse.result ) {
			$(friendsResponse.friends).each( function( i, friend ) {
				$('#shareAlbumDialog .all-friends ul').append(
					$('<li/>').append( 
						$('<input/>', {
							'type'	:	'checkbox',
							'value'	:	friend.friendid
						})
					).append(
						$('<span/>', {
							text: friend.name
						})
					)
				);
			});
		}

		albumResponse = $.ef( 'album.info', {
			'albumid': albumid
		});

	if( albumResponse.album.shared.link ) {
		$('#shareAlbumDialog input[name=publiclink]').val( albumResponse.album.sharingurl );
		$('#shareAlbumDialog input[name=publiclink]').removeAttr('disabled');
		$('#shareAlbumDialog #activeSecretLink').attr( 'checked', true );
		$('#secret-link-preferences').removeClass('disabled');
	} else {
		$('#shareAlbumDialog input[name=publiclink]').attr('disabled', true).val( texts.activateSecretLink );
		$('#shareAlbumDialog #activeSecretLink').attr( 'checked', false );
	}
	

	if( albumResponse.album.shared.password ) {
		$('#shareAlbumDialog #usePassword').attr( 'checked', true );
		$('#shareAlbumDialog .passwordContainer').show();
	}

	$('#activeSecretLink').change( function() {

		if( $(this).is(':checked') ) {
			$(this).hide();
			$(this).after( inlineLoader );

			var responseEnable = $.ef( 'album.share.link.enable', {
				'albumid': albumid
			});

			if( responseEnable.result ) {
				$('#shareAlbumDialog input[name=publiclink]').removeAttr('disabled').val( responseEnable.publiclink );
				$(this).next().remove();
				$(this).show();
				$('#secret-link-preferences').removeClass('disabled', 'slow');
			}
		} else {
			$(this).hide();
			$(this).after( inlineLoader );

			var responseDisable = $.ef( 'album.share.link.disable', {
				'albumid': albumid
			} );

			if( responseDisable.result ) {
				$('#shareAlbumDialog input[name=publiclink]').attr('disabled', true).val( texts.activeSecretLink );
				$(this).next().remove();
				$(this).show();
				$('#secret-link-preferences').addClass('disabled', 'slow');
			}
		}
	});

	$('#shareAlbumDialog #usePassword').click( function() {

		if ( $('#shareAlbumDialog .passwordContainer').is(':visible') ) {
			$('#shareAlbumDialog .passwordContainer').fadeOut();
		} else {
			$('#shareAlbumDialog .passwordContainer').fadeIn();
		}
		
	});

	$('#shareAlbumDialog #saveAlbumPassword').click( function() {
		$(this).after( inlineLoader );
		$(this).hide();

		var responsePassword = $.ef( 'album.set.password', {
			'albumid'	:	albumid, // Optional
			'password'	:	$('#shareAlbumDialog #albumPassword').val()
		});

		$(this).next().remove();
		$(this).show();
	});

	$('#shareAlbumDialog input[name=publiclink]').click( function() { 
		$(this).select(); 
	});
	
	// Social media
	$('#shareAlbumDialog .social-media .facebook').click( function() {
		var url = 'http://www.facebook.com/sharer.php?u=' + encodeURIComponent(albumResponse.album.sharingurl) + '&t=' + encodeURIComponent( $(element).data('title') );
		window.open( url, 'Share ' + $(element).data('title'), 'height=436,width=626');
	});
	$('#shareAlbumDialog .social-media .twitter').click( function() {
		var url = 'http://twitter.com/home?status=' + encodeURIComponent( shorten( $(element).data('title'), 20) ) + ': ' + encodeURIComponent(albumResponse.album.sharingurl);
		window.open( url );
	});
	
	$('#shareAlbumDialog .social-media .forum').click( function() {
		
		var height = 344;
		var width = 425;
		var movie = slideshowpro;
		var xmlFileType = 'Media RSS';
		var initialURL = encodeURIComponent(document.location);
		
		$('#share-html-dialog').dialog( {
			'width'		:		40*15-10,
			'modal'		:		true,
			'resizable'	:		false,
			'close'		:		function() {
				$(this).dialog('destroy');
			}
		});
		
		function getSlideshowEmbed() {
			return createSharedSlideshowEmbed( albumid, albumResponse.album.urlname, albumResponse.album.publickey, width, height, movie);
		}
		
		function updateSlideshowEmbed() {
			$('#share-html-dialog textarea.slideshowEmbed').val( getSlideshowEmbed() );
		}
		
		updateSlideshowEmbed();
		
		$('#share-html-dialog a.preview').click( function() {
			$('#slideshow-preview-dialog').html( getSlideshowEmbed() );
			
			$('#slideshow-preview-dialog').dialog( {
				'modal'		:		true,
				'height'	:		parseInt(height) + 80,
				'width'		:		parseInt(width) + 40,
				'resizable'	:		false,
				'scrollable':		false,
				'close'		:		function() {
					$(this).dialog('destroy');
				}
			});
		});

		
		$('#share-html-dialog input').change( function() {
			if( $('#share-html-dialog input.custom').is(':checked') ) {
				$('#share-html-dialog div.custom').fadeIn();
				width = $('#share-html-dialog input.width').val();
				height = $('#share-html-dialog input.height').val();
				//console.log( width, height);
			} else {
				width = $(this).val().split('x')[0];
				height = $(this).val().split('x')[1];
				$('#share-html-dialog input.width').val( width );
				$('#share-html-dialog input.height').val( height );
				$('#share-html-dialog div.custom').fadeOut();
				
			}
			updateSlideshowEmbed();
		});
		
		$('#share-html-dialog input.width, #share-html-dialog input.height').bind('keyup', function() {
			if ( $(this).hasClass('width') ) {	width = $(this).val();	}
			if ( $(this).hasClass('height') ) {	height = $(this).val();	}
			updateSlideshowEmbed();
		});
		
	});
	$('#shareAlbumDialog .social-media .blog').click( function() {
		alert('Blog');
	});
	
	
	var paramXMLPath = 'http://' + hostname + '/shared/album/slideshowparameters/${album/id}/${album/urlname}/${album/publickey}';
	var xmlFilePath = 'http://' + hostname + '/shared/album/rss/${album/id}/${album/urlname}/${album/publickey}';
	
						
	/*$('#share-link').attr('value', document.location ).focus( function() {$(this).select() }).click( function() {$(this).select() });
	$('#share-embed').attr('value', embed).focus( function() {$(this).select() }).click( function() {$(this).select() });*/


	var buttons = {};
	
	buttons[texts.save] = function() {
		// Saving friends sharing
		var friends = [];
		$('#shareAlbumDialog .all-friends ul li').each( function(i, item) {
			if( $('input', item).is(':checked') ) {
				friends.push( $('input',item).val() );
			}
		});
		var shareFriendResponse = $.ef( 'album.share.friends', {
			'albumid': albumid, // Optional
			'friends': friends // Optional
			//'groups': groups // Optional
		} );
		//$(this).dialog('destroy');
	};
	
	buttons[texts.cancel] = function() {
		$(this).dialog('close');
	};

	$('#shareAlbumDialog').dialog({		
		'resizable'		:	false,
		'height'		:	460,
		'width'			:	790, //span-20
		'modal'			:	true,
		'dialogClass'	:	'gray',
		'buttons'		:	buttons, 
		'close'			:	function() {
			$(this).dialog('destroy');
		}
	});
	
	} else {
		alert('please select an album');
	}
}



function deleteAlbumDialog( albumid, item ) {


	if( item ) {
		$('#deleteAlbumDialog').empty();
		$('#deleteAlbumDialog').append( '<img style="float:left; margin-right: 10px" src="' + $(item).data('thumbnail') + '" width="100" alt="' + $(item).data('title') + '"/>' );
		$('#deleteAlbumDialog').append('<h4>' + $(item).data('title') + ' <small class="quiet">' + $(item).data('numimages') + ' images</small></h4>');
		//$('#deleteAlbumDialog').append('<p>' + $(item).data('album').description + '</p>');	
	} else {
	
		var response = $.ef('album.info', {
			albumid : albumid
		});
		if (response.result) {
			$('#deleteAlbumDialog').empty();
			$('#deleteAlbumDialog').append( '<img style="float:left; margin-right: 10px" src="' + response.album.thumbnailurl + '" width="100" alt="' + response.album.title + '"/>' );
			$('#deleteAlbumDialog').append('<h4>' + response.album.title + ' <small class="quiet">' + response.album.numimages + ' images</small></h4>');
			if( response.album.description != 'undefined' ) {
				$('#deleteAlbumDialog').append('<p>' + response.album.description + '</p>');	
			}
		}
	}
	
	var buttons = {};
	
	buttons[texts.deleteText] = function() {

		$('#deleteAlbumDialog').empty().append( bigLoader );
		
		if( deleteAlbum( albumid, item ) ) {
			$('#deleteAlbumDialog').dialog('destroy');
		} else {
			alert('Error. Could not delete album');
			window.location.reload( false );
		}
	};
	
	buttons[texts.cancel] = function() {
		$('#deleteAlbumDialog').dialog('destroy');
	};

	$('#deleteAlbumDialog').dialog({
		'dialogClass'	:	'negative', 
		'width'			:	500,
		'resizable'		:	false,
		'modal'			:	true,
		'autoOpen'		:	false,
		'buttons'		:	buttons,
		'close'			:	function() {
			$(this).dialog('destroy');
		}
	});
	
	$('#deleteAlbumDialog').dialog('open');
	
}

function mergeSelectedAlbumsDialog( albums ) {


	if( $(albums).length > 1) {

	$(albums).each( function(i, album) {
		
		var thumbnail = $(album).data('thumbnail');
		var id = $(album).data('id');
		var title = $(album).data('title');
		
		$('#mergeSelectedAlbumsDialog ul').append( 
			$('<li/>').data('id', id  ).append(
				'<img src="' + thumbnail + '" height="20" width="20"/> <input type="radio" name="mergeAlbums" />' + title 
			)
		);
	});

	$('#mergeSelectedAlbumsDialog ul li').click( function() {
		$('input', this).trigger('click');
	});

	var buttons = {};
	buttons[texts.merge] = function() {
		if ( mergeAlbums( albums, $('#mergeSelectedAlbumsDialog ul li input:checked').parent().data('id') ) ) {
			window.location.reload( false );
			$('#mergeSelectedAlbumsDialog').dialog('destroy');
		} else {
			alert( 'error' );	
		}
	};

	$('#mergeSelectedAlbumsDialog').dialog({
		'modal'		:	true,
		'autoOpen'	:	false,
		'width'		:	500,
		'resizable':	false,
		'buttons'	:	buttons
	});

	$('#mergeSelectedAlbumsDialog').dialog('open');

	} else if( $(albums).length == 1) {

		alert('Please select more than one album');

	} else {

		alert('Please select some albums');

	}

}

function printSelectedAlbumsDialog( albums ) {

	if( $(albums).length >= 1) {

		var submitform = $( '<form action="/order-prints/choose-quantity" method="post" />' );
		
		$(albums).each( function(i, album) {
		   
		var albumcheckbox = document.createElement('input');
			albumcheckbox.type = 'checkbox';
			albumcheckbox.name = 'albums[]';
			albumcheckbox.checked = albumcheckbox.defaultChecked = true;
			albumcheckbox.value = $(album).data('id');

			submitform.append( albumcheckbox );

		});

		submitform.appendTo('body').submit();

	} else {
		alert('Please select some albums');
	}
	
}


function deleteSelectedImagesDialog( images ) {

	if( $(images).length > 0) {

		//show delete-progress (spinner?) to avoid "do i click again?"-confusion.   

		$(images).each( function(i, image) {
			$('#deleteSelectedImagesDialog ul').append( 
				$('<li/>')
				.data('id', $(image).data('id') )
				.append('<img src="'+$('a img', image).attr('src')+'" height="20" width="20"/><input type="checkbox" name="deleteImages" checked /> ' + $('a', image).attr('title') )
			);
		});

		$('#deleteSelectedImagesDialog ul li').click( function() {
			$('input', this).change();
		});
		
		$('#deleteSelectedImagesDialog').dialog({
			'modal'		:	true,
			'autoOpen'	:	false,
			'resizable'	:	false,
			'buttons'	:	{
				'Delete':	function() {
				
					var imagesToDelete = [];
					
					$('#deleteSelectedImagesDialog ul li input[type=checkbox]:checked').each( function(i, item) {
						imagesToDelete.push( $(item).parent().data('id') );
					});
					
					var response = $.ef( 'images.delete', {
						'images': imagesToDelete.toString()
					});
					
					if( response.result ) {
						$( images ).each( function(i, image) { 
							//console.log( image );

							// Updates the DOM
							// TODO: FIX this
							$(currentAlbum.images).each( function(y, item) { 
								if( $(image).data('id') == item.id) {
									delete currentAlbum.images[y];
								}
							});
							
							$(image).remove(); // Removes the thumbnails
						});
						
						$('#album-accordion-list li.selected small').text( currentAlbum.images.length );
					
						$('#deleteSelectedImagesDialog').dialog('destroy');
					} else {
						alert('error');
					}
   
				}
			}
		});

	  $('#deleteSelectedImagesDialog').dialog('open');

	} else {
	   
		alert('Please select some albums');
		
	}


}

function deleteSelectedAlbumsDialog( albums ) {

	if( $(albums).length > 0) {

		//show delete-progress (spinner?) to avoid "do i click again?"-confusion.   

		$(albums).each( function(i, album) {
			$('#deleteSelectedAlbumsDialog ul').append( 
				$('<li/>')
				.data('id', $(album).data('id') )
				.append('<img src="'+$(album).data('thumbnail')+'" height="20" width="20"/><input type="checkbox" name="deleteAlbums" checked /> ' + $(album).data('title') )
			);
		});

		$('#deleteSelectedAlbumsDialog ul li').click( function() {
			$('input', this).change();
		});

		$('#deleteSelectedAlbumsDialog').dialog({
			'modal'		:	true,
			'autoOpen'	:	false,
			'resizable'	:	false,
			'buttons'	:	{
				'Delete':	function() {
					if ( deleteAlbums( albums, $('#deleteSelectedAlbumsDialog ul li input:checked').parent().data('id') ) ) {
						window.location.reload( false );
						$('#deleteSelectedAlbumsDialog').dialog('destroy');
					} else {
						alert( 'error' );	
					}
				}
			}
		});

	  $('#deleteSelectedAlbumsDialog').dialog('open');

	} else {
	   
		alert('Please select some albums');
		
	}
}

function createNewAlbumDialog() {

	$('#newAlbumDialog').dialog({
		'width'		:	450,
		'modal'		:	true,
		'autoOpen'	:	false,
		'resizable'	:	false,
		'buttons'	:	{
			'Save' : function() {
				if ( newAlbum( $('#newAlbumTitle').val(), $('#newAlbumDescription').val(), $('#newAlbumForSale').is(':checked') ? 'true' : '', $('#newAlbumForDownload').is(':checked')  ? 'true' : '').result ) {
					window.location.reload( false );
					$('#newAlbumDialog').dialog('destroy');
				} else {
					alert( 'error' );
				}
			},
			'Save and upload' : function() {
				var response = newAlbum( $('#newAlbumTitle').val(), $('#newAlbumDescription').val(), $('#newAlbumForSale').is(':checked') ? 'true' : '', $('#newAlbumForDownload').is(':checked')  ? 'true' : '');
				if ( response.result ) {
					var setCurrentAlbumRespone = $.ef( 'upload.setcurrentalbum', {
						'albumid': response.album.id
					});
					$('#newAlbumDialog').dialog('destroy');
					window.location.href = '/account/upload';
				} else {
					alert( 'error' );
				}
			}
		}
	});

	$('#newAlbumDialog').dialog('open');
	  
}

function createMediaclipProduct( product, albums ) {


	if( product == 'photobook') {
		
		alert('Create photobook');
		
	}
	

	//check that albums are selected. add albumid's to collection and post to mediaclip

	var responseclear = $.ef( 'collection.clear', {
		'collection' : 'mediaclip'
	});
	
	if ( responseclear.result ) {

		$(albums).each( function(i, album) {
			var response = $.ef( 'collection.add.album', {
				'collection' : 'mediaclip',
				'albumid': $(album).data('id')
			});
		});
		
		window.location.href = '/create/' + product;

	}
}

function getSecretLinkDialog( albumid ) {

	if( albumid > 0) {

		var responseEnable = $.ef( 'album.share.link.enable', {
			'albumid': albumid
		} );

		if( responseEnable.result ) {
			$('#secretLink').val( responseEnable.publiclink );

			$('#secretLinkDialog').dialog({
				'width'		:	450,
				'modal'		:	true,
				'autoOpen'	:	false,
				'resizable'	:	false,
				'buttons'	:	{
				}
			});

			$('#secretLinkDialog').dialog('open');
		} else {
			alert('error');
		}
	} else {
		alert('please select an album');
	}
	
}

function moveImageDialog( image ) {

	$('#moveImageDialog img.thumbnail').attr('src', image.thumbnail);

	$('#moveImageDialog ul.albums').empty();
	$(albums).each( function(i, album) {
		$('#moveImageDialog ul.albums').append(
			$('<li/>', {
				'text'	:	album.title,
				'click'	:	function() {
					$('#moveImageDialog ul.albums li').removeClass();
					$(this).addClass('selected');
				},
				'rel'	:	album.id
			})
		);
	});
	
	buttons = {};
	buttons[texts.moveImage] = function() {
		var response = $.ef( 'images.move', {
			'images': image.id,
			'albumid': $('#moveImageDialog ul.albums li.selected').attr('rel')
		});
		
		if( response.result ) {
			$('#moveImageDialog').dialog('close');
		}
		
	};
	
	buttons[texts.cancel] = function() {
		$(this).dialog('close');
	};
	
	$('#moveImageDialog').dialog({
		'width'			:		(40*14)+10, //span-14
		'modal'			:		true,
		'dialogClass'	:		'gray',
		'resizable'		:		false,
		'autoOpen'		:		false,
		'buttons'		:		buttons,
		'close'			:		function() {
			$(this).dialog('destroy');
		}
	});
	
	$('#moveImageDialog').dialog('open');

}


function getSharingHTMLDialog( albumid ) {

	if( albumid > 0) {

		$('#insertInBlogCode').val('<html>' + albumid + '</html>');

		$('#insertInBlogDialog').dialog({
			'width'		:	450,
			'modal'		:	true,
			'resizable'	:	false,
			'autoOpen'	:	false,
			'buttons'	:	{
				// buttons
			}
		});

		$('#insertInBlogDialog').dialog('open');

	} else {
		alert('please select an album');
	}
}


function transferImagesDialog( albumid, batchid, sessionid ) {
   
   var buttons = {};
   
   buttons[texts.cancel] = function( ) {
		$(this).dialog('close');
	};

	buttons['OK'] = function( ) {
	   
	   $(this).dialog('close');

	   var redirecturl = '/account/albums/album/' + albumid;
	   
	   uploadSelectAlbum( batchid, albumid, '', redirecturl );

	}
   
	$('#transferImagesDialog').dialog({
		'width'		:	450,
		'modal'		:	true,
		'autoOpen'	:	false,
		'resizable'	:	false,
		'buttons'	:	buttons
	});

	$('#transferImagesDialog').dialog('open');
	
	$('#filedataUploader').remove();
	
   var firstButton=$('.ui-dialog-buttonpane button:last');
   firstButton.attr('id','next-action');
   firstButton.hide();
	
	initUpload( batchid, sessionid,'JPEG Images','Choose files','#next-action' ,'#transfer-status, #total-loader-spinner, #single-transfer-headline', '#show-on-finish', '' );
}

function transferImagesByEmailDialog( albumid ) {

	var response = $.ef( 'user.getmailtoken', {} );

	if ( !response.result ) {
		response = $.ef( 'user.createmailtoken', {
			'clear' : 1
		});
	}

	if ( response.result ) {
		$('#transferImagesByEmailDialog #emailAddress').attr( 'href', 'mailto:' + response.email + (( albumid > 0 ) ? '?subject=' + albumid : '' ) );
		$('#transferImagesByEmailDialog #emailAddress').text( response.email );
	}
	
	 $('#emailAlbumTitle').addClass('quiet');
	var startText = $('#emailAlbumTitle').val();
	
	$('#emailAlbumTitle').focus( function() {
		if( $(this).val() == startText ) {
			$(this).removeClass('quiet');
			$(this).val('');
		}
	});
	
	$('#emailAlbumTitle').blur( function() {
		if( $(this).val() == startText || $(this).val() == '') {
			$(this).addClass('quiet').val(startText);
		} else {
			$(this).removeClass('quiet');
		}
	});
	
	
	var buttons = {};
	
	buttons[texts.sendMail] = function() {
		window.location.href = 'mailto:' + $('#transferImagesByEmailDialog #emailAddress').text() + '?subject=' + $('#emailAlbumTitle').val();
		$(this).dialog('close');
	};
	
	buttons[texts.generateNew] = function() {
		var response = $.ef( 'user.createmailtoken', {
			'clear' : 1
		} );

		if ( response.result ) {
			$('#transferImagesByEmailDialog #emailAddress').attr( 'href', 'mailto:' + response.email + (( albumid > 0 ) ? '?subject=' + albumid : '' ) );
			$('#transferImagesByEmailDialog #emailAddress').text( response.email );
			$('#transferImagesByEmailDialog #emailVcard').attr( 'href', '/api/1.0/user/getvcardbymailtoken/' + response.token );
		}
	};
	
	buttons[texts.downloadVcard] = function() {
		window.location.href = '/api/1.0/user/getvcardbymailtoken/' + response.token;
	};

	$('#transferImagesByEmailDialog').dialog({
		'width'		:	450,
		'modal'		:	true,
		'autoOpen'	:	false,
		'resizable'	:	false,
		'buttons'	:	buttons
	});

	$('#transferImagesByEmailDialog').dialog('open');
		
}

function importImagesDialog() {
	alert('import images');
}


function moveImagesDialog( images ) {

	var imageIds = [];
	
	$(images).each( function(i, image) {
	
		//console.log( $('img', image).attr('src') );
		
		$('#moveImagesDialog ul.images').append(
			$('<div/>', {
				'html'	:	'<img src='+$('img', image).attr('src')+'/>'
			})
		);
		imageIds.push( $(image).data('id') );
	});
	
	$('#moveImagesDialog .imagesContainer small.quantity').text( '(' + images.length + ')' );
	
	$('#moveImagesDialog ul.albums').empty();
	
	$(albums).each( function(i, album) {
		$('#moveImagesDialog ul.albums').append(
			$('<li/>', {
				'text'	:	album.title,
				'click'	:	function() {
					$('#moveImagesDialog ul.albums li').removeClass();
					$(this).addClass('selected');
				},
				'rel'	:	album.id
			})
		);
	});
	
	
	buttons = {};
	buttons[texts.moveImages] = function() {
		var response = $.ef( 'images.move', {
			'images': imageIds.toString(),
			'albumid': $('#moveImagesDialog ul.albums li.selected').attr('rel')
		});
		
		if( response.result ) {
			$('#moveImagesDialog').dialog('close');
		}
		
	};
	
	buttons[texts.cancel] = function() {
		$(this).dialog('close');
	};
	
	$('#moveImagesDialog').dialog({
		'width'			:		(40*14)+10, //span-14
		'modal'			:		true,
		'dialogClass'	:		'gray',
		'resizable'		:		false,
		'autoOpen'		:		false,
		'buttons'		:		buttons,
		'close'			:		function() {
			$(this).dialog('destroy');
		}
	});
	
	$('#moveImagesDialog').dialog('open');

}

function photofunia( image, element ) {
	// show effects
	$('#photofuniaDialog').show();	
	$('#photofuniaLoader').show();
	$('#photofuniaDialog img.original').attr('src', image.thumbnail );
	

	$('#photofuniaDialog').fancybox({
		'height'		: 500,
		'width'			: 800,
		'transitionIn'	: 'elastic',
		'transitionOut'	: 'elastic',
		'speedIn'		: 600, 
		'speedOut'		: 200, 
		'autoScale'		: false,
		'autoDimensions': false,
		'orig'			: element,
		'onClosed'		: function () {
			$('#photofuniaDialog ul').empty();	
		}
	}).trigger('click');
	
	 var response = $.ef( 'services.photofunia.effectlist' );

	 if(response.result) {
	 
		$('#photofuniaLoader').hide();
		$('#photofuniaChooseEffect').show();
		
		$(response.effects).each( function(i, item) {
			var thumb = item.thumburl.replace( /test\./, '' );
			$('#photofuniaDialog ul.effects').append( 
				$('<li/>').append(
					$('<img />', {
						'src'	: thumb,
						'title'	: item.title
					}).bind('click', function() {
						$('#photofuniaChooseEffect').hide();
						$('#photofuniaLoader').show();
						makePhotofunia( item.id, image.id, currentAlbumId, '#photofuniaDialog' );
					})
				)
			);
		});
	 }
}

function effect( image, element ) {
	// show effects
	$('#effectDialog').show();	
	$('#effectLoader').show();
	$('#effectDialog img.original').attr('src', image.thumbnail );

	$('#effectDialog').fancybox({
		'height'		: 500,
		'width'			: 800,
		'transitionIn'	: 'elastic',
		'transitionOut'	: 'elastic',
		'speedIn'		: 600, 
		'speedOut'		: 200, 
		'autoScale'		: false,
		'autoDimensions': false,
		'orig'			: element,
		'onClosed'		: function () {
			$('#effectDialog ul').empty();	
		}
	}).trigger('click');
	
	 var response = $.ef( 'image.effect.list' );

	 if(response.result) {
	 
		$('#effectLoader').hide();
		$('#effectChoose').show();
		
		$(response.effects).each( function(i, item) {
			$('#effectDialog ul.effects').append( 
				$('<li/>').append(
					$('<img />', {
						'src'	: 'http://www.testsolu.com/images/test_logo.gif',
						'title'	: item.title,
						'alt' : item.title
					}).bind('click', function() {
						$('#effectChoose').hide();
						$('#effectLoader').show();
						makeEffect( item.id, image.id, currentAlbumId, '#effectDialog' );
					})
				)
			);
		});
	 }
}


