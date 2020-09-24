<?php
	
	class page extends gui_template {
		
		var $pagesetup;
		var $colwidth;
		var $curcol;
		var $currow;
		
		function page( $pagesetup = false, $autostart = true, $width = '100%' ) {
			
			// document setup
			if( $pagesetup )
			$this->setup( $pagesetup );
			
			// reset document pointers
			$this->curcol = 0;
			$this->currow = 0;
			
			// start the tableset?
			if( $autostart ) 
				$this->start( $width );
			
		}
		
		function setup( $pagesetup ) {
			
			// reset the colwidth array
			$this->colwidth = array();
			
			// patch the colspans with the spacer columns
			foreach( $pagesetup as $rowid => $rows ) 
			foreach( $rows as $colid => $colspan ) {
				if( is_array( $colspan ) ) {
					// new setup operation
					$pagesetup[$rowid][$colid] = ($colspan['colspan'] * 2) - 1;
					$this->celalign[$rowid][$colid] = $colspan['align'];
					$this->colwidth[$colid] = ($colspan['colwidth']);
				} else {
					// old setup operation
					$pagesetup[$rowid][$colid] = ($colspan * 2) - 1;
					$this->celalign[$rowid][$colid] = 'left';
					$this->colwidth[$colid] = false;
				}
			}
			
			// store the pagesetup variable
			$this->pagesetup = $pagesetup;
			
		}
		
		
		function start( $width = "100%" ) {
			
			echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"$width\">\n<tr>\n<td align=\"{$this->celalign[$this->currow][$this->curcol]}\" valign=\"top\"".($this->colwidth[$this->curcol]?" width=\"{$this->colwidth[$this->curcol]}\"":'')." colspan=\"".($this->pagesetup[$this->currow][$this->curcol])."\">";
			
		}
		
		function finish() {
			
			echo "</td>\n</tr>\n</table>\n";
			
		}
		
		function nextcol() {
			
			if( $this->curcol >= count( $this->pagesetup[$this->currow] ) - 1 ) {
				
				$this->currow++;
				$this->curcol=0;
				echo "</td>\n</tr>\n<tr><td width=\"10\">&nbsp;</td></tr><tr>\n<td align=\"{$this->celalign[$this->currow][$this->curcol]}\" valign=\"top\"".($this->colwidth[$this->curcol]?" width=\"{$this->colwidth[$this->curcol]}\"":'')." colspan=\"".($this->pagesetup[$this->currow][$this->curcol])."\">";
				
			} else {
				
				$this->curcol++;
				echo "</td>\n<td width=\"10\">&nbsp;&nbsp;</td>\n<td align=\"{$this->celalign[$this->currow][$this->curcol]}\" valign=\"top\"".($this->colwidth[$this->curcol]?" width=\"{$this->colwidth[$this->curcol]}\"":'')." colspan=\"".($this->pagesetup[$this->currow][$this->curcol])."\">";
				
			}
			
		}
		
	}
	
?>