<?php

   /**
   * class for rendering tables to HTML/WML/XML
   *
   * @author   Oyvind Selbek <oyvind@selbek.com>
   * @package  gui
   * @access   public
   * @version   1.0
   */
   class table extends gui_template {
      
      /**
       * The renderid of the current table. used in a lot of javascripts and
       * other supporting scripts in the underlaying system and core codebase.
       *
       * @var      array
       * @see      table(), setColumns()
       * @access   private
       */
      var $tableRenderId;
      
      /**
       * The actual tablecolumns as held internally until rendered or freed
       *
       * @var      array
       * @see      table(), setColumns()
       * @access   private
       */
      var $tableColumns;
      
      /**
       * The actual tablerows as held internally until rendered or freed
       *
       * @var      array
       * @see      table(), addRow()
       * @access   private
       */
      var $tableRows;
      
      /**
       * An array of $width, $cellspacing, $cellpadding, $border for formatting.
       * All members of this array can be set as parameters to the constructor.
       *
       * @var      array
       * @see      table()
       * @access   private
       */
      var $tableFormatting;
      
      /**
       * Wether or not editMode is enabled
       *
       * @var      boolean 
       * @see      SetEditMode()
       * @access   public
       */
       
      var $editMode;
      
      /**
       * Wether or not autoHide is enabled
       *
       * @var      boolean 
       * @see      SetAutoHide()
       * @access   public
       */
      var $autoHide;
   
      /**
       * The baselink, if set
       *
       * @var      string
       * @see      SetBaseLink()
       * @access   public
       */
      var $baseLink;
   
      /**
       * The addlink, if set
       *
       * @var      string
       * @see      SetAddLink()
       * @access   public
       */
      var $addLink;
   
      /**
       * The addRow's data, if set
       *
       * @var      string
       * @see      SetAddRowData()
       * @access   public
       */
      var $addRowData;
      
      /**
       * The default strings to be used
       *
       * @var      array
       * @see      SetTextString()
       * @access   private
       */
      var $textStrings;
      
      /**
       * The attached form, if any
       *
       * @var      object form
       * @see      attachForm()
       * @access   public
       */
      var $attachedForm;
      
      var $allowCellClickEdit;
      var $sortable = true;
      var $sortRowClassNames;
      var $autoSortRowId;
      var $pathMode;
      
      /**
      * class constructor
      *
      * @access   public
      * @param      string   With of the table in percent or pixels
      * @param      integer   The cellspacing of the tablecells
      * @param      integer   The cellpadding of the tablecells
      * @param      integer   The border of the table
      * @param      array      The actual columns, thrown to SetColumns() if set
      * @see      SetColumns()
      * @author   Oyvind Selbek <oyvind@selbek.com>
      */   
      function table( $width='', $cellspacing=0, $cellpadding=0, $border=0, $columns = array() ) {
         
         // execute the parent constructor
         $this->gui_template();
         
         // setup basic dataholding arrays
         $this->tableRows = array();
         $this->tableColumns = array();
         $this->tableFormatting = array( $width, $cellspacing, $cellpadding, $border );
         
         // forward columnsetup, if any
         if( count( $columns ) ) 
            $this->SetColumns( $columns );
         
         // default enable autohiding of editTools
         $this->setAutoHide( false );
         
         // default enable clicking of tablecells
         $this->SetAllowCellClickEdit( true );
         
         // setup default text strings
         $this->SetTextStrings(
            array(
               'add'      => __( 'add' ),
               'edit'   =>   __( 'edit' ),
               'delete'   => __( 'delete' ),
               'edithead'=> __( 'Edit' ),
            )
         );
            
         
      }
      
      /**
      * Cheap function to enable form-integrated rendering.
      * TODO: someone write a sample on how this works! ;)
      *
      * @access   public
      * @param      object   Form to be attached
      * @author   Oyvind Selbek <oyvind@selbek.com>
      */ 
      function attachForm( &$form ) {
         
         $this->attachedForm = &$form;
         
      }
      
      /**
      * Enables or disables autoHide on the component.
      * Please note that this can be overriden in renderSettings on a user-level
      *
      * @access   public
      * @param      boolean   The autoHide of the component
      * @author   Oyvind Selbek <oyvind@selbek.com>
      */ 
      function SetAutoHide( $autoHide ) {
      
         $this->autoHide = $autoHide;
         
      }
      
      function setSortable( $sortable = true ) {
         
         $this->sortable = $sortable;
         
      }
      
      /**
      * Enables or disables editMode on the component
      *
      * @access   public
      * @param      boolean   The editMode of the component
      * @author   Oyvind Selbek <oyvind@selbek.com>
      */   
      function SetEditMode( $editMode ) {
         
         $this->editMode = $editMode;
         
      }
      
      /**
      * Sets the base link for the back button and other url operations.
      *
      * @access   public
      * @param      string   A baselink for all url operations
      * @author   Oyvind Selbek <oyvind@selbek.com>
      */   
      function SetBaseLink( $baseLink ) {
         
         if( $this->pathMode ) {
            $this->baseLink = $baseLink;
         } else {
            $this->baseLink = $baseLink.( strpos( $baseLink, '?' ) === false ? '?' : '&' );
         }
         
      }
   
      /**
      * Sets the add-link, or disables the feature.
      *
      * @access   public
      * @param      string   A possible addlink, false to disable adding
      * @author   Oyvind Selbek <oyvind@selbek.com>
      */   
      function SetAddLink( $addLink ) {
      
         $this->addLink = $addLink;
         
      }
   
      /**
      * Defines a single or multiple columns of data for the addRow
      *
      * @access   public
      * @param      mixed      string or array of data columns
      * @author   Oyvind Selbek <oyvind@selbek.com>
      */   
      function SetAddRowData( $addRowData ) {
         
         $this->addRowData = $addRowData;
         
      }
      
      /**
      * Sets a textstring, currently add, edit or delete to something
      *
      * @access   public
      * @param      string   the string to set, add, edit or delete
      * @param      string   the changed value, such as "new" for add
      * @author   Oyvind Selbek <oyvind@selbek.com>
      */   
      function SetTextString( $string, $value ) {
      
         $this->textStrings[$string] = $value;
         
      }
      
      function SetTextStrings( $strings ) {
         
         if( is_array( $strings ) ) {
            
            $this->textStrings = $strings;
            
         }
         
      }
      
      function SetAllowCellClickEdit( $allowCellClickEdit = true ) {
         
         $this->allowCellClickEdit = $allowCellClickEdit;
         
      }
      
      /**
      * Specifies the column in the class
      *
      * column sorttypes can be:      String, Number, Date or None
      * column align can be:         left, right or center
      *
      * $table = new table();                                        
      *                                                                 
      * $table->SetColumns( array(                                   
      *                         'Numeric'    => array('number', 'right'),
      *                         'Alpha #1'   => array('string'),         
      *                         'Alpha #2'    => array('string'),         
      *                         'ISO Date'   => array('date'),           
      *                         'NO Date w/Time'   => array('dateno'),   
      *                         'US Date'   => array('dateus'),         
      *                      ) );                                       
      *    
      * $table->Render();
      *
      * @access   public
      * @param   array      The columns, following the above structure
      * @author   Oyvind Selbek <oyvind@selbek.com>
      */   
      function SetColumns( $columns ) {
         
         $this->tableColumns = array();
         $this->AddColumns( $columns );
         
      }
      
      /* Adds columns to a table
      * @access   public
      * @param   array      The columns, following the above structure   
      * @author   Oyvind Selbek <oyvind@selbek.com>
      */
      
      function AddColumns( $columns ) {
         
         foreach( $columns as $column => $typedata ) 
            $this->tableColumns[$column] = $typedata;
      }
   
      /**
      * Clears all rows already added to a table
      *
      * @access   public
      * @return    integer   Number of rows cleared from the table
      * @author   Oyvind Selbek <oyvind@selbek.com>
      */   
      function Clear() {
         $return = count( $this->tableRows );
         unset( $this->tableRows );
         $this->tableRows = array();
         $this->tableColumns = array();
         return $return;
      }
   
   
      /**
      * Adds a row to the table
      *
      * @access      public
      * @param       array       $row                 The actual row to add, as an array. each cell in the array can be an 
      *                                               array( (string) celltext, (int) colspan ) to implement colspanning.
      * @param       string      $editlink            A possible editlink, false to disable editing
      * @param       string      $deletelink          A possible deletelink, false to disable deleting
      * @param       string      $deletequestion      A possible delete question if deleting is enabled
      * @param       array       $rowstyles           Keeps the style of the row, such as class or a custom style attribute
      * @author      Oyvind Selbek <oyvind@selbek.com>
      */   
      function AddRow( $row, $editlink = NULL, $deletelink = NULL, $deletequestion = NULL, $rowstyles = null ) {
         foreach( $row as $cell ) $tableRow[] = $cell;
         $this->tableRows[] = array( $tableRow, $editlink, $deletelink, addslashes( $deletequestion ), $rowstyles );
      }
      
      function setSortRowClassNames( $class1, $class2 ) {
         
         if( $class1 === NULL && $class2 == NULL ) {

            $this->sortRowClassNames = NULL;

         } else {
            
            $this->sortRowClassNames = array( $class1, $class2 );
            
         }
         
      }
      
      function sort( $rowid = 0 ) {
         
         $this->autoSortRowId = $rowid;
         
      }
      
      function setPathMode( $pathMode = true ) {
         
         $this->pathMode = $pathMode;
         
      }
   
      /**
      * Standard GUI function - Return as HTML
      *
      * @access   public
      * @return   string
      * @author   Oyvind Selbek <oyvind@selbek.com>
      */   
      function ReturnHTML() {
   
         // remember to increase the tablerenderid
         global $tableRenderId;
         $this->tableRenderId = ++$tableRenderId;
         
         // allow noautohide to be overridden by user
         if( isset( $this->renderSettings['noautohide'] ) && $this->renderSettings['noautohide'] ) {
            $this->SetAutoHide( false );
         }
         
         // check if we're editing, if so, allow edit-js-features
         if( $this->autoHide && $this->editMode ) {
            
            // generate some javascript if in editmode, starting with the header
            $editshow = "document.getElementById('table_".$this->tableRenderId."_editheader').style.display=''; ";
            $edithide = "document.getElementById('table_".$this->tableRenderId."_editheader').style.display='none'; ";
            
            // process every row
            for( $i = 0; $i < count( $this->tableRows ); $i++ ) {
            $editshow.= "document.getElementById('table_".$this->tableRenderId."_editrow_$i').style.display=''; ";
            $edithide.= "document.getElementById('table_".$this->tableRenderId."_editrow_$i').style.display='none'; ";
            }
            
            // and append the final addrow
            if( $this->addLink ) {
            $editshow.= "document.getElementById('table_".$this->tableRenderId."_editaddrow').style.display=''; ";
            $edithide.= "document.getElementById('table_".$this->tableRenderId."_editaddrow').style.display='none'; ";
            }
            
         } else {
   
            $editshow = '';
            $edithide = '';
            
         }
         
         @list( $width, $cellspacing, $cellpadding, $border ) = $this->tableFormatting;
         
         $return = "
      <table width=\"$width\" class=\"sort-table\" id=\"table$this->tableRenderId\" cellspacing=\"$cellspacing\" cellpadding=\"$cellpadding\" border=\"$border\" onmouseover=\"$editshow\" onmouseout=\"$edithide\">
         ";
         
         // form render support
         if( $this->attachedForm && $this->attachedForm instanceof form ) {
            $return .= $this->attachedForm->returnFormStart();
            foreach( $this->attachedForm->formFields as $field ) 
               if( $field['type']=='hidden' ) // render all hidden fields
                  $return .= $this->attachedForm->returnHidden( $field );
         }
         
         // iterate through all table columns and create column format holders
         foreach( $this->tableColumns as $column => $typedata ) {
            @list( $type, $align, $nowrap ) = $typedata;
            if( !$align ) $align = 'left';
            $return .= "<col ". ($align != 'left'? "style=\"text-align: $align\" ":'')."/>
         ";
         }
         
         // accomodate for editmode
         if ($this->editMode) {
            $return .= "<col />
         ";
         }
         
         $return .= "
         <thead>
            <tr>
               ";
   
         foreach( $this->tableColumns as $column => $typedata ) {
            $return .= "<td class=\"gui\">".($column!=''?str_replace(' ','&nbsp;',$column):'&nbsp;')."</td>
               ";
         }
   
         if ($this->editMode) {
            $style   = $this->autoHide ? ' style="display:none;"':'';
            $return .= "<td class=\"gui\"$style id='table_".$this->tableRenderId."_editheader'>".$this->textStrings['edithead']."</td>
         ";
         }
         
         $return .= "
            </tr>
         </thead>
         <tbody>
               ";
         
         $rowid = 0;
         
         foreach( $this->tableRows as $row => $rowdata ) {
            
            @list( $cells, $editlink, $deletelink, $deletequestion, $rowstyles ) = $rowdata;
            
            $rowclass = null;
            $rowstyle = null;
            
            if( is_array( $rowstyles ) ) {
               
               if( isset( $rowstyles['class'] ) ) {
                  $rowclass = $rowstyles['class'];
               }
               if( isset( $rowstyles['style'] ) ) {
                  $rowstyle = $rowstyles['style'];
               }
               
            }
            
            $return .= "<tr".(is_null($rowclass) ? '':" class=\"$rowclass\"").(is_null($rowstyle) ? '':" style=\"$rowstyle\"")." onmouseover=\"this.className='tableRowMouseOver';\" onmouseout=\"this.className='".(is_null($rowclass) ? 'tableRow':$rowclass)."';\" height=\"24\">
                  "; // 
            foreach( $cells as $column => $cell ) {
               
               // patch to allow for spanning cells
               if( is_array( $cell ) ) { list( $cell, $colspan ) = $cell; } else $colspan = 1;
               
               // not so pretty mapping patch
               reset( $this->tableColumns ); for( $i=0; $i < $column; $i++) next($this->tableColumns);
               @list( $type, $align, $nowrap ) = current($this->tableColumns);
               if( !$align ) $align = 'left';
               $return .= "<td colspan=\"$colspan\" ".($align!='left' ? "align=\"$align\" ":'')."class=\"gui\"".($editlink ? " style=\"cursor: pointer; cursor: hand;\" ".($this->allowCellClickEdit?"onclick=\"window.location='".$this->baseLink.$editlink."';\"":''):'').($nowrap?' nowrap':'').">".($cell!=''||$cell=='0'?$cell:'&nbsp;')."</td>
                  ";
            }
            
            if( $this->editMode && ($editlink || $deletelink) ) {
            $style  = $this->autoHide ? ' style="display:none;"':'';
            $editcell = $editlink ? '<a href="'.$this->baseLink.$editlink.'"><img src="/images/icons/table_edit.png" alt="'.$this->textStrings['edit'].'" border="0"></a>' : '';
            $deletecell = $deletelink ? '<a href="'.$this->baseLink.$deletelink.'"'.($deletequestion ? " onclick=\"return confirm('$deletequestion');\"" : '').'><img src="/images/icons/table_delete.png" alt="'.$this->textStrings['delete'].'" border="0"></a>' : '';
            $return .= "<td class=\"gui\"$style id='table_".$this->tableRenderId."_editrow_".$rowid++."'>".$editcell.($editcell?'&nbsp;':'').$deletecell."</td>
                  ";
            
            $return .= "
               </tr>
               ";
            }
            
         }
         
         if( $this->addLink ) {
            
            $addcell = '<a href="'.$this->baseLink.$this->addLink.'"><img src="/images/icons/table_add.png" alt="'.$this->textStrings['add'].'" border="0" align="absmiddle"></a> <a href="'.$this->baseLink.$this->addLink.'">'.$this->textStrings['add'].'</a>';
            $style   = $this->autoHide ? ' style="display:none;"':'';
            $return .= "<tr$style id='table_".$this->tableRenderId."_editaddrow' class='bottomRow' onmouseover=\"this.style.backgroundColor='ButtonFace';\" onmouseout=\"this.style.backgroundColor='ButtonFace';\">";
            
            if( is_array( $this->addRowData ) ) {
               
               $offset = 0;
               
               foreach( $this->addRowData as $column => $cell ) {
   
                  // not so pretty mapping patch
                  reset( $this->tableColumns ); for( $i=0; $i < $column + $offset; $i++) next( $this->tableColumns );
                  @list( $type, $align, $nowrap ) = current( $this->tableColumns );
                  
                  // patch to allow for spanning cells
                  if( is_array( $cell ) ) { list( $cell, $colspan ) = $cell; } else $colspan = 1;
                  
                  // increasde offset by number of extra colums
                  if( $colspan > 1 ) $offset += $colspan - 1;
                  
                  // add the row to the table
                  $return .= "<td ".($align!='left' ? "align=\"$align\" ":'')."class=\"gui\" style=\"font-weight: bold;\" colspan=\"".$colspan."\">".$cell."</td>";
                  
               }
               
            } else {
               
               $return .= "<td class=\"gui\" colspan=\"".count($this->tableColumns)."\">".( $this->addRowData ? $this->addRowData : '&nbsp;' )."</td>";
               
            }
            
            if( $this->editMode ) {
               
               $return .= "<td class=\"gui\" nowrap>$addcell</td>";
               
            }
            
            $return .= "
               </tr>
               ";
         }

         // form render support
         if( $this->attachedForm && $this->attachedForm instanceof form ) {
            
            $allsubmits = array();
            
            foreach( $this->attachedForm->formFields as $field ) {
               if( $field['type']=='submit' ) // render all submit fields
                   $allsubmits[] = $this->attachedForm->returnSubmit( $field );
               if( $field['type']=='text' ) // render all text fields
                   $allsubmits[] = $this->attachedForm->returnText( $field );
               if( $field['type']=='button' ) // render all button fields
                   $allsubmits[] = $this->attachedForm->returnButton( $field );
            }   
            
            if( count( $allsubmits ) ) {
               
               $allsubmits = '<table class="no-table" cellspacing="0" cellpadding="0" border="0"><tr><td class="gui">'.implode( '</td><td class="gui">', $allsubmits ).'</td></tr></table>';
               $return .= $this->attachedForm->returnRow( $this->attachedForm->returnCol( ( $this->attachedForm->backLink ? "<a href=\"{$this->attachedForm->backLink}\">$field[field]</a>" : '&nbsp;' ), $allsubmits, true ), 'bottomRow' );
            
            }
            
            $return .= $this->attachedForm->returnFormEnd();

         }
         
         $return .= "</tbody>";         
         $return .= "</table>";
         
         foreach( $this->tableColumns as $column => $typedata ) {
            @list( $type, $align, $nowrap ) = $typedata;
            $type = eregi_replace('string','CaseInsensitiveString', $type);
            $type = eregi_replace('date',  'Date',   $type);
            $type = eregi_replace('dateno','DateNO', $type);
            $type = eregi_replace('dateus','DateUS', $type);
            $type = eregi_replace('datedb','DateDB', $type);
            $type = eregi_replace('number','Number', $type);
            $type = eregi_replace('integer','Number', $type);
            $type = eregi_replace('datetimeno','DateTimeNO', $type);
            $type = eregi_replace('datetimeus','DateTimeUS', $type);
            $type = eregi_replace('datetimedb','DateTimeDB', $type);
            $type = eregi_replace('none',  'None',   $type);
            $jointhese[] = '"'.$type.'"';
         }   $jointhese[] = '"None"';
         /*
         if (is_array($jointhese) && $this->sortable ) {
         
            
            $return .= "
      <script type=\"text/javascript\">
      //<![CDATA[
      
      var sorttable$this->tableRenderId = new SortableTable(document.getElementById(\"table$this->tableRenderId\"),
         [";
         
         $return .= implode(', ', $jointhese);
         $return .= "]);
      ";
            if( count( $this->sortRowClassNames )==2 ) {
         
            list( $class1, $class2 ) = $this->sortRowClassNames;
         
            $return .= "
         
      sorttable$this->tableRenderId.onsort = function () {
        var rows = sorttable$this->tableRenderId.tBody.rows;
        var l = rows.length;
        for (var i = 0; i < l; i++) {
          if (rows[i].className != \"bottomRow\")
          {
            rows[i].className = ( i % 2 ? '$class2' : '$class1' );
          }
        }
      }
      
      ";
            }
      
            if( $this->autoSortRowId ) {
         
            $return .= "
      
      // sort asc
      sorttable$this->tableRenderId.sort( $this->autoSortRowId );
      
      // sort desc
      sorttable$this->tableRenderId.sort( $this->autoSortRowId );
      
      ";
      
            }
         
            $return .= "
         
      //]]>
      </script>
         
      ";   
   
            
         }
         */
         // finally, return back the content
         return $return;
         
      }
      
   }

?>