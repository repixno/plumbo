  var fs = require('fs'),
  fabric = require('fabric').fabric;    
  var canvas = fabric.createCanvasForNode(1200, 700);
  
  var wtg = '%7B"type"%3A"i-text"%2C"originX"%3A"center"%2C"originY"%3A"center"%2C"left"%3A691.57%2C"top"%3A332.21%2C"width"%3A175%2C"height"%3A52%2C"fill"%3A"%231f497d"%2C"stroke"%3Anull%2C"strokeWidth"%3A1%2C"strokeDashArray"%3Anull%2C"strokeLineCap"%3A"butt"%2C"strokeLineJoin"%3A"miter"%2C"strokeMiterLimit"%3A10%2C"scaleX"%3A1.76%2C"scaleY"%3A1.76%2C"angle"%3A10.64%2C"flipX"%3Afalse%2C"flipY"%3Afalse%2C"opacity"%3A1%2C"shadow"%3Anull%2C"visible"%3Atrue%2C"clipTo"%3Anull%2C"backgroundColor"%3A""%2C"text"%3A"Tor+Inge"%2C"fontSize"%3A40%2C"fontWeight"%3A"normal"%2C"fontFamily"%3A"Airplanes+in+the+Night+Sky"%2C"fontStyle"%3A""%2C"lineHeight"%3A1.3%2C"textDecoration"%3A""%2C"textAlign"%3A"center"%2C"path"%3Anull%2C"textBackgroundColor"%3A""%2C"useNative"%3Atrue%2C"styles"%3A%7B%7D%7D'; //value will be "banana"
  //var wtg = process.argv[2];
  var uri_dec = urldecode(wtg);
  //var obj = JSON.parse(wtg);
  var obj = JSON.parse(uri_dec);
  
  obj.text = obj.text.replace( /XXXLINJESKIFTXXX/g, "\n" );
  
  console.log( obj ); 
  
  //var font = new canvas.Font(obj.fontFamily, obj.fontfile);
  
  //canvas.contextContainer.addFont(font);  // when using createPNGStream or createJPEGStream
  //canvas.contextTop.addFont(font);      // when using toDataURL or toDataURLWithMultiplier
  
  //var text = new fabric.Text(obj.text, obj) ;
  
   var text = new fabric.Text(obj.text, {
      left: 691.57,
      top: 332.21,
      width:  175,
      height : 52,
      fill: '#1f497d',
      angle: 10.64,
      originX: 'center',
      originY: 'center',
      scaleX:  1.76,
      scaleY : 1.76
  });
   
  canvas.add(text);

/*
var stream = canvas.createPNGStream();
stream.on('data', function(chunk) {
  out.write(chunk);
});*/


// Get PNG data

//var ratio = obj.ratiokake.replace( ',' , '.');

//ratio = parseFloat( ratio );


var out = require('fs').createWriteStream(__dirname + '/rectangle2.png'),
    stream = canvas.createPNGStream();

stream.on('data', function(chunk) {
  out.write(chunk);
});

/*
var dataUrl = canvas.toDataURL('png');
var data = dataUrl.replace(/^data:image\/png;base64,/, '');

// Write PNG file
fs.writeFile(__dirname + '/' + obj.uniqueid + '.png', data, 'base64', function(err) {
    /*if (err)
        console.log('Error saving PNG: ' + err);
    else
        console.log('PNG file saved!');*/
    
//});



function urldecode(str) {
   return decodeURIComponent((str+'').replace(/\+/g, '%20'));
}