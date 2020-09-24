TabsBox = $.klass({

   initialize: function( element ) {

      this.tabs = {};
      this.newElement = false;

      if ( element ) {

         this.baseElement = $( element );

      } else {

         this.baseElement = $( document.createElement( 'div' ) );
         this.newElement = true;

      }

   },

   addTab: function( options ) {

      this.tabs[ options.id ] = new TabsBoxTab( options );
      return this.tabs[ options.id ];

   },

   build: function() {

      this.wrapperBox = $( document.createElement( 'div' ) );
      this.wrapperBox.addClass( 'menutabs' );

      var tabList = $( document.createElement( 'ul' ) );

      for ( var id in this.tabs ) {

         tabList.append( this.tabs[ id ].renderTab() );
         this.wrapperBox.append( this.tabs[ id ].renderContent() );

      }

      this.wrapperBox.prepend( tabList );

      this.baseElement.append( this.wrapperBox );

   },

   render: function() {

      this.build();

      if ( this.newElement ) {

         return this.baseElement;

      } else {

         this.tabObjs = this.wrapperBox.tabs();
         this.postRenderMembers();

      }

   },

   show: function() {

      this.tabObjs = this.wrapperBox.tabs();

   },

   postRenderMembers: function() {

      for ( var id in this.tabs ) {

         if ( this.tabs[id].getObject().postRender ) {

            this.tabs[id].getObject().postRender();

         }

      }

   },

   getTab: function( id ) {

      return this.tabs[ id ];

   },

   select: function( id ) {

      this.tabObjs.tabs( 'select', id );

   }

});

TabsBoxTab = $.klass({

   initialize: function( options ) {

      this.options = options;
      this.id = options.id;
      this.baseElement = null;

   },

   renderTab: function() {

      this.tab = $( document.createElement( 'li' ) );
      var link =
         $( document.createElement( 'a' ) ).
         attr( 'href', '#' + this.id ).
         text( this.options.title );

      return this.tab.append( link );

   },

   renderContent: function() {

      this.baseElement = $( document.createElement( 'div' ) ).attr( 'id', this.id );
      if ( this.options.content ) {

         this.baseElement.html( this.options.content );

      }
      else if ( this.options.object ) {

         this.baseElement.append( this.options.object.render() );
         this.options.object.setParent( this );

      }
      else {

         this.baseElement.html( '<p>'+langvars.strNoContentAvail+'</p>' );

      }

      return this.baseElement;

   },

   getObject: function() {

      return this.options.object;

   }

});
