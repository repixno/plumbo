  var fs = require('fs'),
  fabric = require('fabric').fabric;


  
  
  var wtg = process.argv[2];
  var uri_dec = urldecode(wtg);
  //var obj = JSON.parse(wtg);
  var obj = JSON.parse(uri_dec);
  
  //console.log( obj ); 
  
  
  
  
  
  var canvas = fabric.createCanvasForNode(obj.width, obj.height);
  
  var font = new canvas.Font(obj.fontFamily, obj.fontfile);
  
  console.log( obj );
  
  //canvas.contextContainer.addFont(font);  // when using createPNGStream or createJPEGStream
  canvas.contextTop.addFont(font);      // when using toDataURL or toDataURLWithMultiplier
  
  obj.flipX = false;
  obj.flipY = false;
  
  obj.left = obj.width / 2;
  obj.top = obj.height / 2;
  
  obj.text = "KAKEMONSTER\nRULES";
  
  var text = new fabric.Text(obj.text, obj) ;
  
   
  canvas.add(text);

/*
var stream = canvas.createPNGStream();
stream.on('data', function(chunk) {
  out.write(chunk);
});*/


// Get PNG data

var ratio = obj.ratiokake.replace( ',' , '.');

ratio = parseFloat( ratio );



var out = require('fs').createWriteStream('/tmp/' + obj.uniqueid + '.png'),
    stream = canvas.createPNGStream();

stream.on('data', function(chunk) {
  out.write(chunk);
});


/*
var dataUrl = canvas.toDataURLWithMultiplier('png', ratio);
var data = dataUrl.replace(/^data:image\/png;base64,/, '');

// Write PNG file
fs.writeFile( '/tmp/' + obj.uniqueid + '.png', data, 'base64', function(err) {
    /*if (err)
        console.log('Error saving PNG: ' + err);
    else
        console.log('PNG file saved!');*/
    
//});



function urldecode(str) {
   return decodeURIComponent((str+'').replace(/\+/g, '%20'));
}