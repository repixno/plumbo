<?php

Dispatcher::extendView( 'create.photobook' );

class PosterCreator extends MediaclipProject implements IView {

   protected $module = "Print";
	protected $template = 'create.print';

}


?>