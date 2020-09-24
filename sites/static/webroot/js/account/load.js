function loadAlbum(element, source, target, album, sortby, sorttype, returnfields) {
   
	var loadedimages = 1;
   
	if( album.numimages == 0 ) {
		transferImagesDialog( $(element).data('id'), $('#batchid').val(), $('#sessionid').val());
	}

	currentAlbum = album;
	currentAlbumElement = element;
	currentAlbumId = $(element).data('id');
	
	loadedScreensize = false;

	$('#album-content').empty();

	$('#album-menu, #albumContainer').show();
	
	$(target).scrollTop( '0' );
	
	if( loadedOneTime ) {
		$(source).scrollTop( $(element).offset().top );
		loadedOneTime = false;
	}

	$('#album-title', target).text( $(element).data('title') );
	
	//$('.account-content-box', target ).empty();
	
	$('#album-content', target ).append(
		$('<div />', {
			'id':	'ie-overlay'
		})
	).append( 
		$('<div/>', {
			'class'	:	'center',
			'id'	:	'albumLoadingContainer'
		}).append(
			$('<div/>', {
				'class'	:	'center',
				'id'	:	'progressbarContainer'
			}).append(
				$('<div/>', {
					'id'	:	'progressbar',
					'class'	:	'animated'
				})
			).append(
				$('<div class="loader">Loading <span class="q">1</span> of <span class="tq">'+ $('small', element).text() +'</span></div>') 
			)
		)
	);
	
	$('#album-content #progressbar', target ).progressbar({ 
		'value'	:	5 
	});
	
	var menuFunctionality = function(action, element, position) {
	   
      if( action == 'photofunia') {
      	photofunia( image, element );
      } else if( action == 'delete') {
      	deleteImageDialog( image, element );
      } else if (action == 'setDefaultImage') {
      	setDefaultImage( image.id, currentAlbumId );
      } else if ( action == 'downloadImage') {
      	downloadImage( image );
      } else if ( action == 'select') {
      	$(element).parent().toggleClass('selected');
      } else if ( action == 'open' ) {
      	showImage( element, source, image );
      } else if ( action == 'addToCollection' ) {
      	addToCollection( false, image );
      } else if ( action == 'move' ) {
      	moveImageDialog( image );
      } else if ( action == 'postToBlog' ) {
      	addImageToBlog( image );
      } else if( action == 'rotate-image-clockwise') {
      	rotateImage( image.id, element,  90 );
      } else if( action == 'rotate-image-counterclockwise') {
      	rotateImage( image.id, element,  -90 );
      }
      	
   };
   
	var createImageInfo = function ( fancy, image ) {
	
		var gps;
	
		var header = $('<div />').append( 
			$('<h2>', {
				'text'	:	image.title
			})
		).append(
			$('<p/>', {
				'text'	:	image.description
			})
		);
			
		var accordion = 
			$('<div/>').append(
				$('<h3/>', { 'text'	:	'Info', 'class'	:	'info' })
			).append(
				// EXIF
				$('<div/>', {
					'id'	:	'exif-info'
				}).append(
					$('<table/>')
					.append( $('<tr/>').append( $('<th/>', { 'text'	:	'Date' 			} ) ).append( $('<td/>', { 'text'	:	image.exif.date			} ) ) )
					.append( $('<tr/>').append( $('<th/>', { 'text'	:	'exposuretime' 	} ) ).append( $('<td/>', { 'text'	:	image.exif.exposuretime	} ) ) )
					.append( $('<tr/>').append( $('<th/>', { 'text'	:	'height' 		} ) ).append( $('<td/>', { 'text'	:	image.exif.height		} ) ) )
					.append( $('<tr/>').append( $('<th/>', { 'text'	:	'width' 		} ) ).append( $('<td/>', { 'text'	:	image.exif.width		} ) ) )
					.append( $('<tr/>').append( $('<th/>', { 'text'	:	'make' 			} ) ).append( $('<td/>', { 'text'	:	image.exif.make			} ) ) )
					.append( $('<tr/>').append( $('<th/>', { 'text'	:	'model' 		} ) ).append( $('<td/>', { 'text'	:	image.exif.model		} ) ) )
					.append( $('<tr/>').append( $('<th/>', { 'text'	:	'orientation' 	} ) ).append( $('<td/>', { 'text'	:	image.exif.orientation	} ) ) )
				)
			).append(
				// Functions
				$('<h3/>', { 'text'	:	'Functions', 'class'	:	'functions' })
			).append(
				$('<ul/>')
					.append( 
						$('<li>').append( 
							$('<a/>', { 'class'	:	'',	'text'	: 'Edit image' , 'click' : function() {
								alert('Coming soon');
							}})
						) 
					)
					.append( 
						$('<li>').append( 
							$('<a/>', { 'class'	: '', 'text' : 'Download image', 'click' : function() {
								downloadImage( image );
							}}) 
						) 
					)
					.append( 
						$('<li>').append( 
							$('<a/>', { 'class'	:	'', 		'text'		:	'Post to blog', 'click' : function() {
								postToBlog( image );
							}}) 
						) 
					)
					.append( 
						$('<li>').append( 
							$('<a/>', { 'class'	: 'negative', 'text' : 'Delete image', 'click' : function() {
								deleteImage( image );
							}})
						)
					)
					.append( 
						$('<li>').append( 
							$('<a/>', { 'class'	: '', 'text' : 'Rotate clockwise', 'click' : function() {
								rotateImage( image, '90' );
							}})
						).append( 
							$('<a/>', { 'class'	: '', 'text' : 'Rotate counterclockwise', 'click' : function() {
								rotateImage( image, '-90' );
							}})
						)
					)

					.append( 
						$('<li>').append( 
							$('<a/>', { 'clas'	:	'', 		'text'		:	'Buy image', 'click' : function() {
								printImage( image );
							}})
						 ) 
					)
			).append(
				// Location
				$('<h3/>', { 'text'	:	'Location', 'class'	:	'location' })
			).append(
				gps = $('<div/>')
			);
			
			var products = $('<select />', {
				'id'	:	'productListOnImage'
			});
			
			$( productListOnImage.products ).each( function(i, item) {
				$(item).each( function(i, product) {
					$(products).append( 
						$('<option/>', {
							'text'	:	product.title,
							'value'	:	product.id
						})
					)
				});
			});
			
			var buyButton = $('<a/>', {
				'text'	:	'Buy',
				'class'	:	'button blue'
			})
			
			
			var smallMap = {};
			smallMap.width = 300;
			smallMap.height = 160;
			
			if( image.exif.gps.latitude && image.exif.gps.longitude ) {
				$(gps).append(
					$('<a/>', {
						'target':	'_blank',
						'href'	:	'http://maps.google.com/maps?f=q&geocode=&q=' + image.exif.gps.latitude + ',' + image.exif.gps.longitude
					}).append(
						$('<img/>', {
							'width'	:	smallMap.width,
							'height':	smallMap.height,
							'src'	:	'http://maps.google.com/maps/api/staticmap?size='+smallMap.width+'x'+smallMap.height+'&zoom=10&maptype=roadmap&markers=color:red|' +  image.exif.gps.latitude + ',' + image.exif.gps.longitude + '&sensor=false'	
						})		
					)
				);
			} else {
				$(gps).append( 
					$('<h5>', {
						'text'	:	'No location on image'
					})
				).append( 
					$('<a/>', {
						'text'	:	'Choose location',
						'click'	:	function() {
							alert('Google maps popup');
						}
					})
				)
			}
			
			$(accordion).accordion({
				'autoHeight':	false,
				'clearStyle':	true,
				'fillSpace'	:	true,
				'active'	:	'.functions',
				'change'	:	function(event, ui) {
					activeAccordionOnImage = $(ui.newHeader).attr('class');
					
				}
				
			});
			
			var info = $('<div/>', {
				'class'	:	'infoOnImage'
			}).append( header ).append( accordion ).append( products ).append( buyButton );

			return info;
		
	}
	
	var enumAlbums = function ( albumid, offset, limit, numimages, loadedimages, batch, sortby, sorttype, returnfields ) {
	   
	     var imagebatchcounter = {};

         $.ef('album.images.enum', 
         	{
         		'albumid'	: albumid,
         		'sortby'	: sortby,
         		'sorttype'	: sorttype,
         		'returnfields' : returnfields,
         		'offset' : offset,
         		'limit' : limit
         		
         	}, function(album) {
         
         		currentAlbum = album;
         		
         		images = album.images;
         		
         		var i = 0;
         		
         		$( images ).each( function( i, image ) {
         	   
         			var orientation;
         			firstImageId = images[ 0 ].id;
         			lastImageId = images[ images.length - 1 ].id;
         			
         			if( image.x > image.y ) {
         				orientation = 'landscape';
         			} else {
         				orientation = 'portrait';
         			}
         			
         			// Loads screensize if slider is set to high
         			var thumbnail = image.thumbnail;
         			
         			var slidervalue = $('#slider').slider('value');
         			
         			if( slidervalue ) {
         				if( slidervalue > thumbSizeToggle) {
         					thumbnail = image.screensize;
         				}
         			}

         			albumimages.append(
         			
         				$('<li/>', {
         					'css'	: {
         						'width': slidervalue,
         						'height': slidervalue * 0.7
         					}
         				}).append(
         					$('<a/>', {
         						'href'			: image.screensize,
         						//'href'			: '#show' + image.id,
         						'rel'			: 'test',
         						'class'			: 'fancybox ' + orientation,
         						'title'			: image.title
         					})
         					.data('image', image )
         					.append('<img src="'+ thumbnail+'" />').contextMenu({
                           menu: 'imageContextMenu'} , function( action, element, position ) {
                           if( action == 'photofunia') {
                              photofunia( image, element );
                           } else if( action == 'delete') {
                              deleteImageDialog( image, element );
                           } else if (action == 'setDefaultImage') {
                              setDefaultImage( image.id, currentAlbumId );
                           } else if ( action == 'downloadImage') {
                              downloadImage( image );
                           } else if ( action == 'select') {
                              $(element).parent().toggleClass('selected');
                           } else if ( action == 'open' ) {
                              showImage( element, source, image );
                           } else if ( action == 'addToCollection' ) {
                              addToCollection( false, image );
                           } else if ( action == 'move' ) {
                              moveImageDialog( image );
                           } else if ( action == 'postToBlog' ) {
                              addImageToBlog( image );
                           } else if( action == 'rotate-image-clockwise') {
                              rotateImage( image.id, element,  90 )
                           } else if( action == 'rotate-image-counterclockwise') {
                              rotateImage( image.id, element,  -90 )
                           } else if ( action == 'effect') {
                              effect( image, element );
                           }

                        } )
         					.append( 
         						$('<span/>', {
         							'text'	:	image.title,
         							'class'	:	'title',
         							'css'	:	{ 'display' : 'none' }
         						})
         					)
         
         				).data('id', image.id).data('image', image)
         
         			);

         		});
         		
         		$('#album-content span.tq',target).text( numimages );
         		             		
               $('img', albumimages).load( function( ) {
            
                  loadedimages++;
                  
                  $('#album-content span.q',target).text( loadedimages );
            
                  $('#album-content #progressbar',target).progressbar('value', parseInt( (loadedimages / numimages) * 100 ) );
                  
                  if ( isNaN( imagebatchcounter[batch] ) || ( !imagebatchcounter[batch] ) ) {
                     
                     imagebatchcounter[ batch ] = 1;
                     
                  } else {
         
                     imagebatchcounter[ batch ] = imagebatchcounter[batch] + 1;
                     
                  }
            
                  if ( loadedimages >= numimages ) { 

                     var image;
                     var imageInfo;
                     var imageIsLoaded = false;
                     var fancyObject;
                     
                     $('.loader', target).hide('fast', function() {
                        $(this).remove();
                        $('#progressbar').remove();
                        $('.album-content-menu', target).show();
            
                        $('a.fancybox').fancybox({
                           'type'         :  'imageinfo',
                           'transitionIn' :  'elastic',
                           'transitionOut'   :  'elastic',
                           'changeFade'   :  50,
                           'speedIn'      :  600,
                           'speedOut'     :  200,
                           'titleShow'    :  false,
                           'onStart'      :  function() {
                              fancyObject = this;
                              image = $(this.orig.prevObject).data('image');
                           },
                           'info'         :  function() {
                              return createImageInfo( fancyObject, image );
                           }
                        });
            
                     });
                     
                     $('#slider').show();
            
                     $('#albumLoadingContainer, #ie-overlay').fadeOut('fast', function() {
                        $(this).remove();
                     })
                     
                     // Experimental
                     $('#album-images').selectable({
                        'filter'    :  'li',
                        'selecting'    :  function(event, ui) {
                           $(ui.selecting).addClass('selected');
                        },
                        'unselecting'  :  function(event, ui) {
                           $(ui.selecting).removeClass('selected');
                        }
                     });
                     	
                  	var loaderInserted = false;
                  	
                  	$('#slider').slider({
                  		'range': 'min',
                  		'value': 150,
                  		'max': 600,
                  		'min': 50,
                  		'slide': function(event, ui) {
                  		   
                  			$('#album-images li').css({
                  				'width': ui.value,
                  				'height': ui.value * 0.7
                  			});
                  			
                  			if( loadScreensizeThumbs ) {
                  				if( ui.value > thumbSizeToggle ) {
                  					if( !loadedScreensize ) {
                  					
                  						var loaded = 0;
                  						
                  						if( !loaderInserted ) {
                  							$('#slider').after( inlineLoader );
                  							loaderInserted = true;
                  						}
                  						
                  						$('#album-images li a').each( function(i, item) {
                  						   
                  						   if ( $(item).data('image') ) {
                     						   
                     							var image = new Image();
                     							image.src = $(item).data('image').screensize;
                     							
                     							$(image).load( function() {
                     							   
                     								$('img', item).attr('src', $(item).data('image').screensize );
                     								loaded++;
                     								
                     							});
                     							
                  						   }
                     							
                     						if( loaded == $('#album-images li').length ) {
                     						   
                     							$('.loader.inlineLoader', '#album-menu').remove();
                     							
                     							loadedScreensize = true;
                     							
                     						}
                  						   
                  						});
                  					}
                  				} else {
                  				   
                  					$('#album-images li a').each( function(i, item) {
                  					   
                                    if ( $(item).data('image') ) {
                                       
                                       $('img', item).attr('src', $(item).data('image').thumbnail );
                                       
                                    }
                  					   
                  					});
                  					
                  				}	
                  			}
                  		}
                  	});
                  	
                  	loadedimages = 1;
                     numimages = 0;
            
                  } else if ( ( imagebatchcounter[batch] >= limit ) && ( loadedimages <= numimages ) ) {
                     
                     enumAlbums( albumid, offset + limit, limit, numimages, loadedimages, batch++, sortby, sorttype, returnfields );
                  
                  }
                  
               });

         });

   }

	$(element, source).addClass('selected');
	
	$('#slider').hide();
	
	$('#album-content',target).append( $( '<ul id="album-images"/>' ) );
	
   var albumimages = $( '#album-images' );
	
   var albumimagesparent = albumimages.parent();
   
   albumimages.remove();  

	if( $(element).data('info').numimages < maxQuantityImages ) {
	   
      enumAlbums( $(element).data('id'), 0, 50, $(element).data('info').numimages, 0, 0, sortby, sorttype, returnfields ); 

	} else {
	   
		alert('Too big album!');
		
	}	
	
	albumimages.append('<br class="clear" />');
	
   albumimagesparent.append( albumimages );

}







function loadMyAlbums( target, sortby, sorttype ) {

	if( albumsortingmethod ) {
		if( albumsortingmethod == 'year/desc') {
			sortby = 'year';
			sorttype =  'decending';
		} else if ( albumsortingmethod == 'year/asc' ) {
			sortby = 'year';
			sorttype =  'ascending';
		} else if ( albumsortingmethod == 'title/asc' ) {
			sortby = 'title';
			sorttype =  'ascending';
		} else if ( albumsortingmethod == 'title/desc' ) {
			sortby = 'title';
			sorttype =  'descending';
		} else if ( albumsortingmethod == 'date/asc' ) {
			sortby = 'date';
			sorttype =  'ascending';
		} else if ( albumsortingmethod == 'date/desc' ) {
			sortby = 'date';
			sorttype =  'descending';
		}
	
	} else {
		sortby = sortby || 'title';
		sorttype = sorttype || 'ascending';
	
	}

	
	if( !target ) {
		target = $('#album-accordion-list.accordion-list');
	}

	$(target).empty();
	
	var container = target;
	
	$.ef(albumapi, 
		{	
			'sortby'	: sortby,
			'sorttype'	: sorttype
		}
		, function(result) {

		initLoadAlbums();
		
		var looping = true;
		var element;
		
		myAlbums = result;
		
		albums = result.albums;
		
		var initedLoading = false;
		
		
		
/*		if(sortby == 'year') {
			$('ul', container).remove();
		} else if ( !$('ul', container).is(':visible') ) {
			target = $(container).append('<ul class="accordion-list" id="album-accordion-list" />')
		}*/
		
		$(result.albums).each( function(i, album) {
		
			if(sortby == 'year') {
			
				var year = album.owner.preferences.year;
				
				if( $('#year-' + year).is(':visible') ) {
					target = $('#year-' + year + ' ul');
					
				} else {
					$(container).append(
						$('<div />', {
							'id'	:	'year-' + year
						}).append(
							$('<h3/>', {
								'text'	:	year,
								'class'	:	'prepend-top'
							})
						).append( $('<ul />', {
							'class'	:	'accordion-list'
						}) )
					)
				}
			}
			
			$(target).append( 
				$('<li/>', { 'id': 'album_' + album.id })
				.append( 
					$('<small/>', 
						{ 'text': album.numimages }
					)
				).append( 
					element = $('<a/>', 
						{ 
							'text'	: shorten( album.title, albumListChars ),
							'title'	: album.title,
							'href'	: '#'+ album.id + '/' + album.urlname
						}
					).bind('click',  function() {
						$('.selected', container).removeClass('selected');
						$(this).parent().addClass('selected');
						loadAlbum( $(this).parent(), $(this).parent().parent(), '#albumContainer', album, '', 0, 'id,x,y,thumbnail,screensize,title,description,exif,products,fullsize'  );
					})
				).data( 'id', album.id ).data( 'title' , album.title ).data('info', album)
			);

			var hash = window.location.hash.split('#');
			
			if( hash[1] ) {
				if( album.id ==  hash[1].split('/')[0] ) {
					$( element ).trigger('click');
				}
			}

			
		});
		
	});
}
