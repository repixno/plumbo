    var fs = require('fs'),
    fabric = require('fabric').fabric;
    var canvas = require( 'canvas' );
    var Image = canvas.Image;
    var canvas = fabric.createCanvasForNode( 2102, 1205 );
    var wtg = process.argv[2];
    var uri_dec = urldecode(wtg);
    //var obj = JSON.parse(wtg);
    var obj = JSON.parse(uri_dec);
  
    var ratio = obj.ratio.replace( ',' , '.');
    var img = new Image();
    //var image = new fabric.Image( img );
  
    img.onload = function(){
        //console.log('Resized and saved in buffer');
        var image = new fabric.Image(img);
        imageratio = image.width / obj.width;
        
        image.set({
            originX: obj.originX,
            originY: obj.originY,
            left: obj.left * ratio,
            top: obj.top * ratio,
            width: obj.width * ratio,
            height: obj.height * ratio,
            fill: obj.fill,
            stroke: obj.stroke,
            strokeWidth: parseInt( obj.strokeWidth * ratio ),
            strokeDashArray: obj.strokeDashArray,
            strokeLineCap: obj.strokeLineCap,
            strokeLineJoin: obj.strokeLineJoin,
            strokeMiterLimit: obj.strokeLineJoin,
            scaleX: obj.scaleX,
            scaleY: obj.scaleY,
            angle: obj.angle,
            flipX: false,
            flipY: false,
            opacity: obj.opacity,
            shadow: obj.shadow,
            visible: obj.visible,
            clipTo: obj.clipTo,
            backgroundColor: obj.backgroundColor,
            fillRule: obj.fillRule,
            globalCompositeOperation: obj.globalCompositeOperation,
            crossOrigin: obj.crossOrigin,
            id: obj.id,
        });
        
        canvas.add( image );   
    }
    
    img.src = obj.src;
    var dataUrl = canvas.toDataURLWithMultiplier('png', 1);
    //var data = dataUrl.replace(/^data:image\/png;base64,/, '');
    var data = dataUrl.replace(/^data:image\/png;base64,/, '');

    // Write PNG file
    fs.writeFile( '/tmp/'+ obj.uniqueid +'.png', data, 'base64', function(err) {
        /*if (err)
            console.log('Error saving PNG: ' + err);
        else
            console.log('PNG file saved!');*/
        
    });
    

function urldecode(str) {
    return decodeURIComponent((str+'').replace(/\+/g, '%20'));
}