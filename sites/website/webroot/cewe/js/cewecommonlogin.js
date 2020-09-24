// programming library
var CommonLogin =
{
   cdnHost: '/cewe',
   callback: false,
   opencart: false,
   hasinit: false,
   status: 
   {
      token: '',
      sign:  '',
      loggedIn: false,
      userData: {
         'login': '',
         'fullname': ''
      }
   },
   cart: {
	   'total': 0.00,
      'items': 0
   },
   reDraw: function(){
   	
   	document.getElementById('cewe_common_header_container').innerHTML =
   			'<div class="shared-header">'+
					'<div class="container">'+
						'<div class="logo col-md-6 col-sm-4"><a href="/"><img class="img-responsive" src="/tmp/logo2016.png" /></a></div>'+
				        '<div class="actions" id="shMenu">'+
				        	'<ul>'+
			            	 '<li><a class="chat" href="javascript:;" onclick="CommonLogin.openChat();">LiveChat</a></li>'+
			                '<li><a class="support" href="/hjelp">Kundeservice</a></li>'+
			                (this.status.loggedIn == true ? ('<li><a class="login" href="/myaccount/">Min konto</a></li>'):('<li><a class="login" href="javascript:;" onclick="CommonLogin.login();">Logg inn</a></li>'))+
	 (this.status.loggedIn == true ? ('<li><a class="register" href="javascript:;" onclick="CommonLogin.logout();">Logg ut</a></li>'):('<li><a class="register" href="javascript:;" onclick="CommonLogin.login();">Registrer</a></li>'))+
			         
			            '</ul>'+
				        '</div>'+
				        '<div class="col-md-6 col-sm-8 sh-shopcart-container">'+
                           '<div class="col-md-6 col-md-push-2 sh-contact">'+
				                '<div>57 88 35 00</div> <div class="phone">Man - fre: <span>09 - 22:00</span><br />Søndag: <span>14 - 22:00</span></div>'+
				            '</div>'+
				            '<div class="col-md-6"><a class="sh-shopcart" href="javascript:;" onclick="CommonLogin.openCart();">'+
				                (this.status.loggedIn == true ? ('Handlekurv: <span>'+this.cart.items+' produkt(er)</span>'):(''))+
				            '</a></div>'+
				        '</div>'+
				    '</div>'+
				'</div>';
				
   },
   setStatus: function( status ) {
   	this.status = status;
   	if( this.hasinit ) {
   		
   		this.reDraw();
   		
   		CommonLogin.setCookie( 'CWut', CommonLogin.status.token );
	      CommonLogin.setCookie( 'CWus', CommonLogin.status.sign );
	      
	      if( this.callback != undefined ) {
	         this.callback( CommonLogin.status );
	      }
	      
   	}
   },
   init: function( callback, cart ) {
      
      document.write( '<div id="cewe_common_header_container"></div>' );
      
      this.callback = callback;
      this.setCart( cart );
      this.hasinit = true;
      this.setStatus( this.status );
      
      if (window.addEventListener) {
		    window.addEventListener("message", CommonLogin.setExternalStatus, false);
		} else {
		    window.attachEvent("onmessage", CommonLogin.setExternalStatus);
		}
      
   },
   resizeWindow: function(iframesize){
   	var el = document.getElementById( 'dialogwindow' );
      if( el && iframesize.height && iframesize.width ) {
      	el.style.width = iframesize.width+'px';
      	el.style.height = iframesize.height+'px';
      }
   },
   pullStatus: function(){
   	this.hasinit = true;
		var body = document.getElementsByTagName('body');
		var script = document.createElement('script');
		    script.language = 'JavaScript';
		    script.src = 'https://www.japanphoto.no/StatusJsView?storeId=10151&langId=124&catalogId=10051';
		    body[0].appendChild( script );		
   },
   setExternalStatus: function(event){
		console.log( event.data );   	
   	switch( event.data.action ) {
   		case 'closeDialog':
   			CommonLogin.closeWindow();
				break;   		
   		case 'reloadPage':
   			window.location.reload();
				break;   		
   		case 'pullStatus':
   			CommonLogin.pullStatus();
   			break;   		
   		case 'setStatus':
   			CommonLogin.setStatus( event.data );
   			CommonLogin.closeWindow();
   			break;   		
   		case 'resizeDialog':
   			CommonLogin.resizeWindow( event.data );
   			break;
   		default:
   			if( event.data.token ) {
	   			CommonLogin.setStatus( event.data );
	   			CommonLogin.closeWindow();
   			}
   			break;
   	}
   	
   },
   setCart: function(cart, redraw) {
   	this.opencart = cart.open;
      this.cart.total = cart.total;
      this.cart.items = cart.items;
      if( redraw ) this.reDraw();
   },
   login: function() {
   	CommonLogin.openDialog( 'https://www.japanphoto.no/webapp/wcs/stores/servlet/LogonFormSharedHeaderView?catalogId=10051&langId=124&storeId=10151', 800, 800 );
      return false;
   },
   logout: function() {
   	window.location = 'https://www.japanphoto.no/Logoff?catalogId=10051&myAcctMain=1&langId=124&storeId=10151&URL='+encodeURIComponent(window.location.href);
      return false;
   },
   openChat: function(){
		
	  window.open( 'https://islpronto.islonline.net/live/islpronto/start.html?_s=1&filter=Bilder&lang=no&location=http%3a%2f%2fwww.eurofoto.no%2f&mobile=0&referrer=&template=d%3as2_0_427', 'livechat', "width=800,height=600,menubar=0,location=0,status=0,toolbar=0,resizable=1,scrollbars=1" );
      return false;
      
   },
   openCart: function() {
      if( this.opencart ) {
      	this.opencart();
      } else {
   		CommonLogin.openDialog( 'https://www.japanphoto.no/webapp/wcs/stores/servlet/AjaxOrderItemDisplayView?catalogId=10051&langId=124&storeId=10151', 800, 800 );
      }
      return false;
   },
   openDialog: function( url, height, width, hideclosebutton ) {
      
      var element = document.getElementById( this.id );
      
      if ( element == null ) {
         
         var overlay = document.createElement('div');
             overlay.id = 'dialogwindowcontainer';
             overlay.onclick = this.closeWindow;
             overlay.style.cursor = 'pointer';
             
         var dialog = document.createElement('div');
             dialog.className = 'dialogwindow spinner';
             dialog.id = 'dialogwindow';
             dialog.style.width = parseInt(width) + 'px';
             dialog.style.height = parseInt(height) + 'px';
         
         var timeout = (function(){
			 		iframe.style.display = 'block';
			 		dialog.className = 'dialogwindow';
				 });
             
         var iframe = document.createElement('iframe');
             iframe.allowTransparency = "true";
				 iframe.style.backgroundColor = "transparent";
				 iframe.style.display = 'none';
				 iframe.frameBorder = "0";
				 iframe.id = 'dialogframe';
				 iframe.onload = timeout;
         	 iframe.src = url;
         	 
      	setTimeout( timeout, 2500 );
			
			dialog.appendChild( iframe );
				 
		   /*
		   var closeLink = document.createElement('a');
		       closeLink.href = 'javascript:CommonLogin.closeWindow();';
		       closeLink.className = 'close-button';
		       closeLink.innerText = 'Close';
	      */
			
			// dialog.appendChild( closeLink );
			overlay.appendChild( dialog );
			document.body.appendChild( overlay );
		   
			element = document.getElementById( 'dialogwindowcontainer' );
         element.style.height = document.body.scrollHeight+'px';
         element.style.visibility = 'visible';
         
         overlay.style.top = document.body.scrollTop;
         
      } else {
         
         element.style.visibility = 'visible';
         
      }
      
   },
   
   closeWindow: function() {
      var el = document.getElementById( 'dialogwindowcontainer' );
      if( el ) {
      	el.style.visibility = 'hidden';
      	el.parentNode.removeChild( el );
	      if( this.callback && this.hasinit ) {
	      	 this.callback( this.status );
	      }
      }
   },
	
   setExpDate: function() {
      var today = new Date();
      var expire = new Date(today.getTime() + 365 * 24 * 60 * 60 * 1000);
      expire = expire.toGMTString();
      return expire;
   },

   setCookie: function( cookieName, cookieValue ) {
      var cookieLife = CommonLogin.setExpDate();
      document.cookie = cookieName+ "=" +cookieValue+ "; expires=" +cookieLife;
   },

   setSessionCookie: function( cookieName, cookieValue ) {
      document.cookie = cookieName+ "=" +cookieValue;
   },

   getCookie: function( name ) {
      var currentCookie = document.cookie;
      var index = currentCookie.indexOf(name + "=");
      if (index == -1) return null;
      index = currentCookie.indexOf("=", index) + 1; // first character 
      var endstr = currentCookie.indexOf(";", index);
      if (endstr == -1) endstr = currentCookie.length; // last character 
      return unescape(currentCookie.substring(index, endstr));
   },
   
   toggleVisibility: function (id) {
   	var e = document.getElementById(id);
   	if (e.style.display == 'none' || e.style.display=='') e.style.display = 'block';
   	else e.style.display = 'none';
	}

}

// inject CSS into header
var link = document.createElement("link");
    link.href = CommonLogin.cdnHost+'/css/commonheader.css?1';
    link.type = 'text/css';
    link.rel = 'stylesheet';
    document.getElementsByTagName("head")[0].appendChild(link);