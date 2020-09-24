fabric.Newimage = fabric.util.createClass(fabric.Image, 
{
	type: 'newimage',
    
	initialize: function(element, options) {
        options || (options = { });
        this.crop = [];
        this.callSuper('initialize', options);
        
        
        this.id = options.id;
        
        this.set({
			orgSrc: element.src,
            crop: this.crop,
            id: this.id
		});

        
        this._initElement(element, options);
        this._initConfig(options);

	  
        if( options.crop ){
            this.crop = options.crop;
            this.applyCrop();
        }
    },
    applyCrop: function(callback) {

		if (!this._originalElement) {
		  return;
		}
	
		if (this.crop.length === 0) {
		  this._element = this._originalElement;
		  callback && callback();
		  return;
		}
	
		var imgEl = this._originalElement,
		canvasEl = fabric.util.createCanvasElement(),
		replacement = fabric.util.createImage(),
		_this = this;
	
		canvasEl.width = imgEl.width;
		canvasEl.height = imgEl.height;
		
		var cropx = canvasEl.width * this.crop.z / 100;
		var cropy = canvasEl.height * this.crop.z / 100;
		var ratio = canvasEl.width / canvasEl.height;
		
		var cropwidth = canvasEl.width - ( cropx * 2 ); 
		var cropheight = cropwidth / ratio;
		
		cropx = cropx + this.crop.x;
		cropy = cropy + this.crop.y;
	
		
		canvasEl.getContext('2d').drawImage(imgEl, cropx , cropy , cropwidth, cropheight, 0, 0, imgEl.width, imgEl.height );
		 /** @ignore */
		replacement.width = imgEl.width;
		replacement.height = imgEl.height;
		if (fabric.isLikelyNode) {
		  replacement.src = canvasEl.toBuffer(undefined, fabric.Newimage.pngCompression);
		  
		  // onload doesn't fire in some node versions, so we invoke callback manually
		  _this._element = replacement;
		  callback && callback();
		}
		else {
		  replacement.onload = function() {
			_this._element = replacement;
			callback && callback();
			replacement.onload = canvasEl = imgEl = null;
		  };
		  replacement.src = canvasEl.toDataURL('image/png');
		  //replacement.src = canvasEl.toDataURL('image/jpeg');
		}
	
		return this;
    },
    changeSource: function(src ,callback ) {

        console.log(this);
    
		if (!this._originalElement) {
		  return;
		}
        var img = new Image();
        var imgEl = this._originalElement,
		canvasEl = fabric.util.createCanvasElement(),
		replacement = fabric.util.createImage(),
		_this = this;
        
        
        img.onload = function() {
             
            var width = img.width;
            var height = img.height;
            
            var objectwidth = _this.width;
            var objectheight = _this.height;
            
            console.log( "imageheight" + height);
            console.log(  "imagewidth" +  width );

            console.log( "objectwidth" + objectwidth );
            console.log(imgEl);
            
            canvasEl.getContext('2d').drawImage(img, 0 , 0, width, height, 0 , 0, 400, 300  );
             /** @ignore */
            replacement.width = width;
            replacement.height = height;
            
            if (fabric.isLikelyNode) {
                replacement.src = canvasEl.toBuffer(undefined, fabric.Newimage.pngCompression);
		  
                // onload doesn't fire in some node versions, so we invoke callback manually
                _this._element = replacement;
                callback && callback();
            }
            else {
                replacement.onload = function() {
                    _this._element = replacement;
                    _this._originalElement = img;
                    callback && callback();
                    replacement.onload = canvasEl = imgEl = null;
                };
                replacement.src = canvasEl.toDataURL('image/jpeg');
            }
        
        };
        img.src = src
	
		return this;
    },
	toObject: function()
	{
		return fabric.util.object.extend(this.callSuper('toObject'), {
			orgSrc: this.orgSrc,
			crop: this.crop,
            id: this.id
		});
	},
    _render : function(ctx) {
        this.callSuper('_render', ctx);
    }
});

fabric.Newimage.fromObject = function(object, callback) {
        
	fabric.util.loadImage(object.src, function(img) {
		fabric.Image.prototype._initFilters.call(object, object, function(filters) {
			object.filters = filters || [];
			var instance = new fabric.Newimage(img, object);
			if (callback) { callback(instance); }
		});
	}, null, object.crossOrigin);
};

fabric.Newimage.async = true;
