Menu = $.klass({

   initialize: function() {

      this.baseElement = $( document.createElement( 'div' ) );
      this.editlock = true;
      this.build();

   },

   build: function() {

      this.portallist = new PortalList();
      this.menutree = new MenuTree();
      this.menuitemadder = new MenuItemAdder();

      this.baseElement.append( this.portallist.render() );
      this.baseElement.append( this.menutree.render() );
      this.baseElement.append( this.menuitemadder.render() );

   },

   render: function() {

      return this.baseElement;

   },

   setParent: function( obj ) {

      this.parentObject = obj;

   }

});


MenuTree = $.klass({

   initialize: function() {

      this.classType = 'MenuTree';
      this.spinner = $( document.createElement( 'img' ) ).
         attr( 'src', 'http://static.repix.no/gfx/admin/img/simpletree/spinner.gif' ).
         hide();
      this.baseElement = $( document.createElement( 'div' ) ).
         attr( 'id', 'menutree' ).
         addClass( 'wrap' );
      this.baseWrapper =
         $( document.createElement( 'span' ) ).
         attr( 'id', 'spans-divs' ).
         addClass( 'page-list' );
      this.parentElement = $( document.createElement( 'div' ) ).
         append( this.spinner ).
         append( this.baseElement );

      this.itemObjects = [];
      this.activeItem = null;
      this.activePortal = null;

      var activeitem = getcookie('activeitem');

      this.setActiveItem(document.getElementById(activeitem));

      this.fetchList();
   },

   checkNode: function( context, movedelement ) {

      moveId = movedelement.get(0).baseObject.options.id;

      // Parent (Moved div).
      parent = movedelement.parent();
      grandparent = parent.parent();
      // Grandparent (Node owner).
      granparentId = 0;
      if ( grandparent.attr('id') != 'menutree' ) {

         granparentId = grandparent.get(0).baseObject.options.id;

      }

      // Find position.
      tmpposition = 0;
      parent.children().each( function(i) {

         tstid = $(this).get(0).baseObject.options.id;

         if ( tstid == moveId ) {

            tmpposition = i;

         }

         });

      // Save value according to context.
      if ( context == 'start' ) {

         this.sortedStart = [ granparentId, moveId, tmpposition ];

      }
      if ( context == 'end' ) {

         this.sortedEnd = [ granparentId, moveId, tmpposition ];

      }

   },

   fetchList: function( portal ) {

      this.spinner.show();

      portal = portal || null;

      this.setPortal( portal );

      var self = this;
      $.ajax({
         url: "/menu/api/fetchmenulist",
         type: 'post',
         data: {
            portal: portal
         },
         cache: 'false',
         dataType: 'json',
         success: function( data, status ) {

            self.populateList( data.menuitems );
            self.spinner.hide();

         }
      });

   },

   setPortal: function( portal ) {

      this.activePortal = portal;

   },

   populateList: function( data ) {

      this.baseWrapper.children().remove();

      var curLevel = 0;
      var curSpanNode = this.baseWrapper;
      var curItemNode = null;

      var activeitem = getcookie('activeitem');

      var classPlus = 'ss_bullet_toggle_plus';
      var classMinus = 'ss_bullet_toggle_minus';

      for ( var i in data ) {

         options = data[ i ];
         itemBox = new MenuTreeItem( options );

         this.itemObjects.push( itemBox );
         itemBox.setParent( this );

         if ( data[ i ].level > curLevel ) {

            newSpanNode = $( document.createElement( 'span' ) ).
               addClass( 'page-list' );
            curItemNode.append( newSpanNode );
            curSpanNode = newSpanNode;

         } else if ( data[ i ].level < curLevel ) {

            delta = curLevel - data[ i ].level;
            for ( var j=0; j<delta; j++ ) {

               curSpanNode = curSpanNode.parent().parent();

            }

         }

         curLevel = data[ i ].level;
         newItemNode = itemBox.render();
         curSpanNode.append( newItemNode );
         curItemNode = newItemNode;

      }

      this.postRender();

      for( var j in data ) {

         options = data[ j ];
         curLevel = data[ j ].level;

         if (options.id == activeitem) {

            if (curLevel > 0) {
               var item = $('#item-' + options.id).parent();

               for ( var i = 0; i <= 10; i++ ) {
                  var currentitemid = item.find( 'a:first' ).parent().parent().attr('id');

                  item.children( 'div' ).children( 'span.subopener').click();

                  item = item.parent();

               }
            }

            $('#item-' + options.id).find('a').click();
         }

      }

   },

   updateOrder: function() {

      var self = this;
      $.ajax({
         url: "/menu/api/updatemenuorder",
         type: 'post',
         data: {
            portal: this.activePortal,
            changestart: this.sortedStart.join( ',' ),
            changeend: this.sortedEnd.join( ',' )
         },
         cache: 'false',
         dataType: 'json',
         success: function( data, status ) {

            self.updateItemOptions( data.menulist );

         }
      });

   },

   updateItemOptions: function( list ) {

      for ( var i in this.itemObjects ) {

         for ( var j in list ) {

            if ( this.itemObjects[ i ].options.id == list[ j ].id ) {

               this.itemObjects[ i ].setOptions( list[ j ] );
               this.itemObjects[ i ].updateDependencies();


            }

         }

      }

   },

   addItem: function( options ) {

      itemBox = new MenuTreeItem( options );
      itemBox.setParent( this );

      this.baseWrapper.append( itemBox.render() );
      this.renderNested();

   },

   render: function() {

      this.baseElement.append( this.baseWrapper );

      return this.parentElement;

   },

   postRender: function() {

      if ( this.baseWrapper.children().length > 0 ) {

         // Add correct collapsing icons.
         this.calculateCollapse();

         // Make the list nested.
         this.renderNested();

      }

   },

   calculateCollapse: function( actedon ) {

      // If a node has been moved, this parameter contains parent nodes of original and new position so collapse status icon can be updated.
      actedon = actedon || false;

      var classPlus = 'ss_bullet_toggle_plus';
      var classMinus = 'ss_bullet_toggle_minus';
      // Run through all menu items in a reverse order to make sure all items are processed only once.
      this.baseWrapper.find( 'div.item' ).reverse().each( function( i ) {

         var el = $(this);

         // Do the full calculation only when tree is built at start or if this element is one of the parent nodes inflicted by a moved node.
         if ( !actedon || this.baseObject.options.id == actedon[0] || this.baseObject.options.id == actedon[1] ) {

            // Remove all collapse bindings.
            el.children( 'div' ).eq(0).children( 'span.subopener').removeClass( 'ss_sprite ' + classPlus + ' ' + classMinus ).unbind( 'click' );

            // Add classes and bindings if not top level.
            if ( el.children( 'span' ).length > 0 && el.children( 'span' ).eq(0).children().length > 0 ) {

               // Decide collapsed status of element if a child has been acted on.
               var newCollStatus = classPlus;
               if ( actedon && ( this.baseObject.options.id == actedon[1] || ( this.baseObject.options.id == actedon[0] ) ) ) {

                  newCollStatus = classMinus;
                  el.show();

               }
               // Add initial collapsed class and click binding.
               el.children( 'div' ).eq(0).children( 'span.subopener').eq(0).addClass( 'ss_sprite ' + newCollStatus ).
                  click( function() {

                     var elCl = $(this);
                     if ( elCl.hasClass( classPlus ) ) {

                        elCl.removeClass( classPlus ).addClass( classMinus );
                        elCl.parent().next().show( 300 );

                     } else if ( elCl.hasClass( classMinus ) ) {

                        elCl.removeClass( classMinus ).addClass( classPlus );
                        elCl.parent().next().hide( 300 );

                     }

                  });

               if ( !actedon ) {

                  el.children( 'span' ).hide();

               }

            }

         }

      });

   },

   renderNested: function() {

      var self = this;

      if ( !islockedforedit ) {

         this.baseWrapper.NestedSortable( {
            accept: 'page-item3',
            opacity: 0.8,
            helperclass: 'helper',
            nestingPxSpace: 20,
            currentNestingClass: 'current-nesting',
            fx:400,
            revert: true,
            autoScroll: false,
            onStart: function() {

               self.checkNode( 'start', $(this) );

            },
            onChange: function( serialized ) {

               self.checkNode( 'end', $('#item-'+self.sortedStart[1]) );
               self.calculateCollapse( [self.sortedStart[0],self.sortedEnd[0]] );
               self.updateOrder();

            }
         });

      }

   },

   setActiveItem: function( item ) {

      item = item || null;

      this.activeitem = item;

   },

   getActiveItem: function() {

      return this.activeitem;

   }

});

MenuTreeItem = $.klass({

   initialize: function( options ) {

      this.classType = 'MenuTreeItem';
      this.options = options;
      this.build();
      this.baseElement.get(0).baseObject = this;
      this.parentObject = null;
      this.isActive = false;

   },

   build: function() {

      var self = this;

      this.baseElement =
         $( document.createElement( 'div' ) ).
         attr( 'id', 'item-' + this.options.id ).
         addClass( 'clear-element page-item3 sort-handle left item' );

      // Add element for submenu collapsing.
      var subopener = $( document.createElement( 'span' ) ).
         text( ' ' ).
         addClass( 'subopener' );

      this.itemLink =
         $( document.createElement( 'a' ) ).
         click( function() {

            self.activateItem();

            elements.activeitem.getObject().show( self.options, self.baseElement );

            setcookie('activeitem',self.options.id , 1, '/', '', '');

            });
      this.writeTitle();

      helper = $( document.createElement( 'div' ) ).append( subopener ).append( this.itemLink );
      helper.droppable({
         accept: '.addablecontent',
         tolerance: 'pointer',
         over: function(e,ui) {

            self.baseElement.children().eq(0).addClass( 'active' );

         },
         out: function(e,ui) {

            self.baseElement.children().eq(0).removeClass( 'active' );

         },
         drop: function(e,ui) {

            self.activateItem();
            //self.addContent( ui.draggable.get(0).baseObject.options.id );
            elements.activeitem.getObject().show( self.options, self.baseElement );
            elements.activeitem.getObject().connectedList.addContent( ui.draggable.get(0).baseObject.options.id );

         }
         });

      this.baseElement.append( helper );

   },

   setOption: function( option, value ) {

      this.options[ option ] = value;

   },

   setOptions: function( options ) {

      this.options = options;

      this.writeTitle();

   },

   setTitle: function( newtitle ) {

      this.setOption( 'title', newtitle );
      this.writeTitle();

   },

   writeTitle: function() {

      var title = this.options.display;
      //title = title + ' (' + this.options.id + ',' + this.options.parentid + ',' + this.options.position + ')'
      this.itemLink.text( title );

   },

   render: function() {

      return this.baseElement;

   },

   activateItem: function() {

      this.isActive = true;

      var current = this.parentObject.getActiveItem();
      if ( current ) {

         current.get(0).baseObject.deactivateItem();

      }

      this.parentObject.setActiveItem( this.baseElement );
      this.baseElement.children().eq(0).addClass( 'activated' );

   },

   deactivateItem: function() {

       this.isActive = false;

      this.parentObject.setActiveItem();
      this.baseElement.children().eq(0).removeClass( 'activated active' );

   },

   updateDependencies: function() {

      if ( this.isActive ) {

         elements.activeitem.getObject().setOptions( this.options );
         elements.activeitem.getObject().InfoBox.writeUrl();
         elements.activeitem.getObject().InfoBox.writePath();

      }

   },

   setParent: function( obj ) {

      this.parentObject = obj;

   }

});

MenuItemAdder = $.klass({

   initialize: function() {

      //this.baseElement = $('#menuitemadder');
      this.baseElement = $( document.createElement( 'div' ) ).
         attr( 'id', 'menuitemadder' );
      this.build();

   },

   build: function() {

      itemField =
         $( document.createElement( 'input' ) ).
         attr( 'type', 'text' ).
         attr( 'id', 'addmenuitem' ).
         attr( 'size', 20 );
      itemButton =
         $( document.createElement( 'input' ) ).
         attr( 'type', 'button' ).
         attr( 'id', 'addmenuitembutton' ).
         attr( 'value', langvars.strLabelAdd );

      this.baseElement.append( itemField ).append( itemButton );

      var self = this;
      itemButton.click( function() {

         self.doAdding( itemField );

      });
      itemField.keypress( function(e) {

         if ( e.which == 13 ) {

            self.doAdding( itemField );

         }

      });

   },

   render: function() {

      return this.baseElement;

   },

   doAdding: function( valuefield ) {

      $.ajax({
         url: "/menu/api/newmenuitem",
         type: 'post',
         cache: 'false',
         data: {
            title: valuefield.val(),
            portal: elements.menubox.getTab('tabs01').getObject().menutree.activePortal
         },
         dataType: 'json',
         success: function( data, status ) {

            elements.menubox.getTab('tabs01').getObject().menutree.addItem( data.options );

         },
         error: function( data, status, error ) {

         }
      });

      itemField.get(0).focus();
      itemField.get(0).select();

   }

});

PortalList = $.klass({

   initialize: function() {

      //this.baseElement = $('#portallist');
      this.baseElement = $( document.createElement( 'select' ) ).
         attr( 'id', 'portallist' );
      this.portalList = [];
      this.fetchList();


   },

   fetchList: function() {

      var self = this;
      $.ajax({
            url: "/menu/api/fetchportals",
            type: 'get',
            cache: 'false',
            dataType: 'json',
            success: function( data, status ) {

               self.portalList = data.portals;
               self.build();

            }
         });

   },

   build: function() {

      for ( var i in this.portalList ) {

         opt = $( document.createElement( 'option' ) ).attr( 'value', this.portalList[ i ][ 'id' ] ).text( this.portalList[ i ][ 'title' ] );
         this.baseElement.append( opt );

      }

      //elements.portalconnector.populateList();

      var self = this;
      this.baseElement.change( function() {

         elements.menubox.getTab('tabs01').getObject().menutree.fetchList( self.baseElement.find( 'option:selected' ).val() );

      });

   },

   render: function() {

      return this.baseElement;

   }

});


function setcookie( name, value, expires, path, domain, secure )
{
// set time, it's in milliseconds
var today = new Date();
today.setTime( today.getTime() );

/*
if the expires variable is set, make the correct
expires time, the current script below will set
it for x number of days, to make it for hours,
delete * 24, for minutes, delete * 60 * 24
*/
if ( expires )
{
expires = expires * 1000 * 60 * 60 * 24;
}
var expires_date = new Date( today.getTime() + (expires) );

document.cookie = name + "=" +escape( value ) +
( ( expires ) ? ";expires=" + expires_date.toGMTString() : "" ) +
( ( path ) ? ";path=" + path : "" ) +
( ( domain ) ? ";domain=" + domain : "" ) +
( ( secure ) ? ";secure" : "" );
}

function getcookie( check_name ) {
	// first we'll split this cookie up into name/value pairs
	// note: document.cookie only returns name=value, not the other components
	var a_all_cookies = document.cookie.split( ';' );
	var a_temp_cookie = '';
	var cookie_name = '';
	var cookie_value = '';
	var b_cookie_found = false; // set boolean t/f default f

	for ( i = 0; i < a_all_cookies.length; i++ )
	{
		// now we'll split apart each name=value pair
		a_temp_cookie = a_all_cookies[i].split( '=' );


		// and trim left/right whitespace while we're at it
		cookie_name = a_temp_cookie[0].replace(/^\s+|\s+$/g, '');

		// if the extracted name matches passed check_name
		if ( cookie_name == check_name )
		{
			b_cookie_found = true;
			// we need to handle case where cookie has no value but exists (no = sign, that is):
			if ( a_temp_cookie.length > 1 )
			{
				cookie_value = unescape( a_temp_cookie[1].replace(/^\s+|\s+$/g, '') );
			}
			// note that in cases where cookie is initialized but no value, null is returned
			return cookie_value;
			break;
		}
		a_temp_cookie = null;
		cookie_name = '';
	}
	if ( !b_cookie_found )
	{
		return null;
	}
}
