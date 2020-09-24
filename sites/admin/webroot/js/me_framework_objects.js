var listsAreLoading = false;

ObjectList = $.klass({

   initialize: function( type ) {

      this.groundElement   = $( document.createElement( 'div' ) ).attr( 'class', 'lol' );
      this.baseElement     = $( document.createElement( 'div' ) ).attr( 'id', type + '_list');
      this.contentList     = [];
      this.contentType     = type;
      this.groupHeader     = null;
      this.itemList        = [];
      this.activeGroupList = [];
      this.fetchList();

   },

   fetchList: function() {

      if( listsAreLoading ) {
         var blockself = this;
         setTimeout( function() { blockself.fetchList(); }, 100 );
         return false;
      }

      listsAreLoading = true;

      var myself = this;
      $.ajax({
         url: "/menu/api/fetchcontent",
         type: 'post',
         cache: 'false',
         data: {
            type: this.contentType
         },
         dataType: 'json',
         success: function( data, status ) {

            myself.contentList = data.contentlist;
            myself.build();
            listsAreLoading = false;

         }
      });

   },

   build: function() {

      this.groupHeader = new GroupHeader( this.contentType );
      this.groupHeader.setParent( this );
      this.groundElement.append( this.groupHeader.render() );
      this.groundElement.append( this.baseElement );

      for ( var i in this.contentList ) {

         var groups = this.contentList[i].grouping;

         if( groups == '' || groups == null ) {
            groups = ['nogroup'];
         } else {
            groups = groups.split( ' ' );
         }

         options = {
            id: this.contentList[ i ].id,
            text: this.contentList[ i ].title,
            connected: this.contentList[ i ].connected,
            groups: groups
         }

         newItem = new ObjectItem( options );
         newItem.setParent( this );

         this.itemList.push( newItem );
         this.baseElement.append( newItem.render() );

      }

      this.filterGroups( 'all', true );

   },

   render: function() {

      return this.groundElement;

   },

   trigger: function( tag ) {

      for( var i in this.contentList ) {

         console.log( this.contentList[ i ] );


      }

   },

   filterGroups: function( group, activated ) {

      // Add to actiaved-list if not existing from before
      if( jQuery.inArray( group, this.activeGroupList ) == '-1' && group != 'all' ) {

         if( activated ) {
            this.activeGroupList.push( group );
         } else {
            return false;
         }

      // Drop from list
      } else if( !activated ) {

            var pos = jQuery.inArray( group, this.activeGroupList );
            this.activeGroupList.splice( pos, 1 );

      }

      for( var i in this.itemList ) {
         this.itemList[ i ].baseElement.addClass( 'disabled' );
      }

      for( var i in this.activeGroupList ) {

         // If found in any of the active groups, enable product view
         for( var o in this.itemList ) {

            // If found in active list
            if( jQuery.inArray( this.activeGroupList[ i ], this.itemList[ o ].options.groups ) != '-1' ) {
               this.itemList[ o ].baseElement.removeClass( 'disabled' );
            }

         }

      }

      if( this.activeGroupList.length == 0 || group == 'all' ) {
         for( var i in this.itemList ) {
            this.itemList[ i ].baseElement.removeClass( 'disabled' );
         }
      }

   },

   setParent: function( obj ) {

      this.parentObject = obj;

   }

});

ObjectItem = $.klass({

   initialize: function( options ) {

      this.options = options;
      this.baseElement = $( document.createElement( 'div' ) );
      this.baseElement.get(0).baseObject = this;
      this.parentObject = null;

   },

   render: function() {

      this.baseElement.
         attr( 'id', 'product_' + this.options[ 'id' ] ).
         addClass( 'productitem' ).
         text( this.options[ 'text' ] ).
         addClass( this.options['connected'] ? 'used' : '' ).
         addClass( 'disabled' );

      if ( !islockedforedit ) {

         this.baseElement.draggable({
            revert: false,
            cursor: 'move',
            helper: 'clone',
            start: function( e, ui ) {

               $(this).addClass( 'addablecontent' );

            }
         });

      }

      return this.baseElement;

   },

   setParent: function( obj ) {

      this.parentObject = obj;

   }

});

var groupsAreLoading = false;

GroupHeader = $.klass({

   initialize: function( type ) {

      this.baseElement  = $( document.createElement( 'div' ) ).attr( 'id', type + '_grouplist' );
      this.groups       = $( document.createElement( 'ul' ) ).addClass( 'groups' );
      this.groupList    = [];
      this.baseType     = type;
      this.parentObject = null;
      this.getGroups();

   },

   render: function() {

      return this.baseElement;

   },

   getGroups: function() {

      if( groupsAreLoading ) {
         var blockself = this;
         setTimeout( function() { blockself.getGroups();}, 100 );
         return false;
      }

      groupsAreLoading = true;

      self = this;
      $.ajax( {
         url: "/menu/api/fetchentitygroups/grouplist/"+self.baseType,
         type: 'post',
         cache: 'false',
         data: {
            type: self.baseType
         },
         dataType: 'json',
         success: function( data, status ) {

            data.list.push( 'nogroup' );
            self.groupList = data.list;
            self.build();
            groupsAreLoading = false;

         }

      } );

   },

   build: function() {

      var self = this;
      for( var i in this.groupList ) {

         var groupItem = $( document.createElement( 'li' ) );
         var group = $( document.createElement( 'a' ) );
         group.attr( {

            href: '#',
            id: this.baseType + '_' + this.groupList[ i ],
            'class': 'group'

         } ).bind( 'click', function( e ) {

            $(this).toggleClass( 'active' );
            self.parentObject.filterGroups( $(this).text(), $(this).hasClass( 'active' ) );

         } );

         group.text( this.groupList[ i ] );
         groupItem.append( group );

         this.groups.append( groupItem );

      }
      this.baseElement.append( '<b>Groups:</b>' );
      this.baseElement.append( this.groups );
      this.baseElement.append( '<hr style="margin-bottom: 0">' );

   },

   setParent: function ( obj ) {

      this.parentObject = obj;

   }

});