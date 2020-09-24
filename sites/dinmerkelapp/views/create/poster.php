<?php

Dispatcher::extendView( 'create.photobook' );

class PosterCreator extends MediaclipProject implements IView {

   protected $module = "Poster";
	protected $template = 'create.poster';

}


?>