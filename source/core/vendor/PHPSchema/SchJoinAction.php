<?php
namespace Schema;
use Schema\SchInteractAction;

class SchJoinAction extends SchInteractAction{
	protected $event	=	'Event';
	function __construct(){$this->namespace = "JoinAction";}
}