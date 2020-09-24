		
		// skje

	$("a:external").addClass("ext_link");
	
	$(function() {
		
		$('a.ext_link').click(function() {
			 // open a modal 
			$('a:external').attr('data-toggle', 'modal');
			$('a:external').attr('data-target', '#speedbump');
			//go to link on modal close
			var url = $(this).attr('href');
			$('.btn-modal.btn-continue').click(function() {
				window.open(url);
				$('.btn-modal.btn-continue').off();
			});
			$('.btn-modal.btn-close').click(function() {
				$('#speedbump').modal('hide');
				$('.btn-modal.btn-close').off();
			}); 
		});
		
	});  
