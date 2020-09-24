/*
Copyright (c) 2003-2009, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.addStylesSet( 'blueprint',
[
	/* Inline Styles */

	// These are core styles available as toolbar buttons. You may opt enabling
	// some of them in the Styles combo, removing them from the toolbar.
	{ name : 'Strong'			, element : 'strong', overrides : 'b' },
	{ name : 'Emphasis'			, element : 'em'	, overrides : 'i' },
	{ name : 'Underline'		, element : 'u' },
	{ name : 'Strikethrough'	, element : 'strike' },
	{ name : 'Subscript'		, element : 'sub' },
	{ name : 'Superscript'		, element : 'sup' },


	
	{ name : 'Computer Code'	, element : 'code' },
	/*{ name : 'Keyboard Phrase'	, element : 'kbd' },
	{ name : 'Sample Text'		, element : 'samp' },
	{ name : 'Variable'			, element : 'var' },*/

	{ name : 'Deleted Text'		, element : 'del' },
	{ name : 'Inserted Text'	, element : 'ins' },

	{ name : 'Cited Work'		, element : 'cite' },
	{ name : 'Inline Quotation'	, element : 'q' },

	{ name : 'Language: RTL'	, element : 'span', attributes : { 'dir' : 'rtl' } },
	{ name : 'Language: LTR'	, element : 'span', attributes : { 'dir' : 'ltr' } },

	/* Object Styles */

	{
		name : 'Image on Left',
		element : 'img',
		attributes :
		{
			'class' : 'left',
		}
	},

	{
		name : 'Image on Right',
		element : 'img',
		attributes :
		{
			'class' : 'right',
		}
	}
	,
	{
		name: 'Small',
		element: 'span, p',
		attributes : {
			'class': 'small'
		}
	}
	,
	{
		name: 'Hide',
		element: 'span, p',
		attributes : {
			'class': 'hide'
		}
	}
	,
	{
		name: 'Loud',
		element: 'span, p',
		attributes : {
			'class': 'loud'
		}
	}
	,
	{
		name: 'Removed',
		element: 'span, p',
		attributes : {
			'class': 'removed'
		}
	}
	,
	{
		name: 'Highlight',
		element: 'span',
		attributes : {
			'class': 'highlight'
		}
	}
	,
	{
		name: 'Added',
		element: 'span',
		attributes : {
			'class': 'added'
		}
	}
	,
	{
		name: 'Top',
		element: 'h1, h2, h3, h4, h5, h6, p',
		attributes : {
			'class': 'top'
		}
	}
	,
	{
		name: 'Bottom',
		element: 'h1, h2, h3, h4, h5, h6, p',
		attributes : {
			'class': 'bottom'
		}
	}	

]);