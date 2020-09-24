fabric.Object.prototype.originX = fabric.Object.prototype.originY = 'center';



(function(global) {

  'use strict';

  var extend = fabric.util.object.extend;

  if (!global.fabric) {
    global.fabric = { };
  }

  if (global.fabric.newImage) {
    fabric.warn('fabric.Image is already defined.');
    return;
  }
  /**
   * Image class
   * @class fabric.Image
   * @extends fabric.Object
   * @tutorial {@link http://fabricjs.com/fabric-intro-part-1/#images}
   * @see {@link fabric.Image#initialize} for constructor definition
   */
fabric.newImage = fabric.util.createClass(fabric.Object, /** @lends fabric.Image.prototype */ {

	 
    /**
     * Type of an object
     * @type String
     * @default
     */
    type: 'newimage',

    /**
     * crossOrigin value (one of "", "anonymous", "allow-credentials")
     * @see https://developer.mozilla.org/en-US/docs/HTML/CORS_settings_attributes
     * @type String
     * @default
     */
    crossOrigin: '',

    /**
     * Constructor
     * @param {HTMLImageElement | String} element Image element
     * @param {Object} [options] Options object
     * @return {fabric.Image} thisArg
     */
    initialize: function(element, options) {
      options || (options = { });

      this.filters = [];
	  this.crop = [];

      this.callSuper('initialize', options);

      this._initElement(element, options);
      this._initConfig(options);

      if (options.filters) {
        this.filters = options.filters;
        this.applyFilters();
      }
	  
	  if( options.crop ){
		this.crop = options.crop;
		this.applyCrop();
	  }
    },

    /**
     * Returns image element which this instance if based on
     * @return {HTMLImageElement} Image element
     */
    getElement: function() {
      return this._element;
    },

    /**
     * Sets image element for this instance to a specified one.
     * If filters defined they are applied to new image.
     * You might need to call `canvas.renderAll` and `object.setCoords` after replacing, to render new image and update controls area.
     * @param {HTMLImageElement} element
     * @param {Function} [callback] Callback is invoked when all filters have been applied and new image is generated
     * @return {fabric.Image} thisArg
     * @chainable
     */
    setElement: function(element, callback) {
      this._element = element;
      this._originalElement = element;
      this._initConfig();

      /*if (this.filters.length !== 0) {
        this.applyFilters(callback);
      }*/

      return this;
    },

    /**
     * Sets crossOrigin value (on an instance and corresponding image element)
     * @return {fabric.Image} thisArg
     * @chainable
     */
    setCrossOrigin: function(value) {
      this.crossOrigin = value;
      this._element.crossOrigin = value;

      return this;
    },

    /**
     * Returns original size of an image
     * @return {Object} Object with "width" and "height" properties
     */
    getOriginalSize: function() {
      var element = this.getElement();
      return {
        width: element.width,
        height: element.height
      };
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    _stroke: function(ctx) {
      ctx.save();
      this._setStrokeStyles(ctx);
      ctx.beginPath();
      ctx.strokeRect(-this.width / 2, -this.height / 2, this.width, this.height);
      ctx.closePath();
      ctx.restore();
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    _renderDashedStroke: function(ctx) {
      var x = -this.width / 2,
          y = -this.height / 2,
          w = this.width,
          h = this.height;

      ctx.save();
      this._setStrokeStyles(ctx);

      ctx.beginPath();
      fabric.util.drawDashedLine(ctx, x, y, x + w, y, this.strokeDashArray);
      fabric.util.drawDashedLine(ctx, x + w, y, x + w, y + h, this.strokeDashArray);
      fabric.util.drawDashedLine(ctx, x + w, y + h, x, y + h, this.strokeDashArray);
      fabric.util.drawDashedLine(ctx, x, y + h, x, y, this.strokeDashArray);
      ctx.closePath();
      ctx.restore();
    },

    /**
     * Returns object representation of an instance
     * @param {Array} [propertiesToInclude] Any properties that you might want to additionally include in the output
     * @return {Object} Object representation of an instance
     */
    toObject: function(propertiesToInclude) {
      return extend(this.callSuper('toObject', propertiesToInclude), {
        src: this._originalElement.src || this._originalElement._src,
        filters: this.filters.map(function(filterObj) {
          return filterObj && filterObj.toObject();
        }),
        crossOrigin: this.crossOrigin
      });
    },

    /**
     * Returns source of an image
     * @return {String} Source of an image
     */
    getSrc: function() {
      if (this.getElement()) {
        return this.getElement().src || this.getElement()._src;
      }
    },

    /**
     * Returns string representation of an instance
     * @return {String} String representation of an instance
     */
    toString: function() {
      return '#<fabric.Image: { src: "' + this.getSrc() + '" }>';
    },

    /**
     * Returns a clone of an instance
     * @param {Function} callback Callback is invoked with a clone as a first argument
     * @param {Array} [propertiesToInclude] Any properties that you might want to additionally include in the output
     */
    clone: function(callback, propertiesToInclude) {
      this.constructor.fromObject(this.toObject(propertiesToInclude), callback);
    },

    /**
     * Applies filters assigned to this image (from "filters" array)
     * @mthod applyFilters
     * @param {Function} callback Callback is invoked when all filters have been applied and new image is generated
     * @return {fabric.Image} thisArg
     * @chainable
     */
    applyFilters: function(callback) {

      if (!this._originalElement) {
        return;
      }

      if (this.filters.length === 0) {
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

      canvasEl.getContext('2d').drawImage(imgEl, 0, 0, imgEl.width, imgEl.height);

      this.filters.forEach(function(filter) {
        filter && filter.applyTo(canvasEl);
      });

       /** @ignore */

      replacement.width = imgEl.width;
      replacement.height = imgEl.height;

      if (fabric.isLikelyNode) {
        replacement.src = canvasEl.toBuffer(undefined, fabric.newImage.pngCompression);

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
      }

      return this;
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
		  replacement.src = canvasEl.toBuffer(undefined, fabric.newImage.pngCompression);
		  
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
		  replacement.src = canvasEl.toDataURL('image/jpeg');
		}
	
		return this;
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    _render: function(ctx, noTransform) {
      this._element &&
      ctx.drawImage(
        this._element,
        noTransform ? this.left : -this.width/2,
        noTransform ? this.top : -this.height/2,
        this.width,
        this.height
      );
      this._renderStroke(ctx);
    },

    /**
     * @private
     */
    _resetWidthHeight: function() {
      var element = this.getElement();

      this.set('width', element.width);
      this.set('height', element.height);
    },

    /**
     * The Image class's initialization method. This method is automatically
     * called by the constructor.
     * @private
     * @param {HTMLImageElement|String} element The element representing the image
     */
    _initElement: function(element) {
      this.setElement(fabric.util.getById(element));
      fabric.util.addClass(this.getElement(), fabric.newImage.CSS_CANVAS);
    },

    /**
     * @private
     * @param {Object} [options] Options object
     */
    _initConfig: function(options) {
      options || (options = { });
      this.setOptions(options);
      this._setWidthHeight(options);
      if (this._element && this.crossOrigin) {
        this._element.crossOrigin = this.crossOrigin;
      }
    },

    /**
     * @private
     * @param {Object} object Object with filters property
     * @param {Function} callback Callback to invoke when all fabric.Image.filters instances are created
     */
    _initFilters: function(object, callback) {
      if (object.filters && object.filters.length) {
        fabric.util.enlivenObjects(object.filters, function(enlivenedObjects) {
          callback && callback(enlivenedObjects);
        }, 'fabric.Image.filters');
      }
      else {
        callback && callback();
      }
    },

    /**
     * @private
     * @param {Object} [options] Object with width/height properties
     */
    _setWidthHeight: function(options) {
      this.width = 'width' in options
        ? options.width
        : (this.getElement()
            ? this.getElement().width || 0
            : 0);

      this.height = 'height' in options
        ? options.height
        : (this.getElement()
            ? this.getElement().height || 0
            : 0);
    },

    /**
     * Returns complexity of an instance
     * @return {Number} complexity of this instance
     */
    complexity: function() {
      return 1;
    }
	});

	  /**
   * Default CSS class name for canvas
   * @static
   * @type String
   * @default
   */
  fabric.newImage.CSS_CANVAS = 'canvas-img';

  /**
   * Alias for getSrc
   * @static
   */
  fabric.newImage.prototype.getSvgSrc = fabric.newImage.prototype.getSrc;

  /**
   * Creates an instance of fabric.Image from its object representation
   * @static
   * @param {Object} object Object to create an instance from
   * @param {Function} [callback] Callback to invoke when an image instance is created
   */
  fabric.newImage.fromObject = function(object, callback) {
    fabric.util.loadImage(object.src, function(img) {
      fabric.newImage.prototype._initFilters.call(object, object, function(filters) {
        object.filters = filters || [ ];
        var instance = new fabric.newImage(img, object);
        callback && callback(instance);
      });
    }, null, object.crossOrigin);
  };

  /**
   * Creates an instance of fabric.Image from an URL string
   * @static
   * @param {String} url URL to create an image from
   * @param {Function} [callback] Callback to invoke when image is created (newly created image is passed as a first argument)
   * @param {Object} [imgOptions] Options object
   */
  fabric.newImage.fromURL = function(url, callback, imgOptions) {
    fabric.util.loadImage(url, function(img) {
      callback(new fabric.newImage(img, imgOptions));
    }, null, imgOptions && imgOptions.crossOrigin);
  };

  /* _FROM_SVG_START_ */
  /**
   * List of attribute names to account for when parsing SVG element (used by {@link fabric.Image.fromElement})
   * @static
   * @see {@link http://www.w3.org/TR/SVG/struct.html#ImageElement}
   */
  fabric.newImage.ATTRIBUTE_NAMES = fabric.SHARED_ATTRIBUTES.concat('x y width height xlink:href'.split(' '));

  /**
   * Returns {@link fabric.Image} instance from an SVG element
   * @static
   * @param {SVGElement} element Element to parse
   * @param {Function} callback Callback to execute when fabric.Image object is created
   * @param {Object} [options] Options object
   * @return {fabric.Image} Instance of fabric.Image
   */
  fabric.newImage.fromElement = function(element, callback, options) {
    var parsedAttributes = fabric.parseAttributes(element, fabric.Image.ATTRIBUTE_NAMES);

    fabric.newImage.fromURL(parsedAttributes['xlink:href'], callback,
      extend((options ? fabric.util.object.clone(options) : { }), parsedAttributes));
  };
  /* _FROM_SVG_END_ */

  /**
   * Indicates that instances of this type are async
   * @static
   * @type Boolean
   * @default
   */
  fabric.newImage.async = true;

  /**
   * Indicates compression level used when generating PNG under Node (in applyFilters). Any of 0-9
   * @static
   * @type Number
   * @default
   */
  fabric.newImage.pngCompression = 1;
  
  })(typeof exports !== 'undefined' ? exports : this);
