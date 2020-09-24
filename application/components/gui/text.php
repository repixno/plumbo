<?php
	
/**
* class for rendering plaintext output to HTML/WML/XML
*
* @author   Oyvind Selbek <oyvind@iw.no>
* @package  gui
* @access   public
* @version	1.0
*/
class text extends gui_template {
	
	/**
    * The internal container for the Text
    *
    * @var      array
    * @see      text(), SetText()
    * @access   private
    */
	var $text;
	
	/**
	* class constructors - fills the class with data and optionally 
	  renders it on the current or default render interface.
	*
	* @access   public
	* @param		string	The text to render, can contain basic HTML <a />-links
	* @param		boolean	Wether or not to automatically render the content, default true
	* @author   Oyvind Selbek <oyvind@iw.no>
	*/	
	function text( $text = '', $autoRender = true ) {
		
		// execute the parent constructor
		$this->gui_template();
		
		$this->SetText( $text );
		if ($autoRender)
		$this->render();
		
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
		
		return $this->text = strip_tags( $text, '<a><b><i><u>' );
		
	}
	
	/**
	* Standard GUI function - Return as HTML
	*
	* @access   public
	* @return	string
	* @author   Oyvind Selbek <oyvind@iw.no>
	*/	
	function ReturnHTML() {
		
		echo "<p>".str_replace( '  ', '&nbsp;&nbsp;', nl2br( $this->text ) )."</p>\n";
		
	}
	
}
	
?>