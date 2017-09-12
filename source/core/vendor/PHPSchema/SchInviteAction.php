<?php
namespace Schema;
use Schema\SchCommunicateAction;

class SchInviteAction extends SchCommunicateAction{
	protected $event	=	'Event';
	function __construct(){$this->namespace = "InviteAction";}
}