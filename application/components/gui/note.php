<?php
	
	/**
	* class for rendering notes to HTML/WML/XML
	*
	* @author   Oyvind Selbek <oyvind@selbek.com>
	* @package  gui
	* @access   public
	* @version	1.0
	*/
	class staticNote extends gui_template {
		
		/**
	    * The internal container for the note
	    *
	    * @var      array
	    * @see      note(), Setnote()
	    * @access   private
	    */
		var $note;
		
		/**
		* class constructors - fills the class with data and optionally 
		  renders it on the current or default render interface.
		*
		* @access   public
		* @param		string	The note to render, can contain basic HTML <a />-links
		* @param		boolean	Wether or not to automatically render the content, default true
		* @author   Oyvind Selbek <oyvind@selbek.com>
		*/	
		function staticNote( $note = '', $autoRender = true ) {
			
			// execute the parent constructor
			$this->gui_template();
			
			$this->SetNote( $note );
			if ($autoRender)
			$this->render();
			
		}
		
		/**
		* Sets the note on the current object. Can be automatically called by the constructor.
		*
		* @access   public
		* @param		string	The note to render, can contain basic HTML <a />-links
		* @return	string	The note that was set, stripped for html/crap.
		* @author   Oyvind Selbek <oyvind@selbek.com>
		*/	
		function SetNote( $note ) {
			
			return $this->note = strip_tags( $note, '<a><b><i><u><pre><font>' );
			
		}
		
		/**
		* Standard GUI function - Return as HTML
		*
		* @access   public
		* @return	string
		* @author   Oyvind Selbek <oyvind@selbek.com>
		*/	
		function ReturnHTML() {
			
			$id = 'fadeNote_'.(++note::$numNotes);
			echo "<div id=\"$id\" class=\"note\">\n".preg_replace( "/(([A-Za-z0-9]+_+)|([A-Za-z0-9]+\-+)|([A-Za-z0-9]+\.+)|([A-Za-z0-9]+\++))*[A-Za-z0-9]+@((\w+\-+)|(\w+\.))*\w{1,63}\.[a-zA-Z]{2,6}/","<a href=\"mailto:$0\">$0</a>", preg_replace( "/(?:^|\b)((((http|https|ftp):\/\/)|(www\.))([\w\.]+)([,:%#&\/?~=\w+\.-]+))(?:\b|$)/ise","noteFormatUrl(\"$1\")", preg_replace('~&lt;(.*?)&gt;~', '&lt;<span style="color: blue;">$1</span>&gt;', str_replace( array( '  ', "\t" ), array( '&nbsp;&nbsp;', '&nbsp;&nbsp;&nbsp;&nbsp;' ), nl2br( $this->note ) ) ) ) )."</div>\n";
			echo "<script language=\"JavaScript\">
			   new Effect.Highlight( '$id', {startcolor: '#ffffff', endcolor: '#ffffcc'} );
			</script>";
			
		}
		
	}
	
	/**
	* utility function for formatting URLs
	*
	* @author   Oyvind Selbek <oyvind@selbek.com>
	* @package  gui
	* @access   public
	* @version	1.0
	*/
	function noteFormatUrl( $url ) {
		$link = ( substr( strtolower( $url ),0,4 )!=='http' && substr( strtolower( $url ),0,6 )!=='ftp://' ) ? 'http://'.$url : $url;
		return "<a href=\"$link\" target=\"_blank\">$url</a>";
	}
	
	class floatNote extends staticNote {
	   
	   /**
	    * Constructor
	    *
	    * @param string $note
	    * @param integer $timeout
	    * @param boolean $autoRender
	    * @return note
	    */
		function floatNote( $note = '', $autoRender = true ) {
		   
		   parent::staticNote( $note, $autoRender );
		   
		}
	   
		/**
		* Standard GUI function - Return as HTML
		*
		* @access   public
		* @return	string
		* @author   Oyvind Selbek <oyvind@selbek.com>
		*/	
		function ReturnHTML() {
			
			echo "<div class=\"left note\">\n".preg_replace( "/(([A-Za-z0-9]+_+)|([A-Za-z0-9]+\-+)|([A-Za-z0-9]+\.+)|([A-Za-z0-9]+\++))*[A-Za-z0-9]+@((\w+\-+)|(\w+\.))*\w{1,63}\.[a-zA-Z]{2,6}/","<a href=\"mailto:$0\">$0</a>", preg_replace( "/(?:^|\b)((((http|https|ftp):\/\/)|(www\.))([\w\.]+)([,:%#&\/?~=\w+\.-]+))(?:\b|$)/ise","noteFormatUrl(\"$1\")", preg_replace('~&lt;(.*?)&gt;~', '&lt;<span style="color: blue;">$1</span>&gt;', str_replace( array( '  ', "\t" ), array( '&nbsp;&nbsp;', '&nbsp;&nbsp;&nbsp;&nbsp;' ), nl2br( $this->note ) ) ) ) )."</div>\n";
			
		}
	   
	}
	
	class note extends staticNote {
		
	   static $numNotes;
	   private $timeout;
      
	   /**
	    * Constructor
	    *
	    * @param string $note
	    * @param integer $timeout
	    * @param boolean $autoRender
	    * @return note
	    */
		function note( $note = '', $timeout = 5, $autoRender = true ) {
		   
		   $this->timeout = $timeout;
		   parent::staticNote( $note, $autoRender );
		   
		}
	   
		/**
		* Standard GUI function - Return as HTML
		*
		* @access   public
		* @return	string
		* @author   Oyvind Selbek <oyvind@selbek.com>
		*/	
		function ReturnHTML() {
			
		   $id = 'fadeNote_'.(++note::$numNotes);
			echo "<div id=\"$id\" class=\"note\">\n".preg_replace( "/(([A-Za-z0-9]+_+)|([A-Za-z0-9]+\-+)|([A-Za-z0-9]+\.+)|([A-Za-z0-9]+\++))*[A-Za-z0-9]+@((\w+\-+)|(\w+\.))*\w{1,63}\.[a-zA-Z]{2,6}/","<a href=\"mailto:$0\">$0</a>", preg_replace( "/(?:^|\b)((((http|https|ftp):\/\/)|(www\.))([\w\.]+)([,:%#&\/?~=\w+\.-]+))(?:\b|$)/ise","noteFormatUrl(\"$1\")", preg_replace('~&lt;(.*?)&gt;~', '&lt;<span style="color: blue;">$1</span>&gt;', str_replace( array( '  ', "\t" ), array( '&nbsp;&nbsp;', '&nbsp;&nbsp;&nbsp;&nbsp;' ), nl2br( $this->note ) ) ) ) )."</div>\n";
			echo "<script language=\"JavaScript\">
			   new Effect.Highlight( '$id', {startcolor: '#ffffff', endcolor: '#ffffcc'} );
			   window.setTimeout( \"Effect.Fade( '$id' );\", ".$this->timeout."000 );
			</script>";
			
		}
	   
	}
	
?>