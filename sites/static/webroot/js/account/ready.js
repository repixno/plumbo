var images = [];
var firstImageId = 0;
var lastImageId = 0;

var albums;

var loadedOneTime = true;

var currentImage = false;
var currentImageId = false;

var currentAlbum = false;
var currentAlbumInfo;
var currentAlbumId = false;
var currentAlbumElement = false;

var batchid = 0;

var myAlbums = {};

var albumListChars = 15;
var maxQuantityImages = 1000;

var photoBookHardCoverMinImages = 30;
var photoBookHardCoverMaxImages = 103;

var photoBookSoftCoverMinImages = 43;
var photoBookSoftCoverMaxImages = 97;

var loadScreensizeThumbs = true;
var thumbSizeToggle = 220;
var loadedScreensize = false;

var productListOnImage = false;

var activeAccordionOnImage = false;

var albumapi = 'albums.enum';


$(document).ready( function() {

	//init
	updateCollection( false, false, false, false );
	
	
	// Get the products that can be bought when viewing an image
	productListOnImage = $.ef( 'products.enum', { 'group' : 'buyonimage' } );

	
	// Selects all albums in a year when clicking on the title-year
	$('h2.year').click( function() {
		$(this).next().find('li.albumPreview').toggleClass('selected');
	});
	
	
	//Opens menu
	$('#album-menu a.open-menu, #album-accordion a.open-menu').click( function() {
		var href = $(this).attr('href');
		if( !$(href).is(':visible') ) {
			$(href).slideDown('fast');
		} else {
			$(href).slideUp('fast');
		}
		$(href).mouseleave( function() {
			$(href).slideUp('fast');
		}).hover(
			function() { 
				$.data(this, 'hover', true); 
			},
			function() { 
				$.data(this, 'hover', false); 
			}
		);
		return false;
	});
	
	// Hides menu when clicking an item
	$('#account .functions a').bind('click', function() {
		$('#album-menu .functions').slideUp('fast');
	});
	
	// Accordion sorting
	$('#sort-albumlist a').bind('click', function() {

		$('#sort-albumlist').slideUp('fast');

		$('#album-accordion-list').empty();
		
		

		if( $(this).attr('href') == '#sort-album-by-title-ascending')  {
			albumsortingmethod = 'title/asc';
			loadMyAlbums(false, 'title', 'ascending', false);
			setSortingMethod( 'title/asc' );
		} else if ( $(this).attr('href') == '#sort-album-by-title-descending')  {
			albumsortingmethod = 'title/desc';
			loadMyAlbums(false, 'title', 'descending', false);
			setSortingMethod( 'title/desc' );
		} else if ( $(this).attr('href') == '#sort-album-by-date-ascending' ) {
			albumsortingmethod = 'date/asc'
			loadMyAlbums(false, 'date', 'ascending', false);
			setSortingMethod( 'date/asc' );
		} else if ( $(this).attr('href') == '#sort-album-by-date-descending' ) {
			albumsortingmethod = 'date/desc';
			loadMyAlbums(false, 'date', 'descending', false);
			setSortingMethod( 'date/desc' );
		} else if ( $(this).attr('href') == '#sort-album-by-year-descending' ) {
			albumsortingmethod = 'year';
			loadMyAlbums(false, 'year', 'descending', false);
			setSortingMethod( 'year' );
		} else {
			alert('DOH!');
		}
		
	});

	// Open slideshow	
	$('#album-menu a.slideshow.fancybox').click( function() {
		var album = $(currentAlbumElement).data('info');
		$.fancybox( '<div id="fancySlideshow"></div>', {
			'width'			:	/*$(window).width() - 250,*/ 790,
			'height'		:	/*$(window).height() - 150,*/ 553,
			'type'			:	'swf',
			'autoDimensions':	false,
			'autoScale'		:	false,
			'overlayOpacity':	0.9,
			'titleShow'		:	false,
			'href'			:	'http://static.repix.no/flash/slideshowpro.swf',
			'swf'			:	{
					'flashvars'			:	'paramXMLPath=/myaccount/album/slideshowparameters/' + currentAlbumId + '&initialURL=' + escape(document.location) + '&xmlFilePath=/myaccount/album/rss/' + currentAlbumId + '&xmlFileType=Media RSS',
					'bgcolor'			:	"#000000",
					'allowfullscreen'	:	"true",
					'allowScriptAccess'	:	"always"
			},
			'onClosed'		:	function() {
				$('#fancySlideshow').remove();
			}
		})
	
	});
	
	
	
	

	
	
	$('#downloadCurrentAlbum').click( function() {
		downloadAlbumDialog( currentAlbumId ); 
	});
	
	$('#albumPreferences').click( function() { 
		albumPreferences( currentAlbumId, this, false );
		return false;
	});
	
	$('#mergeSelectedAlbums').click( function() { 
		mergeSelectedAlbumsDialog( getSelectedAlbums() );
		return false;
	});

	$('#printAllAlbums').click( function() {
		printSelectedAlbumsDialog( getAllAlbums() );
		return false;
	});
	
	$('#printSelectedAlbums').click( function() {
		printSelectedAlbumsDialog( getSelectedAlbums() );
		return false;
	});
	
	
	$('#printSelectedImages').click( function() {
		printImagesDialog( getSelectedImages() );
		return false;
	});
	
	$('#printCurrentAlbum').click( function() {
		printAlbum( getCurrentAlbum() );
		return false;
	});
	
	
	$('#deleteSelectedAlbums').click( function() {
		deleteSelectedAlbumsDialog( getSelectedAlbums() );
		return false;
	});
	
	$('#deleteCurrentAlbum').click( function() {
		deleteAlbumDialog( currentAlbumId );
	});
	
	$('#album-menu .deleteSelectedImages').click( function() {
		deleteSelectedImagesDialog( getSelectedImages() );
		return false;
	});

	
	$('#createNewAlbum').click( function() {
		createNewAlbumDialog();
		return false;
	});
	
	$('#createPhotoBook').click( function() {
		createMediaclipProduct( 'photobook', getSelectedAlbums() );
		return false;
	});
	
	$('#createPhotoBookFromAlbum').click( function() {
		createMediaclipProduct( 'photobook', currentAlbumElement );
		return false;
	});

	
	$('#createCalendar').click( function() {
		createMediaclipProduct( 'calendar', getSelectedAlbums() );
		return false;
	});	

	$('#createGift').click( function() {
		createMediaclipProduct( 'gift', getSelectedAlbums() );
		return false;
	});

	$('#createPoster').click( function() {
		createMediaclipProduct( 'poster', getSelectedAlbums() );
		return false;
	});	
	
	$('#createGreetingCard').click( function() {
		createMediaclipProduct( 'greetingcard', getSelectedAlbums() );
		return false;
	});
	
	$('#shareSelectedAlbum').click( function( element ) {
		shareAlbumDialog( getSelectedAlbum().attr('rel') , element );
		return false;
	});
	
	$('#shareCurrentAlbum, #shareCurrentAlbumFromImage, #album-menu .buttons .share-album').click( function( element ) {
		shareAlbumDialog( currentAlbumId , currentAlbumElement );
		return false;
	});
	
	$('#getSecretLinkForSelectedAlbum').click( function( element ) {
		getSecretLinkDialog( getSelectedAlbum().attr('rel') );
		return false;
	});
	
	
	$('#moveCurrentImage').click( function() {
		moveImageDialog( currentImage );
	});
	
	
	$('#album-menu .buttons .order-prints-from-current-album').click( function() {
		orderPrints( $(currentAlbumElement).data('info'), getSelectedImages() );
	})
	
	$('#insertSelectedAlbumInBlog').click( function( element ) {
		getSharingHTMLDialog( getSelectedAlbum().attr('rel') );
		return false;
	});
	
	$('#uploadFromComputer').click( function( element ) {
		transferImages( );
		return false;
	});
	
	$('#transferWithEmail').click( function( element ) {
		transferImagesByEmailDialog( getSelectedAlbum().attr('rel') );
		return false;
	});
	
	$('#importFromOtherSite').click( function( element ) {
		//importImagesDialog( );
		window.location.href = '/account/images/import/';
		return false;
	});
	
	$('#album-menu .rotate-selected-images-clockwise').click( function() {
		getSelectedImages().each( function(i, item) {
			rotateImage( $(item).data('image').id, $('a', item), 90 );
		});
		return false;
	});
	
	$('#album-menu .rotate-selected-images-counterclockwise').click( function() {
		getSelectedImages().each( function(i, item) {
			rotateImage( $(item).data('image').id, $('a', item), -90 );
		});
		return false;
	});
	
	
	$('#album-menu .move-selected-images').click( function() {
		moveImagesDialog( getSelectedImages() );
		return false;
	});
	
	
	$('#deleteCurrentImage').click( function() {
		deleteImageDialog( currentImage );
		return false;
	});
	
	
	// Inline edit of album-title
	$('#albumContainer .album-title').click( function() {
		var title = this;
		$(this).hide();
		$(this).after( 
			$('<input/>', {
				'value'	:	$(title).text(),
				'class'	:	'text',
				'type'	:	'text',
				'focus'	:	function() {
					$(this).select();
				}
			}).after( 
				$('<button/>', {
					'css'	:	{ 'margin-left'	:	'10px' },
					'class'	:	'green small',
					'text'	:	'Save',
					'click'	:	function() {
						if( $(this).prev().val() != $(title).text() ) {
							
							$(this).prev().hide().after( inlineLoader );
							
							var response = setAlbumTitle(currentAlbumId, $(title).next().val(), currentAlbum );
							
							if ( response ) {
								$(this).prev().remove();
								$(this).prev().remove();
								$(this).next().remove();
								$(this).remove();
								$(title).show();
							} else {
								$(this).prev().remove();
								$(this).prev().show();
								$(this).next().show().next().show();
							}
						} else {
							$(this).prev().remove();
							$(this).next().remove();
							$(this).remove();
							$(title).show();
						}
					}
				})
			).after( 
				$('<button/>', {
					'css'	:	{ 'margin-left'	:	'10px' },
					'class'	:	'red small',
					'text'	:	'Cancel',
					'click'	:	function() {
						$(this).prev().prev().remove();
						$(this).prev().remove();
						$(this).remove();
						$(title).show();
					}
				})
			) 
		);
		$(this).next().focus();
		
	});
	
	
		
	// Change view
	$('#album-list-appearance').change( function() {
		var e = this;
		$(e).hide().after( inlineLoader);
		
		var response = $.ef( 'user.set.preference', {
			'key': $(e).attr('name'),
			'value': $(e).val() 
		});
		
		if(response.result) {
			document.location.reload( true );
		} else {
			alert('error');
		}
	});

	/* image collection */
	$('#imageCollection .addSelected').click( function() {
		var albums = getSelectedAlbums();
		var images = $('#album-images li.selected'); // getSelectedImages
		if( addToCollection( albums, images ) ) {
			// ok
		} else {
			alert('error');
		}
		
	});
	$('#imageCollection .empty').click( function() {
		var response = $.ef( 'collection.clear');
		if( response.result ) {
			$('#imageCollection .albums').text('0');
			$('#imageCollection .images').text('0');
		}
	});
	
	$('#imageCollection .show').click( function() {
		var response = $.ef( 'collection.enum');
		if( response.result ) {
			
			$('#imageCollection .albums').text( '?' );
			$('#imageCollection .images').text( $(response.collection).length );

		}
	});

	// Check for selected images when moving away
	$('#account-menu a').bind('click', function() {
	
		var source = this;
		var collection = false;
		var goOn = true;
		var albums = $('#main-content li.albumPreview.selected');
		var images = $('#album-images li.selected');
	
		if( albums.length > 0 || images.length > 0) {
			goOn = addToCollectionDialog(albums, images, collection, source );
		}
		
		return goOn;
		
	});
	
	
	// Select All / None
	$('#select-all-albums').click( function() {
		$('#show-albums #main-content ul.albums li').addClass('selected');
		return false;
	});
	
	$('#select-none-albums').click( function() {
		$('#show-albums #main-content ul.albums li').removeClass('selected');
		return false;
	});
	
	$('#album-menu .select-all').click( function() {
		$('#album-images li').addClass('selected');
		return false;
	});
	
	$('#album-menu .select-none').click( function() {
		$('#album-images li').removeClass('selected');
		return false;
	});

	
	$('#toggle-maxsize').click( function () {
		$('#account .account-wrapper').removeClass('container');
		return false;
	});

	$('.account-accordion h3').bind('click', function() {
		var container = $(this).parent();
		$('.account-content-box', container).slideUp('fast');
		$(container).parent().parent().find('h3').removeClass('selected');
		$(this).addClass('selected');
		$(this).next().slideDown('fast');

	});

	$('#album-accordion h3').bind('click', function() {
		if( $(this).hasClass('my-albums') ) {
			loadMyAlbums('#album-accordion-list');
		} else if( $(this).hasClass('friends-albums') ) {
			loadFriendsAlbums('#friends-albums-accordion-list');
		} else if ( $(this).hasClass('shared-albums') ) {
			loadSharedAlbums('#shared-albums-accordion-list');
		}
		
	});
	
   $('#uploadFromComputere-fromAlbum').bind('click', function() {
      
	  transferImagesDialog( currentAlbumId, $('#batchid').val(), $('#sessionid').val());

	});

   $('#transferWithEmail-fromAlbum').bind('click', function() {

     transferImagesByEmailDialog( currentAlbumId );

   });
   
   
   $('#show-image-title').change( function() {
   		if( $(this).is(':checked') ) {
   			$('ul#album-images li a span.title').show();
   		} else {
   			$('ul#album-images li a span.title').hide();
   		}
   		
   })
   
	$('#image-sorting').change( function() {
	
		var sorting = $('#image-sorting').val().split( '_' );
		var sortby = sorting[0];
		var sorttype = '';

		if ( sorting[1] == 'desc') sorttype = 1;
		if ( sorting[1] == 'asc') sorttype = 0;
		
		loadAlbum( $('#album_' + currentAlbumId), $('#album-accordion-list'), '#albumContainer', currentAlbumId, sortby, sorttype, 'id,x,y,thumbnail,screensize,title,description,exif,products' );
		
	});
	
	
   $('#choose-albumlist a').bind('click', function() {
   
      $('#choose-albumlist').slideUp('fast');
      
   	$('#album-accordion-list').empty();
   
   	switch ( $(this).attr('href') ) {
   	   case '#my-albums':
   	     albumapi = 'albums.enum';
   	     loadMyAlbums();
   	    
   	     break;
   	   case '#friends-albums':
           albumapi = 'albums.sharedto';
           loadMyAlbums();
   	   	     
   	     break;
   	      
   	   case '#my-shared-albums':
   	     albumapi = 'albums.sharedby';
   	     loadMyAlbums();
   	  
   	     break;
   	}

   });
	
});
