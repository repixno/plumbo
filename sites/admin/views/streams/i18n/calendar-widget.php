<?php
   
   class i18n_CalendarWidget extends WebPage implements IView {
      
      protected $template = false;
      
      public function Execute() {
         
         header( 'content-type: text/javascript' );
         
         $Sunday = __( 'Sunday' );
         $Monday = __( 'Monday' );
         $Tuesday = __( 'Tuesday' );
         $Wednesday = __( 'Wednesday' );
         $Thursday = __( 'Thursday' );
         $Friday = __( 'Friday' );
         $Saturday = __( 'Saturday' );

         $Sun = __( 'Sun' );
         $Mon = __( 'Mon' );
         $Tue = __( 'Tue' );
         $Wed = __( 'Wed' );
         $Thu = __( 'Thu' );
         $Fri = __( 'Fri' );
         $Sat = __( 'Sat' );
         
         $January = __( 'January' );
         $February = __( 'February' );
         $March = __( 'March' );
         $April = __( 'April' );
         $May = __( 'May' );
         $June = __( 'June' );
         $July = __( 'July' );
         $August = __( 'August' );
         $September = __( 'September' );
         $October = __( 'October' );
         $November = __( 'November' );
         $December = __( 'December' );
         
         $Jan = __( 'Jan' );
         $Feb = __( 'Feb' );
         $Mar = __( 'Mar' );
         $Apr = __( 'Apr' );
         $May = __( 'May' );
         $Jun = __( 'Jun' );
         $Jul = __( 'Jul' );
         $Aug = __( 'Aug' );
         $Sep = __( 'Sep' );
         $Oct = __( 'Oct' );
         $Nov = __( 'Nov' );
         $Dec = __( 'Dec' );
         
         $Close = __( 'Close' );
         $Today = __( 'Today' );
         $today = __( 'today' );
         $Time = __( 'Time' );
         $wk = __( 'wk' );
         
         $DisplayFirst = __( 'Display %s first', '%s' );
         $DragToMove = __( 'Drag to move' );
         $SelectDate = __( 'Select date' );
         $GoToday = __( 'Go Today' );
         $PrevYear = __( 'Prev. year (hold for menu)' );
         $NextYear = __( 'Next year (hold for menu)' );
         $PrevMonth = __( 'Prev. month (hold for menu)' );
         $NextMonth = __( 'Next month (hold for menu)' );
         $AboutTheCalendar = __( 'About the calendar' );
         
         echo <<<JAVASCRIPT
// ** I18N

// full day names
Calendar._DN = new Array
("$Sunday",
 "$Monday",
 "$Tuesday",
 "$Wednesday",
 "$Thursday",
 "$Friday",
 "$Saturday",
 "$Sunday");

// Please note that the following array of short day names (and the same goes
// for short month names, _SMN) isn't absolutely necessary.  We give it here
// for exemplification on how one can customize the short day names, but if
// they are simply the first N letters of the full name you can simply say:
//
//   Calendar._SDN_len = N; // short day name length
//   Calendar._SMN_len = N; // short month name length
//
// If N = 3 then this is not needed either since we assume a value of 3 if not
// present, to be compatible with translation files that were written before
// this feature.

// short day names
Calendar._SDN = new Array
("$Sun",
 "$Mon",
 "$Tue",
 "$Wed",
 "$Thu",
 "$Fri",
 "$Sat",
 "$Sun");

// full month names
Calendar._MN = new Array
("$January",
 "$February",
 "$March",
 "$April",
 "$May",
 "$June",
 "$July",
 "$August",
 "$September",
 "$October",
 "$November",
 "$December");

// short month names
Calendar._SMN = new Array
("$Jan",
 "$Feb",
 "$Mar",
 "$Apr",
 "$May",
 "$Jun",
 "$Jul",
 "$Aug",
 "$Sep",
 "$Oct",
 "$Nov",
 "$Dec");

// tooltips
Calendar._TT = {};
Calendar._TT["INFO"] = "$AboutTheCalendar";

Calendar._TT["ABOUT"] = "Calendar Control Version: ";

Calendar._TT["PREV_YEAR"] = "$PrevYear";
Calendar._TT["PREV_MONTH"] = "$PrevMonth";
Calendar._TT["GO_TODAY"] = "$GoToday";
Calendar._TT["NEXT_MONTH"] = "$NextMonth";
Calendar._TT["NEXT_YEAR"] = "$NextYear";
Calendar._TT["SEL_DATE"] = "$SelectDate";
Calendar._TT["DRAG_TO_MOVE"] = "$DragToMove";
Calendar._TT["PART_TODAY"] = " ($today)";

// the following is to inform that "%s" is to be the first day of week
// %s will be replaced with the day name.
Calendar._TT["DAY_FIRST"] = "$DisplayFirst";

// This may be locale-dependent.  It specifies the week-end days, as an array
// of comma-separated numbers.  The numbers are from 0 to 6: 0 means Sunday, 1
// means Monday, etc.
Calendar._TT["WEEKEND"] = "0,6";

Calendar._TT["CLOSE"] = "$Close";
Calendar._TT["TODAY"] = "$Today";
Calendar._TT["TIME_PART"] = "(Shift-)Click or drag to change value";

// date formats
Calendar._TT["DEF_DATE_FORMAT"] = "%Y-%m-%d";
Calendar._TT["TT_DATE_FORMAT"] = "%a, %b %e";

Calendar._TT["WK"] = "$wk";
Calendar._TT["TIME"] = "$Time:";
JAVASCRIPT;
         
      }
      
   }

?>