
function getSelectedImages() {

	var images = $('#album-images li.selected');
	
	if( images.length == 0 ) {
		alert('Please select some images.');
		return false;
	}

	return $('#album-images li.selected');
}		
	
function initLoadAlbums() {
	$('#album-accordion .loader').fadeOut().remove();
	$('#album-accordion .accordion-list').empty();
}


function setSortingMethod( sortingMethod ) {

		// Saving
		var response = $.ef( 'user.set.preference', {
			'key': 'albumsortingmethod',
			'value': sortingMethod 
		});
		
		if(response.result) {
			albumsortingmethod = sortingMethod;
		} else {
			alert('Could not save sorting method');
		}

		

}

function updateCollection( albums, images, albumimages, collection ) {

	var countResponse = false;

	if( !albums || !images || !albumimages ) {
		countResponse = $.ef( 'collection.count', {
			'collection': collection 
		} );
		albums = countResponse.albums;
		albumimages = countResponse.albumimages;
		images = countResponse.images;
		
	}

	if( ( albumimages > 0 || albums > 0 || images > 0 ) ) {
		$('#imageCollection').removeClass('hide', 'fast');
	}
	
	
	$('#imageCollection .albums').text( albums );
	
	if (albumimages) {
		$('#imageCollection .images').text( albumimages );
	} else {
		$('#imageCollection .images').text( '0' );
	} 
	
	$('#imageCollection .total-images').text( images );

}

	
function addToCollection( albums, images, collection ) {

	//console.log( 'Add to collection', albums, typeof(albums), images, collection );

	$('#imageCollection').append( inlineLoader );
	
	var status = true;
	var albumIds = [];
	var imageIds = [];

	if( typeof(albums) == 'object' ) {
		$(albums).each( function(i, album) {
			albumIds.push( $(album).data('id') );
			$(album).removeClass('selected');
		});
	}

	if( albumIds.length > 0 ) {
		var albumResponse = $.ef( 'collection.add.album', {
			'albumid': albumIds.toString()
		});
		if( albumResponse.result ) {
			updateCollection( albumResponse.count, false, false, collection );
		}

	}	
	
	if ( typeof(images) == 'object' ) {
		$(images).each( function(i, image) {
			imageIds.push( image.id );
			$(image).removeClass('selected');
		});
	}
	
	if( imageIds.length > 0 ) {
		var imageResponse = $.ef( 'collection.add.image', {
			'albumid': imageIds.toString()
		});
		if( imageResponse.result ) {
			updateCollection( false, imageResponse.count, false, collection );
		}
	}
	
	if(status) {
		$('#imageCollection .loader').remove();
	}
	
	if( $('#imageCollection').is(':hidden') ) {
		$('#imageCollection').slideDown('fast');
	}
	
	return status;

}

	
function getSelectedAlbums() {
	return $('#main-content li.albumPreview.selected');
}

function getSelectedAlbum() {
	return $('#main-content li.albumPreview.selected').first();
}

function getCurrentAlbum() {
	return currentAlbumElement;
}

function getAllAlbums() {
	return $('#main-content li.albumPreview');
}

function newAlbum( title, description, forsale, fordownload ) {

	var response = $.ef( 'album.create', {
		'title'			:	title,
		'description'	:	description,
		'forsale'		:	forsale,
		'fordownload'	:	fordownload
	});
	
	return response;
   
}

function guessAlbumYear( albumid ) {
	var result = false;

	var response = $.ef( 'album.images.enum', {
		'albumid': albumid
	});

	var totalYears = 0;
	var imagesWithDate = 0;
	
	if(response.result) {
		$(response.images).each( function (i, image) {
			if( image.exif.date ) {
				totalYears += parseInt( image.exif.date.substring(0,4), 10 );
				imagesWithDate++;
			}
		});
		result = parseInt( totalYears/imagesWithDate, 10 );
	}
	
	return result;
}


function createSharedSlideshowEmbed(albumid, urlname, publickey, width, height, movie) {

		var paramXMLPath = 'http://' + hostname + '/shared/album/slideshowparameters/' + albumid + '/' + urlname + '/' + publickey;
		
		var embed = '<object width="' + width + '" height="' + height + '"><param name="movie" value="'+movie+'"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><param name="flashvars" value="paramXMLPath='+paramXMLPath+'"></param><embed src="'+movie+'" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" flashvars="paramXMLPath='+paramXMLPath+'" width="'+width+'" height="'+height+'"></embed></object>';

		return embed;
}


function showLoaderOverElement( element ) {

	var loader;

	$(element).css('position', 'relative').append( 
		loader = $('<div/>', {
			'css'	:	{
				'position'	:	'absolute',
				'top'		:	0,
				'left'		:	0,
				'height'	:	'100%',
				'width'		:	'100%',
				'background-color'	:	'#000',
				'opacity'	:	.6
			}
		}).append(
			$('<div/>', {
				'css'	:	 {
					'margin-top'	:	parseInt( ( $(element).height() / 2 ) - 32 ),
					'margin-left'	:	parseInt( ( $(element).width()  / 2 ) - 16 )
				}
			}).append(
				$('<div/>', {
					'css'	:	 {
						'height'		:	'32px',
						'width'			:	'32px'
					},
					'html'	:	bigLoaderWhiteOnBlack
				})
			)
		)
	)
	
	return loader;
}


function setAlbumTitle( albumid, title, album, element, menuElement) {

	var response = $.ef( 'album.set.title', {
		'albumid': albumid,
		'title': title
	});
	
	// updates dom
	
	if( response.result ) {
		if(album) {
			album.title = title;
		}
		$('#album-accordion-list li').each( function(i, item) {
			if( $(item).data('id') == albumid ) {
				$(item).data('title', title);
				$('a', item).text( shorten( title, albumListChars ) );
			}
		});
		
		if( albumid == currentAlbumId ) {
			$('#albumContainer .album-title').text( title );
		}
		
	}
	
	return response.result;
}

function downloadImage( image ) {
	window.open(image.fullsize);
}

function makePhotofunia( effectid, imageid, albumid ) {
	
	var response = $.ef( 'services.photofunia.create', {
		'imageid': imageid,
		'effectid': effectid,
		'albumid': albumid
	});

	if( response.result ) {
	
		var newImage = new Image();
		newImage.src = response.newimage.screensize;
		
		$(newImage).load( function() {
			$('#photofuniaLoader').hide();
			$('img.newimage', '#photofuniaDialog').attr('src', response.newimage.screensize ); 
			$('img.newimage', '#photofuniaDialog').show();
			$('#photofuniaNewImage').show();
			
			$('#photofuniaKeepImage').click( function() {
				$.fancybox.close();
				return false;
			});
			
			$('#photofuniaRemoveImage').click( function() {
				alert('delete image: ' + response.newimage.id );
				if( removeImage( response.newimage.id ) ) {
					$.fancybox.close();
				}
			});
		});
	}
}


function makeEffect( effectid, imageid, albumid ) {
	
	var response = $.ef( 'image.effect.create', {
		'imageid': imageid,
		'effectid': effectid,
		'albumid': albumid
	});

	if( response.result ) {
	
		var newImage = new Image();
		newImage.src = response.newimage.screensize;
		
		$(newImage).load( function() {
			$('#effectLoader').hide();
			$('img.newimage', '#effectDialog').attr('src', response.newimage.screensize ); 
			$('img.newimage', '#effectDialog').show();
			$('#effectNewImage').show();
			
			$('#effectKeepImage').click( function() {
				$.fancybox.close();
				return false;
			});
			
			$('#effectRemoveImage').click( function() {
				alert('delete image: ' + response.newimage.id );
				if( removeImage( response.newimage.id ) ) {
					$.fancybox.close();
				}
			});
		});
	}
}

function deleteAlbum( albumid, item ) {
	var result = false;
	
	var response = $.ef( 'album.delete', {
		'albumid': albumid // Optional
	});
	
	if( response.result ) {
		var albumYearCollection = $(item).parent().parent().parent();
		$('h2 span.quantity', albumYearCollection).text( parseInt( $('h2 span.quantity', albumYearCollection), 10 ) - 1 );
		$(item).remove();
		result = true;
	}
	
	return result;
}

function deleteAlbums( albums, item ) {

	// one delete operation here ?

	$(albums).each( function(i, item) {
		if( $(item).data('id') !== 0 ) {
			deleteAlbum( $(item).data('id'), item );
		}
	});

	return true;

}

function mergeAlbums( albums, albumid ) {

	var albumList = [];
	
	$(albums).each( function(i, item) {
	   
		if( $(item).data('id') !== 0 ) {
			albumList.push( $(item).data('id') );
		} else {
			alert('you can not merge the inbox');
			return false;
		}
		
	});
	
	var response = $.ef( 'albums.merge', {
		'albumid': albumid, // Optional
		'albums': albumList.toString() // Optional
	});
	
	
	return response.result;

}

function rotateImage( id, element, degrees) {

	var loader = showLoaderOverElement( element );
	var random = Math.round(Math.random(100)*1000);
	var response = $.ef( 'image.rotate', {
		'imageid': id, // Optional
		'degrees': degrees // Optional
	});
	
	if( response.result ) {
		var thumb = new Image();
		thumb.src = response.image.thumbnail;

		if( response.image.x <= response.image.y ) {
			$(element).removeClass('landscape').addClass('portrait')
		} else {
			$(element).removeClass('portrait').addClass('landscape')
		}
		
		$(thumb).load( function() {
			$('img', element).attr('src', thumb.src );
			//$(element).parent().removeClass('selected');
			$(loader).remove();
		});
	}
}

function transferImages() {
	document.location.href = '/account/upload';
}
