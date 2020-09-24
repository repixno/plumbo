<?php
	
	/**
	* class for rendering plaintext output to HTML/WML/XML
	*
	* @author   Oyvind Selbek <oyvind@iw.no>
	* @package  gui
	* @access   public
	* @version	1.0
	*/
	class gui_template {
		
		/**
		* default class constructor
		*
		* @access   public
		* @author   Oyvind Selbek <oyvind@iw.no>
		*/	
		function gui_template() {

			
		}
		
		/**
		* Standard GUI function - Render the returned code
		*
		* @access   public
		* @return	string
		* @author   Oyvind Selbek <oyvind@iw.no>
		*/	
		function Render() {
			
			echo $this->ReturnCode();
			
		}
		
		
		/**
		* Standard GUI function - Return code from the GUI object
		*
		* @access   public
		* @return	string
		* @author   Oyvind Selbek <oyvind@iw.no>
		*/	
		function ReturnCode() {
			
			// apply user spesific global rendersettings from session.
			$this->renderSettings = array();
			
			// Later on, we will here to agent-based 
			// media-detection and render correctly,
			// such as rendering WML instead of HTML.
			if( method_exists( $this, 'ReturnHTML' ) )
				return $this->ReturnHTML();
			else
				return "There is no HTML support in this class: ".get_class( $this )."<br />\n";
			
		}
		
	}

?>