<?php
namespace Schema;
use Schema\SchInteractAction;

class SchLeaveAction extends SchInteractAction{
	protected $event	=	'Event';
	function __construct(){$this->namespace = "LeaveAction";}
}