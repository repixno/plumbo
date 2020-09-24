
var fabric = require('fabric').fabric,
    canvas = fabric.createCanvasForNode(200, 200);

canvas.add(new fabric.Rect({
  top: 100,
  left: 100,
  width: 100,
  height: 50,
  angle: 30,
  fill: 'rgba(255,0,0,0.5)'
}));

var out = require('fs').createWriteStream(__dirname + '/rectangle.png'),
    stream = canvas.createPNGStream();

stream.on('data', function(chunk) {
  out.write(chunk);
});