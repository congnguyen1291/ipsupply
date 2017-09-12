<?php
namespace Schema;
use Schema\SchAction;

class SchPlayAction extends SchAction{
	protected $audience	=	'Audience';
	protected $event	=	'Event';
	function __construct(){$this->namespace = "PlayAction";}
}