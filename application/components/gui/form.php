<?php
	
	/**
	* class for rendering forms to HTML/WML/XML
	*
	* @author   Oyvind Selbek <oyvind@selbek.com>
	* @package  gui
	* @access   public
	* @version	1.0
	*/
	class form extends gui_template {
		
		/**
	    * Holds the current form name - all fields in a form is placed 
	    * under this value as array items under $this->parameters in a
	    * module after the user submits the form somewhere in the gui.
	    *
	    * @var      string
	    * @see      form() setFormVar()
	    * @access   private
	    */
		var $formVar;
	
		/**
	    * Holds the internal list of formfields
	    *
	    * @var      string
	    * @access   private
	    */
		var $formFields;
	
		/**
	    * Holds the internal submitText
	    *
	    * @var      string
	    * @access   private
	    */
		var $submitText;
	
		/**
	    * Holds the internal backText
	    *
	    * @var      string
	    * @access   private
	    */
		var $backText;
	
		/**
	    * Holds the internal baseLink
	    *
	    * @var      string
	    * @access   private
	    */
		var $baseLink;
	
		/**
	    * Holds the internal backLink
	    *
	    * @var      string
	    * @access   private
	    */
		var $backLink;
	
		/**
	    * Holds the internal width of the form elements
	    *
	    * @var      string
	    * @access   private
	    */
		var $styleWidth;
	
	   /**
	   * Holds additional parameteres for <form>.
	   *
	   * @var string
	   * @access private
	   */
	   var $formAdditional;
	   var $submitAction;
		
		/**
		* class constructor
		*
		* @access   public
		* @param		string	The current formname, default 'save'
		* @see		$formVar setFormvar()
		* @author   Oyvind Selbek <oyvind@selbek.com>
		*/	
		function form( $formVar = 'save', $styleWidth = '150px' ) {
			
			// execute the parent constructor
			$this->gui_template();
			
			$this->setFormVar( $formVar );
			$this->formFields = array();
			$this->setStyleWidth( $styleWidth );
			
		}

		function setSubmitAction( $action ) {
		
			$this->submitAction = $action;
		
		}
		
		/**
		* Sets the current formname to something
		*
		* @access   public
		* @see		$formVar, form()
		* @param		string	The current formname to be set
		* @author   Oyvind Selbek <oyvind@selbek.com>
		*/ 
		function setFormVar( $formVar ) {
			
			$this->formVar = $formVar;
			
		}
		
		/**
		* Sets the current styleWidth to something
		*
		* @access   public
		* @see		$styleWidth, form()
		* @param		string	The current formname to be set
		* @author   Oyvind Selbek <oyvind@selbek.com>
		*/ 
		function setStyleWidth( $styleWidth ) {
			
			$this->styleWidth = $styleWidth;
			
		}
		
		/**
		* Sets the baselink in the system
		*
		* @access   public
		* @see		$baseLink
		* @param		string	The current baselink to be set
		* @author   Oyvind Selbek <oyvind@selbek.com>
		*/ 
		function setBaseLink( $baseLink ) {
			
			$this->baseLink = $baseLink;
			
		}
	
		/**
		* Sets the backselink in the system
		*
		* @access   public
		* @see		$backLink
		* @param		string	The current backlink to be set
		* @author   Oyvind Selbek <oyvind@selbek.com>
		*/ 
		function setBackLink( $backLink ) {
			
			$this->backLink = $backLink;
			
		}
	   
		/**
		* Adds a field to the form of a given type
		*
		* @access   public
		* @see		$formFields
		* @param		string	The description for the field in the rendered form
		* @param		string	The fieldname in the returned array
		* @param		string	The default value (if any)
		* @param		mixed		Either an array for a combobox, or similar data stream
		* @param		string	The type of field: text, hidden, combo, list, area, radio or check
		* @author   Oyvind Selbek <oyvind@selbek.com>
		*/ 
		function addField( $description, $fieldname, $default = null, $data = null, $type = 'text', $onchange = '' ) {
			
			$this->formFields[] = array( 'desc'		=> $description,
												  'field'	=> $fieldname,
												  'default'	=> $default,
												  'data'		=> $data,
								 				  'type'		=> strtolower( $type ),
								 				  'onchange'=> $onchange,
								 				);
		}
		
		/**
		* Quickfunction to add a submit button to the current form
		*
		* @access   public
		* @see		addField()
		* @param		string	The text on the submit button to create
		* @param		string	The text on the back button or blank to hide it
		* @author   Oyvind Selbek <oyvind@selbek.com>
		*/ 
		function addSubmit( $submit = 'Submit', $back = 'Back' ) {
			
			$this->addField( $submit, $back, null, null, 'submit' );
			
		}

		/**
		* Quickfunction to add a generic button to the current form
		*
		* @access   public
		* @see		addField()
		* @param		string	The text on the generic button to create
		* @param		string	The url to go to when its clicked (optional)
		* @param		string	Alternative javascript to execute instead.
		* @author   Oyvind Selbek <oyvind@selbek.com>
		*/
		function addButton( $caption = 'Send', $url = '', $javascript = '' ) {
			
			$this->addField( $caption, '', $url, $javascript, 'button' );
			
		}
		
		/**
		* Quickfunction to add a hidden field to the current form
		*
		* @access   public
		* @see		addField()
		* @param		string	The fieldname of the variable
		* @param		string	The value to store in the variable
		* @author   Oyvind Selbek <oyvind@selbek.com>
		*/ 
		function addHidden( $fieldname, $value ) {
			
			$this->addField( null, $fieldname, $value, null, 'hidden' );
			
		}
	
		/**
		* Add RAW HTML / JAVASCRIPT
		*
		* @access   public
		* @see		addField()
		* @param	string	Optional html in the row
		* @author   Oyvind Selbek <oyvind@selbek.com>
		*/ 
	
		function addRaw($description, $text) {
			$this->addField( $description, null, null, $text, 'raw' );
	
		}	
	
		/**
		* Quickfunction to add a spacer row to the current form
		*
		* @access   public
		* @see		addField()
		* @param	string	Optional text in the row
		* @author   Oyvind Selbek <oyvind@selbek.com>
		*/ 
		function addSpacer( $text = null ) {
		
			$this->addField( null, null, null, $text, 'spacer' );
	     	
		}
	
		/**
		* Quickfunction to add a spacer row to the current form
		*
		* @access   public
		* @see		addField()
		* @param	string	Optional text in the row
		* @author   Oyvind Selbek <oyvind@selbek.com>
		*/ 
		function addHeader( $text = null ) {
		
			$this->addField( null, null, null, '<b>'.$text.'</b>', 'spacer' );
	     	
		}
	   
		/**
		* Standard GUI function - Return as HTML
		*
		* @access   public
		* @return	string
		* @author   Oyvind Selbek <oyvind@selbek.com>
		*/	
		function ReturnHTML() {
			
			$return = "<table class=\"form-table\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\">\n";
			$return.= $this->returnFormStart($this->formVar);
			
			foreach( $this->formFields as $field ) {
				
				switch( $field['type'] ) {
	
					//addRaw() - EIVINDHACK
					case 'raw':
						$return .= $this->returnRow( $this->returnCol( $field['desc'], $field['data'] ));
						break;
					
					case 'text':
						$return .= $this->returnRow( $this->returnCol( $field['desc'], $this->returnText( $field ) ) );
						break;
					
					case 'date':
						$return .= $this->returnRow( $this->returnCol( $field['desc'], $this->returnDate( $field ) ) );
						break;
	
					case 'datetime':
						$return .= $this->returnRow( $this->returnCol( $field['desc'], $this->returnDateTime( $field ) ) );
						break;
					
					case 'password':
						$return .= $this->returnRow( $this->returnCol( $field['desc'], $this->returnText( $field, true ) ) );
						break;
					
					case 'list':
						$return .= $this->returnRow( $this->returnCol( $field['desc'], $this->returnList( $field ) ) );
						break;
					
					case 'area':
						$return .= $this->returnRow( $this->returnCol( $field['desc'], $this->returnArea( $field ) ) );
						break;
					
					case 'combo':
						$return .= $this->returnRow( $this->returnCol( $field['desc'], $this->returnCombo( $field ) ) );
						break;
					
					case 'combolist':
						$return .= $this->returnRow( $this->returnCol( $field['desc'], $this->returnComboList( $field ) ) );
						break;
					
					case 'autocomplete':
						$return .= $this->returnRow( $this->returnCol( $field['desc'], $this->returnAutoComplete( $field ) ) );
						break;
					
					case 'radio':
						$return .= $this->returnRow( $this->returnCol( $field['desc'], $this->returnRadio( $field ) ) );
						break;
					
					case 'check':
						$return .= $this->returnRow( $this->returnCol( $field['desc'], $this->returnCheck( $field ) ) );
						break;
					
					case 'slider':
						$return .= $this->returnRow( $this->returnCol( $field['desc'], $this->returnSlider( $field ) ) );
						break;

					case 'button':
						$return .= $this->returnRow( $this->returnCol( $field['desc'], $this->returnButton( $field ) ) );
						break;
					
					case 'submit':
						$return .= $this->returnRow( $this->returnCol( ( $this->backLink ? "<a href=\"$this->backLink\">" . $field['field'] . "</a>" : '&nbsp;' ), $this->returnSubmit( $field ) ) );
						break;
					
					case 'file':
                  $return .= $this->returnRow( $this->returnCol( $field['desc'], $this->returnFile( $field ), (isset($class) ? $class : null) ), (isset($class) ? $class : null) );
                  break;
					
					case 'spacer':
                  if( $field['data'] ) {
                     $return .= $this->returnRow( $this->returnCol( $field['data'] ) );
                  } else {
                     $return .= $this->returnRow( $this->returnCol( '&nbsp;' ) );
                  }
						break;
					
					case 'hidden':
						$return .= $this->returnHidden( $field );
						break;
					
				}
				
			}
			
			$return .= $this->returnFormEnd();
			$return .= "\n</table>\n";
			
			return $return;
			
		}
		
		/**
		* Returns a form start-tag, possibly with a overridden name-attribute
		*
		* @access   public
		* @see		ReturnHTML()
		* @param		string	Optional name on the form-tag
		* @return	string
		* @author   Oyvind Selbek <oyvind@selbek.com>
		*/ 
		function returnFormStart( $overRideFormName = null ) {
			
			if( $this->formStartReturned++ ) return '';
			$formName = $overRideFormName ? " name=\"$overRideFormName\"":"";
			return "<form$formName method=\"post\" id=\"".$this->formVar."form\" action=\"$this->baseLink\" onSubmit=\"$this->submitAction\" enctype=\"multipart/form-data\">\n<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"268435456\">\n";
			
		}
	
		/**
		* Returns a form end-tag
		*
		* @access   public
		* @see		ReturnHTML()
		* @return	string
		* @author   Oyvind Selbek <oyvind@selbek.com>
		*/ 
		function returnFormEnd() {
	
			return "</form>\n";
	
		}
		
		/**
		* Returns a formatted HTML row from a regular td-set row
		*
		* @access   public
		* @see		ReturnHTML()
		* @param		string	the row of tds in a string
		* @return	string
		* @author   Oyvind Selbek <oyvind@selbek.com>
		*/ 
		function returnRow( $row, $class = null ) {
			
			return "<tr ".($class?'class="'.$class.'" ':'')."height=\"24\">\n$row</tr>\n";
			
		}
		
		/**
		* Returns a formatted HTML column/columnset
		*
		* @access   public
		* @see		ReturnHTML()
		* @param		string	Columndata
		* @param		string	Optional Columndata
		* @return	string
		* @author   Oyvind Selbek <oyvind@selbek.com>
		*/
		function returnCol( $col1 = null, $col2 = null, $nowrap = false ) {
			
			if( strlen( $col2 ) > 0 ) { 
				
				return "<td class=\"gui\" valign=\"top\" style=\"padding-top: 4px;\"".($nowrap?' nowrap':'').">".$col1."</td><td class=\"gui\" colspan=\"20\" width=\"$this->styleWidth\"".($nowrap?' nowrap':'').">".$col2."</td>";
				
			} else {
				
				return "<td class=\"gui\" colspan=\"2\"".($nowrap?' nowrap':'').">".$col1."</td>";
				
			}
			
		}
		
		/**
		* Returns a field formatted as a text-input field
		*
		* @access   public
		* @see		ReturnHTML()
		* @param		string	the field to be formatted
		* @param		string	wether or not to mask the text (for a password field)
		* @return	string
		* @author   Oyvind Selbek <oyvind@selbek.com>
		*/
		function returnText( $field, $masktext = '' ) {
			
			return "<input class=\"form\" style=\"width: $this->styleWidth;\" ".($masktext ? 'autocomplete="off"':'')." type=\"".($masktext?'password':'text')."\" id=\"$this->formVar$field[field]\" name=\"$this->formVar[$field[field]]\" value=\"".utf8_encode( htmlentities( utf8_decode( $field['default'] ), ENT_QUOTES ) )."\" ".(isset($field['attrdata'])?$field['attrdata']:'').($field['onchange']?' onchange="'.$field['onchange'].'"':'')."/>\n";
			
		}

		/**
		* Returns a field formatted as a text-input field
		*
		* @access   public
		* @see		ReturnHTML()
		* @param		string	the field to be formatted
		* @param		string	wether or not to mask the text (for a password field)
		* @return	string
		* @author   Oyvind Selbek <oyvind@selbek.com>
		*/
		function returnButton( $field ) {
			
			$url = $field['default'];
			$jst = $field['data'];
			
			return "<input class=\"formbutton\" type=\"button\" id=\"$this->formVar$field[field]\" value=\"".htmlentities( $field['desc'], ENT_QUOTES )."\" onclick=\"".($jst ? $jst : "window.location='$url';")."\" />\n";
			
		}
		
		/**
		* Returns a field formatted as a hidden field
		*
		* @access   public
		* @see		ReturnHTML()
		* @param		string	the field to be formatted
		* @return	string
		* @author   Oyvind Selbek <oyvind@selbek.com>
		*/ 
		function returnHidden( $field ) {
			
			return "<input type=\"hidden\" id=\"$this->formVar$field[field]\" name=\"$this->formVar[$field[field]]\" value=\"".htmlentities( $field['default'], ENT_QUOTES )."\" />\n";
	
		}
		
		/**
		* Returns a field formatted as a combobox (pulldown)
		*
		* @access   public
		* @see		ReturnHTML()
		* @param		string	the field to be formatted
		* @return	string
		* @author   Oyvind Selbek <oyvind@selbek.com>
		*/ 
		function returnCombo( $field ) {
			
		   $elementsize = 155; //(int) $this->styleWidth;
			$return = "<select class=\"form\" style=\"width: {$elementsize}px !important;\" id=\"$this->formVar$field[field]\" name=\"$this->formVar[$field[field]]\"".($field['onchange']?' onchange="'.$field['onchange'].'"':'').">\n";
			if(count($field['data'] ))
			foreach( $field['data'] as $key => $value ) {
				$return .= "<option value=\"".utf8_encode( htmlentities( utf8_decode( $key ), ENT_QUOTES ) )."\"".($key==$field['default']?' selected':'').">".str_replace( '&amp;', '&', utf8_encode( htmlentities( utf8_decode( $value ), ENT_QUOTES ) ) )."</option>\n";
			}
			return $return."</select>\n";
			
		}
		
		/**
		 * Returns an Local Autocompleter
		 *
		 * @param unknown_type $field
		 * @return unknown
		 */
		function returnAutoComplete( $field ) {
		   
		   $buffer = "<input class=\"form\" style=\"width: $this->styleWidth;\" autocomplete=\"off\" type=\"text\" id=\"{$this->formVar}{$field['field']}_text\" value=\"".utf8_encode( htmlentities( utf8_decode( $field['data'][$field['default']] ), ENT_QUOTES ) )."\" ".(isset($field['attrdata'])?$field['attrdata']:'').($field['onchange']?' onchange="'.$field['onchange'].'"':'')."/>\n";
		   $buffer.= "<input type=\"hidden\" id=\"$this->formVar$field[field]\" name=\"$this->formVar[$field[field]]\" value=\"".utf8_encode( htmlentities( utf8_decode( $field['default'] ), ENT_QUOTES ) )."\" />\n";
		   $buffer.= "<div class=\"autocomplete\" id=\"{$this->formVar}{$field['field']}_update\" style=\"display:none\"></div>";
		   
		   $fieldhash = md5( $this->formVar.$field['field'] );
		   foreach( $field['data'] as $key => $value ) {
		      $items[] = "'$key|".utf8_encode( htmlentities( utf8_decode( $value ), ENT_QUOTES ) )."'";
		   }  $items = implode( ",\n", $items );
		   
		   $buffer.= "<script language=\"JavaScript\">
		   Autocompleter.LocalFieldUpdater = Class.create(Autocompleter.Local, {
            
            selectEntry: function() {
               this.active = false;
               var currentEntry = this.getCurrentEntry();
               var splititems = currentEntry.id.split('_');
               $( splititems[1] ).value = splititems[2];
               this.updateElement(currentEntry);
            },
            
		   } );
		   
		   var list$fieldhash = [$items];
         new Autocompleter.LocalFieldUpdater('{$this->formVar}{$field['field']}_text', '{$this->formVar}{$field['field']}_update', list$fieldhash, { 
            fullSearch: true,
            partialSearch: true,
            selector: function(instance) {
        var ret       = []; // Beginning matches
        var partial   = []; // Inside matches
        var entry     = instance.getToken();
        var count     = 0;

        for (var i = 0; i < instance.options.array.length &&  
          ret.length < instance.options.choices ; i++) { 

          var elem = instance.options.array[i].split('|');
          var indx = elem[0];
              elem = elem[1];
          var foundPos = instance.options.ignoreCase ? 
            elem.toLowerCase().indexOf(entry.toLowerCase()) : 
            elem.indexOf(entry);

          while (foundPos != -1) {
            if (foundPos == 0 && elem.length != entry.length) { 
              ret.push('<li id=\"item_{$this->formVar}{$field['field']}_'+indx+'\"><strong>' + elem.substr(0, entry.length) + \"</strong>\" + 
                elem.substr(entry.length) + \"</li>\");
              break;
            } else if (entry.length >= instance.options.partialChars && 
              instance.options.partialSearch && foundPos != -1) {
              if (instance.options.fullSearch || /\s/.test(elem.substr(foundPos-1,1))) {
                partial.push('<li id=\"item_{$this->formVar}{$field['field']}_'+indx+'\">' + elem.substr(0, foundPos) + \"<strong>\" +
                  elem.substr(foundPos, entry.length) + \"</strong>\" + elem.substr(
                  foundPos + entry.length) + \"</li>\");
                break;
              }
            }
            
            foundPos = instance.options.ignoreCase ? 
              elem.toLowerCase().indexOf(entry.toLowerCase(), foundPos + 1) : 
              elem.indexOf(entry, foundPos + 1);

          }
        }
        if (partial.length)
          ret = ret.concat(partial.slice(0, instance.options.choices - ret.length))
        return \"<ul>\" + ret.join('') + \"</ul>\";
      }
         } );
         </script>
         ";
		   
		   return $buffer;
			
		   
		}
		
		/**
		* Returns a field formatted as a combobox (pulldown)
		*
		* @access   public
		* @see		ReturnHTML()
		* @param		string	the field to be formatted
		* @return	string
		* @author   Oyvind Selbek <oyvind@selbek.com>
		*/ 
		function returnComboList( $field ) {
			
			$id = md5("$this->formVar[$field[field]]");
			
			$return .= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td id=\"div_$id\"></td></tr></table>";
			$return .= "<script>cb_$id = new ComboBox(\"cb_$id\", document.getElementById('div_$id'));\n";
			if(count($field['data'] ))
			foreach( $field['data'] as $key => $value ) 
				$return .= "cb_$id.add( new ComboBoxItem('".htmlentities( $value, ENT_QUOTES )."','".htmlentities( $key, ENT_QUOTES )."') );";
			return $return."</script>\n";
			
		}
		
		/**
		* Returns a field formatted as a listbox
		*
		* @access   public
		* @see		ReturnHTML()
		* @param		string	the field to be formatted
		* @return	string
		* @author   Oyvind Selbek <oyvind@selbek.com>
		*/ 
		function returnList( $field ) {
			
			$return = "<listbox class=\"form\" style=\"width: $this->styleWidth;\" id=\"$this->formVar$field[field]\" name=\"$this->formVar[$field[field]]\">\n";
			foreach( $field['data'] as $key => $value ) 
				$return .= "<option value=\"".htmlentities( $key, ENT_QUOTES )."\"".($key==$field['default']?' selected':'').">".str_replace( '&amp;', '&', htmlentities( $value, ENT_QUOTES ) )."</option>\n";
			return $return."</listbox>\n";
			
		}
		
		/**
		* Returns a field formatted as a textarea
		*
		* @access   public
		* @see		ReturnHTML()
		* @param		string	the field to be formatted
		* @return	string
		* @author   Oyvind Selbek <oyvind@selbek.com>
		*/ 
		function returnArea( $field ) {
			
			return "<textarea class=\"form\" style=\"width: $this->styleWidth; height: {$field['data']['height']}\" id=\"$this->formVar$field[field]\" name=\"$this->formVar[$field[field]]\" ".(isset($field['attrdata'])?$field['attrdata']:'').">$field[default]</textarea>\n";
	
		}
		
		/**
		* Returns a field formatted as a radiobutton-set
		*
		* @access   public
		* @see		ReturnHTML()
		* @param		string	the field to be formatted
		* @return	string
		* @author   Oyvind Selbek <oyvind@selbek.com>
		*/ 
		function returnRadio( $field ) {
			
			return "<input type=\"radio\" id=\"$this->formVar$field[field]\" name=\"$this->formVar[$field[field]]\" value=\"1\" ".($field['default']?'checked':'')."/>\n";
	
		}
	
		/**
		* Returns a field formatted as a checkbox-set
		*
		* @access   public
		* @see		ReturnHTML()
		* @param		string	the field to be formatted
		* @return	string
		* @author   Oyvind Selbek <oyvind@selbek.com>
		*/ 
		function returnCheck( $field ) {
			
			return "<input type=\"checkbox\" id=\"$this->formVar$field[field]\" name=\"$this->formVar[$field[field]]\" value=\"1\" ".(isset($field['default'])&&$field['default']?'checked':'')."/>\n";
			
		}
		
		/**
		* Returns a field formatted as a submit-button
		*
		* @access   public
		* @see		ReturnHTML()
		* @param		string	the field to be formatted
		* @return	string
		* @author   Oyvind Selbek <oyvind@selbek.com>
		*/ 
		function returnSubmit( $field ) {
			
			return "<input class=\"formbutton\" type=\"submit\" name=\"$this->formVar[".strtolower(str_replace(' ','', $field['desc'] ))."]\" value=\"$field[desc]\" />\n";
			
		}
		
		function returnDate( $field ) {
			
			$format = $field['data'] ? $field['data'] : '%Y-%m-%d';
			return "<input class=\"form\" type=\"text\" name=\"$this->formVar[$field[field]]\" id=\"$this->formVar$field[field]\" value=\"".htmlentities( $field['default'], ENT_QUOTES )."\" size=\"30\" style=\"width: 130px;\" /><input class=\"formbutton\" type=\"reset\" value=\"...\" onclick=\"return showCalendar('$this->formVar$field[field]', '$format', false, true);\" style=\"width: 20px;\">";
			
		}
	
		function returnDateTime( $field ) {
			
			$format = $field['data'] ? $field['data'] : '%Y-%m-%d %H:%M:00';
			return "<input class=\"form\" type=\"text\" name=\"$this->formVar[$field[field]]\" id=\"$this->formVar$field[field]\" value=\"".htmlentities( $field['default'], ENT_QUOTES )."\" size=\"30\" style=\"width: 130px;\" /><input class=\"formbutton\" type=\"reset\" value=\"...\" onclick=\"return showCalendar('$this->formVar$field[field]', '$format', '24', true);\" style=\"width: 20px;\">";
			
		}
		
		function returnSlider( $field ) {
			
			// retrieve the value for the field
			$pos = htmlentities( $field['default'], ENT_QUOTES );
			
			// check for minmax-data
			if( $field['data'] ) {
				list( $min, $max, $sign ) = explode( ',', $field['data'] );
			}
			
			// sanitize input
			$min = (int) $min;
			$max = (int) $max;
			$pos = (int) $pos;
			
			// define values
			if( !$pos ) $pos = 0;
			if( !$min ) $min = 0;
			if( !$max ) $max = 100;
			
			// generate a unique id
			$id = md5("$this->formVar[$field[field]]");
			
			// return the HTML
			return "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td><div class=\"slider\" id=\"slider_$id\" tabIndex=\"1\"><input name=\"$this->formVar[$field[field]]\" class=\"slider-input\" id=\"slider_input_$id\" /></div></td><td>&nbsp;<span id=\"slider_text_$id\">$pos$sign</span></td></tr></table>
	<script type=\"text/javascript\">
		var slider_obj_$id = new Slider(document.getElementById(\"slider_$id\"), document.getElementById(\"slider_input_$id\"));
		slider_obj_$id.setValue($pos);
		slider_obj_$id.setMinimum($min);
		slider_obj_$id.setMaximum($max);
		slider_obj_$id.onchange = function () {
			if( document.getElementById(\"slider_text_$id\") ) {
				document.getElementById(\"slider_text_$id\").innerHTML = slider_obj_$id.getValue()+'$sign';
			}
		};
	</script>";
			
		}
		
   	function returnFile( $field ) {
   		
   		if( $field['data'] ) {
   			
   			return "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td valign=\"top\" class=\"gui\">{$field['data']}</td><td valign=\"top\" class=\"gui\">&nbsp;</td><td valign=\"top\" class=\"gui\"><input class=\"form\" type=\"file\" name=\"$this->formVar[".$field['field']."]\" /></td></tr></table>\n"; // strtolower(str_replace(' ','', $field[desc] ))
   			
   		} else {
   			
   			return "<input class=\"form\" type=\"file\" name=\"$this->formVar[".$field['field']."]\" />\n"; // strtolower(str_replace(' ','', $field[desc] ))
   			
   		}
   		
   	}
		
	}
	
?>