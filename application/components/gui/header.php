<?php

/**
* class for rendering headers to HTML/WML/XML
*
* @author   Oyvind Selbek <oyvind@iw.no>
* @package  gui
* @access   public
* @version	1.0
*/
class header extends gui_template {
	
	/**
    * The internal container for the headerText
    *
    * @var      array
    * @see      header(), SetText()
    * @access   private
    */
	var $headerText;
   var $level;
	
	/**
	* class constructors - fills the class with data and optionally 
	  renders it on the current or default render interface.
	*
	* @access   public
	* @param		string	The text to render, can contain basic HTML <a />-links
	* @param		integer  The header level to use, default 1 produces a <H1 />-tag
	* @param		boolean	Wether or not to automatically render the content, default true
	* @author   Oyvind Selbek <oyvind@iw.no>
	*/	
	function header( $text = '', $level = 1, $autorender = true ) {
		
		// execute the parent constructor
		$this->gui_template();
		
		$this->SetText( $text );
      
		$this->level = $level;
		
		if( $autorender ) $this->Render();
		
	}
	
	/**
	* Sets the text on the current object. Can be automatically called by the constructor.
	*
	* @access   public
	* @param		string	The text to render, can contain basic HTML <a />-links
	* @return	string	The text that was set, stripped for html/crap.
	* @author   Oyvind Selbek <oyvind@iw.no>
	*/	
	function SetText( $text ) {
		
		return $this->headerText = strip_tags( $text, '<a>' );
		
	}
	
	/**
	* Standard GUI function - Return as HTML
	*
	* @access   public
	* @return	string
	* @author   Oyvind Selbek <oyvind@iw.no>
	*/	
	function ReturnHTML() {
   	
		return sprintf( '<H%1$d style="margin-bottom: 6px;" class="header">%2$s</H%1$d>', $this->level, $this->headerText );
		
	}
	
}

?>