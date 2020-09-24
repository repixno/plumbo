  var fs = require('fs'),
  fabric = require('fabric').fabric,
  http = require('http');
  
  var obj;
  var wtg = process.argv[2];
  
  
  //console.log( wtg );
  
  //var uri_dec = urldecode(wtg);
  //var obj = JSON.parse(wtg);
  //var obj = JSON.parse(uri_dec);
  
  var malid = 38;
  var malpageid = wtg;
  
  //console.log( obj );
  const querystring = require('querystring');
  var post_data = querystring.stringify({
    'malid' : malid,
    'malpageid' : malpageid
  });
 
  var options = {
      host: 'marie.eurofoto.no',
      port: '80',
      path: '/api/1.0/tedit/loadorder',
      method: 'POST',
      post_data: post_data,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'Content-Length': post_data.length
      }
  };
    
  var req = http.request(options, (res) => {
    res.setEncoding('utf8');
    
    res.on('data', (chunk) => {
      
      
      //console.log( chunk );
      
    chunk = chunk.replace( '\\"', '' );
    chunk = chunk.replace( '\\"', '' );
      
    var data =  JSON.parse( chunk );
      
    var objects =  data.content.objects;

    var tmparray =  new Array();
    
        for (i = 0; i < objects.length; i++) { 
            //console.log( objects[i].type);
            
            if( objects[i].type == 'image' ){
                data.content.objects[i]['filters'] = '';
                
                //console.log( data.content.objects[i] );
            }
            
            if( objects[i].type != 'image' ){
                tmparray.push(objects[i]);
            }
        }
    
        data.content.objects = tmparray;
      
        obj =  data;
      
        canvasimage( obj );
      
      //var data = `BODY: ${chunk}`;
      //console.log(  data );
    });
    res.on('end', () => {
      //console.log('No more data in response.')
    })
  });
  
  req.on('error', (e) => {
    console.log(`problem with request: ${e.message}`);
  });
  
  // write the request parameters
  req.write(post_data);
  req.end();


function canvasimage( obj ){
  
  fabric.Object.prototype.originX = 'center';
  fabric.Object.prototype.originY = 'center';
  
  
  var width = obj.editsize.x;
  var height = obj.editsize.y;
  
  var canvas = fabric.createCanvasForNode(width, height);

  //var font = new canvas.Font("Harrington", '/var/www/marie.eurofoto.no/sites/normerk/webroot/editorfonts/harrington.ttf' );
  //canvas.contextContainer.addFont(font);  // when using createPNGStream or createJPEGStream
  //canvas.contextTop.addFont(font);      // when using toDataURL or toDataURLWithMultiplier
  
  var font2 = new canvas.Font("Georgia", '/var/www/marie.eurofoto.no/sites/normerk/webroot/editorfonts/georgia.ttf' );
  canvas.contextTop.addFont(font2);
  
  var font3 = new canvas.Font("Arial", '/var/www/marie.eurofoto.no/sites/normerk/webroot/editorfonts/arial.ttf' );
  canvas.contextTop.addFont(font3);
  
  var font4 = new canvas.Font("Comic Sans MS", '/var/www/marie.eurofoto.no/sites/normerk/webroot/editorfonts/comic.ttf' );
  canvas.contextTop.addFont(font4);
  
  var font5 = new canvas.Font("Franklin Gothic Book", '/var/www/marie.eurofoto.no/sites/normerk/webroot/editorfonts/frabk.ttf' );
  canvas.contextTop.addFont(font5);
  
  var font6 = new canvas.Font("Tahoma", '/var/www/marie.eurofoto.no/sites/normerk/webroot/editorfonts/tahoma.ttf' );
  canvas.contextTop.addFont(font6);
  
  var font7 = new canvas.Font("Times New Roman", '/var/www/marie.eurofoto.no/sites/normerk/webroot/editorfonts/times.ttf' );
  canvas.contextTop.addFont(font7);
  
  var font8 = new canvas.Font("Trebuchet MS", '/var/www/marie.eurofoto.no/sites/normerk/webroot/editorfonts/trebuc.ttf' );
  canvas.contextTop.addFont(font8);
  
  var font9 = new canvas.Font("Verdana", '/var/www/marie.eurofoto.no/sites/normerk/webroot/editorfonts/trebuc.ttf' );
  canvas.contextTop.addFont(font9);
  
  var str = JSON.stringify(obj.content);
  
  str = str.replace( new RegExp("\"false\"", "g"), "false" );
  str = str.replace( new RegExp("#2559a8", "g"), "#000000" );
  str = str.replace( new RegExp("#ed1c24", "g"), "#000000" );
  str = str.replace( new RegExp("#00a650", "g"), "#000000" );

  
  canvas.loadFromJSON( unescape( str )  , canvas.renderAll.bind(canvas), function(){
    
    //canvas.renderAll();
    
    });
  
  //canvas.setBackgroundColor( "red" );
  //canvas.renderAll();
  
  //var image = canvas.toDataURL("image/png").replace("image/png", "image/octet-stream"); 
  
  
  //var image = canvas.toDataURL("image/png").replace("image/png", "image/octet-stream");
  
  
  var imagepath = '/tmp/' + malpageid + '.png';
  
  var out = fs.createWriteStream('/tmp/' + malpageid + '.png'),
  
  stream = canvas.createPNGStream();
 
  stream.on('data', function(chunk){
    out.write(chunk);
  });
  
  
  
  //console.log( imagepath );

  
}

function urldecode(str) {
   return decodeURIComponent((str+'').replace(/\+/g, '%20'));
}