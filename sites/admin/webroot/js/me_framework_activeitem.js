ActiveItem = $.klass({

   initialize: function() {

      this.classType = 'ActiveItem';
      this.baseElement = $( document.createElement( 'div' ) );
      this.baseElement.get(0).baseObject = this;

      // Options for item.
      this.options = null;
      this.menuitemid = null;
      this.itemelement = null;
      this.itemObject = null;

   },

   render: function() {

      // Clear box and print placer text.
      this.clear( true );

      return this.baseElement;

   },

   setOptions: function( options ) {

      this.options = options;
      this.menuitemid = this.options.id;

   },

   show: function( options, itemelement ) {

      this.clear();

      this.setOptions( options );
      this.itemelement = itemelement;
      this.itemObject = this.itemelement.get(0).baseObject;

      // Setup elements.
      this.InfoBox = new ItemInfo();
      this.InfoBox.setParent( this );

      connectedTab = new TabsBox();
      this.connectedList = connectedTab.addTab({
         id: 'actitem01',
         title: langvars.strConnected,
         object: new ConnectedItems()
         }).getObject();
      this.itemOptions = connectedTab.addTab({
         id: 'actitem02',
         title: langvars.strOptions,
         object: new ItemOptions()
         }).getObject();

      // Connect elements to base.
      this.baseElement.append( this.InfoBox.render() );

      this.baseElement.append( connectedTab.render() );
      connectedTab.show();

      this.connectedList.setParent( this );
      this.itemOptions.setParent( this );

      // Populate elements.
      this.InfoBox.build();
      this.connectedList.populate();
      this.itemOptions.build();

   },

   clear: function( showplacer ) {

      showplacer = showplacer || false;

      this.baseElement.children().remove();
      this.options = null;
      this.menuitemid = null;

      if ( showplacer ) {

         this.baseElement.html( '<p>'+langvars.strNoItemChosen+'</p>' )

      }

   },

   getMenuItemId: function() {

      return this.menuitemid;

   },

   setParent: function( obj ) {

      this.parentObject = obj;

   }

});

ItemInfo = $.klass({

   initialize: function() {

      this.classType = 'ItemInfo';
      this.baseElement = $( document.createElement( 'div' ) ).
         attr( 'id', 'iteminfocontainer' );
      this.baseElement.get(0).baseObject = this;
      this.parentObject = null;

   },

   build: function() {

      var self = this;

      this.baseElement.children().remove();

      this.pathBox = $( document.createElement( 'div' ) ).
         addClass( 'pathbox' ).
         append( $( document.createElement( 'span' ) ).addClass( 'label' ).text( langvars.strPath + ':' ) ).
         append( $( document.createElement( 'span' ) ).addClass( 'content' ) ).
         append( delLink );
      this.writePath();

      var toolBox = $( document.createElement( 'div' ) ).
         addClass( 'tools' );

      var delLink = $( document.createElement( 'a' ) ).
         text( langvars.strDelete ).
         click( function(e) {

            if ( !confirm( langvars.strDoYouReallyWantToDeleteThisMenuItem ) || !confirm( langvars.strAreYouReallySureAboutThis ) ) {

               return false;

            }

            var delIds = [self.parentObject.getMenuItemId()];
            // Find all subitems to be deleted.
            self.parentObject.itemelement.find( '.item' ).each( function() {
               delIds.push( this.baseObject.options.id );
            });

            $.ajax({
               url: "/menu/api/deletemenuitem",
               type: 'post',
               cache: 'false',
               data: {
                  tobedeleted: delIds.join( ',' ),
                  parentid: self.parentObject.options.parentid,
                  position: self.parentObject.options.position
               },
               dataType: 'json',
               success: function( data, status ) {

                  elements.menubox.getTab('tabs01').getObject().menutree.fetchList();
                  elements.activeitem.getObject().clear( true );

               }
            });

            });
      var delEl = $( document.createElement( 'div' ) ).
         append( delLink );

      var editLink = $( document.createElement( 'a' ) ).
         text( langvars.strEdit ).
         click( function(e) {
            self.changeTitleBox.toggle( 'slow' );
            });
      var editEl = $( document.createElement( 'div' ) ).
         append( editLink );


      toolBox.append( editEl ).append( delEl );

      this.changeTitleField = $( document.createElement( 'input' ) ).
         attr( 'type', 'text' ).
         val( this.parentObject.options.title );
      var changeTitleButton = $( document.createElement( 'input' ) ).
         attr( 'type', 'submit' ).
         val( langvars.strSave ).
         click( function(e) {

               var titles = [];
               $('.title-translation').each( function() {
                  titles.push( {
                     code: $(this).attr( 'name' ),
                     title: $(this).val()
                  } );
               });
               titles = $.toJSON(titles);

               $.ajax({
                  url: "/menu/api/updateitemtitle",
                  type: 'post',
                  cache: 'false',
                  data: {
                     menu: self.parentObject.getMenuItemId(),
                     newtitle: self.changeTitleField.val(),
                     translated: titles
                  },
                  dataType: 'json',
                  success: function( data, status ) {

                     var mitemopt = self.parentObject.itemObject;
                     mitemopt.setTitle( data.title );
                     self.changeTitleField.val( data.title );
                     self.pathBox.find( 'span:last' ).text( data.title );

                     self.parentObject.options.translated.titles = data.translated;
                     self.parentObject.options.display = data.display;

                     $('#item-'+data.menuitemid+'>div>a' ).text( data.display );

                     $('.title-translation').each( function() {
                        $(this).css( 'backgroundColor', '#ffffff' );
                     } );

                  }
               });

            });

      var translateSource = this.changeTitleField;
      var translateButton = $( document.createElement( 'input' ) ).
         attr( 'type', 'button' ).
         val( langvars.strTranslate ).
         click( function( e ) {

            $('.title-translation').each( function() {

               var item = $(this);
               var text = translateSource.val();

               var targetlanguage = $(this).attr('name').split('_')[0];
               if( targetlanguage == 'nb' ) targetlanguage = 'no';
               if( targetlanguage != 'en' ) {

                  google.language.translate( text, 'en', targetlanguage, function(result) {

                     if( result.translation ) {

                        item.val( result.translation );
                        item.css( 'backgroundColor', '#00cc00' );

                     }

                  });

               } else {

                  item.val( text );
                  item.css( 'backgroundColor', '#00cc00' );

               }

            } );

         } );

      this.changeTitleBox = $( document.createElement( 'div' ) ).
         addClass( 'changetitlebox' ).
         css({
            display: 'none'
            }).
         append( $( document.createElement( 'span' ) ).addClass( 'label' ).text( langvars.strChangeTitle + ':' ) ).
         append( this.changeTitleField ).
         append( translateButton ).
         append( changeTitleButton ).
         append( $( document.createElement( 'br' ) ) );

      var changeTitleBox = this.changeTitleBox;
      $(alllanguages).each( function() {

         var translationFieldFlag = $( document.createElement( 'img' ) ).
            attr( 'src', 'http://static.repix.no/gfx/flags/'+this.short+'.png' );

         var translationFieldLabel = $( document.createElement( 'span' ) ).
            text( this.title ).
            addClass( 'label' );

         var translationFieldElem = $( document.createElement( 'input' ) ).
            attr( 'id', 'title_lang_' + this.code ).
            attr( 'name', this.code ).
            attr( 'rel', this.short ).
            attr( 'type', 'text' ).
            attr( 'value', '' ).
            addClass( 'title-translation' );

         changeTitleBox.
            append( translationFieldLabel ).
            append( translationFieldElem ).
            append( translationFieldFlag ).
            append( $( document.createElement( 'br' ) ) );

      } );

      this.urlBox = $( document.createElement( 'div' ) ).
         addClass( 'urlbox' ).
         append( $( document.createElement( 'span' ) ).addClass( 'label' ).text( langvars.strURL + ':' ) ).
         append( $( document.createElement( 'span' ) ) );
      this.writeUrl();

      this.baseElement.append( this.pathBox ).append( toolBox ).append( this.urlBox ).append( this.changeTitleBox );

   },

   writePath: function() {

      this.pathBox.find( 'span.content' ).html( this.buildPath() );

   },

   writeUrl: function() {

      this.urlBox.find( 'span:last' ).html(
         $( document.createElement( 'a' ) ).
         attr( 'href', this.parentObject.options.url ).
         attr( 'target', '_blank' ).
         text( this.parentObject.options.url )
         );

   },

   buildPath: function() {

      var curPos = this.parentObject.itemelement;
      var path = [];
      do {

         path.unshift( '<span>' + curPos.get(0).baseObject.options.title + '</span>' );
         curPos = curPos.parent().parent();

      } while( curPos.attr( 'id' ) != 'menutree' );

      return path.join( ' > ' );

   },

   render: function() {

      return this.baseElement;

   },

   setParent: function( obj ) {

      this.parentObject = obj;

   }

});

ConnectedItems = $.klass({

   initialize: function() {

      this.classType = 'ConnectedItems';

      this.baseElement = $( document.createElement( 'div' ) ).
         attr( 'id', 'connectedcontainer' );
      this.spinner = $( document.createElement( 'img' ) ).
         attr( 'src', 'http://static.repix.no/gfx/admin/img/simpletree/spinner.gif' ).
         hide();
      this.baseElement.get(0).baseObject = this;
      this.parentObject = null;
      this.connectedList = [];
      this.sectionList = [];

      this.parentElement = $(document.createElement( 'div' ) ).
         append( this.spinner ).
         append( this.baseElement );


   },

   populate: function() {

      this.connectedList = [];

      this.spinner.show();

      var self = this;
      $.ajax({
         url: "/menu/api/fetchconnected",
         type: 'post',
         cache: 'false',
         data: {
            id: this.parentObject.getMenuItemId()
         },
         dataType: 'json',
         success: function( data, status ) {

            self.connectedList = data.connected;
            self.sectionList = data.sections;
            self.build();
            self.spinner.hide();

         }
      });

   },

   build: function() {

      this.baseElement.children().remove();

      var sectionElements = [];
      var activeSections = [];
      var newSection = null;
      for ( var i in this.sectionList ) {

         newSection = new ConnectedSection( this.sectionList[ i ] );
         newSection.setParent( this );
         sectionElements[ this.sectionList[ i ].id ] = newSection;
         this.baseElement.append( newSection.render() );
         activeSections.push( this.sectionList[ i ].id );

      }

      var newItem = null;
      for ( var i in this.connectedList ) {

         if ( jQuery.inArray( this.connectedList[ i ].section, activeSections ) == -1 ) {

            this.connectedList[ i ].section = '_default';

         }

         newItem = new ConnectedItem( this.connectedList[ i ] );
         newItem.setParent( sectionElements[ this.connectedList[ i ].section ] );
         sectionElements[ this.connectedList[ i ].section ].addItem( newItem.render() );

      }

      for ( var i in sectionElements ) {

         sectionElements[ i ].postRender();

      }

   },

   render: function() {

      return this.parentElement;

   },

   updateOrder: function() {

      var orderList = [];

      this.baseElement.children().each( function() {
         this.baseObject.baseElement.children( '.connecteditem' ).each( function() {

            orderList.push( this.baseObject.options.id + '::' + $(this).parent().get(0).baseObject.options.sysid + "::" + this.baseObject.options.section );

         });
      });

      var self = this;

      $.ajax({
         url: "/menu/api/updateconnectedorder",
         type: 'post',
         cache: 'false',
         data: {
            id: this.parentObject.getMenuItemId(),
            orderlist: orderList.join(',')
         },
         dataType: 'json',
         success: function( data, status ) {
            
            if ( !data.result ) {
               alert( data.message );
            }

            self.connectedList = data.connected;
            self.build();

         }
      });

   },

   setParent: function( obj ) {

      this.parentObject = obj;

   }

});

ConnectedSection = $.klass({

   initialize: function( options ) {

      this.classType = 'ConnectedSection';
      this.options = options;
      this.baseElement = $( document.createElement( 'div' ) );
      this.baseElement.get(0).baseObject = this;
      this.parentObject = null;

   },

   render: function() {

      this.baseElement.
         attr( 'id', 'contentsection_' + this.options.id ).
         addClass( 'connectedsection' );

      var title = $( document.createElement( 'div' ) ).
         addClass( 'connectedsectiontitle' ).
         append(
            $( document.createElement( 'span' ) ).
            text( this.options.title )
         );

      this.baseElement.append( title );

      var self = this;
      if ( !islockedforedit ) {

         this.baseElement.sortable({
            axis: 'y',
            connectWith: '.connectedsection',
            items: '.connecteditem',
            update: function( ev, ui ) {

               self.parentObject.updateOrder();

            }
         }).
         droppable({
            accept: '.addablecontent',
            tolerance: 'pointer',
            over: function(e,ui) {},
            out: function(e,ui) {},
            drop: function(e,ui) {

               self.addContent( ui.draggable.get(0).baseObject.options.id );

            }
            });

      }

      return this.baseElement;

   },

   postRender: function() {

      if ( this.baseElement.children().length == 1 ) {

         this.baseElement.append( '<p>'+langvars.strDropHereToAddContent+'</p>' );

      }

   },

   addItem: function( element ) {

      this.baseElement.append( element );

   },

   addContent: function( contentid ) {

      var addAt = this.getPrecedingItemsSize() + this.getSize();

      var self = this;
      $.ajax({
         url: "/menu/api/newconnecteditem",
         type: 'post',
         cache: 'false',
         data: {
            menuitem: this.parentObject.parentObject.getMenuItemId(),
            content: contentid,
            position: addAt,
            section: this.options.sysid
         },
         dataType: 'json',
         success: function( data, status ) {

            self.parentObject.populate();
            try {
               $("#product_" + contentid).addClass( 'used' );
               $("#article_" + contentid).addClass( 'used' );
            } catch(e) {}

         }
      });

   },

   removeItem: function( item, elem ) {

      var self = this;

      $.ajax({
         url: "/menu/api/deleteconnecteditem",
         type: 'post',
         cache: 'false',
         data: {
            menuitem: this.parentObject.parentObject.getMenuItemId(),
            content: item,
            section: elem.context.baseObject.options.section
         },
         dataType: 'json',
         success: function( data, status ) {

            for ( var i in self.parentObject.connectedList ) {

               if ( self.parentObject.connectedList[ i ].id = item ) {

                  self.parentObject.connectedList.splice( i, 1 );
                  break;

               }

            }

            elem.remove();

            if( self.baseElement.children().length == 1 ) {
               self.baseElement.append( '<p>'+langvars.strDropHereToAddContent+'</p>' );
            }

            if( !data.connected ) {
               try {
                  $("#product_" + item).removeClass( 'used' );
                  $("#article_" + item).removeClass( 'used' );
               } catch(e) {}
            }

         }
      });

   },

   getPrecedingItemsSize: function() {

      var numitems = 0;
      this.baseElement.prevAll().each( function() {
         numitems += $(this).children( '.connecteditem' ).length;
      });

      return numitems;

   },

   getSize: function() {

      return this.baseElement.children( '.connecteditem' ).length;

   },

   setParent: function( obj ) {

      this.parentObject = obj;

   }

});

ConnectedItem = $.klass({

   initialize: function( options ) {

      this.classType = 'ConnectedItem';
      this.options = options;
      this.baseElement = $( document.createElement( 'div' ) );
      this.baseElement.get(0).baseObject = this;
      this.parentObject = null;

   },

   render: function() {

      var self = this;

      this.baseElement.
         attr( 'id', 'content_' + this.options.id ).
         addClass( 'connecteditem' );

      var identBox = $( document.createElement( 'div' ) ).
         addClass( 'ident' );

      var iconEl = $( document.createElement( 'span' ) ).
         addClass( 'icon ss_sprite' ).
         addClass( this.options.cssclass );
      var textEl = $( document.createElement( 'span' ) ).
         text( this.options.title );

      identBox.append( iconEl ).append( textEl );

      var toolBox = $( document.createElement( 'div' ) ).
         addClass( 'tools' );

      var editEl = $( document.createElement( 'div' ) ).
         addClass( 'edit' ).
         append( $( document.createElement( 'a' ) ).
            text( langvars.strEdit ).
            click( function(e) {

                  window.location = '/content/' + self.options.type + 's/' + self.options.id;

               })
            );
      var delEl = $( document.createElement( 'div' ) ).
         addClass( 'delete' ).
         append( $( document.createElement( 'a' ) ).
            text( langvars.strDelete ).
            click( function(e) {

               self.parentObject.removeItem( self.options.id, self.baseElement );

               })
            );

      toolBox.append( editEl ).append( delEl );

      this.baseElement.append( identBox ).append( toolBox );

      return this.baseElement;

   },

   setParent: function( obj ) {

      this.parentObject = obj;

   }

});

ItemOptions = $.klass({

   initialize: function() {

      this.classType = 'ItemOptions';
      this.baseElement = $( document.createElement( 'div' ) ).
         attr( 'id', 'optionscontainer' );
      this.baseElement.get(0).baseObject = this;
      this.parentObject = null;
      this.portalsList = [];

   },

   build: function() {

      // Add URL changer.
      var urlch = new URLChanger();
      urlch.setParent( this );
      urlch.populate( this.parentObject.getMenuItemId() );
      this.baseElement.append( urlch.render() );

      // Add template connector selector.
      var tconn = new TemplateConnector();
      tconn.setParent( this );
      tconn.populate( this.parentObject.getMenuItemId() );
      this.baseElement.append( tconn.render() );

      // add the article connector selector.
      var aconn = new MenuArticleConnector();
      aconn.setParent( this );
      aconn.populate( this.parentObject.getMenuItemId() );
      this.baseElement.append( aconn.render() );

      // Add portal connector box.
      var pconn = new PortalConnector();
      pconn.setParent( this );
      pconn.populate( this.parentObject.getMenuItemId() );
      this.baseElement.append( pconn.render() );

   },

   render: function() {

      return this.baseElement;

   },

   setParent: function( obj ) {

      this.parentObject = obj;

   },

   setPortals: function( list ) {

      this.portalsList = list;

   },

   getPortals: function() {

      return this.portalsList;

   }


});

PortalConnector = $.klass({

   initialize: function() {

      this.classType = 'PortalConnector';

      this.spinner = $( document.createElement( 'img' ) ).
         attr( 'src', 'http://static.repix.no/gfx/admin/img/simpletree/spinner.gif' ).
         hide();

      this.baseElement = $( document.createElement( 'div' ) ).
         attr( 'id', 'menuitemportalconnector');

      this.parentElement = $( document.createElement( 'div' ) ).
         append( this.spinner ).
         append( this.baseElement );

      this.parentObject = null;

   },

   render: function() {

      return this.parentElement;

   },

   populate: function( menu ) {

      this.menuitemid = menu;
      this.getPortalConnections();

   },

   build: function() {

      var self = this;

      var portals = elements.menubox.getTab('tabs01').getObject().portallist.portalList;
      var itemid = portbox = checkfield = contentbox = null;
      for ( var i in portals ) {

         itemid = portals[ i ][ 'id' ];

         portbox = $( document.createElement( 'div' ) );

         checkfield =
            $( document.createElement( 'input' ) ).
            attr( 'id', 'portconn_' + itemid ).
            attr( 'type', 'checkbox' ).
            attr( 'value', itemid );

         for ( var j in this.tmpconnectedportals ) {

            if ( itemid == this.tmpconnectedportals[ j ] ) {

               checkfield.attr( 'checked', 'checked' );

            }

         }

         contentbox =
            $( document.createElement( 'label' ) ).
            attr( 'for', 'portconn_' + itemid ).
            text( portals[ i ][ 'id' ] );

         portbox.append( checkfield ).append( contentbox );

         this.baseElement.append( portbox );

         checkfield.click( function() {

            $.ajax({
                  url: "/menu/api/updateportalconnection",
                  type: 'post',
                  cache: 'false',
                  data: {
                     action: $(this).is(':checked') ? 'add' : 'remove',
                     menu: self.menuitemid,
                     portal: $(this).val(),
                  },
                  dataType: 'json',
                  success: function( data, status ) {

                     //

                  }
               });

            });

      }

   },

   getPortalConnections: function() {

      this.spinner.show();

      var self = this;
      $.ajax({
         url: "/menu/api/fetchitemoptions",
         type: 'post',
         cache: 'false',
         data: {
            menu: this.menuitemid
         },
         dataType: 'json',
         success: function( data, status ) {

            self.parentObject.setPortals( data.portals );
            self.tmpconnectedportals = data.portals;
            self.build();
            self.spinner.hide();

         }
      });

   },

   setParent: function( obj ) {

      this.parentObject = obj;

   }

});

MenuArticleConnector = $.klass({

   initialize: function() {

      this.classType = 'MenuArticleConnector';
      this.parentElement = $( document.createElement( 'div' ) );

      var templateFieldLabel = $( document.createElement( 'span' ) ).
         text( langvars.menuArticleFieldLabel ).
         addClass( 'label' );

      this.baseElement = $( document.createElement( 'select' ) ).
         attr( 'id', 'menuitemarticleconnector');
         
      this.articleLink = $( document.createElement( 'span') );

      this.parentElement.
         append( templateFieldLabel ).
         append( this.baseElement ).
         append( this.articleLink );

      this.parentObject = null;
      this.articleList = [];

   },

   render: function() {

      return this.parentElement;

   },

   populate: function( menu ) {

      this.menuitemid = menu;

      var self = this;
      self.articleList = menuarticles;
      self.build();

   },

   build: function() {

      var opt = null;
      for ( var i in this.articleList ) {

         opt = $( document.createElement( 'option' ) ).attr( 'value', this.articleList[ i ][ 'id' ] ).text( this.articleList[ i ][ 'title' ] );
         // Set selected.
         if ( this.articleList[ i ][ 'id' ] == this.parentObject.parentObject.options.articleid ) {

            opt.attr( 'selected', 'selected' );
            this.articleLink.html(' <a href="/content/articles/' + this.articleList[ i ][ 'id' ] + '">Edit article</a> ');

         }
         this.baseElement.append( opt );

      }

      var self = this;
      this.baseElement.change( function() {
         self.articleLink.html(' <a href="/content/articles/' + self.baseElement.find( 'option:selected' ).val() + '">Edit article</a> ');
         self.updateTemplate( self.baseElement.find( 'option:selected' ).val() );

      });

   },

   updateTemplate: function( id ) {

      var self = this;

      $.ajax({
         url: "/menu/api/updatearticleidconnection",
         type: 'post',
         cache: 'false',
         data: {
            menu: this.menuitemid,
            articleid: id
         },
         dataType: 'json',
         success: function( data, status ) {

            var mitemopt = self.parentObject.parentObject.itemObject;
            mitemopt.setOption( 'articleid', data.updatedarticleid );
            self.parentObject.parentObject.setOptions( mitemopt.options );

         }
      });

   },

   setParent: function( obj ) {

      this.parentObject = obj;

   }

});

TemplateConnector = $.klass({

   initialize: function() {

      this.spinner = $( document.createElement( 'img' ) ).
         attr( 'src', 'http://static.repix.no/gfx/admin/img/simpletree/spinner.gif' ).
         hide();

      this.classType = 'TemplateConnector';
      this.parentElement = $( document.createElement( 'div' ) );

      var templateFieldLabel = $( document.createElement( 'span' ) ).
         text( langvars.templateFieldLabel ).
         addClass( 'label' );

      this.baseElement = $( document.createElement( 'select' ) ).
         attr( 'id', 'menuitemtemplateconnector');

      this.parentElement.
         append( templateFieldLabel ).
         append( this.baseElement ).
         append( this.spinner );

      this.parentObject = null;
      this.templateList = [];

   },

   render: function() {

      return this.parentElement;

   },

   populate: function( menu ) {

      this.spinner.show();

      this.menuitemid = menu;

      var self = this;
      $.ajax({
         url: "/menu/api/fetchtemplates",
         type: 'post',
         cache: 'false',
         data: {
            id: this.menuitemid
         },
         dataType: 'json',
         success: function( data, status ) {

            self.templateList = data.templates;
            self.build();
            self.spinner.hide();

         }
      });

      $(this.parentObject.parentObject.options.translated.urlnames).each( function() {
         $( '#urlname_lang_' + this.code ).val( this.urlname );
      } );

   },

   build: function() {

      var opt = null;
      for ( var i in this.templateList ) {

         opt = $( document.createElement( 'option' ) ).attr( 'value', this.templateList[ i ][ 'id' ] ).text( this.templateList[ i ][ 'title' ] );
         // Set selected.
         if ( this.templateList[ i ][ 'id' ] == this.parentObject.parentObject.options.template ) {

            opt.attr( 'selected', 'selected' );

         }
         this.baseElement.append( opt );

      }

      var self = this;
      this.baseElement.change( function() {

         self.updateTemplate( self.baseElement.find( 'option:selected' ).val() );

      });

   },

   updateTemplate: function( id ) {

      var self = this;

      $.ajax({
         url: "/menu/api/updatetemplateconnection",
         type: 'post',
         cache: 'false',
         data: {
            menu: this.menuitemid,
            template: id
         },
         dataType: 'json',
         success: function( data, status ) {

            var mitemopt = self.parentObject.parentObject.itemObject;
            mitemopt.setOption( 'template', data.updatedtemplate );
            self.parentObject.parentObject.setOptions( mitemopt.options );

         }
      });

   },

   setParent: function( obj ) {

      this.parentObject = obj;

   }

});

URLChanger = $.klass({

   initialize: function() {

      this.classType = 'URLChanger';
      this.baseElement = $( document.createElement( 'div' ) ).
         attr( 'id', 'urlchanger');
      this.parentObject = null;

      this.build();

   },

   build: function() {

      var self = this;

      var urlFieldLabel = $( document.createElement( 'span' ) ).
         text( langvars.urlFieldLabel ).
         addClass( 'label' );

      this.urlFieldElem = $( document.createElement( 'input' ) ).
         attr( 'type', 'text' ).
         attr( 'value', '' );

      var urlButton = $( document.createElement( 'input' ) ).
         addClass( 'button' ).
         attr( 'type', 'button' ).
         attr( 'value', langvars.strSave ).
         click( function() {

            var urlnames = [];
            $('.urlname-translation').each( function() {
               urlnames.push( {
                  code: $(this).attr( 'name' ),
                  urlname: $(this).val()
               } );
            });
            urlnames = $.toJSON(urlnames);

            $.ajax({
               url: "/menu/api/updateitemurl",
               type: 'post',
               cache: 'false',
               data: {
                  menu: self.parentObject.parentObject.getMenuItemId(),
                  newurlname: self.urlFieldElem.val(),
                  translated: urlnames
               },
               dataType: 'json',
               success: function( data, status ) {

                  if ( data.result ) {

                     self.urlFieldElem.val( data.urlname );
                     var mitemopt = self.parentObject.parentObject.itemObject;
                     mitemopt.setOption( 'urlname', data.urlname );
                     mitemopt.setOption( 'url', data.url );
                     self.parentObject.parentObject.setOptions( mitemopt.options );
                     self.parentObject.parentObject.InfoBox.writeUrl();

                     self.parentObject.parentObject.options.translated.urlnames = data.translated;

                     $('.urlname-translation').each( function() {
                        $(this).css( 'backgroundColor', '#ffffff' );
                     } );

                  } else {

                     alert( langvars.strURLisNotUnique );

                  }

               }

            });

         });

      var translateSource = this.urlFieldElem;
      var translateButton = $( document.createElement( 'input' ) ).
         attr( 'type', 'button' ).
         val( langvars.strTranslate ).
         click( function( e ) {

            $('.urlname-translation').each( function() {

               var item = $(this);
               var text = translateSource.val();

               var targetlanguage = $(this).attr('name').split('_')[0];
               if( targetlanguage == 'nb' ) targetlanguage = 'no';
               if( targetlanguage != 'en' ) {

                  google.language.translate( text, 'en', targetlanguage, function(result) {

                     if( result.translation ) {
                        $.ajax({
                           url: "/streams/strings/urlize",
                           type: 'post',
                           cache: 'false',
                           data: {
                              string: result.translation
                           },
                           dataType: 'json',
                           success: function( data, status ) {

                              if( data.result && data.string != '' ) {

                                 item.val( data.string );
                                 item.css( 'backgroundColor', '#00cc00' );

                              } else {

                                 $.ajax({
                                    url: "/streams/strings/urlize",
                                    type: 'post',
                                    cache: 'false',
                                    data: {
                                       string: text
                                    },
                                    dataType: 'json',
                                    success: function( data, status ) {

                                       if ( data.result ) {

                                          item.val( data.string );
                                          item.css( 'backgroundColor', '#00cc00' );

                                       }

                                    }

                                 });

                              }

                           }

                        });

                     }

                  });

               } else {

                  $.ajax({
                     url: "/streams/strings/urlize",
                     type: 'post',
                     cache: 'false',
                     data: {
                        string: text
                     },
                     dataType: 'json',
                     success: function( data, status ) {

                        if ( data.result ) {

                           item.val( data.string );
                           item.css( 'backgroundColor', '#00cc00' );

                        }

                     }

                  });

               }

            } );

         } );

      var imageFieldLabel = $( document.createElement( 'span' ) ).
         text( langvars.imageFieldLabel ).
         addClass( 'label' );

      this.imageFieldElem = $( document.createElement( 'input' ) ).
         attr( 'id', 'image-field-elem' ).
         attr( 'type', 'text' ).
         attr( 'value', '' ).
         attr( 'readonly', '1').
         click( function() {
            $(this).select();
         } ).
         change( function() {
            self.parentObject.parentObject.options.image = $(this).val();
         } );

      this.imageImageElem = $( document.createElement( 'img' ) ).
         attr( 'id', 'image-image-elem' ).
         attr( 'src', '' ).
         attr( 'align', 'right' ).
         css( 'max-width', '100px' ).
         css( 'max-height', '40px' );

      this.imageIFrameElem = $( document.createElement( 'iframe' ) ).
         attr( 'id', 'image-iframe-elem' ).
         attr( 'src', '/menu/upload' ).
         attr( 'frameborder', 0 ).
         attr( 'scrolling', 'no' ).
         css( 'width', '250px' ).
         css( 'height', '30px' );

      var identifierFieldLabel = $( document.createElement( 'span' ) ).
         text( langvars.identifierFieldLabel ).
         addClass( 'label' );

      this.identifierFieldElem = $( document.createElement( 'input' ) ).
         attr( 'type', 'text' ).
         attr( 'value', '' ).
         attr( 'readonly', '1').
         click( function() {

            $(this).select();

         } );
      
      this.identifierLinkElem = $( document.createElement( 'a' ) ).
         attr( 'href', '' ).
         attr( 'target', '_blank' ).
         css('display', 'block').
         css('float', 'right').
         css('padding-right', '5px').
         text( langvars.identifierLinkLabel );

      var scoreFieldLabel = $( document.createElement( 'span' ) ).
         text( langvars.scoreFieldLabel ).
         addClass( 'label' );

      this.scoreSpinnerElem = $( document.createElement( 'img' ) ).
         attr( 'src', 'http://static.repix.no/gfx/admin/img/simpletree/spinner.gif' ).
         hide();

      this.scoreFieldElem = $( document.createElement( 'input' ) ).
         attr( 'type', 'text' ).
         attr( 'value', '' ).
         click( function() {

            $(this).select();

         } ).
         change( function() {
            var elem = $(this);
            self.scoreSpinnerElem.show();
            self.parentObject.parentObject.options.score = elem.val();
            $.post( '/menu/api/updateitemscore', {
               menu: self.parentObject.parentObject.getMenuItemId(),
               score: $(this).val()
            }, function( result ) {
               self.scoreSpinnerElem.hide();
               self.parentObject.parentObject.options.score = result.score;
               elem.val( result.score );
            }, 'json' );
         } );

      this.baseElement.
         append( urlFieldLabel ).
         append( this.urlFieldElem ).
         append( translateButton ).
         append( urlButton ).
         append( $( document.createElement( 'br' ) ) );

      var baseElement = this.baseElement;

      $(alllanguages).each( function() {

         var translationFieldFlag = $( document.createElement( 'img' ) ).
            attr( 'src', 'http://static.repix.no/gfx/flags/'+this.short+'.png' );

         var translationFieldLabel = $( document.createElement( 'span' ) ).
            text( this.title ).
            addClass( 'label' );

         var translationFieldElem = $( document.createElement( 'input' ) ).
            attr( 'id', 'urlname_lang_' + this.code ).
            attr( 'name', this.code ).
            attr( 'rel', this.short ).
            attr( 'type', 'text' ).
            attr( 'value', '' ).
            addClass( 'urlname-translation' );
         
         var AddImagebutton = $( document.createElement( 'input' ) ).
            attr( 'id', 'urlname_lang_' + this.code ).
            attr( 'name', this.code ).
            attr( 'rel', this.short ).
            attr( 'type', 'button' ).
            attr( 'value', 'Add image' ).
            addClass( 'image-translation' ).
            click( function() {
            var elem = $(this);

               $('#uploadlanguage').find( '#filename' ).val( $('#image-field-elem').val() );
               $('#uploadlanguage').find( '#filelang' ).val( $(this).attr('name') );
               $('#uploadlanguage').find( '#menuid' ).val( $("#image-iframe-elem").attr('src') );
               
               
               $('#translatedImage').attr( 'src' , $('#image-image-elem').attr('src') + '/' + $(this).attr('name') );
               
               console.log( $(this).attr('name') );
            
               $('#uploadlanguage').dialog("open");
               
               //alert( $('#image-field-elem').val() );
               return false;
            } );

         baseElement.
            append( translationFieldLabel ).
            append( translationFieldElem ).
            append( translationFieldFlag ).
            append( AddImagebutton ).
            append( $( document.createElement( 'br' ) ) );

      } );

      this.baseElement.
         append( identifierFieldLabel ).
         append( this.identifierFieldElem ).
         append( $( document.createElement( 'br' ) ) ).
         append( scoreFieldLabel ).
         append( this.scoreFieldElem ).
         append( this.scoreSpinnerElem ).
         append( $( document.createElement( 'br' ) ) ).
         append( imageFieldLabel ).
         append( this.imageImageElem ).
         append( this.imageFieldElem ).
         append( $( document.createElement( 'br' ) ) ).
         append( this.imageIFrameElem );
         
      $('.urlbox').append( this.identifierLinkElem );

   },

   render: function() {

      return this.baseElement;

   },

   populate: function() {

      this.urlFieldElem.attr( 'value', this.parentObject.parentObject.options.urlname );
      this.identifierFieldElem.attr( 'value', this.parentObject.parentObject.options.identifier );
      this.identifierLinkElem.attr( 'href', 'http://eurofoto.no/tests/menuitem/'+this.parentObject.parentObject.options.identifier );
      this.scoreFieldElem.attr( 'value', this.parentObject.parentObject.options.score );
      this.imageFieldElem.attr( 'value', this.parentObject.parentObject.options.image );
      this.imageIFrameElem.attr( 'src', '/menu/upload/'+this.parentObject.parentObject.options.id );
      this.imageImageElem.attr('src',request.systemroot + '/images/menu/' + this.parentObject.parentObject.options.image );

      $(this.parentObject.parentObject.options.translated.titles).each( function() {
         $( '#title_lang_' + this.code ).val( this.title );
      } );

   },

   setParent: function( obj ) {

      this.parentObject = obj;

   }

});

function setImageUUID( imageuuid ) {

   $('#image-field-elem').val( imageuuid );
   $('#image-field-elem').change();
   $('#image-field-elem').effect( 'highlight', {}, 3000 );
   $('#image-image-elem').attr('src',request.systemroot + '/images/menu/' + imageuuid );

}
