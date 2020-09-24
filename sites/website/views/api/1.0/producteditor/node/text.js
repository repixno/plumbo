  var fs = require('fs'),
  fabric = require('fabric').fabric;


  var canvas = fabric.createCanvasForNode(1200, 1200);
  
  var wtg = process.argv[2];
  var uri_dec = urldecode(wtg);
  //var obj = JSON.parse(wtg);
  var obj = JSON.parse(uri_dec);
  
  //console.log( obj ); 
  
  var font = new canvas.Font(obj.fontFamily, obj.fontfile);
  
  //canvas.contextContainer.addFont(font);  // when using createPNGStream or createJPEGStream
  canvas.contextTop.addFont(font);      // when using toDataURL or toDataURLWithMultiplier
  
  obj.flipX = false;
  obj.flipY = false;
  
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

var dataUrl = canvas.toDataURLWithMultiplier('png', ratio);
var data = dataUrl.replace(/^data:image\/png;base64,/, '');

// Write PNG file
fs.writeFile( '/tmp/' + obj.uniqueid + '.png', data, 'base64', function(err) {
    /*if (err)
        console.log('Error saving PNG: ' + err);
    else
        console.log('PNG file saved!');*/
    
});



function urldecode(str) {
   return decodeURIComponent((str+'').replace(/\+/g, '%20'));
}