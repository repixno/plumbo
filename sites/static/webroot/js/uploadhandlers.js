function setImageTitle( imageid, title ) {
   
   $.post( '/api/1.0/image/set/title', {
      'imageid': imageid,
      'title': title
   } );
   
}

function setImageDescription( imageid, description ) {
   
   $.post( '/api/1.0/image/set/description', {
      'imageid': imageid,
      'description': description
   } );
   
}

function uploadSelectAlbum( batchid, albumid, albumtitle, redirecturl ) {
   
   $.post( '/api/1.0/upload/selectalbum', {
      'batchid': batchid,
      'albumid': albumid,
      'albumtitle': albumtitle
   },
   function() {
      
      if( typeof( redirecturl ) == 'function' ) {
         
         redirecturl( batchid, albumid, albumtitle );
         
      } else if( redirecturl ) {
         
         window.location = redirecturl;
         
      }
      
   } );
   
   return false;
   
}

function initUpload( batchid, sessionid, strFileDesc, strButtonText, nextButton, showOnStart, showOnFinished, hideOnStart ) {
               
   // the uploader.swf appears to need to be on the same domain 

      var fileQueue = [];
      var uploadCount = 0;
      var uploadBytes = 0;
      var uploadTotal = 0;
      var startOffset = 0;

      $(nextButton).attr('disabled', 'disabled');
      $(nextButton).addClass('disabled');
      
   	$(document).ready( function() {
      		$('#transfer-stop').click(function(){
         		$('#filedata').uploadifyClearQueue();
               $( '#transfer-stop' ).hide();
         		$( '#uplspeed' ).text('');
         		$(showOnFinished).show('fast');
         		$(nextButton).removeAttr( 'disabled' );
         		$(nextButton).removeClass( 'disabled' );
         		$('h3', showOnFinished).hide();
         		$('#total-loader-spinner').remove();
         		return false;
      		});
      });
         
      $('#filedata').uploadify({
         
         'uploader':'/uploadify.swf',
         'script':'/upload/receive',
         'scriptData': {
            'batchid': batchid, 
            'sessionid': sessionid
         },
         'cancelImg':'http://static.repix.no/gfx/upload/cancel.png',
         'fileDataName': 'image',
         'folder':'/myaccount/uploads',
         'queueID':'upload-queue',
         'auto': true,
         'multi':true,
         'simUploadLimit': 3,
         'fileExt': '*.jpg;*.jpeg;*.JPG;*.JPEG',
         'fileDesc': strFileDesc,
         'buttonText': strButtonText,
         'onSelect': function( event, queueID, fileObj ) {
            
            // fileObj.name
            // fileObj.size
            // fileObj.creationDate
            // fileObj.modificationDate
            // fileObj.type
            
            var master = $('#upload-master');
            var object = master.clone(true);
            object = $(object);
            object.attr('id','fileUpload'+queueID)
               .attr('title', fileObj.name );
               
            master.parent().append( object );
            
            $('.progressbar', object).progressbar({
      			value: 0
      		});
      		
      		var fileObjName = fileObj.name.split('.');
            fileObjName.pop();
            fileObjName = fileObjName.join('.');
            
      		$('input', object)
               .val( fileObjName )
               .attr( 'title', fileObjName );
      		
      		$('input', object).focus( function(){
      		   if( $(this).hasClass('quiet') ) {
      		      $(this)
      		       .select()
      		       .removeClass('quiet');
      		   }
      		});
      		
      		$('input', object).blur( function(){
      		   if( $(this).val() == '' ) {
      		      $(this)
      		       .val($(this).attr('title'))
      		       .removeClass('changed')
      		       .addClass('quiet');
      		   } else {
      		      if( $('img', object ).attr('id') ) {
      		         setImageTitle( $('img', object ).attr('id'), $(this).val() );
      		      } else {
         		      $(this).addClass('changed');
      		      }
      		      
      		   }
      		});
      		
      		$('textarea', object).focus(function(){
      		   if( $(this).hasClass('quiet') ) {
      		      $(this)
      		       .val('')
      		       .removeClass('quiet');
      		   }
      		});
      		
      		$('textarea', object).blur(function(){
      		   if( $(this).val() == '' ) {
      		      $(this)
      		       .val($(this).attr('title'))
      		       .removeClass('changed')
      		       .addClass('quiet');
      		   } else {
      		      if( $('img', object ).attr('id') ) {
      		         setImageDescription( $('img', object ).attr('id'), $(this).val() );
      		      } else {
         		      $(this).addClass('changed');
                  }
      		   }
      		});
      		
      		object.show();
      		
      		fileQueue.push( queueID );
      		uploadBytes += fileObj.size;
            uploadTotal++;
      		
            return false;
            
         },
         
         'onSelectOnce': function( event, data ) {
            
            // data.fileCount;
            // data.filesSelected;
            // data.filesReplaced;
            // data.allBytesTotal;
            
            $(showOnStart).show('fast');
            $('#transfer-status').show();
            $('#transfer-stop').show();
      		
            $( '#filedata' ).uploadifySettings( 'scriptData', {'queueid': fileQueue.shift() } );
            
            $('#numimages').text( uploadTotal );
            $('#uplimages').text( uploadCount );
            
            if( hideOnStart ) {
               $(hideOnStart).hide();
            }
      		
            return false;
            
         },
         
         'onCancel': function( event, queueID, fileObj, data ) {
            
            // fileObj.name
            // fileObj.size
            // fileObj.creationDate
            // fileObj.modificationDate
            // fileObj.type
            
            // data.fileCount;
            // data.allBytesTotal;
            
            index = fileQueue.indexOf( queueID );
            if( index != -1 ) {
               // uploadBytes -= fileObj.size;
               uploadBytes = 0;
               uploadTotal--;
               fileQueue.splice( index, 1 );
            }
            
            $( '#fileUpload' + queueID ).remove();
            
         },
         
         'onClearQueue': function( event, data ) {
            
            // data.fileCount;
            // data.allBytesTotal;
            
            fileQueue = [];
            
            uploadCount = 0;
            uploadTotal = 0;
            uploadBytes = 0;
            
            $('#numimages').text( uploadTotal );
            $('#uplimages').text( uploadCount );
      		$( '#progressbar-total' )
               .progressbar( 'option', 'value', 0 );
            
         },
         
         'onQueueFull': function( event, queueSizeLimit ) {
            
            return false;
            
         },
         
         'onError': function( event, queueID, fileObj, errorObj ) {
            
            // fileObj.name
            // fileObj.size
            // fileObj.creationDate
            // fileObj.modificationDate
            // fileObj.type
            
            // errorObj.type;
            // errorObj.info;
            
            // alert( errorObj.type + ' error: ' + errorObj.info );
            
            $('#filedata').uploadifyCancel( queueID );
            
            return false;
            
         },
         
         'onProgress': function( event, queueID, fileObj, data ) {
            
            // fileObj.name
            // fileObj.size
            // fileObj.creationDate
            // fileObj.modificationDate
            // fileObj.type
            
            // data.percentage;
            // data.bytesLoaded;
            // data.allBytesLoaded;
            // data.speed;
            
            $( '#progressbar-total' )
               .progressbar( 'option', 'value', 100 * data.allBytesLoaded / uploadBytes );
            
            $( '#fileUpload' + queueID + ' .progressbar' )
               .progressbar( 'option', 'value', data.percentage );
            
            $( '#uplspeed' ).text( Math.round( data.speed, 1 ) + ' KB/s' ); 
            
            return false;
            
         },
         
         'onComplete': function( event, queueID, fileObj, response, data ) {
            
            // fileObj.name
            // fileObj.size
            // fileObj.creationDate
            // fileObj.modificationDate
            // fileObj.type
            
            // data.fileCount;
            // data.speed;
            
            $( '#uplimages' ).text( ++uploadCount );
      		
            $( '#filedata' ).uploadifySettings( 'scriptData', {'queueid': fileQueue.shift() } );
      		$( '#fileUpload' + queueID + ' .progressbar' )
               .progressbar( 'option', 'value', 100 );
            $( '#fileUpload' + queueID + ' img' )
               .attr( 'src', '/images/stream/thumbnail/' + response )
               .attr( 'id', response );
            
            $( '#fileUpload' + queueID + ' .progressbar' )
            
            var input = $( '#fileUpload' + queueID + ' input' );
            var desc = $( '#fileUpload' + queueID + ' textarea' );
            
            if( input.hasClass( 'changed' ) ) {
               setImageTitle( response, input.val() );
               input.removeClass( 'changed' );
            }
            
	         if( desc.hasClass( 'changed' ) ) {
               setImageDescription( response, desc.val() );
               desc.removeClass( 'changed' );
            }
            
            return false;
            
         },
         
         'onAllComplete': function( event, data ) {
            
            // data.filesUploaded;
            // data.errors;
            // data.allBytesLoaded;
            // data.speed;
            
            
            
            uploadBytes = 0;
            
            $( '#transfer-stop' ).hide();
      		$( '#uplspeed' ).text('');
      		$(showOnFinished).show('fast');
      		$(nextButton).removeAttr( 'disabled' );
      		$(nextButton).removeClass( 'disabled' );
      		$('#total-loader-spinner').remove();
      		$(nextButton).effect('pulsate');
            
            return false;
            
         },
         
         'onCheck': function(event, checkScript, fileQueue, folder, single ) {
            
            return false;
            
         }
         
      });
   
}

