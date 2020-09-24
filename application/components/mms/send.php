<?php
// +-------------------------------------------------------------+
// | InteleSMS MMS SDK Version 1.70                              |
// +-------------------------------------------------------------+
// | Copyright (c) 2003-2010 Interweb Norge as, InteleSMS AS     |
// +-------------------------------------------------------------+
// | Authors:  Oyvind Selbek <oyvind@iw.no>                      |
// +-------------------------------------------------------------+
// | Purpose:  Provides an interface for sending MMS messages    |
// |           using SMSfun AS, Norway as MMS gateway provider.  |
// +-------------------------------------------------------------+
// | History:  1.70 - [OS] - Updated for use in PhotoBox Client  |
// |           1.60 - [OS] - Added support for PHP5/SoapClient   |
// |           1.50 - [OS] - Removed zLib requirement, added     |
// |                         two different transfer modes and    |
// |                         automatic detection between them.   |
// |                         Quite a few new functions!          |
// |           1.40 - [OS] - Added SetPrice and SetParameters    |
// |           1.30 - [OS] - Added proper error handling         |
// |           1.20 - [OS] - Added native .zip writer support    |
// |           1.10 - [OS] - Rewrote the script into a class     |
// |           1.00 - [OS] - Initial Test Implementation         |
// +-------------------------------------------------------------+
// | Requires: This class requires the PHP5 SOAP Client Library! |
// |                                                             |
// |           This class also requires zLib support in the PHP  |
// |           version compiling the script in order to support  |
// |           sending of compressed Multimedia Messages.        |
// |                                                             |
// |           For more information about how to meet this class |
// |           requirement, please consult the README file.      |
// +-------------------------------------------------------------+
//

/* CLASS MMSsend */
   class sfMMS {
      
      var $auth; /* contains authorization details */
      var $body; /* contains the message parameters */
      var $data; /* contains the filedata to be sent */
      var $size; /* contains the size of the zip-data */
      var $send; /* contains the final sending object */
      var $xfer; /* contains the selected xfermode */
      var $gzip; /* wether or not gzip is supported */
      var $surl; /* the service url to use for soap */
      var $client;    /* */
      var $namespace; /* namespace name */      
      
      /* CONSTRUCTOR sfMMS( [customerid], [password], [xfermode] )
         
         Purpose:    Create a new sfMMS object for sending messages,
                     and optionally configure service authorization.
         Parameters: int      customerid  - Your SMSfun CustomerID.
                     string   password    - Your SMSfun Password.
                     string   xfermode    - Auto or Raw xfermode.
         Example:    $mms = new sfMMS( 2103, 'kda2dcs3kxda231' );
                  or $mms = new sfMMS();
      */
      public function __construct( $customerid = 0, $password = '', $xfermode = 'auto' ) {

         $this->namespace="intelesms.services";

         /* detect and configure potential zip support */
         $this->data = new sfArchiveZIP();
         $this->gzip = function_exists( 'gzcompress' );
         
         /* configure service url */
         $this->ServiceURL();
         
         /* initialize SOAP and setup authorization */
         $this->client = new SoapClient( $this->surl."/scriptv2.asmx?WSDL", array(
            'trace' => true,
         ) );
         $this->Authorize( $customerid, $password );
         $this->SetXferMode( $xfermode );
         
         /* configure some defaults */
         $this->body['priority']   = 'Normal';
         $this->body['gateway']    = '1933';
         $this->body['price']      = '0';
         $this->body['expires']    = '';
         $this->body['messageid']  = '';
         $this->body['fromnumber'] = '';
         
      }
      
      /* FUNCTION ServiceURL( [urltype] )
      
         Purpose:    Used internally in the constructor, but can
                     also be used later on, when you want to change
                     the primary delivery gateway to the backup one
                     on a failure to send a message through "main".
         Parameters: int      urltype  - "Main" or "Backup" url.
         Example:    $mms->ServiceURL( 'backup' );
         Returns:    The actual ServiceURL that was set by the urltype.
      */
      function ServiceURL( $urltype = 'main' ) {
         switch( $urltype ) {
            case 'backup':
               $this->surl = "https://smsgw2.intelesms.no/mms";
               break;
            default:
               $this->surl = "https://smsgw.intelesms.no/mms";
               break;
         }
         
         return $this->surl;
      }
      
      /* FUNCTION Authorize( customerid, password )
      
         Purpose:    Used internally in the constructor, but can
                     also be used later on, when you want to either
                     change authorization after creating the object,
                     or if you want to authorize at a later time.
         Parameters: int      customerid  - Your SMSfun CustomerID.
                     string   password    - Your SMSfun Password.
         Example:    $mms->Authorize( 2103, 'kda2dcs3kxda231' );
         Returns:    Always returns True
      */
      function Authorize( $customerid, $password ) {
         $this->auth['customerid'] = $customerid;
         $this->auth['password']   = $password;
         return true;
      }
      
      /* FUNCTION SetXferMode( xfermode )
      
         Purpose:    Used internally in the constructor, but can
                     also be used later on, when you want to change
                     the xfermode to enforce raw mode or switch 
                     back to automatic (zlib if avail, else raw).
         Parameters: string   xfermode    - Auto or Raw xfermode.
         Example:    $mms->SetXferMode( 'raw' );
         Returns:    Returns True if it is a valid xfermode, False otherwise.
      */
      function SetXferMode( $xfermode ) {
         switch($mode = strtolower($xfermode)) {
            case 'auto':
            case 'raw':
               $this->xfer = $mode;
               $this->UpdateSize();
               return true;
               break;
            default:
               return false;
               break;
         }
      }
      
      
      /* FUNCTION GetMessageSize()
      
         Purpose:    This function is used to determine the
                     size of the message before sending. This
                     function is here so that you, the developer
                     can make sure the message fits within the 
                     size restrictions for your target cellphone.
         Parameters: None.
         Example:    $size = $mms->GetMessageSize();
         Returns:    Returns the size of the current message in bytes.
      */
      function GetMessageSize() {
         return $this->size ? $this->size : 0;
      }
      
      
      /* FUNCTION SetRecipient( recipient )
      
         Purpose:    Used internally in Send(), but can also be used 
                     by the programmer to set the recipient before 
                     sending off the message.
         Parameters: string   recipient   - recipient with countrycode
         Example:    $mms->SetRecipient( '4799442952' );
         Returns:    Always returns True
      */
      function SetRecipient( $recipient ) {
         $this->body['recipient'] = $recipient;
         return true;
      }
      
      
      /* FUNCTION SetSubject( subject )
      
         Purpose:    Used internally in Send(), but can also be used 
                     by the programmer to set the subject before sending
                     off the message. Only the first 40 chars will be sent.
         Parameters: string   subject  - a subject with up to 40 chars.
         Example:    $mms->SetSubject( 'Hey Oyvind Selbek!' );
         Returns:    Always returns True
      */
      function SetSubject( $subject ) {
         $this->body['subject'] = substr($subject,0,40);
         return true;
      }
      

      /* FUNCTION SetFromNumber( fromnumber )
      
         Purpose:    You can use SetFromNumber to set the "From:" field
                     of the MMS to something intelligent. WARNING!! This 
                     field also defines who will be billed for the message!
                     If you want the recipient to pay for the message, make
                     sure to ALWAYS set this to a blank string, or call the
                     function without any parameters.
         Parameters: string   fromnumber  - a number in 47xxxxxxxx form.
         Example:    $mms->SetFromNumber( '4799000000' );
         Returns:    Always returns True
      */
      function SetFromNumber( $fromnumber = '' ) {
         $this->body['fromnumber'] = $fromnumber;
         return true;
      }
      
      
      /* FUNCTION SetParameters( params )
      
         Purpose:    You can use SetParameters to set multiple fields
                     in just one function call to the mms object.
         Parameters: array parameters - An indexed array of params.
         Example:    $mms->SetParameters( Array( 'price' => 10 ) );
         Returns:    Returns true if params is an array, false otherwise.
      */
      function SetParameters( $params ) {
         
         if (!is_array( $params ) ) return false;
         foreach( $params as $param => $value ) {
            switch( strtolower($param) ) {
               
               case 'recipient':
                  $this->SetRecipient( $value );
                  break;
               case 'subject':
                  $this->SetSubject( $value );
                  break;
               case 'price':
                  $this->SetPrice( $value );
                  break;
               case 'priority':
                  $this->SetPriority( $value );
                  break;
               case 'messageid':
                  $this->SetMessageID( $value );
                  break;
               case 'gateway':
                  $this->SetGateway( $value );
                  break;
               case 'fromnumber':
                  $this->SetFromNumber( $value );
                  break;
            }
         }  return true;
      }
      
      
      /* FUNCTION SetPrice( price )
      
         Purpose:    You can use SetPrice to set the message price
                     in norwegian kroner. When sending to a foreign
                     supported MMS recipient, translation to the 
                     local currency might occur, and income pr. 
                     message may differ from the Norwegian prices.
                     At the time of writing, this is not an issue
                     as no other countries but Norway is supported,
                     however it might be in the future. Price is a
                     floating point number from 0 to 60 or 0 if you
                     want to send a free message (billed to you).
                     Please note that all prices must be whole 
                     numbers at the time of writing, and no decimals 
                     are allowed.
         Parameters: integer  price - 0 up to 60 in 1 steps or 0 
         Example:    $mms->SetPrice( 10 );
         Returns:    Returns true if input is valid, false otherwise.
      */
      function SetPrice( $price ) {
         
         if ($price==0||($price>=1&&$price<=60)) {
            $this->body['price'] = $price * 100;
            return true;
         } else {
            return false;
         }
         
      }
      
      
      /* FUNCTION SetPriority( priority )
      
         Purpose:    You can use SetPriority to set the message queue 
                     priority in the mms delivery systems. Messages
                     marked as "High" priority will be delivered faster
                     then messages marked as "Low" priority during high-
                     peek hours where a lot of messages are queued.
         Parameters: string   priority - Either Low, Normal or High.
         Example:    $mms->SetPriority( 'High' );
         Returns:    Always returns True
      */
      function SetPriority( $priority ) {
         $this->body['priority'] = $priority;
         return true;
      }
      
      
      /* FUNCTION SetMessageID( messageid )
      
         Purpose:    You can use SetMessageID to give the message an 
                     unique identifier in the MMS system. When you 
                     recieve a callback on your configured callback URL,
                     you will get this field passed in as well, so you
                     can trace the success or failure of any MMS.
         Parameters: string   messageid   - a messageid up to 30 chars.
         Example:    $mms->SetMessageID( 'MMS'.sprintf('%09d', $id ) );
         Returns:    Always returns True
      */
      function SetMessageID( $messageid ) {
         $this->body['messageid'] = $messageid;
         return true;
      }
      
      
      /* FUNCTION SetGateway( gateway )
      
         Purpose:    You can use SetGateway to set the MMS gateway to
                     which the message will be delivered. SMSfun AS
                     only supports MMS on 1933 at the time of writing,
                     but this as this might change in the future, you
                     can set this parameter to any numeric integer.
         Parameters: int   gateway - the gateway to send mmses through.
         Example:    $mms->SetGateway( '1933' );
         Returns:    Always returns True
      */
      function SetGateway( $gateway ) {
         $this->body['gateway'] = $gateway;
         return true;
      }
      
      
      /* FUNCTION AddTextPage( textpage )
      
         Purpose:    Adds a file to the MMS with the textdata from $textpage in it, 
                     under the filename 001 up to 999.txt.
         Parameters: string   textpage    - the textdata to be added to the file.
         Example:    $mms->AddTextPage( 'Hey, how are you?' );
         Returns:    True if the textpage was added to the message, false otherwise.
      */    
      function AddTextPage( $textpage ) {
         $GLOBALS[$mmspagenumber]++;
         if ($textpage) {
            $this->data->AddFileData( sprintf('%03d', $GLOBALS[$mmspagenumber])
                                       .'.txt', $textpage );
            $this->UpdateSize();
            return true;
         } else
            return false;
      }
      

      /* FUNCTION AddFileData( data, nameinarchive )
      
         Purpose:    Adds a file to the MMS with the data from $data in it, 
                     under the filename nameinarchive in the archive.
         Parameters: string   data        - the data to be added to the file.
                     string   nameinarchive - filename for the data in the mms.
         Example:    $mms->AddFileData( 'Hey!!', 'test.txt' );
                  or $mms->AddFileData( $apicture, 'my.jpg' );
         Returns:    True if the file was added to the message, false otherwise.
      */    
      function AddFileData( $data, $nameinarchive ) {
         
         if($this->data->AddFileData( $nameinarchive, $data ) ) {
            $this->UpdateSize();
            return true;
         } else {
            return false;           
         }
         
      }


      /* FUNCTION AddDiskFile( filename, [nameinarchive] )
      
         Purpose:    Adds a file from disk to the MMS. If nameinarchive is
                     not specified, the base filename on disk will be used.
                     ie.: /home/myuser/datafiles/boat.jpg gives boat.jpg.
         Parameters: string   filename    - the filename on disk. must exist!
                     string   nameinarchive - filename for the file in the mms.
         Example:    $mms->AddDiskFile( '/tmp/datafiles/boat.jpg' );
                  or $mms->AddDiskFile( '/tmp/datafiles/boat.jpg', 'my.jpg' );
         Returns:    True if the file was added to the message, false otherwise.
      */    
      function AddDiskFile( $filename, $nameinarchive = false ) {
         
         if (!file_exists($filename)) return false;
         if (!$nameinarchive) $nameinarchive = $this->data->ExtractFileName( $filename );
         
         $this->data->AddFile( $nameinarchive, $filename );
         $this->UpdateSize();
         return true;
      }


      /* FUNCTION GetLastError( [errorField] )
      
         Purpose:    getLastError() returns the last occured sending error.
         Parameters: string   field    - an errorField or none for both.
         Example:    $errorClass   = $mms->getLastError( 'errorClass' );
                  or $errorMessage = $mms->getLastError( 'errorMessage' );
                  or $fullErrorStr = $mms->getLastError();
                  or list($err,$msg)=$mms->getLastError('errorArray');
         Returns:    The chosen error string(s) from the gateway.
      */
      function GetLastError( $errorField = false ) {
         
         switch( $errorField ) {
            case 'errorClass':
               return $this->send->error;
               break;
            case 'errorMessage':
               return $this->send->errorMessage;
               break;
            case 'errorArray':
               return array( $this->send->error, $this->send->errorMessage );
               break;
            default:
               return $this->send->error.': '.$this->send->errorMessage;
               break;
         }
         
      }
      
      
      /* FUNCTION Send( [recipient], [subject] )
      
         Purpose:    Send() sends the already configured message
                     object to SMSfun for delivery. In order for
                     the programmer to quickly change subject and
                     recipient for sending the same message to 
                     multiple recipients with different subjects.
         Parameters: string   recipient   - recipient with countrycode
                     string   subject     - a subject with up to 40 chars.
         Example:    $mms->Send( '4799442952', 'Hey Oyvind Selbek!' );
                  or $mms->Send(); // You have to call SetRecipient() and
                                   // SetSubject() or SetParameters() first.
         Returns:    True on success, False otherwise. Use getLastError()
                     to get more complex information about any errors.
                     If you're experiencing really strange behaviour, you
                     might be having a SOAP problem. For debugging purposes,
                     you may try to print_r( $mms->sent ); which will be
                     either an stdClass with error and errorMessage members,
                     a SOAP Object with message status or a PEAR Error 
                     Object depending on the outcome of the multimedia 
                     message sending operation and the PEAR operations.
      */
      function Send( $recipient = false, $subject = false ) {
         
         /* configure recipient and subject, if applicable */
         if ($recipient) $this->SetRecipient( $recipient );
         if ($subject)   $this->SetSubject( $subject );
         
         /* some data verification before sending off */
         if (!$this->auth[customerid]) 
            return new sfErrorMMS( "Missing customerid field, try \$mms->Authorize() before \$mms->Send()!" );
            
         if (!$this->auth[password]) 
            return new sfErrorMMS( "Missing password field, try \$mms->Authorize() before \$mms->Send()!" );
            
         if (!$this->body[gateway]) 
            return new sfErrorMMS( "Missing gateway field, try \$mms->SetGateway() before \$mms->Send()!" );
            
         if (!$this->body[subject]) 
            return new sfErrorMMS( "Missing subject field, try \$mms->SetSubject() before \$mms->Send()!" );
            
         if (!$this->body[recipient]) 
            return new sfErrorMMS( "Missing recipient field, try \$mms->SetRecipient() before \$mms->Send()!" );
            
         if (!is_numeric($this->body[gateway])) 
            return new sfErrorMMS( "The gateway field must be numeric, try \$mms->SetGateway('1933') before \$mms->Send()!" );
            
         if (!is_numeric($this->body[recipient])) 
            return new sfErrorMMS( "The recipient field must be numeric, try \$mms->SetRecipient('4799000000') before \$mms->Send()!\nThis error can also occur if you accidently swap the parameters to the \$mms->Send() function." );
         
         if ($this->body[fromnumber])
         if (!is_numeric($this->body[fromnumber])) 
            return new sfErrorMMS( "The fromnumber field must be numeric or blank, try \$mms->SetFromNumber('4799000000') before \$mms->Send()!" );
            
         if ($this->body[priority]!='Low'&&$this->body[priority]!='Normal'&&$this->body[priority]!='High') 
            return new sfErrorMMS( "The priority field can only be one of these values: Low or Normal or High. Try \$mms->SetPriority() before \$mms->Send()!" );
         
         if (!count($this->data->rawfiles))
            return new sfErrorMMS( "Nothing to send! Try \$mms->AddTextPage() before \$mms->Send()!" );
         
         /* create the authorization header */
         $header = new SoapHeader(
            $this->namespace, 'Authorizer',
            array(
               'CustomerID'=> $this->auth['customerid'],
               'SubCustomerID'    => 0,
               'Password'  => $this->auth['password'],
            )
         );
         
         /* create the message body */
         $body = array( 
            'Gateway'          => $this->body['gateway'], 
            'Subject'          => $this->body['subject'], 
            'Recipient'        => $this->body['recipient'], 
            'Price'            => $this->body['price'], 
            'MessagePriority'  => $this->body['priority'], 
            'MessageID'        => $this->body['messageid'], 
            'FromNumber'       => $this->body['fromnumber'],
            'AgeLimit'         => 0,
            // These fields are currently documented, but not used:
            'ServiceID'        => 0, 
         );
         
         // append the period of validly set
         if( $this->body['expires'] ) {
            $body['ValidationPeriod'] = $this->body['expires'];
         }
         
         // set the correct xfermode (raw||zip)
         $xfermode = 'raw'; if( $this->xfer=='auto' && $this->gzip ) $xfermode = 'zip';
         if($xfermode=='zip') {
         
            /* take the zipdata and figure out the datalength */
            $zipdata = $this->data->ReturnArchive(false);
            $this->size = strlen($zipdata);
            
            file_put_contents( '/tmp/test.zip', $zipdata );
            
            /* Take whatever content is in the archive and encode it */
            $body['ContentType'] = 'Zip';
            $body['Content']     = array( 
                                          'File'       => $zipdata,
                                          'FileName'   => 'archive.zip',
                                    );
         } else {

            $this->size = $this->data->RawSize();
            
            /* for each file, add it manually to the message body */
            $body['ContentType'] = 'Raw';
            
            /* iterate through the raw files and add them to the body */
            foreach( $this->data->rawfiles as $rawfile ) {
               $body['Content'][] = $rawfile;
            }
            
         }
         
         /* add the header to myself */
         $this->client->__setSOAPHeaders( array( $header ) );
         
         /* finally, send and return the result */
         $this->send = $this->_SOAPSend( $body );
         
         #print_r( $this->client->__getLastRequest() );
         #print_r( $this->client->__getLastResponse() );
         
         /* return a sensible true/false value */
         return $this->send->SendResult->error=='Success' ? true : false;
         
      }

      function UpdateSize() {

         // set the correct xfermode (raw||zip)
         $xfermode = 'raw'; if( $this->xfer=='auto' && $this->gzip ) $xfermode = 'zip';
         if($xfermode=='zip') {
         
            /* take the zipdata and figure out the datalength */
            $this->size = strlen($this->data->ReturnArchive(false));
            
         } else {
            
            /* implement raw processing here */
            $this->size = $this->data->RawSize();
            
         }
         
      }
      
      function _SOAPSend( $mms ) {

         /* ...and finally ship it off and return the result */
         $retvar = $this->client->Send( array( 'mms' => $mms ) );
         
         /* ... */
         return $retvar;
         
      }
      
   }
   
   
   /* CLASS sfErrorMMS
   
      Purpose: Spawns an error dialog and kills the script
      
      Authors: Oyvind Selbek [oyvind@iw.no], Interweb Norge as

      History: 1.00 - [OS] - First and last version ;)
   */
   class sfErrorMMS {
      
      public function __construct( $errormessage ) {
         
         /* write out the error (html or plain) and... */
         if( $_SERVER['HTTP_HOST'] )
            echo "<pre><b>ERROR:</b> $errormessage</pre>";
         else
            echo "ERROR: $errormessage\n";
            
         /* ...die! ;) */
         die;
         
      }
      
   }

   class MMSSend extends sfMMS {
      function __construct( $clientid = 0, $password = '', $xfermode = '' ) {
         parent::__construct( $clientid, $password, $xfermode );
      }
   }
   
   
   /* CLASS sfArchive
   
      Purpose: Base class for inheriting archive implementations
      
      Authors: Oyvind Selbek [oyvind@iw.no], Interweb Norge as

      History: 1.00 - [OS] - First and last version ;)
   */
   class sfArchive {
      
      var $rawfiles;
      
      function AddFile( $nameinarchive, $localfile, $unixtime = 0 ) {
         
         if(!$unixtime) $unixtime = @filemtime($localfile);
         if(!$unixtime) $unixtime = 0; // make sure its integral false.
         return $this->_addFileData( @implode('',@file($localfile)), $nameinarchive, $unixtime );
         
      }
      
      function AddFileData( $nameinarchive, $data, $unixtime = 0 ) {
         
         if (!$unixtime) $unixtime = time();
         return $this->_addFileData( $data, $nameinarchive, $unixtime );
         
      }
      
      function RenderArchive( $renderContentType = true ) {
         
         echo $this->ReturnArchive( $renderContentType );
         
      }

      function ExtractFileName( $filename ) {
         return end(explode('/',$filename ) );
      }
      
      function ReturnArchive( $renderContentType = true ) {
         
         if ($renderContentType) header('content-type: '.$this->_getMIMEType);
         return $this->_getArchive();
         
      }
      
      function RawSize() {
         
         if( !is_array( $this->rawfiles ) ) return 0;
         foreach( $this->rawfiles as $file )
            $size += strlen( $file['File'] );
         return $size; // return the new raw-size
         
      }
      
      function SetRenderFilename( $RenderFileName ) {
         
         $RenderFileName = str_replace(' ', '_', $RenderFileName );
         header( "Content-Disposition: attachment; filename=$RenderFileName" ); // 
         
      }
      
   }
   
   
   /* CLASS sfArchiveZIP
   
      History:    1.00 - [OS] - 10. jul 2003 - Initial version
      
      Authors:    Oyvind Selbek <oyvind@iw.no>
      
      Based on    http://www.zend.com/codex.php?id=535&single=1
      code from:  By Eric Mueller <eric@themepark.com>
                  
                  http://www.zend.com/codex.php?id=470&single=1
                  by Denis125 <webmaster@atlant.ru>
                  
                  http://www.pkware.com/products/enterprise/white_papers/appnote.html
                  by PKWARE, Inc. www.pkware.com
      
   */
   class sfArchiveZIP extends sfArchive {
      
      var $datasec      = array();                            // Array to store compressed data
      var $ctrl_dir     = array();                            // Central directory
      var $eof_ctrl_dir = "\x50\x4b\x05\x06\x00\x00\x00\x00"; // End of central directory record
      var $old_offset   = 0;                                  // Last offset position
      
      public function __construct() {
         
         $this->gzip = function_exists( 'gzcompress' );
         
      }
      
      /* FUNCTION _unix2DosTime( $unixtime = 0 )
      
         Parameters:    int   $unixtime   default 0   - The UNIX timestamp to convert to DOStime
         Description:   Converts an Unix timestamp to a four byte DOS date and time format (date
                        in high two bytes, time in low two bytes allowing magnitude comparison).
         Returns:       the current date in a four byte DOS format
      */
      function _unix2DosTime($unixtime = 0) {
          $timearray = ($unixtime == 0) ? getdate() : getdate($unixtime);
      
          if ($timearray['year'] < 1980) {
            $timearray['year']    = 1980;
            $timearray['mon']     = 1;
            $timearray['mday']    = 1;
            $timearray['hours']   = 0;
            $timearray['minutes'] = 0;
            $timearray['seconds'] = 0;
          }
      
          return (($timearray['year'] - 1980) << 25) | ($timearray['mon'] << 21) | ($timearray['mday'] << 16) |
                  ($timearray['hours'] << 11) | ($timearray['minutes'] << 5) | ($timearray['seconds'] >> 1);
      }
      
      
      /* FUNCTION _addFileData($data, $name, $time = 0)
      
         Parameters:    string   $data file contents
                        string   $name name of the file in the archive (may contains the path)
                        int      $time the current UNIX timestamp of the file
         Description:   Adds "file" to archive.
         Returns:       the current date in a four byte DOS format
      */
      function _addFileData($data, $name, $time = 0)
      {
          $this->rawfiles[] = array(
                                       'File'       => $data,
                                       'FileName'   => $name,
                                    );
          if( !$this->gzip ) return true;
          
          $name     = str_replace('\\', '/', $name);
          $dtime    = dechex($this->_unix2DosTime($time));
          $hexdtime = '\x' . $dtime[6] . $dtime[7]
                    . '\x' . $dtime[4] . $dtime[5]
                    . '\x' . $dtime[2] . $dtime[3]
                    . '\x' . $dtime[0] . $dtime[1];
          eval('$hexdtime = "' . $hexdtime . '";');
         
          $fr   = "\x50\x4b\x03\x04";
          $fr   .= "\x14\x00";            // VER needed to extract
          $fr   .= "\x00\x00";            // Gen.purpose bit flag
          $fr   .= "\x08\x00";            // Compression method
          $fr   .= $hexdtime;             // Last mod time and date
         
          // "local file header" segment
          $unc_len = strlen($data);
          $crc     = crc32($data);
          $zdata   = gzcompress($data);
          $zdata   = substr(substr($zdata, 0, strlen($zdata) - 4), 2); // fix crc bug         
          $c_len   = strlen($zdata);
          $c_len   = $c_len + 12;                  // include below struct in length
          $fr      .= pack('V', $crc);             // crc32
          $fr      .= pack('V', $c_len);           // compressed filesize
          $fr      .= pack('V', $unc_len);         // uncompressed filesize
          $fr      .= pack('v', strlen($name));    // length of filename
          $fr      .= pack('v', 0);                // extra field length
          $fr      .= $name;
      
          // "file data" segment
          $fr .= $zdata;
      
          // "data descriptor" segment (optional but necessary if archive is not
          // served as file)
          $fr .= pack('V', $crc);                 // crc32
          $fr .= pack('V', $c_len);               // compressed filesize
          $fr .= pack('V', $unc_len);             // uncompressed filesize
      
          // add this entry to array
          $this->datasec[] = $fr;
          $new_offset      = strlen(implode('', $this->datasec));
      
          // now add to central directory record
          $cdrec = "\x50\x4b\x01\x02";
          $cdrec .= "\x00\x00";                // version made by
          $cdrec .= "\x14\x00";                // version needed to extract
          $cdrec .= "\x00\x00";                // gen purpose bit flag
          $cdrec .= "\x08\x00";                // compression method
          $cdrec .= $hexdtime;                 // last mod time & date
          $cdrec .= pack('V', $crc);           // crc32
          $cdrec .= pack('V', $c_len);         // compressed filesize
          $cdrec .= pack('V', $unc_len);       // uncompressed filesize
          $cdrec .= pack('v', strlen($name) ); // length of filename
          $cdrec .= pack('v', 0 );             // extra field length
          $cdrec .= pack('v', 0 );             // file comment length
          $cdrec .= pack('v', 0 );             // disk number start
          $cdrec .= pack('v', 0 );             // internal file attributes
          $cdrec .= pack('V', 32 );            // external file attributes - 'archive' bit set
      
          $cdrec .= pack('V', $this -> old_offset ); // relative offset of local header
          $this->old_offset = $new_offset;
      
          $cdrec .= $name;
      
          // optional extra field, file comment goes here
          // save to central directory
          $this->ctrl_dir[] = $cdrec;
          
          return true;
      }
      
      /* FUNCTION _getArchive()
      
         Parameters:    None
         Description:   Returns the ZIP file's content to constructor
         Returns:       The bytes which make up the complete ZIPfile.
      */
      function _getArchive()
      {
          $data    = implode('', $this->datasec);
          $ctrldir = implode('', $this->ctrl_dir);
      
          return
              $data.
              $ctrldir.
              $this->eof_ctrl_dir.
              pack('v', sizeof($this->ctrl_dir)).  // total # of entries "on this disk"
              pack('v', sizeof($this->ctrl_dir)).  // total # of entries overall
              pack('V', strlen($ctrldir)).         // size of central dir
              pack('V', strlen($data)).            // offset to start of central dir
              "\x00\x00";                          // .zip file comment length
      }
      
      /* FUNCTION _getMIMEType()
      
         Parameters:    None
         Description:   Returns the MIME type of this archive type.
         Returns:       The MIME type of this archive type.
      */
      function _getMIMEType() {
         
         return 'application/zip';
         
      }
      
   }
   
?>