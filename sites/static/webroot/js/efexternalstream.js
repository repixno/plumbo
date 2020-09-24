function __efWrapper( element ) {
   
   this.elm = element;
   this.support = null;
   this.init();
   
}

__efWrapper.prototype = {
   
   init: function() {
   
      this.agentSupport();
      this.elm.style.position = 'relative';
      
   },

   addLogo: function() {
    
      var logoHeight = 70;
    
      var lbox = document.createElement( 'div' );
      var csslist1 = {
         position: 'absolute',
         width: parseInt( this.elm.style.width ) + 'px',
         height: logoHeight + 'px',
         left: '0px',
         top: ( parseInt( this.elm.style.height ) - logoHeight) + 'px',
         backgroundColor: 'white',
         opacity: 0.0,
         display: 'none'
      }
      __efWrapper.styling.setStyle( lbox, csslist1 );
   
      var limg = document.createElement( 'img' );
      limg.src = 'http://d.static.repix.no/css/portals/eurofoto/gfx/eurofoto-68px_jubileum2.gif';
      lbox.appendChild( limg );
   
      this.elm.appendChild( lbox );

      var csslist2 = {
         cssFloat: 'right',
         paddingRight: '5px'
      }
      __efWrapper.styling.setStyle( limg, csslist2 );
      
      this.bindEvent( this.elm, 'mouseover', function() {
   
         /*self.setStyle( lbox, {
            display: 'block'
         });*/
         __efWrapper.effect.show( lbox );
          
      });
   
      this.bindEvent( this.elm, 'mouseout', function() {
          
         /*__efWrapper.styling.setStyle( lbox, {
            display: 'none'
         });*/
         __efWrapper.effect.hide( lbox );
          
      });
   
   },
   
   agentSupport: function() {
    
      // From jQuery.
      var root = document.documentElement,
         div = document.createElement("div");
      
      div.style.display = "none";
      div.innerHTML = "   <link/><table></table><a href='/a' style='color:red;float:left;opacity:.55;'>a</a><input type='checkbox'/>";
      a = div.getElementsByTagName("a")[0];
    
      __efWrapper.support = {
         opacity: /^0.55$/.test( a.style.opacity ),
         cssFloat: !!a.style.cssFloat
      };
    
   },
   
   bindEvent: function( element, event, func ) {
   
      if ( typeof element.attachEvent != 'undefined' )
         element.attachEvent( 'on' + event, func );
      if ( typeof element.addEventListener != 'undefined' )
         element.addEventListener( event, func, false );
    
   },
   
   unbindEvent: function( element, event, func ) {
   
      if ( typeof element.attachEvent != 'undefined' )
         element.attachEvent( 'on' + event, func );
      if ( typeof element.addEventListener != 'undefined' )
         element.addEventListener( event, func, false );
    
   }

   
    
    
    
};

__efWrapper.styling = {
   
   setStyle: function( element, style ) {

      for ( id in style ) {

         switch ( id.toLowerCase() ) {

            case 'cssfloat':
               if ( __efWrapper.support.cssFloat )
                  element.style[ id ] = style[ id ];
               else
                  element.style[ 'styleFloat' ] = style[ id ];
               break;
          
            case 'opacity':

               if ( __efWrapper.support.opacity ) {
                  element.style.opacity = style[ id ];
               }
               else {
                  if ( element.filter ) {
                     if ( !element.currentStyle.hasLayout ) element.style.zoom = 1;
                     element.style.filter = "alpha(opacity=" + (style[ id ]*100) + ")";
                  }
                  //element.filters.item("DXImageTransform.Microsoft.Alpha").Opacity = style[ id ]*100;
               }
               break;
       
            default: 
               element.style[ id ] = style[ id ];
               break;
          
         }
       
      }
    
   },
   
   getStyle: function( element, stylename ) {
      
      return element.style[ stylename ];
      
   }
   
}

__efWrapper.effect = {
   
   stdinterval: 13,
   animstart: null,
   animstop: null,
   animdirup: null,
   element: null,
   
   hide: function( element ) {
      
      //__efWrapper.styling.setStyle( element, { display: 'none' } );
      //__efWrapper.styling.setStyle( element, { opacity: 0.0 } );
      this.animate( element, 0.5, 0.0 );
      
   },
   
   show: function( element ) {

      //__efWrapper.styling.setStyle( element, { display: 'block' } );
      this.animate( element, 0.0, 0.5 );
      
   },
   
   animate: function( element, start, stop ) {

      this.animstart = start;
      this.animstop = stop;
      this.animdirup = start < stop ? true : false;
      this.element = element;
      __efTimerId = setInterval( __efWrapper.effect.doAnim, 100 );
      
   },
   
   doAnim: function() {

      var element = __efWrapper.effect.element;
      var animstart = __efWrapper.effect.animstart;
      var animstop = __efWrapper.effect.animstop;
      
      var animend = false;
      if ( __efWrapper.effect.animdirup && animstart >= animstop ) animend = true;
      if ( !__efWrapper.effect.animdirup && animstart <= animstop ) animend = true;
      
      if ( animend ) {
         
         //__efWrapper.styling.setStyle( element, { display: 'none' } );
         clearInterval( __efTimerId );
         __efTimerId = null;
         __efWrapper.effect.animstart = null;
         __efWrapper.effect.animstop = null;
         __efWrapper.effect.element = null;
         
      } else {
      
         if ( __efWrapper.styling.getStyle( element, 'display' ) !== 'block' )
            __efWrapper.styling.setStyle( element, { display: 'block' } );
         
         var curPos = parseFloat( __efWrapper.styling.getStyle( element, 'opacity' ) );

         animstart = __efWrapper.effect.animdirup ? curPos + 0.1 : curPos - 0.1;

         __efWrapper.styling.setStyle( element, { opacity: animstart } );
         __efWrapper.effect.animstart = animstart;
         
      }
      
   }
   
};

var __efTimerId = null;

var __efLoaderScript = function() {
   
   function init() {

      var elements = getElementsByClassName( 'eurofoto-image' );
      for ( var i = 0; i < elements.length; i++ ) {
      
         el = new __efWrapper( elements[ i ] );
         el.addLogo();
      
      }
      
   }
   return {
      init: init
   };
}();

if ( typeof window.attachEvent != 'undefined' )
   window.attachEvent( 'onload', __efLoaderScript.init );
else if ( typeof window.addEventListener != 'undefined' )
   window.addEventListener( 'load', __efLoaderScript.init, false );

/*
	Developed by Robert Nyman, http://www.robertnyman.com
	Code/licensing: http://code.google.com/p/getelementsbyclassname/
*/	
var getElementsByClassName = function (className, tag, elm){
	if (document.getElementsByClassName) {
		getElementsByClassName = function (className, tag, elm) {
			elm = elm || document;
			var elements = elm.getElementsByClassName(className),
				nodeName = (tag)? new RegExp("\\b" + tag + "\\b", "i") : null,
				returnElements = [],
				current;
			for(var i=0, il=elements.length; i<il; i+=1){
				current = elements[i];
				if(!nodeName || nodeName.test(current.nodeName)) {
					returnElements.push(current);
				}
			}
			return returnElements;
		};
	}
	else if (document.evaluate) {
		getElementsByClassName = function (className, tag, elm) {
			tag = tag || "*";
			elm = elm || document;
			var classes = className.split(" "),
				classesToCheck = "",
				xhtmlNamespace = "http://www.w3.org/1999/xhtml",
				namespaceResolver = (document.documentElement.namespaceURI === xhtmlNamespace)? xhtmlNamespace : null,
				returnElements = [],
				elements,
				node;
			for(var j=0, jl=classes.length; j<jl; j+=1){
				classesToCheck += "[contains(concat(' ', @class, ' '), ' " + classes[j] + " ')]";
			}
			try	{
				elements = document.evaluate(".//" + tag + classesToCheck, elm, namespaceResolver, 0, null);
			}
			catch (e) {
				elements = document.evaluate(".//" + tag + classesToCheck, elm, null, 0, null);
			}
			while ((node = elements.iterateNext())) {
				returnElements.push(node);
			}
			return returnElements;
		};
	}
	else {
		getElementsByClassName = function (className, tag, elm) {
			tag = tag || "*";
			elm = elm || document;
			var classes = className.split(" "),
				classesToCheck = [],
				elements = (tag === "*" && elm.all)? elm.all : elm.getElementsByTagName(tag),
				current,
				returnElements = [],
				match;
			for(var k=0, kl=classes.length; k<kl; k+=1){
				classesToCheck.push(new RegExp("(^|\\s)" + classes[k] + "(\\s|$)"));
			}
			for(var l=0, ll=elements.length; l<ll; l+=1){
				current = elements[l];
				match = false;
				for(var m=0, ml=classesToCheck.length; m<ml; m+=1){
					match = classesToCheck[m].test(current.className);
					if (!match) {
						break;
					}
				}
				if (match) {
					returnElements.push(current);
				}
			}
			return returnElements;
		};
	}
	return getElementsByClassName(className, tag, elm);
};
