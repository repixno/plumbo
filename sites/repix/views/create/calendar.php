<?php
Dispatcher::extendView( 'create.photobook' );

class CalendarCreator extends MediaclipProject implements IView {
	protected $module = "Calendar";

	protected $template = 'create.calendar';

}


?>