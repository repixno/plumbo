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
  
  var width = obj.editsize.x;
  var height = obj.editsize.y;
  
  
  var canvas = fabric.createCanvasForNode(width, height);
  
  
  var font = new canvas.Font("Harrington", '/var/www/marie.eurofoto.no/sites/normerk/webroot/editorfonts/harrington.ttf' );
  //canvas.contextContainer.addFont(font);  // when using createPNGStream or createJPEGStream
  canvas.contextTop.addFont(font);      // when using toDataURL or toDataURLWithMultiplier
  
  var font2 = new canvas.Font("Georgia", '/var/www/marie.eurofoto.no/sites/normerk/webroot/editorfonts/georgia.ttf' );
  canvas.contextTop.addFont(font2); 
  var str = JSON.stringify(obj.content);
  str = str.replace( new RegExp("\"false\"", "g"), "false" ); 

  
  try{
    canvas.loadFromJSON( unescape( str )  ,  function( ){
        console.log(1);
        canvas.renderAll();
  
        var imagepath = '/tmp/' + malpageid + '.png';
      
        var out = fs.createWriteStream('/tmp/' + malpageid + '.png'),
      
        stream = canvas.createPNGStream();
       
        stream.on('data', function(chunk){
          out.write(chunk);
        });
      } );
  }catch(err) {
      console.log(  err.message );
  }
  
  
  
  console.log('2');
  
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