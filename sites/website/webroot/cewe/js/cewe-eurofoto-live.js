/**
 *@class AllcopWidget Integrationcode for CEWE shopsystem loading the editor into an iframe and handling interframe messaging and resizing
 *@function AllcopWidget creates the class
 *@parameter partnerIframeConfig Object Configdata for creating the iframe given from CEWE container
 */
window.EurofotoWidget = function(partnerIframeConfig) {
  
  var debug = ("true" === "false");
  
  if (debug) {
    console.log("aoc.js.integration.CeweIframeIntegrator", partnerIframeConfig);
  }

  var url = "https://jp.eurofoto.no/";
  
  
  // copy all data from the transfer object
  var eurofotoProductId = partnerIframeConfig.eurofoto_article_id;
  var eurofotoCartProjectId = partnerIframeConfig.project_id;
  var sci = partnerIframeConfig.sci;
  var quantity = partnerIframeConfig.quantity;
  var addToCart = partnerIframeConfig.add_to_cart;
  var goToCartUrl = partnerIframeConfig.cart_url;
  var loginUrl = partnerIframeConfig.login_url;
  var registerUrl = partnerIframeConfig.register_url;
  var getOtatUrl = partnerIframeConfig.get_otat_url;
  var verifyOtatUrl = partnerIframeConfig.verify_otat_url;
  var goToProfileUrl = partnerIframeConfig.profile_url;
  var logoutUrl = partnerIframeConfig.logout_url;
  var containerId = partnerIframeConfig.container_id;
  var lostPasswordUrl = partnerIframeConfig.lost_password_url;
  //var openProjectUrl = partnerIframeConfig.open_project_url.replace("xhtml", "jsf");
  var refreshIPS = partnerIframeConfig.refresh_ips;
  
  var userEmail = null;
  var userDisplay = null;
  
  function getQueryVariable(variable) {
    var query = window.location.search.substring(1);
    var vars = query.split('&');
    for (var i = 0; i < vars.length; i++) {
        var pair = vars[i].split('=');
        if (decodeURIComponent(pair[0]) == variable) {
            return decodeURIComponent(pair[1]);
        }
    }
    return null;
  }
  
  /* retrieve project as get parameter in case of project list redirect */
  var eurofotoProjectId = getQueryVariable("project");
  var eurofotoProjectMode = getQueryVariable("mode");
  
  var eurofotoProduct = getQueryVariable("pfbProduct");
  if (eurofotoProduct) {
    eurofotoProductId = eurofotoProduct;
  }
  
  //DMO-206 support prolong project
  var eurofotoProlongProject = getQueryVariable("prolong");

  /**
   *@function handleResize handle window resizes
   */
  this.handleResize = function(){
    $("#eurofoto-frame").height($(window).height()-40);
  };
  
  /**
   *@function updateUserData update objects internal user data
   */
  this.updateUserData = function(email, display){
    //handle update for email
    if (email) {
      userEmail = email;
    }
    else {
      userEmail = (userEmail) ? userEmail : (window.IPS) ? window.IPS.getUserEmailAddress() : "";
    }
    
    //handle update for display
    if (display) {
      userDisplay = display;
    }
    else {
      
      if (!userDisplay) {
       var ipsUser =  (window.IPS) ? window.IPS.getUserName() : "";
       if (ipsUser && ipsUser.firstName && ipsUser.firstName.length > 0 && ipsUser.lastName && ipsUser.lastName.length > 0 ) {
         userDisplay = ipsUser.firstName + " " + ipsUser.lastName;
       }
       else {
         userDisplay = userEmail;
       }
      }
    }
  };
  
  /**
   *@function handleMessageFromChild callback function for handling inter window communication
   *@parameter event MessageEvent the jquery message event - careful message data is to be retrieved from event.originalEvent
   */
  this.handleMessageFromChild = function(event){
    var self = this;
    
    if (debug) {
      console.log("aoc.js.integration.CeweIframeIntegrator.handleMessageFromChild", event);
    }

    //Origin Check (XSS)
    if (event.originalEvent.origin !== 'https://jp.eurofoto.no' ){
      console.log("invalid origin! - " + event.originalEvent.origin);
      return;
    }
    
    //get data from event
    var data = JSON.parse(event.originalEvent.data);
    
    if (debug) {
      console.log("DATA", data);
    }
    
    //handle action
    if (data.action === "addToCart") {
      if (debug) {
        console.log("addToCart");
      }
      
      data.callback = this.handleMessageFromContainer;
      
      //var items = {items:[{"eurofoto_article_id": 7193, "quantity": 2, "project_id": "1234567-12334-1223", "thumb_url": "https://some.thumb.nail/1234567-12334-1223.jpg"}, {"eurofoto_article_id": 7194, "project_id": "1234567-12334-1223", "thumb_url": "https://some.thumb.nail/1234567-12334-1223.jpg"}]};
      
      addToCart(data);
    }
    else if (data.action === "goToCart") {
      if (debug) {
        console.log("goToCart", goToCartUrl);
      }
      window.location = goToCartUrl;
    }
    else if (data.action === "goToUrl") {
      if (debug) {
        console.log("goToCart", goToCartUrl);
      }
      window.location = data.url;
    }
    else if (data.action === "loadProject") {
      if (debug) {
        console.log("loadProject", data);
      }
      
      var projectUrl = openProjectUrl + "?productId=7351&project=" + data.project + "&pfbProduct=" + data.product;
      
      if (data.preview === true) {
        projectUrl += "&mode=preview";
      }
      
      if (debug) {
        console.log("OPENING PROJECT", projectUrl);
      }
      
      window.location = projectUrl;
    }
    else if (data.action === "contentHeight") {
      if (debug) {
        console.log("contentHeight", data);
      }
      $("#eurofoto-frame").height(parseInt(data.height));
    }
    else if (data.action === "refreshSession") {
      if (debug) {
        console.log("refreshSession");
      }
      refreshIPS({
        //success
        success : function () {
          
          if (debug) {
            console.log("SUCCESS", data);
          }
          
          userEmail = null;
          userDisplay = null;
          self.updateUserData();
          
          data = {
            email : userEmail,
            display : userDisplay,
            ceweCartId : window.IPS.getShoppingCartId(), //DMO-184
            action : "refreshSessionSuccess"
          };
          
          if (debug) {
            console.log("SUCCESS2", data);
          }
          
          self.sendMessageToChild(data);
        },
        //error
        error : function () {
          
          if (debug) {
            console.log("ERROR");
          }
          
          var data = {
            action : "refreshSessionError"
          };
          self.sendMessageToChild(data);
        }
      });
    }
    else if (data.action === "register") {
      
      if (debug) {
        console.log("register", data);
      }
      
      //synch with server
      $.ajax(registerUrl, { 
          type  : 'post',
          cache : false,
          dataType: 'json',
          data : {
            register : true,
            appId : "eurofoto",
            "ssl-login" : data.loginName,
            "ssl-password": data.loginPassword
          },
          success: function (data) {
            
            if (debug) {
              console.log("aoc.js.integration.CeweIframeIntegrator.handleMessageFromChild.register:SUCCESS", data);
            }
            
            self.updateUserData(data.login, data.user);
            
            data.email = userEmail,
            data.display = userDisplay,
            data.action = "registerSuccess";
            self.sendMessageToChild(data);
            self.updateOtat();
          },
          error : function (data) {
            
            if (debug) {
              console.log("aoc.js.integration.CeweIframeIntegrator.handleMessageFromChild.register:ERROR", data);
            }
            
            data.action = "registerError";
            self.sendMessageToChild(data);
          }
      });
    }
    else if (data.action === "goToProfile") {
      
      if (debug) {
        console.log("goToProfile");
      }
      
      window.location = goToProfileUrl;
    }
    else if (data.action === "goToLostPassword") {
      
      if (debug) {
        console.log("goToLostPassword");
      }
      
      //ATTENTION: open in new tab can cause browser to warn for popups or the browser prevents it like IE does most of the time
      window.open(lostPasswordUrl, "_blank");
    }
    else if (data.action === "login") {
      
      if (debug) {
        console.log("login", data);
      }
      
      //synch with server
      $.ajax(loginUrl, { 
          type  : 'post',
          cache : false,
          dataType: 'json',
          data : {
            "ssl-login" : data.loginName,
            "ssl-password": data.loginPassword
          },
          success: function (data) {
            
            if (debug) {
              console.log("SUCCESS", data);
            }
            
            self.updateUserData(data.login, (data.user) ? data.user : data.login);
            
            data.email = userEmail,
            data.display = userDisplay,
            data.action = "loginSuccess";
            self.sendMessageToChild(data);
            self.updateOtat();
          },
          error : function (data) {
            
            if (debug) {
              console.log("ERROR", data);
            }
            
            data.action = "loginError";
            self.sendMessageToChild(data);
          }
      });
    }
    else if (data.action === "logout") {
      
      if (debug) {
        console.log("logout", data);
      }
      
      //synch with server
      $.ajax(logoutUrl, { 
          type  : 'post',
          cache : false,
          dataType: 'json',
          success: function (data) {
            
            if (debug) {
              console.log("SUCCESS", data);
            }
            
            data.action = "logoutSuccess";
            self.sendMessageToChild(data);
          },
          error : function (data) {

            if (debug) {
              console.log("ERROR", data);
            }
            
            data.action = "logoutError";
            self.sendMessageToChild(data);
          }
      });
    }
    else if (data.action === "getOtat"){
      if (debug) {
        console.log("getOtat", data);
      }

      this.updateOtat();
    }
  };
  
  this.updateOtat = function(){
    var self = this;
    //synch with server
    $.ajax(getOtatUrl, { 
        type  : 'post',
        cache : false,
        dataType: 'json',
        success: function (data) {
          
          if (debug) {
            console.log("SUCCESS", data);
          }
          
          self.updateUserData();
          
          data.action = "getOtatSuccess";
          data.verifyUrl = verifyOtatUrl;
          data.email = userEmail,
          data.display = userDisplay,
          self.sendMessageToChild(data);
        },
        error : function (data) {

          if (debug) {
            console.log("ERROR", data);
          }
          
          data.action = "getOtatError";
          self.sendMessageToChild(data);
        }
    });
  };
  
  /**
   *@function sendMessageToChild send data to child iframe
   */
  this.sendMessageToChild = function (data){
    if (debug) {
      console.log("sendMessageToChild", data);
    }
    
    document.getElementById("eurofoto-frame").contentWindow.postMessage(JSON.stringify(data), "*");
  };
  
  /**
   *@function handleMessageFromContainer callback for events from cewe container - will post data to iframe
   */
  this.handleMessageFromContainer = function (){
    if (debug) {
      console.log("handleMessageFromContainer");
    }
    
    var data = {};
    data.status = "success";
    data.action = "addToCartSuccess";
    
    if (debug) {
      console.log("Data", data);
    }
    
    document.getElementById("eurofoto-frame").contentWindow.postMessage(JSON.stringify(data), "*");
  };
  
  /**
   *@function render will be called by CEWE container for initialization - set up iframe, add listeners
   */
  this.render = function(){
    
    
    $('div.ips_content').attr( "style",  "width: 100% !important" );
    
    if (debug) {
      console.log("aoc.js.integration.CeweIframeIntegrator.render", eurofotoProductId );
    }
    var parentElement;
    var iframeHTML;
    var self = this;

    //create the editor url
    var productUrl = ""
    
    
    switch( eurofotoProductId ){
      case 7193:
      case 7195:
         productUrl = url + 'bestilling'
        break;
      case 7262:
      case 7261:
        productUrl = url + 'bestilling/fargelapp'
        break;
      case 7374:
      case 7376:
        productUrl = url + 'bestilling/stempel'
        break;
      case 7454:
        productUrl = url + 'studentplakat'
        break;
        case 7455:
        productUrl = url + 'komplett'
        break;
      case 7461:
        productUrl = url + 'klassisktplakat'
        break;
      case 7481:
      case 7476:
         productUrl = url + 'bestilling/accessories/' + eurofotoProductId
         break;  
      default:
        productUrl = url + 'error'
    }
    
    //for testing purpose
   
    
    if (debug) {
      console.log("ProductURL", productUrl);
    }
    
    //create frame containing the editor
    iframeHTML = '<iframe id="eurofoto-frame" src="' + productUrl + '" frameborder="0" width="100%" style="min-height: 1000px"></iframe>';
    parentElement = document.getElementById(containerId);
    parentElement.innerHTML = iframeHTML;

    //add listener
    $(window).on("message onmessage", function(event){ self.handleMessageFromChild(event); });

    //listener for resizing
    $(window).resize(function() { self.handleResize(); });

    //initial resize
    this.handleResize();
  };
  
  /**
   *@function renderProjects will be called by CEWE container for initialization o project page - set up iframe, add listeners
   */
  this.renderProjects = function() {
    if (debug) {
      console.log("renderProjects");
    }

    var parentElement;
    var iframeHTML;
    var self = this;

    //create the editor url
    var iFrameUrl = url + '/cewe/projectsIFrame?container=cewe';

    //DMO-206 - provide the list iframe with the given project and action
    if (eurofotoProlongProject !== null) {
      iFrameUrl += "&project=" + eurofotoProlongProject + "&action=prolongSave";
    }
    
    if (debug) {
      console.log("IFRAMEURL ", iFrameUrl);
    }
    
    
    //for testing purpose
    iFrameUrl = url + 'merkelapp'
    
    //create frame containing the editor
    iframeHTML = '<iframe id="eurofoto-frame" src="' + iFrameUrl + '" frameborder="0" width="100%" ></iframe>';
    parentElement = document.getElementById(containerId);
    parentElement.innerHTML = iframeHTML;

    //add listener
    $(window).on("message onmessage", function(event){ self.handleMessageFromChild(event); });
  };
}