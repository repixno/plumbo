        var efcountarray = new Array();
        efcountarray['collage'] = 0;
        efcountarray['folded'] = 0;
        efcountarray['postkort'] = 0;
		var efarticle = new Array();
		efarticle['collage'] = "all";
		efarticle['folded'] = "all";
		efarticle['postkort'] = "all";
        var efcount = 1;
        
        
        
        var cewecountarray = new Array();
        cewecountarray['takkekort'] = 0;
        cewecountarray['foldekort'] = 0;
        cewecountarray['foldekort_xl'] = 0;
		var cewearticle = new Array();
		cewearticle['takkekort'] = 'all';
        cewearticle['foldekort'] = 'all';
        cewearticle['foldekort_xl'] = 'all';
        var cewecount = 1;
    
        $( function(){
            
            InitAddCard( );
            /*$(window).resize(function() {
                if ($(window).width() < 960) {
                   $('.cardtitle').hide();
                }
               else {
                  $('.cardtitle').show();
               }
            });*/
            
            $(document).on('click', ".showmoreef" , function(){
                addEFcards( $(this).attr('id'), "more", null );
                return false;
            });
			$(document).on('click', ".showless" , function(){
                removeEf( $(this).attr('id') );
                return false;
            });
            $(document).on('click', ".showmorecewe" , function(){
                addCEWEcards( $(this).attr('id'), "more" );
                return false;
            });
			
			$(document).on( 'click', '.articleid' , function(){
				$(this).parent().find( '.selectedarticle' ).removeClass('selectedarticle');
				$(this).addClass( 'selectedarticle' );
				var cardid = $(this).parent().parent().attr('id');
				addEFcards( cardid, "byarticle", $(this).attr('id') );
				return false;
			
			});
			
			$(document).on( 'click', '.cewearticleid' , function(){
				$(this).parent().find( '.selectedarticle' ).removeClass('selectedarticle');
				$(this).addClass( 'selectedarticle' );
				var cardid = $(this).parent().parent().attr('id');
				addCEWEcards( cardid, "byarticle", $(this).attr('id') );
				return false;
			
			});
			
            $(document).on( 'click', '.showlesscewe', function(){
                removeCewe( $(this).attr('id') );
				return false;
            });
            
            $(document).on('click', '.ef_examplethumb', function(){
                
                    var id = $(this).attr('id');
                    
                    $.post( "/api/1.0/takkekort/clickstat", {id:id}, function(data){
                            //alert( data )
                        });
                    
                    //return false;
                    
                });
            $(document).on('click', '.cewe_examplethumb', function(){
                
                    var id = $(this).attr('id');
                    
                    $.post( "/api/1.0/takkekort/clickstatcewe", {id:id}, function(data){
                            //alert( data )
                        });
                    
                    //return false;
                    
                });
		
        });
        
        function InitAddCard( ){
            $('.cewe_examples').each(function(){
				if( typeof  efonly !== "undefined" ){
					
					$('.main').remove();
				}else{
					addCEWEcards($(this).attr('id'));
				}
            });        
            $('.ef_examples').each( function(){
                addEFcards($(this).attr('id'));
            });
        }
        
        function addCEWEcards( id, more, articleid ){
            
            cewecount = cewecountarray[id];
			
			if( articleid ){
				cewearticle[id] = articleid;
			}
			
			if( id == "all" ){
				var cardcontainer = $('.cewe_examples');
			}else{
				var cardcontainer = $('#' + id );
			}
			
			if( more == "byarticle" ){
				cewecount = 0;
				cewecountarray[id] = 0;
				cardcontainer.find( 'li').each(  function(){
					$(this).remove();
				});
			}
            
            $.post( "/api/1.0/takkekort/cewecards", {catid:catid, count: cewecount, group: id, articleid: cewearticle[id] } ,function( data ) {
                    
                var arr = JSON.parse( data  );
                var message = arr.message;
                arr = arr.cards;
                var length = arr.length, element = null;
                
                
                $(cardcontainer).find('.cmore h2').remove();
                
                $(cardcontainer).find('.cmore').append($('<img/>', {src:loadingsrc}));
                for (var i = 0; i < length; i++) {
                	element = arr[i];
                	cardcontainer.append(
                							 $('<li/>').append(
                								$('<a/>', { href: arr[i].purchaseurl,id: arr[i].id, class: "cewe_examplethumb"}).append( 
                											   $('<img/>',{
                													src : arr[i].thumbnail
                													})
                											   )
                								).append(
                									$('<p/>', {html:  arr[i].title +"<br><span id=\"price\">fra " +  arr[i].price + "</span> pr stk" })
                							 )
                							 );
                    cewecountarray[id]++;
                }
                
                $(cardcontainer).find('.cmore').remove();
                
                if( message == "continue" ){
                    $(cardcontainer).append(
                    $('<li/>', {class: "cmore"} ).append(
                       $('<h2/>', { text: "Vis flere", class:"showmorecewe", id:id   })
                       )
                    );
                }else {
                    var articleid =  arr[0].articleid;
                    var cewecardurl = "https://as.photoprintit.com/web/85021473/views/productEditor.jsf?productId=" + articleid + "&openDesigns=true";
                    if( catid == 458 ){
                        cewecardurl = cewecardurl + "&categoryId=category-christmas";
                    }
                    if( message != 'stop' ){
						$(cardcontainer).append(
							$('<li/>', {class: "cmore"} ).append($('<h2/>', { text: "Vis færre", class:"showlesscewe", id:id})
							));
					}
                    $(cardcontainer).append(
                        $('<li/>', {class: "showall"} ).append($('<h2/>').append( $("<a/>", {text:"Vis alle", href: cewecardurl }))
                        ));
                }
            });
        }
        
        
        function removeCewe( id ){
            var cardcontainer = $('#' + id );
            var i = 0;
            $( cardcontainer).find('li').each( function(){
                    i++;
                    if( i > 5 ){
                        $(this).remove();
                    }
                });
            
            cewecountarray[id]  = 5; 
            $(cardcontainer).append(
                    $('<li/>', {class: "cmore"} ).append(
                       $('<h2/>', { text: "Vis flere", class:"showmorecewe", id:id   })
                       )
            );
        }
        
        
        function addEFcards(id, more, articleid ){
            
            efcount = efcountarray[id];
			
			if( articleid ){
				efarticle[id] = articleid;
			}
			
			if( id == "all" ){
				var cardcontainer = $('.ef_examples');
			}else{
				var cardcontainer = $('#' + id );
			}
			
			if( more == "byarticle" ){
				efcount = 0;
				efcountarray[id] = 0;
				cardcontainer.find( 'li').each(  function(){
					$(this).remove();
				});
			}
			
			$(cardcontainer).append( $('<div/>', {class: "loading"} ).append($('<img/>', { src:loadingsrc })) );
            
            $.post( "/api/1.0/takkekort/cards", {catid:catid, count: efcount, group: id, articleid: efarticle[id] } ,function( data ) { 
                var arr = JSON.parse( data  );
                var message = arr.message;
                arr = arr.cards;
                var length = arr.length, element = null;
                $(cardcontainer).find('.cmore').remove();
                /*if( more == "more" ){
                    //$(cardcontainer).append( $('<li/>', {class: "cardtitle"} ) );
                }*/
                for (var i = 0; i < length; i++) {
                	element = arr[i];
                	cardcontainer.append(
                							 $('<li/>').append(
                								$('<a/>', { href: arr[i].purchaseurl, id: arr[i].id, class: "ef_examplethumb" }).append( 
                											   $('<img/>',{
                													src : "/images/eftakkekort/" + arr[i].thumbnail
                													})
                											   )
                								).append(
                									$('<p/>', {html:  arr[i].title +"<br><span id=\"price\">fra " + formatPrice( arr[i].price ) + "</span> pr stk" })
                							 )
                							 );
                efcountarray[id]++;
                }
                if( message == "continue" ){
                    $(cardcontainer).append(
                                                 $('<li/>', {class: "cmore"} ).append(
                                                    $('<h2/>', { text: "Vis flere", class:"showmoreef", id:id   })
                                                    )
                                                 );
                }else{
					if( message != 'stop' ){
						$(cardcontainer).append(
							$('<li/>', {class: "cmore"} ).append($('<h2/>', { text: "Vis færre", class:"showless", id:id})
							));
					}
                    $(cardcontainer).append(
                        $('<li/>', {class: "cmore"} ).append(
                            $('<h2/>', {  class:"showall", id:id
                              }).append( $('<a/>', {text: "Vis alle", href: '/produkter/fotokort' } ))
                        ));
                }
            }).done( function(){ $('.loading').hide('fast' , function(){ $(this).remove() }) });
        }
		
		function removeEf( id ){
            var cardcontainer = $('#' + id );
            var i = 0;
            $( cardcontainer).find('li').each( function(){
                    i++;
                    if( i > 5 ){
                        $(this).remove();
                    }
                });
            
            efcountarray[id]  = 5; 
            $(cardcontainer).append(
                    $('<li/>', {class: "cmore"} ).append(
                       $('<h2/>', { text: "Vis flere", class:"showmoreef", id:id   })
                       )
            );
        }