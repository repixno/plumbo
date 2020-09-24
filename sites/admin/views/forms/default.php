<?PHP
   
   import( 'pages.admin' );
   import( 'website.form.submission' );
   
   class FormsViewer extends AdminPage implements IView {
      
      protected $template = 'forms.list';
      protected $encoding = 'utf8';
      
      public function Execute() {
         
         $entries = array();
         foreach( DB::query( 'SELECT DISTINCT(identifier) FROM site_form_submissions' )->fetchAll() as $row ) {
            
            list( $entry ) = $row;
            
            $entries[] = array(
               'id' => base64_encode( $entry ),
               'name' => $entry,
            );
            
         }
         
         $this->entries = $entries;
         
      }
      
      public function csv( $identifier64 ) {
         
         header( 'Content-type: text/plain' );
         $this->encoding = 'utf8';
         $this->View( $identifier64 );
         
         $this->setTemplate( 'forms.csv' );
         
      }
      
      public function excel( $identifier64 ) {
         
         $this->encoding = 'latin1';
         
         $this->View( $identifier64 );
         
         $this->setTemplate( false );
         
         $oldreporting = error_reporting( 0 );
         
         require_once 'Spreadsheet/Excel/Writer.php';
         
         // Creating a workbook
         $workbook = new Spreadsheet_Excel_Writer();
         
         // sending HTTP headers
         $workbook->send( util::urlize( base64_decode( $identifier64 ) ).'.xls' );
         
         // Creating a worksheet
         $worksheet =& $workbook->addWorksheet( 'Report' );
         
         // write the data
         $worksheet->writeRow( $row++, 0, $this->fields );
         foreach( $this->entries as $line ) {
            $worksheet->writeRow( $row++, 0, $line );
         }
         
         // Let's send the file
         $workbook->close();
         
         error_reporting( $oldreporting );
         
      }
      
      public function View( $identifier64 ) {
         
         $this->setTemplate( 'forms.view' );
         
         $identifier = base64_decode( $identifier64 );
         $rows = array();
         
         $collection = new FormSubmission();
         foreach( $collection->collection( array( 'uid', 'email', 'portal', 'data', 'sent' ), array( 'identifier' => $identifier ), 'sent' )->fetchAll() as $row ) {
            
            list( $uid, $email, $portal, $data, $sent ) = $row;
            $data = unserialize( $data );
            
            if( $this->encoding == 'latin1' ) {
               
               $uid = utf8_decode( $uid );
               $email = utf8_decode( $email );
               $sent = utf8_decode( $sent );
               
               if( count( $data ) )
               foreach( $data as $key => $val ) {
                  $data[$key] = utf8_decode( $val );
               }
               
            }
            
            $fields['uid'] = true;
            $fields['email'] = true;
            $fields['portal'] = true;
            $fields['sent'] = true;
            
            if( count( $data ) )
            foreach( $data as $key => $val ) {
               $fields[$key] = true;
            }
            
            $rows[] = array_merge( array(
               'uid' => $uid,
               'email' => $email,
               'portal' => $portal,
               'sent' => $sent,
            ), $data );
            
         }
         
         $entries = array();
         foreach( $rows as $row ) {
            
            $record = array();
            foreach( $fields as $field => $d ) {
               $record[] = isset( $row[$field] ) ? $row[$field] : '';
            }
            
            $entries[] = $record;
            
         }
         
         $this->fields = array_keys( $fields );
         $this->entries = $entries;
         
      }
      
   }
   
?>