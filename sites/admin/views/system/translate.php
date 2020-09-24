<?php
   
   model( 'i18n.language' );
   model( 'i18n.languagestring' );
   model( 'i18n.languagetranslation' );
   
   import( 'pages.admin' );
   
   import( 'gui.toolkit' );
   
   class Translate extends AdminPage implements IView {
      
      public function Delete( $objectid ) {
         
         $languagestring = new LanguageString( $objectid );
         $languagestring->delete();
         
         $collection = new LanguageTranslation();
         foreach( $collection->collection( array( 'translationid' ), array( 'stringid' => $objectid ) )->fetchAll() as $row ) {
            
            list( $objectid ) = $row;
            
            $languagetranslation = new LanguageTranslation( $objectid );
            $languagetranslation->delete();
            
         }
         
         i18n::reCache( true );
         
         relocate( '/system/translate' );
         
      }
      
      public function Execute( $showConfirmed = false ) {
         
         $language = Language::fromFieldValue( array( 'code' => i18n::$language ), 'language' );
         
         $this->header = __( 'Translate to %s', $language->elementname );
         $this->language = i18n::$language;
         
         if( $showConfirmed ) {
            
            new text( sprintf( '<a href="/system/translate/">%s</a>', __( 'Hide confirmed' ) ) );
            
         } else {
            
            new text( sprintf( '<a href="/system/translate/showconfirmed">%s</a>', __( 'Show confirmed' ) ) );
            
         }
         
         $strings = array();
         $form = new form( 'editor' );
         $table = new table();
         $table->attachForm( $form );
         $table->SetEditMode( true );
         
         $table->SetColumns( array(
            '&nbsp;' => array( 'string'),
            __( 'String' )      => array( 'string' ),
            __( 'Translation' ) => array( 'string' ),
            __( 'OK' ) => array( 'string' ),
            __( 'Template' )    => array( 'string' ),
            __( 'Class' )       => array( 'string' ),
         ) );
         
         $table->SetTextString( 'edithead', __( 'Delete' ) );
         
         // retreive the strings and store them in the i18n strings db
         $collection = new LanguageString();
         
         foreach( $collection->collection( 
            array( 'stringid', 'hash', 'elementname', 'sourcefile', 'sourceclass' ), array(), 'stringid DESC'
         )->fetchAll() as $row ) {
            
            // expand the variables
            list( $objectid, $hash, $string, $file, $class ) = $row;
            
            // attempt to lookup the translation
            $translation = isset( i18n::$strings[$hash] ) ? i18n::$strings[$hash] : '';
            
            // hash up the string
            $strings[$hash] = array( $objectid, $string, $translation, $file, $class );
            
         }
         
         // create a list of confirmed translations
         $collection = new LanguageTranslation();
         foreach( $collection->collection( array( 'stringid' ), array( 'confirmed' => true, 'languageid' => i18n::getLanguageId() ) )->fetchAll() as $row ) {
            list( $objectid ) = $row;
            $wasConfirmed[$objectid] = true;
         }
         
         if( isset( $_POST['editor']['strings'] ) && is_array( $_POST['editor']['strings'] ) ) {
            
            foreach( $_POST['editor']['strings'] as $hash => $var ) {
               
               // make sure its set for this language, and that its not empty
               if( isset( $var[i18n::$language] ) && $var[i18n::$language]['translation'] != '' ) {
                  
                  // fetch the string
                  $string = $var[i18n::$language]['translation'];
                  
                  // check whether we confirmed the value
                  $confirmed = isset( $var[i18n::$language]['confirmed'] ) ? true : false;
                  
                  // find the stringid from the hash in the languagestringtable
                  $languagestring = LanguageString::fromFieldValue( array( 'hash' => $hash ), 'languagestring' );
                  
                  // if already set, make sure its changed
                  if( isset( $strings[$hash] ) ) {
                     
                     list(,,$oldstring ) = $strings[$hash];
                     
                     $oldconfirmed = isset( $wasConfirmed[$languagestring->stringid] ) ? true : false;
                     
                     if( $oldstring == $string && $confirmed == $oldconfirmed ) continue;
                     
                  }
                  
                  // if we found it...
                  if( $languagestring->stringid ) {
                     
                     // find and/or create a new translation record based on the stringid and the languageid
                     $translation = LanguageTranslation::fromFieldValue( array( 'stringid' => $languagestring->stringid, 'languageid' => $language->languageid ), 'languagetranslation' );
                     $translation->translation = $string;
                     $translation->languageid = $language->languageid;
                     $translation->stringid = $languagestring->stringid;
                     $translation->confirmed = $confirmed;
                     $translation->save();
                     
                     // hash up the translation-data so we don't have to look it up again to display it
                     $strings[$hash] = array( $languagestring->stringid, $languagestring->elementname, $string, $languagestring->sourcefile, $languagestring->sourceclass );
                     
                  }
                  
               }
               
            }
            
            // recache the selected language
            i18n::reCache( true );
            
         }
         
         // create a list of confirmed translations
         $collection = new LanguageTranslation();
         foreach( $collection->collection( array( 'stringid' ), array( 'confirmed' => true, 'languageid' => i18n::getLanguageId() ) )->fetchAll() as $row ) {
            list( $objectid ) = $row;
            $isConfirmed[$objectid] = true;
         }

         $table->addrow( array( '&nbsp;', array( '<table class="no-table" cellspacing="0" cellpadding="0" border="0"><tr><td class="gui"><input class="formbutton" type="submit" name="editor[lagre]" value="'.__('Save').'" /></td><td class="gui"><input class="formbutton" type="button" id="editor" value="'.__('AutoTranslate').'" onclick="autoTranslate();" /></td></tr></table>',10 ) ) );
         
         // for each string we have...
         foreach( $strings as $hash => $fields ) {
            
            // expand the fields
            list( $objectid, $string, $translation, $file, $class ) = $fields;
            
            // skip confirmed items if we're not explicitly showing them
            if( !$showConfirmed && isset( $isConfirmed[$objectid] ) ) continue;
            
            // patch the file path a bit
            $file = str_replace( array( 'template:///sites/', '/sites/' ), '', $file );
            
            // add the column to the table
            $table->AddRow( array( '<a href="/system/translate/edit/'.$objectid.'" class="">edit</a>', $string, $form->returnArea( array( 
                     'field' => sprintf( 'strings][%s][%s][translation', $hash, i18n::$language ),
                     'default' => $translation,
                     'data' => array( 'height' => '18px; width: 300px; overflow: hidden;' ),
                     'attrdata' => "onfocus=\"setTextAreaHeightAuto(this); this.select();\" onkeyup=\"setTextAreaHeightAuto(this);\" onblur=\"this.style.height='18px'; var tmp=this.value; this.value=''; this.value=tmp;\" ",
                  ) ), $form->returnCheck( array(
                     'field' => sprintf( 'strings][%s][%s][confirmed', $hash, i18n::$language ),
                     'default' => isset( $isConfirmed[$objectid] ) ? true : false,
                     'data' => '1',
                  ) ), $file, $class ), null, '/system/translate/delete/'.$objectid, __( 'Are you sure you want to delete this language variable and its translations?' ) );
         }
         
         $form->addSubmit( __( 'Save' ) );
         
         $form->addButton( __( 'AutoTranslate' ), '#', 'autoTranslate();' );
         
         $table->render();
         
      }
      
      public function Edit( $objectid ) {
         

         if( $_POST  && $objectid ){
            
            foreach( $_POST['editor']['strings'] as $key=>$string ){
               
               $poststring  =  $string['translation'];
               $postconfirmed  =  $string['confirmed'] == 1 ? true: false;
               
               if( $string['translationid'] > 0 ){
                  $translation = new LanguageTranslation( $string['translationid'] );      
                  $translation->translation =  $poststring;
                  $translation->confirmed = $postconfirmed;
                  $translation->save();
               }else{
                  
                  if( strlen( $poststring ) > 0 && $objectid > 0 && $key > 0 ){
                     $newtranslation = new LanguageTranslation( );
                     $newtranslation->stringid =  $objectid;
                     $newtranslation->languageid = $key;
                     $newtranslation->translation =  $poststring;
                     $newtranslation->confirmed = $postconfirmed;
                     $newtranslation->save();
                  } 
               }
               
            }
            i18n::reCache( true );
            relocate( '/system/translate/edit/' . $objectid );
            die();
         }
         
         
         
         $this->header = __( 'Translate' );
         
         new text(  '<a href="/system/translate/">Tilbake</a>' );
         
         $collection = new LanguageString($objectid);
         $languages = DB::query( "SELECT * FROM i18n_language WHERE active = 't'" )->fetchAll( DB::FETCH_ASSOC );
         $translations = DB::query( "SELECT * FROM i18n_languagestring WHERE stringid = ?", $objectid )->fetchAll( DB::FETCH_ASSOC );
         
         $strings = array();
         $form = new form( 'editor' );
         $table = new table();
         $table->attachForm( $form );
         
         $table->SetColumns( array(
            __( 'Language' ) => array( 'string'),
            __( 'String' )   => array( 'string' ),
            __( 'Confirmed' ) => array( 'string' )
         ) );
         
         
         
         $table->AddRow( array( '&nbsp;',  '<b>' .  $translations[0]['elementname'] . '</b>' , '&nbsp;' ) );
         
         foreach( $languages as $language ){
            
               $collection = new LanguageTranslation();
               $languagestring =  $collection->collection( array( 'translationid', 'languageid',  'stringid', 'translation', 'confirmed' ), array( 'stringid' => $objectid, 'languageid' => $language['languageid']  ) )->fetchAll( DB::FETCH_ASSOC );
               $languagestring = $languagestring[0];
               $table->AddRow(
                              array(
                                 $language['code'] .
                                 $form->returnHidden(
                                                   array(
                                                      'field' => sprintf( 'strings][%s][translationid', $language['languageid'] , $languagestring['translationid'] ),
                                                      'default' => $languagestring['translationid']
                                                      )
                                                   ),
                                 $form->returnArea(
                                                   array(
                                                      'field' => sprintf( 'strings][%s][translation', $language['languageid'] ),
                                                      'default' => $languagestring['translation'],
                                                      'data' => array( 'height' => '18px; width: 300px; overflow: hidden;' ),
                                                      'attrdata' => "onfocus=\"setTextAreaHeightAuto(this); this.select();\" onkeyup=\"setTextAreaHeightAuto(this);\" onblur=\"this.style.height='18px'; var tmp=this.value; this.value=''; this.value=tmp;\" ",
                                                      )
                                                   ),
                                 $form->returnCheck(
                                                    array(
                                                      'field' => sprintf( 'strings][%s][confirmed', $language['languageid'] ),
                                                      'default' => $languagestring['confirmed'] == true ? true : false,
                                                      'data' => '1',
                                                      )
                                                   )
                                 )
                              );   
            
         }
         $form->addSubmit( __( 'Save' ) );
         
         $table->render();
      
      }
      
   }
   
?>